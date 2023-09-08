---
title: "IOP/Reva integrations with Nextcloud and ownCloud10"
linkTitle: "IOP Integrations with NC and OC10"
weight: 250
description: >
  Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10
---

## List of known issues to be aware of including **blessed versions** of components
(last updated 8 September 2023):
* Use Reva version 1.26 or later.
* If your EFSS is based on ownCloud 10 or Nextcloud, use the `sciencemesh` app from the app store / marketplace.
  * Recommended Nextcloud installation: make sure you are using at least Nextcloud version 26 to be able to share to other sites of the mesh. In order to access remote shares, you'll need a version of Nextcloud that includes [this patch](https://github.com/nextcloud/server/pull/39574).
  * Recommended ownCloud10 installation: make sure you are using at least ownCloud version 10.12.
* If your EFSS is based on OCIS, contact Giuseppe Lo Presti to get help with the configuration.
* Permissions management and unsharing are currently broken. The full list of known issues is [here](https://github.com/sciencemesh/nc-sciencemesh/issues).

## List of moving parts

There are a number of moving parts involved, they all need to be exactly right for things to work:
* your site's registration in gocdb. Make sure your site is registered with 'infrastructure = production' and with the right configuration
* your revad version
* your various reva config.toml files, one for each revad process. THIS IS IMPORTANT!
* your OC-10 or NC version (patched or from a git branch); OC10.12 already contains the patch, so skip patching if you're running 10.12. OC10.12 is the recommended version.
* in the case of OC-10, your config.php
* the sciencemesh app
* the settings for the sciencemesh app (in the admin settings dialog)

## Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10

To enable the IOP to talk to your Nextcloud and/or ownCloud10 installation,
you need to install the ScienceMesh app.

### Nextcloud

See above for the *recommended version*.

For Nextcloud, you can use Nextcloud Apps:
https://apps.nextcloud.com/apps/sciencemesh.
This is the preferred way.

Or, if you prefer doing it by hand or need a specific version, go to your Nextcloud apps folder, and run (using appropriate version):

```
git clone -b nextcloud https://github.com/sciencemesh/nc-sciencemesh sciencemesh
cd sciencemesh
make
```

Go to the apps panel in the Nextcloud admin GUI and enable the sciencemesh app as untested code.
Go there again and click a second time, to actually enable it.

**Configuration**

iopUrl is url of your main revad instance. Configure “iopUrl” to point to your main revad instance.

Go to the admin settings for Science Mesh and set the IOP URL to e.g. https://example.com/iop/

There is also a `shared_secret` that must be same in `reva.toml` file and Nextcloud database. This secret use to reva can authenticate the requests from Nextcloud.

Set a shared secret that matches the one you configured in the TOML file of your main revad instance. A reference TOML file is available [here](/docs/technical-documentation/iop/configuration/example-of-toml-file/).

Make sure that `revaSharedSecret` in there matches the `shared_secret` entry in the following sections of your `revad.toml` file:

    * `[grpc.services.storageprovider.drivers.nextcloud]`
    * `[grpc.services.authprovider.auth_managers.nextcloud]`
    * `[grpc.services.userprovider.drivers.nextcloud]`
    * `[grpc.services.ocmcore.drivers.nextcloud]`
    * `[grpc.services.ocmshareprovider.drivers.nextcloud]`

Set the base address of running Nextcloud instance in the following sections of `reva.toml` file:

    * `[grpc.services.storageprovider.drivers.nextcloud]`
    * `[grpc.services.authprovider.auth_managers.nextcloud]`
    * `[grpc.services.userprovider.drivers.nextcloud]`
    * `[http.services.dataprovider.drivers.nextcloud]`

### ownCloud10

Note: this section is not relevant for OCIS.

See above for the *recommended version*.

For ownCloud, you can use ownCloud Marketplace application:
https://marketplace.owncloud.com/apps/sciencemesh. This is the preferred
way.

Or, if you prefer doing it by hand or you need a specific version, in your ownCloud apps folder, run (using appropriate version):

```
git clone -b owncloud https://github.com/sciencemesh/nc-sciencemesh sciencemesh
cd sciencemesh
make
```

Enable the app in the Nextcloud/ownCloud admin dashboard.

**Configuration**

`iopUrl` is url of your main revad instance. Configure `iopUrl` to point to your revad instance. You can set this value through the admin settings of the ScienceMesh app, or in your ownCloud database:

```
insert into oc_appconfig (appid, configkey, configvalue) values ('sciencemesh', 'iopUrl', 'https://revanc1.docker/');
```

There is also a `shared_secret` that must be same in `reva.toml` file and ownCloud database. This secret use to Reva can authenticate the requests from ownCloud. A reference TOML file is available [here](/docs/technical-documentation/iop/configuration/example-of-toml-file/).

Make sure that `revaSharedSecret` in there matches the `shared_secret` entry in the following sections of your `revad.toml` file:

   * `[grpc.services.storageprovider.drivers.nextcloud]`
   * `[grpc.services.authprovider.auth_managers.nextcloud]`
   * `[grpc.services.userprovider.drivers.nextcloud]`
   * `[grpc.services.ocmcore.drivers.nextcloud]`
   * `[grpc.services.ocmshareprovider.drivers.nextcloud]`

There must also exist a row in ownCloud database for `revaLoopbackSecret`.

`revaLoopbackSecret` is a key in ownCloud for authenticating Reva users by ownCloud. Reva sends this key in body instead of real user’s password. This loopback secret send from ownCloud to reva in request’s body.

If this key does not exists in ownCloud database, insert a random string for this key as value.

Set the base address of running ownCloud instance in the following sections of reva.toml file:

   * `[grpc.services.storageprovider.drivers.nextcloud]`
   * `[grpc.services.authprovider.auth_managers.nextcloud]`
   * `[grpc.services.userprovider.drivers.nextcloud]`
   * `[http.services.dataprovider.drivers.nextcloud]`

And edit the config so ScienceMesh is used for all OCM operations:
```
sed -i "3 i\  'sharing.managerFactory' => 'OCA\\\\ScienceMesh\\\\ScienceMeshProviderFactory'," /var/www/html/config/config.php
sed -i "4 i\  'sharing.remoteShareesSearch' => 'OCA\\\\ScienceMesh\\\\Plugins\\\\ScienceMeshSearchPlugin'," /var/www/html/config/config.php
```

### Check the Database

In the end, your OC10 or NC database should contain someting similar to this:

```
MariaDB [bitnami_owncloud]> SELECT * FROM oc_appconfig WHERE appid = 'sciencemesh';
+-------------+--------------------+------------------------------------+
| appid       | configkey          | configvalue                        |
+-------------+--------------------+------------------------------------+
| sciencemesh | enabled            | yes                                |
| sciencemesh | installed_version  | 0.1.0                              |
| sciencemesh | iopUrl             | https://sciencemesh.cesnet.cz/iop/ |
| sciencemesh | revaLoopbackSecret | some-secret                        |
| sciencemesh | revaSharedSecret   | another-secret                     |
| sciencemesh | types              |                                    |
+-------------+--------------------+------------------------------------+
```
