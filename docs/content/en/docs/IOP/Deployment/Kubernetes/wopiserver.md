---
title: "Deploying and configuring the WOPI Server with Collabora Code"
linkTitle: "WOPI Server"
weight: 17
description: >
  Enable the deployment of the WOPI Server as part of the IOP.
---

## Prerequisites

- This tutorial focuses on enabling the [`WOPI Server`](https://github.com/cs3org/wopiserver) as part of an **existing IOP deployment**. For detailed instructions on how to achieve the latter, [refer to the Kubernetes section of this documentation](https://developer.sciencemesh.io/docs/iop/deployment/kubernetes).

> **Note:** Verify you have the `sciencemesh/iop` chart sources on version `0.0.3` or greater:

```bash
helm repo update
Hang tight while we grab the latest from your chart repositories...
...Successfully got an update from the "sciencemesh" chart repository
Update Complete. ⎈ Happy Helming!⎈

helm search repo sciencemesh/iop
NAME                    	CHART VERSION	APP VERSION	DESCRIPTION
sciencemesh/iop         	0.0.3        	0.0.1      	ScienceMesh IOP is the reference Federated Scie...
```

## (Optional) Deploying a Collabora Code instance on your cluster

To demonstrate how an office-online application provider can integrate with the IOP through the WOPI protocol, we need an actual provider we can connect to. If you don't have such application at hand, don't worry. You can use a self-contained [Collabora Code](https://www.collaboraoffice.com/code/) installation on Kubernetes instead.

The official Helm chart for [`stable/collabora-code`](https://hub.helm.sh/charts/stable/collabora-code) can be used to deploy everything on our cluster in a very simple way.

First, we need to add the Chart's repo to our helm sources:

```bash
helm repo add stable https://kubernetes-charts.storage.googleapis.com/
"stable" has been added to your repositories

helm repo update
Hang tight while we grab the latest from your chart repositories...
...Successfully got an update from the "sciencemesh" chart repository
...Successfully got an update from the "stable" chart repository
Update Complete. ⎈ Happy Helming!⎈

helm search repo stable/collabora-code
NAME                 	CHART VERSION	APP VERSION	DESCRIPTION
stable/collabora-code	1.0.6        	4.0.3.1    	A Helm chart for Collabora Office - CODE-Edition
```

Next, we need to create a minimal YAML file holding the custom deployment values:

- `collabora.domain` which points to the `wopiserver` url. Since we're running it all on the same cluster, we can use the service [DNS record](https://kubernetes.io/docs/concepts/services-networking/dns-pod-service/#services) for this.
- `collabora.server_name` will be used to generate the public URLs to be passed to the users.

> **Note:** notice we need to slash-escape periods for this value: `<hostname\.domain\.tld>`.

- The `ingress.paths` needs to be configured according to the [Collabora Code reverse-proxy documentation](https://www.collaboraoffice.com/code/apache-reverse-proxy/).

Putting it all together, we will end up with something similar to:

```bash
cat << EOF > collabora.yaml
collabora:
  domain: iop-wopiserver
  server_name: <hostname\.domain\.tld>
ingress:
  enabled: true
  annotations:
    kubernetes.io/ingress.class: nginx
    nginx.ingress.kubernetes.io/ssl-redirect: "true"
  hosts:
  - <hostname.domain.tld>
  paths:
  - /loleaflet
  - /hosting/discovery
  - /hosting/capabilities
  - /lool
  - /lool/adminws
  - /lool/(.*)/ws$
EOF
```

And now, to deploy it on the cluster, we just need to install the chart as follows:

```bash
helm upgrade -i collabora stable/collabora-code -f collabora.yaml
```

## Enabling wopiserver on the IOP deployment

For convenience, wopiserver ships as a separate chart from Reva but it comes bundled on the IOP. It can be explicitly enabled via feature flag.

> **Note:** the wopiserver chart has been developed with a simplified ingress configuration (i.e. single `host` and `path`) to ease the deployment. If you need to expose multiple hosts/paths, please open a [feature request on cs3org/charts](https://github.com/cs3org/charts/issues/new) to support this feature.

The most important settings you'll need to provide are the `wopiserver.config.appProviders.codeurl` and `wopiserver.config.cs3.revahost`: the endpoints for Collabora Code and Reva, respectively. [Additional values](https://github.com/cs3org/wopiserver/blob/master/wopiserver.conf) can also be configured.

If Code was deployed inside the cluster following the steps on the previous section, this configuration will connect all the required services together:

```bash
cat << EOF > wopiserver.yaml
wopiserver:
  config:
    appProviders:
      codeurl: http://collabora-collabora-code:9980
    cs3:
      revahost: iop-revad:19000
  ingress:
    enabled: true
    annotations:
      kubernetes.io/ingress.class: nginx
      nginx.ingress.kubernetes.io/ssl-redirect: "true"
    hostname: <hostname>
    path: /wopi
EOF
```

Last, we just need to install/upgrade the IOP stack by passing this custom configuration as well as the `wopiserver.enable` flag.

> **Note:** If it is the first time deploying the IOP, you should also provide [the custom configuration for Reva](https://developer.sciencemesh.io/docs/iop/deployment/kubernetes/#configuring-an-iop-deployment). Otherwise helm will pick the values from the last deployment unless `--reset-values` is specified.

```bash
helm upgrade -i iop sciencemesh/iop \
  --set wopiserver.enable=true \
  -f wopiserver.yaml
```

## Configure REVA for the WOPI Server

For the `reva open-file-in-app-provider` client subcommand to work, two additional services need to be configured in the gRPC gateway:

- [`appprovider`](https://reva.link/docs/config/grpc/services/appprovider/).
- [`appregistry`](https://reva.link/docs/config/grpc/services/appregistry/).

Here is an example on some parameters that need to be configured in the reva daemon. They include the WOPI server location as well as some static rules to match different MIME types to be opened with it:

```toml
[grpc.services.gateway]
appprovidersvc = "iop-gateway:19000"
appregistry = "iop-gateway:19000"

[grpc.services.appprovider]
driver = "demo"
wopiurl = "https://<hostname>/"

[grpc.services.appregistry]
driver = "static"

[grpc.services.appregistry.static.rules]
"text/plain" = "iop-gateway:19000"
"text/markdown" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.text" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.spreadsheet" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.presentation" = "iop-gateway:19000"
```

Lastly, a shared secret must also be provided in REVA with one of the available options:

| Environment variable         | Config file                           | Value                                                          |
|------------------------------|---------------------------------------|----------------------------------------------------------------|
| `REVA_APPPROVIDER_IOPSECRET` | `grpc.services.appprovider.iopsecret` | Shared secret used to connect REVA with the WOPI Server.       |

This setting can be shared amongst the two deployments by either:

- Defining one and passing it as value for the `--set wopiserver.config.iopsecret` and `--set gateway.env.REVA_APPPROVIDER_IOPSECRET` flags.
- Or provisioned by the WOPI Server chart as a Secret and consumed by REVA by passing:

```yaml
extraEnv:
   - name: REVA_APPPROVIDER_IOPSECRET
     valueFrom:
       secretKeyRef:
         name: iop-wopiserver-secrets
         key: iopsecret
```

## Testing the deployment

To test wopiserver's `open` workflow with Reva, refer to the detailed [how-to on wopiserver's README](https://github.com/cs3org/wopiserver#test-the-open-workflow-with-reva).

> **Note:** for the `x-access-token` on step 3 after a successful login, you can use the contents of the `.reva-token` file created by reva on your (client's) home directory.

