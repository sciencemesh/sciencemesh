---
title: "How to Deploy EFSS"
linkTitle: "How to Deploy EFSS"
weight: 200
description: >
    Description of docker installation of EFSS including integration applications.
---

Recommended way of installing ownCloud 10 and/or Nextcloud into a container
is to adapt files from the following example to your needs. If this doesn't
fit your environment, we provide a guide [to install the components
manually](manual-efss-installation) as an alternative.

## Installation and deployment of ownCloud 10.10 (Docker+Kubernetes)

This section describes the deployment using Docker and Kubernetes. It is based on CESNET's QA instance of Sciencemesh. In includes both changes to ownCloud as well as the integration application. **As your specific environment may vary, you need to edit particular files with respect to your own environment, deployment, endpoints etc.**

**Check [current blessed
versions](../iop/iop-nextcloud-owncloud10-integrations/) when deploying the QA environment.** Note that the example below generally installs master versions, which may not be what you want. You **must modify them** to install intended versions as stated in the above linked page! Always check you're installing intended versions.

1. Prepare the [Dockerfile](https://github.com/sciencemesh/efss-deployment-sample/blob/main/cesnet-owncloud-qa/Dockerfile) fil to build patched ownCloud including Sciencemesh app.

1. Prepare the [Makefile.diff](https://github.com/sciencemesh/efss-deployment-sample/blob/main/cesnet-owncloud-qa/Makefile.diff) to install the Sciencemesh app within docker build.

1. Build the Sciencemesh-enabled ownCloud image and push it to your registry.

1. Once you have built the image, you can deploy it in your Kubernetes infrastructure using [ownCloud Helm chart](https://github.com/owncloud-docker/helm-charts/blob/main/charts/owncloud/README.md). We have prepared the [values.yaml](https://github.com/sciencemesh/efss-deployment-sample/blob/main/cesnet-owncloud-qa/values.yaml) for the deployment of patched ownCloud using Helm.

1. Last step is to register the Sciencemesh app into ownCloud DB. So you need to login into DB itself and run following commands.

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

1. In the end, your OC10 database should contain someting similar to this:

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

