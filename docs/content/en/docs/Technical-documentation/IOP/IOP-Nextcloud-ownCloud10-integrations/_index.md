---
title: "IOP/Reva integrations with Nextcloud and ownCloud10"
linkTitle: "IOP Integrations with NC and OC10"
weight: 250
description: >
  Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10
---

## List of known issues to be aware of including **blessed versions** of components
(last updated 24 February 2023):

Due to breaking changes in the OCM invites protocol, we now have a network split. You need to decide whether you want your site to be part of "Network Alpha" or of "Network Beta".

### For Network Alpha
* Due to [this issue](https://github.com/pondersource/sciencemesh-php/issues/133) you should use [this blessed version of Reva](https://hub.docker.com/layers/michielbdejong/reva/mentix-fixes/images/sha256-1892d788892022606fc305338e72dba9cbe17ebda7c719f842a5c774b33193b4?context=explore)
* Due to [this issue](https://github.com/pondersource/oc-sciencemesh/pull/39#issuecomment-1402051991) you should use the `main` branch of https://github.com/pondersource/oc-sciencemesh
* Due to [this issue](https://github.com/pondersource/sciencemesh-php/issues/135) you should use the `main` branch of https://github.com/pondersource/nc-sciencemesh
* [Problems with `verify_request_hostname`](https://github.com/pondersource/sciencemesh-php/issues/122)

### For Network Beta (not recommended yet!
* Due to [this issue](https://github.com/cs3org/reva/issues/3677) you should use [this blessed version of Reva](https://hub.docker.com/r/pondersource/dev-stock-revad-network-beta)

* Due to [this issue](https://github.com/pondersource/oc-sciencemesh/pull/39#issuecomment-1402051991) you should use the `oc-10` branch of https://github.com/pondersource/nc-sciencemesh instead of the app from the ownCloud marketplace
* Due to [this issue](https://github.com/pondersource/sciencemesh-php/issues/135) you should use the `sciencemesh` branch of https://github.com/pondersource/nc-sciencemesh instead of the app from the Nextcloud marketplace


### In both cases
* Recommended Nextcloud installation: make sure you are using a version of Nextcloud that includes [this patch](https://patch-diff.githubusercontent.com/raw/nextcloud/server/pull/36228.patch),
for instance, [the pondersource/sciencemesh branch](https://github.com/pondersource/server/tree/sciencemesh).

* Recommended ownCloud10 installation: make sure you are using a version of OC-10 that includes [this patch](https://patch-diff.githubusercontent.com/raw/owncloud/core/pull/40577.patch),
for instance, [the pondersource/sciencemesh branch](https://github.com/pondersource/core/tree/sciencemesh).

## List of moving parts

There are a number of moving parts involved, they all need to be exactly right for things to work:
* your revad version
* your reva config.toml file
* your OC-10 or NC version (patched or from a git branch); OC10.12 already contains the patch, so skip patching if you're running 10.12. OC10.12 is the recommended version.
* in the case of OC-10, your config.php
* the sciencemesh app (oc-sciencemesh or nc-sciencemesh)
* the settings for the sciencemesh app (as stored in the oc_appconfig database table)

## Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10

To enable the IOP to talk to your Nextcloud and/or ownCloud10 installation,
you need to install the ScienceMesh app.

### Nextcloud

See above for the *recommended version*.

NB: Nextcloud app in marketplace [is not up to date](https://github.com/pondersource/sciencemesh-php/issues/135)
~~For Nextcloud, you can use Nextcloud Apps:
https://apps.nextcloud.com/apps/sciencemesh.
This is the preferred way.~~

~~Or, if you prefer doing it by hand or need a specific version,~~ go to your Nextcloud apps folder, and run (using appropriate version):

```
git clone -b v0.2.0 https://github.com/pondersource/nc-sciencemesh sciencemesh
cd sciencemesh
make
```

Go to the apps panel in the Nextcloud admin GUI and enable the sciencemesh app as untested code.
Go there again and click a second time, to actually enable it.

**Configuration**

iopUrl is url of your running reva instance. Configure “iopUrl” to point to your revad instance.

Go to the admin settings for Science Mesh and set the IOP URL to e.g. https://example.com/iop/

There is also a `shared_secret` that must be same in `reva.toml` file and Nextcloud database. This secret use to reva can authenticate the requests from Nextcloud.

Set a shared secret that matches the one you configured in the TOML file of your revad instance.

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

NB: Due to https://github.com/pondersource/sciencemesh-php/issues/122 make sure you set `verify_request_hostname` to false during testing.


### ownCloud10

Note: this section is not relevant for OCIS.

See above for the *recommended version*.

FIXME: [app in marketplace needs updating](https://github.com/pondersource/oc-sciencemesh/pull/39#issuecomment-1402051991)

~~For ownCloud, you can use ownCloud Marketplace application:
https://marketplace.owncloud.com/apps/sciencemesh. This is the preferred
way.~~

Or, if you prefer doing it by hand or you need a specific version, in your ownCloud apps folder, run (using appropriate version):

```
git clone -b v0.2.0 https://github.com/pondersource/oc-sciencemesh sciencemesh
cd sciencemesh
make
```

Enable the app in the Nextcloud/ownCloud admin dashboard.

**Configuration**

`iopUrl` is url of your running reva instance. Configure `iopUrl` to point to your revad instance. You can set this value in your Owncloud database:

```
insert into oc_appconfig (appid, configkey, configvalue) values ('sciencemesh', 'iopUrl', 'https://revanc1.docker/');
```

There is also a `shared_secret` that must be same in `reva.toml` file and ownCloud database. This secret use to Reva can authenticate the requests from ownCloud.

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

NB: Due to https://github.com/pondersource/sciencemesh-php/issues/122 make sure you set `verify_request_hostname` to false during testing.


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

