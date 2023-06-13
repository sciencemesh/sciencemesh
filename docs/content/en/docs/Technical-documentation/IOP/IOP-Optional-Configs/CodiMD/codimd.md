---
title: "CodiMD"
linkTitle: "CodiMD"
weight: 31
description: >
    Real-time collaborative markdown editing with [CodiMD](https://github.com/hackmdio/codimd)
---

CodiMD is not built upon the WOPI Protocol (as it happens with [Collabora Code](../../iop/deployment/wopiserver)), yet the CS3 WOPI Server includes a "bridge" extension that can be configured to support CodiMD.

## Configuring the WOPI Server for CodiMD

A small configuration patch needs to be applied to an IOP deployment to enable the WOPI Server to interact with CodiMD:

```yaml
wopiserver:
  config:
    codimd:
      enabled: true
      inturl: http://meshapps-codimd
```

Refer to the [chart's documentation](https://artifacthub.io/packages/helm/cs3org/wopiserver) for a complete reference of all the configuration options available. Once ready, the release can be patched with the following command:

```bash
helm upgrade -i iop sciencemesh/iop --reuse-values -f wopiserver.yaml
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

```toml
[grpc.services.appprovider]
driver = "wopi"
app_provider_url = "iop-gateway:19001"
mime_types = ["text/markdown", "application/compressed-markdown", "text/plain", "text/html"]

[grpc.services.appprovider.drivers.wopi]
iop_secret = "REVA_IOPSECRET"    # must match the `/etc/wopi/iopsecret` deployed in the wopiserver image
wopi_url = "https://your_wopi_server:port"
app_name = "CodiMD"
app_url = "https://your_codimd_server"
insecure_connections = true    # if you need to disable SSL verification

[grpc.services.appregistry]
driver = "static"

[grpc.services.appregistry.drivers.static]
mime_types = [
        { mime_type = "text/markdown", extension = "md", name = "Markdown file", description = "Markdown text", default_app = "CodiMD", allow_creation = true }
        { mime_type = "application/compressed-markdown", extension = "zmd", name = "CodiMD file", description = "Compressed Markdown with images", default_app = "CodiMD", allow_creation = true }
        ...
]
```

Note that CodiMD supports a custom MIME type named `application/compressed-markdown`, but currently the support of custom MIME types is not functioning in Reva. Once fixed, it will be supported as well.
