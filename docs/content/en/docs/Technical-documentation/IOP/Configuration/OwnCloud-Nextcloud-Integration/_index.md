---
title: 'OwnCloud & Nextcloud integration'
linkTitle: 'OwnCloud & Nextcloud integration'
weight: 20
description: >
  **ADDON:** How to integrate your IOP deployment with the Nextcloud or OwnCloud EFSS solutions.
---

Both Nextcloud (NC) and OwnCloud (OC) implements the OCM APIs, manages users, storage and files synchronization via WebDAV, so it makes perfect sense to delegate some of the things we configured at [Basic services](/docs/technical-documentation/iop/configuration/basic/) to be handled by these EFSS solutions, if you happen to run one.

## Prerequisites

You will need to run a patched installation of NC/OC, having installed & enabled the `sciencemesh` integration app. Please refer to the [Deployment docs](/docs/technical-documentation/support-for-nextcloud-and-owncloud-10/) for a guide.

## Installation

We will need to change the following configuration sections in the Reva config.

### Storage

To tell the IOP to store/access user data on NC/OC,
switch the [storageprovider](/docs/technical-documentation/iop/configuration/basic/#storage-provider-docs-httpsrevalinkdocsconfiggrpcservicesstorageprovider) configuration to use the `nextcloud` driver and point `endpoint` to an URL of your OC/NC service.

```toml
[grpc.services.storageprovider]
driver = "nextcloud"
data_server_url = "https://$yourdomain.iop$/data"

[grpc.services.storageprovider.drivers.nextcloud]
endpoint = "https://$yourdomain.cloud$/index.php/apps/sciencemesh/"
shared_secret = "$some-random-secret-to-be-shared-with-your-oc-nc$"
mock_http = false
```

> NOTE: The `nextcloud` driver is compatible both with Nextcloud and ownCloud installations.

### Authentication

To tell the IOP to authenticate users using NC/OC built-in authentication,
switch the [authprovider](/docs/technical-documentation/iop/configuration/basic/#authentication-provider-docs-httpsrevalinkdocsconfiggrpcservicesauthprovider) configuration to use the `nextcloud` driver and point `endpoint` to an URL of your OC/NC service.

```toml
[grpc.services.authprovider]
auth_manager = "nextcloud"

[grpc.services.authprovider.auth_managers.nextcloud]
endpoint = "https://$yourdomain.cloud$/index.php/apps/sciencemesh/"
shared_secret = "$some-random-secret-to-be-shared-with-your-oc-nc$"
mock_http = false
```

### Users

To tell the IOP to use NC/OC as its user backend, switch the [userprovider](/docs/technical-documentation/iop/configuration/basic/#user-provider-docs-httpsrevalinkdocsconfiggrpcservicesuserprovider) configuration to use the `nextcloud` driver and point `endpoint` to an URL of your OC/NC service.

```toml
[grpc.services.userprovider]
driver = "nextcloud"

[grpc.services.userprovider.drivers.nextcloud]
endpoint = "https://$yourdomain.cloud$/index.php/apps/sciencemesh/"
shared_secret = "$some-random-secret-to-be-shared-with-your-oc-nc$"
mock_http = false
```

### OCM

In the same way, we want to override the following OCM services to point to your NC/OC service.

[OCM Core](/docs/technical-documentation/iop/configuration/basic/#ocm-core-docs-httpsrevalinkdocsconfiggrpcservicesocmcore)

```toml
[grpc.services.ocmcore]
driver = "nextcloud"

[grpc.services.ocmcore.drivers.nextcloud]
webdav_host = "https://$yourdomain.cloud$/"
endpoint = "https://$yourdomain.cloud$/index.php/apps/sciencemesh/"
shared_secret = "$some-random-secret-to-be-shared-with-your-oc-nc$"
mock_http = false
```

[OCM Share provider](/docs/technical-documentation/iop/configuration/basic/#ocm-share-provider-docs-httpsrevalinkdocsconfiggrpcservicesocmshareprovider)

```toml
[grpc.services.ocmshareprovider]
driver = "nextcloud"

[grpc.services.ocmshareprovider.drivers.nextcloud]
webdav_host = "https://$yourdomain.cloud$/"
endpoint = "https://$yourdomain.cloud$/index.php/apps/sciencemesh/"
shared_secret = "$some-random-secret-to-be-shared-with-your-oc-nc$"
mock_http = false
```
