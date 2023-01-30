---
title: "How to Deploy EFSS"
linkTitle: "How to Deploy EFSS"
weight: 200
description: >
    Description of docker installation of EFSS including integration applications.
---

## Installation and deployment of ownCloud 10.10

In the following guide we have documented the deployment which we did in CESNET to build QA instance of Sciencemesh. We also prepared particular sample files which could help you to understand the process. **Be aware that you need to edit particular files with respect to your own environment, deployement, endpoints etc.**

1. Prepare the [Dockerfile](https://github.com/sciencemesh/efss-deployment-sample/blob/main/cesnet-owncloud-qa/Dockerfile) file for your Sciencemesh instance.

2. Prepare the [Makefile.diff](https://github.com/sciencemesh/efss-deployment-sample/blob/main/cesnet-owncloud-qa/Makefile.diff) to install the Sciencemesh app within docker build.

3. Build the Sciencemesh image and push it to your registry.

4. Once you have built the image, you can deploy it in your Kubernetes infrastructure using [ownCloud Helm chart](https://github.com/owncloud-docker/helm-charts/blob/main/charts/owncloud/README.md). We have prepared the [values.yaml](https://github.com/sciencemesh/efss-deployment-sample/blob/main/cesnet-owncloud-qa/values.yaml) for the deployment of patched ownCloud using Helm.

5. Last step is to register the Sciencemesh app into ownCloud DB. So you need to login into DB itself and run following commands.

    First you need to update the `iopUrl` according to your [IOP deployment]({{< ref "docs/Technical-documentation/IOP/Configuration" >}}).
    ```
    UPDATE oc_appconfig SET configvalue = 'https://sciencemesh.cesnet.cz/iop/' WHERE configkey = 'iopUrl';
    ```
    Similarly for the secret.
    ```
    UPDATE oc_appconfig SET configvalue = 'another-secret' WHERE configkey = 'revaSharedSecret';
    ```

    **Be aware**, that `shared_secret` must be the same in `reva.toml` file and ownCloud database. This secret uses Reva to authenticate the requests from ownCloud.

    Make sure that `revaSharedSecret` in there matches the `shared_secret` entry in the following sections of your `revad.toml` file:

   * `[grpc.services.storageprovider.drivers.nextcloud]`
   * `[grpc.services.authprovider.auth_managers.nextcloud]`
   * `[grpc.services.userprovider.drivers.nextcloud]`
   * `[grpc.services.ocmcore.drivers.nextcloud]`
   * `[grpc.services.ocmshareprovider.drivers.nextcloud]` 

    The `revaLoopbackSecret` is a key in ownCloud for authenticating Reva users by ownCloud. Reva sends this key in body instead of real user’s password. This loopback secret send from ownCloud to reva in request’s body.

    If this key does not exists in ownCloud database, insert a random string for this key as value.

    ```
    UPDATE oc_appconfig SET configvalue = 'some-secret' WHERE configkey = 'revaLoopbackSecret;
    ```

6. In the end, your OC10 database should contain someting similar to this:

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

