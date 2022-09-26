---
title: 'Mesh Directory'
linkTitle: 'Mesh Directory'
weight: 15
description: >
  **ADDON:** How to configure your IOP site to host a [Mesh Directory](https://github.com/sciencemesh/meshdirectory-web) service.
---

By hosting a Mesh Directory service, your (and others) IOP site can use the service as a part of the Mesh collaboration workflows (e.g. for share invitation workflow).

## Installation

To enable the service on your IOP site, simply add the following section to the Reva config file:

```toml
[http.services.meshdirectory]
prefix = "meshdir"
```

To check that service is running properly, open the following URL in the browser:

```url
https://$yourdomain.iop$/meshdir/?token=test&providerDomain=$yourdomain.iop$
```
