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

This is provided by the `sciencemesh/meshapps` chart - which relies on the official Helm chart for [`stable/collabora-code`](https://hub.helm.sh/charts/stable/collabora-code) and can be used to deploy everything on our cluster in a very simple way.

First, we need to update our helm sources:

```bash
helm repo update
Hang tight while we grab the latest from your chart repositories...
...Successfully got an update from the "sciencemesh" chart repository
Update Complete. ⎈ Happy Helming!⎈

helm search repo sciencemesh/meshapps
NAME                 	CHART VERSION	APP VERSION	DESCRIPTION
sciencemesh/meshapps  0.0.1        	0.0.1    	  Umbrella-repository of apps supported by the IOP and its adapters
```

Next, we need to create a minimal YAML file holding the custom `collabora-code` deployment values:

- `collabora.domain` which points to the `wopiserver` url. Since we're running it all on the same cluster, we can use the service [DNS record](https://kubernetes.io/docs/concepts/services-networking/dns-pod-service/#services) for this.
- `collabora.server_name` will be used to generate the public URLs to be passed to the users.

> **Note:** notice we need to slash-escape periods for this value: `<hostname\.domain\.tld>`.

- The `collabora-code.ingress.paths` need to be configured according to the [Collabora Code reverse-proxy documentation](https://www.collaboraoffice.com/code/apache-reverse-proxy/).

Putting it all together, we will end up with something similar to:

```bash
cat << EOF > collabora.yaml
collabora-code:
  enabled: true
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
helm upgrade -i meshapps sciencemesh/meshapps -f collabora.yaml
```

## Configure REVA to integrate with the WOPI Server

For the WOPI Server to be able to work together with Reva, two additional services need to be configured in the gRPC gateway:

- [`appprovider`](https://reva.link/docs/config/grpc/services/appprovider/).
- [`appregistry`](https://reva.link/docs/config/grpc/services/appregistry/).

Here is an example on some parameters that need to be set in the reva daemon. They include the WOPI Server location inside the cluster as well as some static rules to match different MIME types to be opened with it:

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
"application/vnd.oasis.opendocument.text" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.spreadsheet" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.presentation" = "iop-gateway:19000"
```

Lastly, a shared secret must also be provided in Reva with one of the available options:

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
      codeurl: http://meshapps-collabora-code:9980
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

> **Note:** If it is the first time deploying the IOP, you should also provide [the custom configuration for Reva](https://developer.sciencemesh.io/docs/iop/deployment/kubernetes/#configuring-an-iop-deployment) in addition to the one for the WOPI Server. Otherwise, you will need to use helm's `--reuse-values` to keep your previous config.

```bash
helm upgrade -i iop sciencemesh/iop \
  --set wopiserver.enable=true \
  -f wopiserver.yaml \
  --reuse-values
```

## Testing the integration with the `open-file-in-app-provider` sub-command

Reva's `open-file-in-app-provider` subcomand allows an authenticated user to generate a URL pointing to the application provider that will open the file for viewing/editing (depending on the `-viewmode` flag).

Behind the scenes, Reva will ask the WOPI server to generate a unique token and URI to access the file through the CS3 APIs. The WOPI Server will determine what is the right application to open such file (based on the available `wopiserver.config.appProviders` config) and abstract all the implementation details from the user (e.g. save operations, file locking, etc.).

The upload-open workflow can be all carried out from the `reva` cli:

```bash
reva upload sciencemesh.odt /home/sciencemesh.odt
Local file size: 11176 bytes
Data server: https://<hostname.domain.tld>/iop/datagateway
Allowed checksums: [type:RESOURCE_CHECKSUM_TYPE_MD5 priority:100  type:RESOURCE_CHECKSUM_TYPE_UNSET priority:1000 ]
Checksum selected: RESOURCE_CHECKSUM_TYPE_MD5
Local XS: RESOURCE_CHECKSUM_TYPE_MD5:21751696b491472501ffa1ec6cc3021d
 10.91 KiB / 10.91 KiB [======================================================================================================================] 100.00% 0s
File uploaded: 123e4567-e89b-12d3-a456-426655440000:fileid-<user>%2Fsciencemesh.odt 11176 /home/sciencemesh.odt

reva open-file-in-app-provider -viewmode write /home/sciencemesh.odt
App provider url: https://<hostname.domain.tld>/loleaflet/ed4f732/loleaflet.html?permission=edit&WOPISrc=https%3A%2F%2F<hostname.domain.tld>%2Fwopi%2Ffiles%2F123e4567-e89b-12d3-a456-426655440000-3380161333240892090&access_token=<token>
```

### Collaborative editing workflows

Effectively, a site running the WOPI Server configured and enabled together with the IOP as described above, can share files (via the [OCM Share](https://reva.link/docs/tutorials/share-tutorial/) mechanism) with a second user. Even if the recipient site does not have a WOPI Server deployed. This mechanism relies on Reva's capacity to forward the requests to `open-file-in-app-provider` from the receiving site to the original grantor's.

If the originating site is also connected to a WOPI-compatible application, that supports the file's MIME type and **has also collaborative-editing capabilities** (like e.g. Collabora Code), the document can be opened by both users at the same time. In this session, users are able to see each other's contributions and changes and save their progress on the original document,  living on the original storage backend - no matter what it is.
