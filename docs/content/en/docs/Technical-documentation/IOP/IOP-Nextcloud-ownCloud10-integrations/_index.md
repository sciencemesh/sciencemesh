---
title: "IOP/Reva integrations with Nextcloud and ownCloud10"
linkTitle: "IOP Integrations with NC and OC10"
weight: 250
description: >
  Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10
---

## List of known issues to be aware of
(last updated 24 January 2023):
* [Problems with `verify_request_hostname`](https://github.com/pondersource/sciencemesh-php/issues/122)
* (for NC and OC-10) [OC-10 app not up to date in marketplace](https://github.com/pondersource/oc-sciencemesh/pull/39#issuecomment-1402051991); [NC app not up to date in marketplace](https://github.com/pondersource/sciencemesh-php/issues/135)
* (fixed for NC, maybe still happening OC-10?) [only first contact is displayed](https://github.com/pondersource/nc-sciencemesh/issues/235)
* (for Reva) [latest master not supported yet](https://github.com/pondersource/sciencemesh-php/issues/133)


## Inter-Operability Platform/Reva Integrations with Nextcloud and ownCloud10

To enable the IOP to talk to your Nextcloud and/or ownCloud10 installation,
you need to install the ScienceMesh app.

### Nextcloud

Make sure you are using a version of Nextcloud that includes [this patch](https://patch-diff.githubusercontent.com/raw/nextcloud/server/pull/36228.patch),
for instance, [the pondersource/sciencemesh branch](https://github.com/pondersource/server/tree/sciencemesh).

NB: Nextcloud app in marketplace [is not up to date](https://github.com/pondersource/sciencemesh-php/issues/135)
~~For Nextcloud, you can use Nextcloud Apps:
https://apps.nextcloud.com/apps/sciencemesh.
This is the preferred way.~~

~~Or, if you prefer doing it by hand or need a specific version,~~ go to your Nextcloud apps folder, and run (using appropriate version):

```
git clone -b v0.1.0 https://github.com/pondersource/nc-sciencemesh sciencemesh
cd sciencemesh
make
```

FIXME: In both cases, you need to register the application to the Nextcloud
database like this:
```
docker exec -e DBHOST=maria1.docker -e USER=einstein -e PASS=relativity  -u www-data nc1.docker sh /init.sh
docker exec maria1.docker mariadb -u root -passwd nextcloud -e "insert into oc_appconfig (appid, configkey, configvalue) values ('sciencemesh', 'iopUrl', 'https://revanc1.docker/');"
docker exec maria1.docker mariadb -u root -passwd nextcloud -e "insert into oc_appconfig (appid, configkey, configvalue) values ('sciencemesh', 'revaSharedSecret', 'shared-secret-1');"
```

**Configuration**

iopUrl is url of your running reva instance. Configure “iopUrl” to point to your revad instance. You can set this value in your Nextcloud database:

```
insert into oc_appconfig (appid, configkey, configvalue) values ('sciencemesh', 'iopUrl', 'https://revanc1.docker/');
```

There is also a `shared_secret` that must be same in `reva.toml` file and Nextcloud database. This secret use to reva can authenticate the requests from Nextcloud.

Make sure that `revaSharedSecret` in there matches the `shared_secret` entry in the following sections of your `revad.toml` file:

    * `[grpc.services.storageprovider.drivers.nextcloud]`
    * `[grpc.services.authprovider.auth_managers.nextcloud]`
    * `[grpc.services.userprovider.drivers.nextcloud]`
    * `[grpc.services.ocmcore.drivers.nextcloud]`
    * `[grpc.services.ocmshareprovider.drivers.nextcloud]`

There must also exist a row in Nextclouddatabase for `revaSharedSecret`.

`revaLoopbackSecret` is a key in Nextcloud for authenticating reva users by Nextcloud. Reva sends this key in body instead of real user’s password. This loopback secret send from Nextcloud to reva in request’s body.

If this key does not exists in Nextcloud database insert a random string for this key as value.

Set the base address of running Nextcloud instance in the following sections of `reva.toml` file:

    * `[grpc.services.storageprovider.drivers.nextcloud]`
    * `[grpc.services.authprovider.auth_managers.nextcloud]`
    * `[grpc.services.userprovider.drivers.nextcloud]`
    * `[http.services.dataprovider.drivers.nextcloud]`


NB: Due to https://github.com/pondersource/sciencemesh-php/issues/122 make sure you set `verify_request_hostname` to false during testing.


### ownCloud10

Note: this section is not relevant for OCIS.

Make sure you are using a version of OC-10 that includes [this patch](https://patch-diff.githubusercontent.com/raw/owncloud/core/pull/40577.patch),
for instance, [the pondersource/sciencemesh branch](https://github.com/pondersource/core/tree/sciencemesh).


FIXME: [app in marketplace needs updating](https://github.com/pondersource/oc-sciencemesh/pull/39#issuecomment-1402051991)

~~For ownCloud, you can use ownCloud Marketplace application:
https://marketplace.owncloud.com/apps/sciencemesh. This is the preferred
way.~~

Or, if you prefer doing it by hand or you need a specific version, in your ownCloud apps folder, run (using appropriate version):

```
git clone -b v0.1.0 https://github.com/pondersource/oc-sciencemesh sciencemesh
cd sciencemesh
make
```

Enable the app in the Nextcloud/ownCloud admin dashboard.
This will cause a few necessary database tables to be created.


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

There must also exist a row in ownCloud database for `revaSharedSecret`.

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

