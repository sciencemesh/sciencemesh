---
title: "ScienceMesh-Nextcloud Admin Documentation"
linkTitle: "ScienceMesh-Nextcloud Admin Documentation"
weight: 400
description: >
  Documentation for administrators of a ScienceMesh-Nextcloud integration.
---

# Admin Documentation

Configure `iopUrl` to point to your revad instance. You can check this value in your Nextcloud database:
```
  select * from oc_appconfig where appid='sciencemesh';
```

Make sure that `revaSharedSecret` in there matches the `shared_secret` entry in the following sections of your revad.toml file:

* `[grpc.services.storageprovider.drivers.nextcloud]`
* `[grpc.services.authprovider.auth_managers.nextcloud]`
* `[grpc.services.userprovider.drivers.nextcloud]`
* `[grpc.services.ocmcore.drivers.nextcloud]`
* `[grpc.services.ocmshareprovider.drivers.nextcloud]`

## Registration flow API

You can set some manually estimated statistics data in the admin interface of the Nextcloud app.
You can follow the [join instructions](https://developer.sciencemesh.io/docs/how-to-join-sciencemesh/) to add your site to the ScienceMesh directory.
Once that is complete, other sites will update their approved providers whitelists.