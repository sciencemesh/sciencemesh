---
title: 'Configuration'
linkTitle: 'Configuration'
weight: 15
description: >
  How to configure Reva IOP to provide Science Mesh services
---

To get an understanding on how to create, use and manage Reva configuration,
please refer to the [official Reva documentation](https://reva.link/docs/getting-started/beginners-guide/).

> **IMPORTANT**: values marked with `$...$` are meant to be changed to something more meaningful

## Basic ScienceMesh services

Here we further describe how a Reva IOP site should be configured to provide services required for [joining the Science Mesh](/docs/how-to-join-sciencemesh/).

### Shared settings

Shared options applies to all configured IOP services.

```toml filename:revad.toml
[shared]
gatewaysvc = "iop-gateway:19000"
jwt_secret = "$something secret$"
```

> Here we set an address of the Reva [gateway](https://reva.link/docs/config/grpc/services/gateway/)
> service as many other services refers to the gateway to make/handle their requests.

### Gateway settings ([docs 📖](https://reva.link/docs/config/grpc/services/gateway/))

Gateway service is the main entrypoint to the IOP, handling and forwarding
most of the requests — it should be knowledgeable of locations of any other services
provided by the IOP instance.

```toml filename:revad.tom
[grpc.services.gateway]
commit_share_to_storage_grant = false
commit_share_to_storage_ref = true
datagateway = "https://yourdomain.iop/datagateway"
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

> Gateway implicitly expects that every service runs under the same `host:port` as itself.
> But we can define it explicitly like this

### Storage registry

TODO:
