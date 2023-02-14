---
title: "How to Install EFSS Manually"
linkTitle: "How to Install EFSS Manually"
weight: 200
description: >
    Manual installation of EFSS
---

We provide two ways of deploying EFSS:

  * installing manually using standard ownCloud 10 or Nextcloud installations, patching and installing the integration application,
  * or deploying directly from the github repository. This option is suitable for developers only and is not recommended for production use.

**Check [current blessed
versions](../../iop/iop-nextcloud-owncloud10-integrations/) when deploying the QA environment.** Note that the example below generally installs master versions, which may not be what you want. You **must modify them** to install intended versions as stated in the above linked page! Always check you're installing intended versions.

## Manual installation of Science Mash enabled EFSS using patches

This procedure leads to the same result as the official Kubernetes deployment, it is just performed manually.

1. Deploy ownCloud 10 and or Nextcloud to your infrastructure, following
   the official documentation of [Nextcloud](https://nextcloud.com/install/) or [ownCloud](https://doc.owncloud.com/docs/next/), respectively.

1. Patch the installation with:
   - ownCloud 10:
     (https://patch-diff.githubusercontent.com/raw/owncloud/core/pull/40577.patch)
   - Nextcloud:
     (https://patch-diff.githubusercontent.com/raw/nextcloud/server/pull/36228.patch)

   (suitable patch options could be, e.g. `patch -p1 -t --forward --no-backup-if-mismatch < ...patch...` that should be performed in the folder where the EFSS is installed, typically something like /var/www/owncloud)

1. Install the integration application. Go the the EFSS installation
   directory, and perform
   - ownCloud 10:
     ```
     git clone https://github.com/pondersource/oc-sciencemesh sciencemesh
     cd sciencemesh
     make
     ```
   - Nextcloud:
     ```
     git clone https://github.com/pondersource/nc-sciencemesh sciencemesh
     cd sciencemesh
     make
     ```
1. The next step is to register the OC/NC application in to database.
    * **ownCloud 10:**
    
    First you need to update the `iopUrl` depending where your [IOP deployment]({{< ref "docs/Technical-documentation/IOP/Configuration" >}}) runs (or will run).
    ```
    UPDATE oc_appconfig SET configvalue = 'https://sciencemesh.cesnet.cz/iop/' WHERE configkey = 'iopUrl';
    ```
    Similarly for the secret.
    ```
    UPDATE oc_appconfig SET configvalue = 'another-secret' WHERE configkey = 'revaSharedSecret';
    ```

    The `shared_secret` **must be** the same in `reva.toml` file and ownCloud database. This secret is used by Reva to authenticate requests from ownCloud.

    Make sure that `revaSharedSecret` matches the `shared_secret` entry in the following sections of your `revad.toml` file:

   * `[grpc.services.storageprovider.drivers.nextcloud]`
   * `[grpc.services.authprovider.auth_managers.nextcloud]`
   * `[grpc.services.userprovider.drivers.nextcloud]`
   * `[grpc.services.ocmcore.drivers.nextcloud]`
   * `[grpc.services.ocmshareprovider.drivers.nextcloud]`

    The `revaLoopbackSecret` is a key in ownCloud for authenticating Reva users by ownCloud. Reva sends this key in body instead of real user’s password. This loopback secret send from ownCloud to reva in request’s body.

    If this key does not exists in ownCloud database, insert a random string for this key as value.

    ```
    INSERT oc_appconfig SET configvalue = 'some-secret' WHERE configkey = 'revaLoopbackSecret;
    ```

    * **Nexcloud:**
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

1. Check the database
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

## Manual installation of Science Mesh enabled EFSS for developers

Beware: those are alternative technical steps for developers, they are **not recommended** for production use and are **not supported** by the Science Mesh. You've been warned, this can bite you and kill your cat.

Repositories linked from this subsection already contain necessary patches,
and those repositories mostly track ownCloud 10 and Nextcloud git masters.
Have we mentioned it wasn't suitable for production?

1. In order to join the ScienceMesh, you as the operator of a site are expected to run one of supported EFSS (Enterprise File Sync and Share Systems). Firstly you need to deploy desired EFSS in your environment. Currently is Sciencemesh support implemented in forks of several EFSSs with added sharing applications, specifically [Nextcloud](https://github.com/pondersource/server/tree/sciencemesh) and/or [ownCloud](https://github.com/pondersource/core/tree/sciencemesh). Use versions (branches) linked here.

	You should use official documentation of [Nextcloud](https://nextcloud.com/install/) or [ownCloud](https://doc.owncloud.com/docs/next/) respectively to deploy your testing instance with ScienceMesh patch/support.

1. Then install an [integration application](../technical-documentation/iop/iop-nextcloud-owncloud10-integrations) that provides an interface between your EFSS and Reva.


1. Now you can continue with the app registration as described in the step 4 **in the previous section**.

1. Now please check the state of the database as described in the step 5 **in the previous section**.

