---
title: "CodiMD"
linkTitle: "CodiMD"
weight: 31
description: >
    Real-time collaborative markdown editing with [CodiMD](https://github.com/hackmdio/codimd)
---

CodiMD is not built upon the [WOPI Protocol](https://wopi.readthedocs.io/en/latest/) (as it happens with [Collabora Code](../../iop/deployment/wopiserver)), but the CS3 WOPI Server includes a "bridge" extension that needs to be configured to support CodiMD.

## Configuring the WOPI Server for CodiMD

A small configuration patch needs to be applied to an IOP deployment to enable the WOPI Server to interact with CodiMD:

```yaml
wopiserver:
  config:
    codimd:
      enabled: true
      inturl: http://meshapps-codimd
```

> **Note:** depending on the ingress controller path-matching policies, you might experience routing issues between requests addressed to the WOPI Server and the Bridge (as their `path`-s can collide). Review your controller's [path priority](https://kubernetes.github.io/ingress-nginx/user-guide/ingress-path-matching/#path-priority) policies and use an alternative path in case that could be an issue.

Refer to the [chart's documentation](https://artifacthub.io/packages/helm/cs3org/wopiserver#wopi-bridge-configuration) for a complete reference of all the configuration options available. Once ready, the release can be patched with the following command:

```bash
helm upgrade -i iop sciencemesh/iop --reuse-values -f wopibridge.yaml
```

## Deploying CodiMD through the `sciencemesh/meshapps` chart

To deploy the application, the `sciencemesh/meshapps` umbrella chart already provides a handful of defaults for a lightweight and "_stateless_" CodiMD installation:

- Based on a patched `codimd` container image (`gitlab-registry.cern.ch/authoring/notes/codimd:cernbox-integration`) with some extra features to enable this workflow.

The only key aspects left for the user to configure are:

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
    extraEnvironmentVariables:
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

2. Add a mapping between the custom `.zmd` file extension, used to package markdown files together with uploaded images, and the `application/compressed-markdown` MIME-type used to identify these files. This needs to be done on all the running `storageprovider` services: whether using a [standalone](../../iop/deployment/kubernetes) deployment or one based on [multiple, decoupled storage providers](../../iop/deployment/kubernetes/providers).

```toml
[grpc.services.storageprovider.mimetypes]
".zmd" = "application/compressed-markdown"
```
