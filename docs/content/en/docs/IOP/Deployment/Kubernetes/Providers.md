---
title: "Running multiple storage providers"
linkTitle: "Storage Providers"
weight: 17
description: >
  Enable creation of WebDAV references by using different storage providers.
---

## Prerequisites

- This guide describes the procedure for running multiple instances of revad. For shared storage and consistency amongst them, we need to share a Persistent Volume (PV) between the three deployments.

> **Note:** In case you choose to auto-provision it with the Persistent Volume Claim (PVC) generation built in the REVA chart:
>  - Just the gateway will generate the PVC manifest.
>  - The two storage providers can reuse it via `storageprovider-<type>.persistentVolume.existingClaim`.
>
> Refer to [enabling persistency](https://developer.sciencemesh.io/docs/iop/deployment/kubernetes/#enabling-and-configuring-persistency) on the docs for details about the options available for the storage configuration.
>
> Note that, in future IOP upgrades, the `persistentVolume.existingClaim` option may be used for the gateway as well, to prevent provisioning a new PVC.

- If upgrading from an older version of the IOP, we'll also need to supply the most recent version of the [`ocm-partners`](https://github.com/cs3org/reva/tree/master/examples/ocm-partners) JSON file. To override the previous version, simply using the `--set-file` flag with `helm upgrade` will do.

## Configuring a multi-provider IOP deployment

We are going to take the ['Storage References' example](https://github.com/cs3org/reva/tree/master/examples/storage-references) as reference for the different bits and pieces of this deployment layout:

```
         +---------+                             +----+
         |         |                             |    |
         |         M---------------------------->+    |
reva--+->G         |                             |    |
      |  |         |     +--------H--------+     |    |
      |  |         |     |                 |     |    |
      |  |         +---->G /reva           M---->+    |
      |  |         |     |                 |     |    |
      |  |         |     +------local------+     |    |
      |  |         |                             |    |
      |  |         |                             |    |
      V  |         |     +--------H--------+     |    |
curl---->H         |     |                 |     |    |
         |         +---->G /home           M---->+    |
         |         |     |                 |     |    |
         +---------+     +----localhome----+     +----+

           GATEWAY        STORAGE PROVIDERS      SHARED
                                                 VOLUME

    M: Mountpoint             H: HTTP exposed service
                              G: gRPC exposed service
```

As it can be seen in the `gateway.toml` file, the `gateway` needs to be provided with some additional settings to determine where to store the different user data:

```toml
[grpc.services.gateway]
datagateway = "<hostname>/iop/datagateway"
commit_share_to_storage_grant = true
commit_share_to_storage_ref = true

[grpc.services.storageregistry]
[grpc.services.storageregistry.drivers.static]
home_provider = "/home"

[grpc.services.storageregistry.drivers.static.rules]
"/home" = "iop-storageprovider-home:17000"
"/reva" = "iop-storageprovider-reva:18000"
"123e4567-e89b-12d3-a456-426655440000" = "iop-storageprovider-reva:18000"
```

The two additional storage providers are shipped as part of the default IOP Chart and based on [REVA](https://reva.link/) as the gateway does. Their configuration will be almost identical, with an exceptional difference: the use of either `local` or `localhome` as the `storageprovider` (and `dataprovider`) driver. Apart from that:

- They both need to be enabled in helm with the `storageProviders.<provider-name>.enabled` boolean flag, to be installed as part of the release.
- The two will need to **enable persistency and mount the shared volume**, either the one provisioned by the `gateway` chart or a pre-existing one on the cluster. We can assume its name is `iop-gateway` in both cases.
- They need to expose both gRPC and HTTP services, for which we will use different port ranges for clarity.

### The `/home` storage provider configuration

```bash
cat << EOF > home-sp.yaml
storageProviders:
  home:
    enabled: true

storageprovider-home:
  service:
    grpc:
      port: 17000
    http:
      port: 17001
  persistentVolume:
    enabled: true
    existingClaim: iop-gateway
  configFiles:
    revad.toml: |
      [grpc]
      address = "0.0.0.0:17000"

      [grpc.services.storageprovider]
      driver = "localhome"
      mount_path = "/home"
      mount_id = "123e4567-e89b-12d3-a456-426655440000"
      data_server_url = "http://iop-storageprovider-home:17001/data"

      [http]
      address = "0.0.0.0:17001"

      [http.services.dataprovider]
      driver = "localhome"
EOF
```

### The `/reva` storage provider configuration

```bash
cat << EOF > reva-sp.yaml
storageProviders:
  reva:
    enabled: true

storageprovider-reva:
  service:
    grpc:
      port: 18000
    http:
      port: 18001
  persistentVolume:
    enabled: true
    existingClaim: iop-gateway
  configFiles:
    revad.toml: |
      [grpc]
      address = "0.0.0.0:18000"

      [grpc.services.storageprovider]
      driver = "local"
      mount_path = "/reva"
      mount_id = "123e4567-e89b-12d3-a456-426655440000"
      data_server_url = "http://iop-storageprovider-reva:18001/data"

      [http]
      address = "0.0.0.0:18001"

      [http.services.dataprovider]
      driver = "local"
EOF
```

After these two are defined, we have all the ingredients to bring the release up to date.

## Installing the IOP release

Simply run:

```bash
helm upgrade -i iop sciencemeshcharts/iop \
  --set gateway.persistentVolume.enabled=true \
  --set-file gateway.configFiles.revad\\.toml=revad.toml \
  --set-file gateway.configFiles.users\\.json=users-cern.json \
  -f home-sp.yaml -f reva-sp.yaml
```

> **Note:** if deploying the IOP for the first time, you'll also need some ingress resource config to enable external access to your cluster services. Check the ['configuring the IOP'](https://developer.sciencemesh.io/docs/iop/deployment/kubernetes/#configuring-an-iop-deployment) section on this manual for instructions on how to do it.
