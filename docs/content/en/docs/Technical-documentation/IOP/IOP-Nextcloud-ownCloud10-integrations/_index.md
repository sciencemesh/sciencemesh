---
title: "IOP/Reva integrations with Nextcloud and ownCloud10"
linkTitle: "IOP Integrations with NC and OC10"
weight: 250
description: >
  Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10
---

## Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10


To enable the IOP to talk to your Nextcloud and/or ownCloud10 installation,
you need to install the ScienceMesh app.

For Nextcloud, you can use Nextcloud Apps:
https://apps.nextcloud.com/apps/sciencemesh.
This is the preferred way.

Or, if you prefer doing it by hand or need a specific version, go to your Nextcloud apps folder, and run (using appropriate version):

```
git clone -b v0.1.0 https://github.com/pondersource/nc-sciencemesh sciencemesh
cd sciencemesh
Make
```

For ownCloud, you can use ownCloud Marketplace application:
https://marketplace.owncloud.com/apps/sciencemesh. This is the preferred
way.

Or, if you prefer doing it by hand or you need a specific version, in your ownCloud apps folder, run (using appropriate version):

```
git clone -b v0.1.0 https://github.com/pondersource/oc-sciencemesh sciencemesh
cd sciencemesh
Make
```

Enable the app in the Nextcloud/ownCloud admin dashboard.
This will cause a few necessary database tables to be created.

Then follow steps described in https://sciencemesh-nextcloud.readthedocs.io/en/latest/admin.html to configure the application.

