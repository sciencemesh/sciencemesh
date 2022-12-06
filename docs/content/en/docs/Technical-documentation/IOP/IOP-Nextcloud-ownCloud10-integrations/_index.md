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

In your Nextcloud apps folder, run:

```
git clone -b v0.1.0 https://github.com/pondersource/nc-sciencemesh sciencemesh
cd sciencemesh
Make
```

For ownCloud In your ownCloud apps folder, run:

```
git clone -b v0.1.0 https://github.com/pondersource/oc-sciencemesh sciencemesh
cd sciencemesh
Make
```

Enable the app in the Nextcloud/ownCloud admin dashboard.
This will cause a few necessary database tables to be created.

Then follow steps described in https://sciencemesh-nextcloud.readthedocs.io/en/latest/admin.html to configure the application.

