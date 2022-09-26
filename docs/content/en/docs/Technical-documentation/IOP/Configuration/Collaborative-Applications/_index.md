---
title: 'Collaborative applications'
linkTitle: 'Collaborative applications'
weight: 20
description: >
  **ADDON:** How to configure your IOP site to share access to some CS3MESH-compatible collaborative applications
---

## Prerequisites

Some collaborative application has to be deployed. For a
list of supported applications and deployment guides, please
refer to ...xxx

> **FIXME:**: link to actual applications deployment guides after moving & merging:
> /docs/technical-documentation/iop/deployment/kubernetes/wopiserver/
> /docs/technical-documentation/integrations/codimd/

## Installation

To provide an access to some collaborative applications hosted at your site, you need to register them by assigning the `mime-type` of documents they can open to a location of the application's service. This is done by adding and customizing the following [appregistry](https://reva.link/docs/config/grpc/services/appregistry/) sections to the Reva config file.

```toml
[grpc.services.appregistry]
driver = "static"

[grpc.services.appregistry.static.rules]
"text/plain" = "iop-gateway:19000"
"text/markdown" = "iop-gateway:19000"
"application/compressed-markdown" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.text" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.spreadsheet" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.presentation" = "iop-gateway:19000"
```
