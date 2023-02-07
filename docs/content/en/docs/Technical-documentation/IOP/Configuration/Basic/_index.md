---
title: 'Basic ScienceMesh services'
linkTitle: 'Basic services'
weight: 15
description: >
  How a Reva IOP site should be configured to provide a minimal set of services **required** for [joining the Science Mesh](/docs/how-to-join-sciencemesh/).
---

> NOTE: service settings may need to be changed depending on your actual deployment environment and chosen method of [Deployment](/docs/technical-documentation/iop/deployment/).

## Server settings

### GRPC ([docs 📖](https://reva.link/docs/config/grpc/))

Configures server endpoint for GRPC services, e.g. the address to listen on.

```toml
[grpc]
address = "0.0.0.0:19000"
```

### HTTP ([docs📖](https://reva.link/docs/config/http/))

Configures HTTP server endpoint — the address to listen on, enabled HTTP services & middlewares.

```toml
[http]
address = "0.0.0.0:19001"
enabled_services = ["ocmd"]
enabled_middlewares = ["providerauthorizer", "cors"]
```

### SSL/TLS

HTTP server uses insecure TCP communication by default,
to enable encryption, add the following options:

```toml
keyfile = "/etc/revad/ssl.key"
certfile = "/etc/revad/ssl.crt"
```

## Shared settings

Shared options applies to all configured IOP services.

```toml filename:revad.toml
[shared]
gatewaysvc = "iop-gateway:19000"
jwt_secret = "$something secret$"
```

> We have to set an address of the Reva [gateway](https://reva.link/docs/config/grpc/services/gateway/)
> service as many other services refers to the gateway GRPC service to make/handle their requests.
> Here we expect the service to run under (at least locally) resolvable `iop-gateway`
> hostname and bind to a (default) port of `19000`.

## Gateway settings ([docs 📖](https://reva.link/docs/config/grpc/services/gateway/))

Gateway service is the main entrypoint to the IOP, handling and forwarding
most of the requests — it should know the locations of any other services
running in the IOP instance.

```toml filename=revad.tom
[grpc.services.gateway]
commit_share_to_storage_grant = false
commit_share_to_storage_ref = true
datagateway = "https://$yourdomain.iop$/datagateway"
transfer_expires = 6

appprovidersvc = "iop-gateway:19000"
appregistry = "iop-gateway:19000"
authregistrysvc = "iop-gateway:19000"
ocmcoresvc = "iop-gateway:19000"
ocminvitemanagersvc = "iop-gateway:19000"
ocmproviderauthorizersvc = "iop-gateway:19000"
ocmshareprovidersvc = "iop-gateway:19000"
preferencessvc = "iop-gateway:19000"
publicshareprovidersvc = "iop-gateway:19000"
storageregistrysvc = "iop-gateway:19000"
userprovidersvc = "iop-gateway:19000"
usershareprovidersvc = "iop-gateway:19000"
```

> NOTE: Gateway implicitly expects that every service runs under the same `host:port` as itself.
> Here we can explicitly define/override default location by appending `svc` to service name. [Here](https://reva.link/docs/config/grpc/services/) is the complete list of services available.

> NOTE: `datagateway` should be an externally accessible URL of your IOP deployment

## Storage registry

Here we define that static mapping of storage mountpoints to storage providers should
be used, and mountpoint used for user's homes:

```toml
[grpc.services.storageregistry]
driver = "static"

[grpc.services.storageregistry.drivers.static]
home_provider = "/home"
```

The following enables the required [Data gateway](https://reva.link/docs/config/http/services/datagateway/) HTTP service, using default config.

```toml
[http.services.datagateway]
```

Next we need to specify, which storage providers should handle which mountpoints.
You can choose one of the following scenarios:

### 1) Using a single storage provider

The simlest case where all storage mountpoints are handled by
a single storage provider runing integrated on Reva Gateway host.

```toml filename=revad.toml
[grpc.services.storageregistry.drivers.static.rules."/home"]
address = "iop-gateway:19000"
[grpc.services.storageregistry.drivers.static.rules."/reva"]
address = "iop-gateway:19000"
[grpc.services.storageregistry.drivers.static.rules."123e4567-e89b-12d3-a456-426655440000"]
address = "iop-gateway:19000"
```

### 2) With multiple storage providers

Here we configure user's `/home/` mountpoint to be handled by storage service running at `iop-storageprovider-home:1700` and `/reva/` mountpoint to be handled by a separate service at `iop-storageprovider-reva:18000`.

```toml filename=revad.toml
[grpc.services.storageregistry.drivers.static.rules."/home"]
address = "iop-storageprovider-home:17000"
[grpc.services.storageregistry.drivers.static.rules."/reva"]
address = "iop-storageprovider-reva:18000"
[grpc.services.storageregistry.drivers.static.rules."123e4567-e89b-12d3-a456-426655440000"]
address = "iop-storageprovider-reva:18000"
```

> NOTE: In this scenario, you will need to separately configure and deploy the storage providers.
> Please refer to the [Deployment docs](/docs/technical-documentation/iop/deployment/kubernetes/providers/) for a guide on how to do it.

## Storage provider ([docs 📖](https://reva.link/docs/config/grpc/services/storageprovider/))

For IOP to handle the storage mountpoints, you will need to enable & configure atleast one
storage provider and a data provider service. Here we set up a simple [`localhome`](https://reva.link/docs/config/packages/storage/fs/localhome/) driver based storage and configure an externaly accessible URL for the Data server.

```toml
[grpc.services.storageprovider]
driver = "localhome"
data_server_url = "https://$yourdomain.iop$/data"
mount_id = "123e4567-e89b-12d3-a456-426655440000"
expose_data_server = true
enable_home_creation = true

[http.services.dataprovider]
driver = "localhome"

[http.services.dataprovider.drivers.localhome]
user_layout = "{{.Username}}"
```

## Authentication registry ([docs 📖](https://reva.link/docs/config/grpc/services/authregistry/))

Next we statically set a mapping of authentication methods to available auth providers.

```toml
[grpc.services.authregistry]
driver = "static"
[grpc.services.authregistry.drivers.static.rules]
basic = "iop-gateway:19000"
```

> Here we say that `Basic` authentication of IOP users should be handled by our Gateway service.

## Authentication provider ([docs 📖](https://reva.link/docs/config/grpc/services/authprovider/))

Alteast one auth provider is needed in order to authenticate IOP users. Here
we set up a provider that uses a `json` file database based `auth_manager` to authenticate users.

```toml
[grpc.services.authprovider]
auth_manager = "json"

[grpc.services.authprovider.auth_managers.json]
users = "users.db.json"
```

> Example [users.db.json](https://github.com/cs3org/reva/tree/master/examples/ocm-partners) files

> For a complete list of `auth_managers` available, refer to the [AuthManager docs](https://reva.link/docs/config/packages/auth/manager/).

## User provider ([docs 📖](https://reva.link/docs/config/grpc/services/userprovider/))

This service is required to feed user data to other services. Here we set it to
share user `json` database with our [Auth provider](#authentication-provider-docs-httpsrevalinkdocsconfiggrpcservicesauthprovider).

```toml
[grpc.services.userprovider]
driver = "json"

[grpc.services.userprovider.drivers.json]
users = "users.db.json"
```

## Group provider ([docs 📖](https://reva.link/docs/config/grpc/services/groupprovider/))

This service is required to provide group and user-group membership information to
other IOP services. By default, a `json` database file based group manager is being used.

```toml
[grpc.services.groupprovider]
driver = "json"

[grpc.services.groupprovider.drivers.json]
users = "groups.json"
```

> Example [groups.json](https://github.com/cs3org/reva/blob/master/examples/standalone/groups.demo.json) file

## Security middleware

The following HTTP middleware services are required to be enabled:

### CORS ([docs 📖](https://reva.link/docs/config/http/middlewares/cors/))

[CORS attack](https://owasp.org/www-community/attacks/CORS_OriginHeaderScrutiny) prevention middleware.

```toml
[http.middlewares.cors]
```

### Provider authorizer ([docs 📖](https://reva.link/docs/config/http/middlewares/providerauthorizer/))

This middleware is used to authorize requests made by other Science Mesh providers.
We set it up to validate against provider data published by Central Component's [Mentix](/docs/technical-documentation/central-component/#mentix) service.

```toml
[http.middlewares.providerauthorizer]
driver = "mentix"

[http.middlewares.providerauthorizer.drivers.mentix]
url = "https://iop.sciencemesh.uni-muenster.de/iop/mentix/cs3"
verify_request_hostname = true
insecure = false
timeout = 10
refresh = 900
```

## OCM

In this section we set up [Open Cloud Mesh](https://wiki.geant.org/display/OCM/Open+Cloud+Mesh) related services, which are necessary for enabling all the cross-provider sharing & collaboration workflows.

### OCM Core ([docs 📖](https://reva.link/docs/config/grpc/services/ocmcore/))

Here we setup OCM Core services, using a `json` file based database.

```toml
[grpc.services.ocmcore]
driver = "json"
[grpc.services.ocmcore.drivers.json]
file = "/var/tmp/reva/shares_server_1.json"
```

### OCM Share provider ([docs 📖](https://reva.link/docs/config/grpc/services/ocmshareprovider/))

Service responsible for handling/serving share info. It should
share the database file with the [OCM Core](#ocm-core-docs-httpsrevalinkdocsconfiggrpcservicesocm).
An URL prefix for OCM Share HTTP APIs (`ocs`) also needs to be configured here.

```toml
[grpc.services.ocmshareprovider]
driver = "json"

[grpc.services.ocmshareprovider.drivers.json]
file = "/var/tmp/reva/shares_server_1.json"

[http.services.ocs]
prefix = "ocs"

[grpc.services.usershareprovider]  # FIXME: clarify why this is memory
driver = "memory"

[grpc.services.publicshareprovider]  # FIXME: clarify why this is memory
driver = "memory"
```

### OCM Invite manager ([docs 📖](https://reva.link/docs/config/grpc/services/ocminvitemanager/))

Manages sharing invitations from/to users of another OCM mesh providers. We use `json` file
based database to store invites data.

```toml
[grpc.services.ocminvitemanager]
driver = "json"

[grpc.services.ocminvitemanager.drivers.json]
file = "/var/tmp/reva/ocm-invites.json"
```

### OCM Provider authorizer ([docs 📖](https://reva.link/docs/config/grpc/services/ocmproviderauthorizer/))

Just like the [Provider authorizer](#provider-authorizer-docs-httpsrevalinkdocsconfighttpmiddlewaresproviderauthorizer) HTTP middleware, this authorizes OCM API requests made by other OCM mesh providers. We use
the same Mentix-based configuration as above.

```toml
[grpc.services.ocmproviderauthorizer]
driver = "mentix"

[grpc.services.ocmproviderauthorizer.drivers.mentix]
url = "https://iop.sciencemesh.uni-muenster.de/iop/mentix/cs3"
verify_request_hostname = true
insecure = false
timeout = 10
refresh = 900
```

### OCMD ([docs 📖](https://reva.link/docs/config/grpc/services/ocmd/))

> **FIXME:** verify & clarify the responsibilities of this services?

OCM HTTP API service. Here we configure IOP API prefix for OCM APIs, and some
more settings used for share e-mail notifications or generation and sending of share invitations.

```toml
[http.services.ocmd]
mesh_directory_url = "https://sciencemesh.cesnet.cz/iop/meshdir/"
prefix = "ocm"

[http.services.ocmd.config]
host = "iop-gateway"
provider = "$your-provider-name$"  # FIXME: what is this option for?? Needs to be clarified

[http.services.ocmd.smtp_credentials]
disable_auth = true
sender_mail = "$put-your-email-here$"
smtp_server = "$put-your-smtp-server-here$"
smtp_port = 25
```

### WebDAV ([docs 📖](https://reva.link/docs/config/grpc/services/ocdav/))

OCM WebDAV HTTP APIs service. We expose WebDAV API under `ocdav` URL prefix.

```toml
[http.services.ocdav]
prefix = "ocdav"
```

## Monitoring & accounting

Here we will configure services needed for external site health monitoring and
export of basic site metrics used for accounting.

```toml
[http.services.sysinfo]
[http.services.prometheus]
```
