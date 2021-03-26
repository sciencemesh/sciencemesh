---
title: "CodiMD"
linkTitle: "CodiMD"
weight: 31
description: >
    Real-time collaborative markdown editing with [CodiMD](https://github.com/hackmdio/codimd)
---

Since CodiMD is not built upon the [WOPI Protocol](https://wopi.readthedocs.io/en/latest/) (as it happens with [Collabora Code](../../iop/deployment/wopiserver)), an additional component needs to be deployed alongside the WOPI Server to fill this gap between the application provider and the application itself.

## Deploying and configuring the WOPI Bridge

Bridging that gap will be the job for the **WOPI Bridge**: a sidecar container to translate CodiMD API calls into file operations and vice-versa.

A small configuration patch needs to be applied to an IOP deployment to enable this new component and plug it into the WOPI Server application providers. The main parameters to configure being:

- The WOPI Server configuration needs to be extended to connect the WOPI Bridge via the `config.appProviders.wopibridgeurl` parameter.
- `CODIMD_INT_URL` connects the bridge and the CodiMD service running inside the cluster.
- The `CODIMD_EXT_URL` will be used to generate the URL presented to the end-user when `open-file-in-app-provider` is called in reva.

```yaml
wopiserver:
  config:
    appProviders:
      wopibridgeurl: https://<hostname.domain.tld>/wopib
  wopibridge:
    enabled: true
    env:
      APP_ROOT: /wopib
      CODIMD_EXT_URL: https://<hostname.domain.tld>/codimd
      CODIMD_INT_URL: http://meshapps-codimd
    ingress:
      enabled: true
      annotations:
        kubernetes.io/ingress.class: nginx
        nginx.ingress.kubernetes.io/ssl-redirect: "true"
      hostname: <hostname.domain.tld>
      path: /wopib
```

> **Note:** depending on the ingress controller path-matching policies, you might experience routing issues between requests addressed to the WOPI Server and the Bridge (as their `path`-s can collide). Review your controller's [path priority](https://kubernetes.github.io/ingress-nginx/user-guide/ingress-path-matching/#path-priority) policies and use an alternative path in case that could be an issue.

Refer to the [chart's documentation](https://artifacthub.io/packages/helm/cs3org/wopiserver#wopi-bridge-configuration) for a complete reference of all the configuration options available. Once ready, the release can be patched with the following command:

```bash
helm upgrade -i iop sciencemesh/iop --reuse-values -f wopibridge.yaml
```

## Deploying CodiMD through the `sciencemesh/meshapps` chart

To deploy the application, the `sciencemesh/meshapps` umbrella chart already provides a handful of defaults for a lightweight and "_stateless_" CodiMD installation:

- Based on a patched `codimd` container image (`gitlab-registry.cern.ch/authoring/notes/codimd:cernbox-integration`) with some extra features to enable this workflow.
- Enables anonymous access, viewing, and editing.
- Disables some defaults from the vanilla CodiMD installation:
  - The registration and authentication mechanisms.
  - The image store and PostgreSQL disk persistence, as it will rely on the IOP Storage Providers to read and write all the content, including the attached pictures. The CodiMD pods will maintain ephemeral, in-memory persistence for the time being for its internal use.

The only key aspects left for the user to configure are:

- The location for the `CMD_SAVE_WEBHOOK` env. variable: pointing to the `/save` endpoint of the WOPI Bridge, that will be called when a document is closed.

> **Note:** this webhook needs to be served over HTTPS to work with CodiMD, hence the need to use its external FQDN.

- A combination of `CMD_URL_PATH` and an ingress rewrite annotation to strip the base URL (`/codimd`) from external requests.
- A `postgresql.postgresqlPassword` to protect the database that will be used as a temporary cache for the notes.

> **Note:** alternatively, MariaDB can be used instead of PostgreSQL when deploying CodiMD. Refer to [the chart's parameters](https://artifacthub.io/packages/helm/codimd/codimd#deploy-an-internal-database-parameters) for all the options available.

Here's an example `codimd.yaml` combining all these:

```yaml
codimd:
  enabled: true
  codimd:
    connection:
      domain: <hostname.domain.tld>
      protocolUseSSL: true
    noteCreation:
      freeUrlEnabled: true
    extraEnvironmentVariables:
      CMD_SAVE_WEBHOOK: https://<hostname.domain.tld>/wopib/save
      CMD_URL_PATH: codimd
  postgresql:
    postgresqlPassword: <password>
  ingress:
    annotations:
      kubernetes.io/ingress.class: nginx
      nginx.ingress.kubernetes.io/rewrite-target: /$2
      nginx.ingress.kubernetes.io/ssl-redirect: "true"
    enabled: true
    hosts:
    - host: <hostname.domain.tld>
      paths:
      - /codimd(/|$)(.*)
```

After this file is defined, it's just a matter of upgrading the chart while reusing any previous values, as follows:

```bash
helm upgrade -i meshapps sciencemesh/meshapps --reuse-values -f codimd.yaml
```

### Registering the MIME-types and custom file extensions on your Storage Provider

There are three extra steps left for the IOP to integrate with this application. These will require modifying the Revad configuration to identify the files that will be opened by CodiMD:

1. Append a new set of rules in the `appregistry` service running on the gateway to identify these files:

```toml
[grpc.services.appregistry.static.rules]
"text/markdown" = "iop-gateway:19000"
"application/compressed-markdown" = "iop-gateway:19000"
```

2. Optionally, the `wopibridge` URL might be inserted as part of the `appprovider` settings on the gateway; to generate a shorter and user-friendlier URL when opening from the reva cli.

```toml
[grpc.services.appprovider]
wopibridgeurl = "https://<hostname.domain.tld>/wopib"
```


3. Add a mapping between the custom `.zmd` file extension, used to package markdown files together with uploaded images, and the `application/compressed-markdown` MIME-type used to identify these files. This needs to be done on all the running `storageprovider` services: whether using a [standalone](../../iop/deployment/kubernetes) deployment or one based on [multiple, decoupled storage providers](../../iop/deployment/kubernetes/providers).


```toml
[grpc.services.storageprovider.mimetypes]
".zmd" = "application/compressed-markdown"
```
