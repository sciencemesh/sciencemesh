---
title: 'Example of toml file to set up REVA'
linkTitle: 'Example of toml file to set up REVA'
weight: 80
description: >
  Here you can find an example of toml file needed to set up all necessary services for your reva instance. It consists of the services described in the others [Configuration sections]({{< ref "docs/Technical-documentation/IOP/Configuration" >}}). This toml file is used for [REVA deployment]({{< ref "docs/Technical-documentation/IOP/Deployment/Kubernetes" >}}) and referenced there as `standalone.toml`.
---

To get an understanding on how to create, use and manage Reva configuration,
please refer to the [official Reva documentation](https://reva.link/docs/getting-started/beginners-guide/).

> NOTE: This is example of toml file used for deploying CESNET QA instance.

> **IMPORTANT**: To use the example file, you need to change the values inside of the example to those according to your instance and deployment.

```
[grpc]
address = "0.0.0.0:19000"

[http]
address = "0.0.0.0:19001"
enabled_services = ["ocmd"]
enabled_middlewares = ["providerauthorizer", "cors"]

[shared]
gatewaysvc = "iop-gateway:19000"
jwt_secret = "your-jwt-secret"

[grpc.services.gateway]
commit_share_to_storage_grant = false
commit_share_to_storage_ref = true
share_folder = "Shares"
datagateway = "https://sciencemesh.cesnet.cz/iop/datagateway"
transfer_expires = 6

appprovidersvc = "iop-gateway:19000"
appregistry = "iop-gateway:19000"
authregistrysvc = "iop-gateway:19000"
ocmcoresvc = "iop-gateway:19000"
ocminvitemanagersvc = "iop-gateway:19000"
ocmproviderauthorizersvc = "iop-gateway:19000"
ocmshareprovidersvc = "iop-gateway:19000"
preferencessvc = "iop-gateway:19000"
publicshareprovidersvc = "iop-gateway:19000"
storageregistrysvc = "iop-gateway:19000"
userprovidersvc = "iop-gateway:19000"
usershareprovidersvc = "iop-gateway:19000"

[grpc.services.storageregistry]
driver = "static"

[grpc.services.storageregistry.drivers.static]
home_provider = "/home"

[grpc.services.storageregistry.drivers.static.rules."/home"]
address = "iop-gateway:19000"
[grpc.services.storageregistry.drivers.static.rules."123e4567-e89b-12d3-a456-426655440000"]
address = "iop-gateway:19000"

[http.services.datagateway]
[http.middlewares.cors]

[grpc.services.storageprovider]
driver = "nextcloud"
mount_path = "/home"
mount_id = "123e4567-e89b-12d3-a456-426655440000"
expose_data_server = true
data_server_url = "https://sciencemesh.cesnet.cz/iop/data"
enable_home_creation = true
disable_tus = true

[grpc.services.storageprovider.drivers.nextcloud]
endpoint = "https://oc-mesh.du.cesnet.cz/index.php/apps/sciencemesh/"
shared_secret = "some-top-secret"

[http.services.dataprovider]
driver = "nextcloud"
disable_tus = true

[http.services.dataprovider.drivers.nextcloud]
endpoint = "https://oc-mesh.du.cesnet.cz/index.php/apps/sciencemesh/"
shared_secret = "some-top-secret"

[grpc.services.authregistry]
driver = "static"
[grpc.services.authregistry.drivers.static.rules]
basic = "iop-gateway:19000"

[grpc.services.authprovider]
auth_manager = "nextcloud"

[grpc.services.authprovider.auth_managers.nextcloud]
endpoint = "https://oc-mesh.du.cesnet.cz/index.php/apps/sciencemesh/"
shared_secret = "some-top-secret"

[grpc.services.userprovider]
driver = "nextcloud"

[grpc.services.userprovider.drivers.nextcloud]
endpoint = "https://oc-mesh.du.cesnet.cz/index.php/apps/sciencemesh/"
shared_secret = "some-top-secret"

[grpc.services.groupprovider]
driver = "json"

[grpc.services.ocmcore]
driver = "nextcloud"

[grpc.services.ocmcore.drivers.nextcloud]
webdav_host = "https://oc-mesh.du.cesnet.cz/"
endpoint = "https://oc-mesh.du.cesnet.cz/index.php/apps/sciencemesh/"
shared_secret = "some-top-secret"

[grpc.services.ocmshareprovider]
driver = "nextcloud"

[grpc.services.ocmshareprovider.drivers.nextcloud]
webdav_host = "https://oc-mesh.du.cesnet.cz/"
endpoint = "https://oc-mesh.du.cesnet.cz/index.php/apps/sciencemesh/"
shared_secret = "some-top-secret"

[http.services.ocs]
prefix = "ocs"

[grpc.services.usershareprovider]  # FIXME: clarify why this is memory
driver = "memory"

[grpc.services.publicshareprovider]  # FIXME: clarify why this is memory
driver = "memory"

[grpc.services.ocminvitemanager]
driver = "json"

[grpc.services.ocminvitemanager.drivers.json]
file = "/var/tmp/reva/ocm-invites.json"

[grpc.services.ocmproviderauthorizer]
driver = "mentix"

[grpc.services.ocmproviderauthorizer.drivers.json]
verify_request_hostname = false

[grpc.services.ocmproviderauthorizer.drivers.mentix]
url = "https://iop.sciencemesh.uni-muenster.de/iop/mentix/cs3"
verify_request_hostname = false
insecure = false
timeout = 10
refresh = 900

[http.services.prometheus]
[http.services.sysinfo]

[http.services.ocmd]
mesh_directory_url = "https://sciencemesh.cesnet.cz/iop/meshdir/"

[http.services.ocmd.smtp_credentials]
disable_auth = true
sender_mail = "no-reply@sciencemesh.cesnet.cz"
smtp_server = "postfix-relay.mail.svc.cluster.local"
smtp_port = 25

[http.services.ocdav]
prefix = "ocdav"

[grpc.services.appprovider]
driver = "demo"
wopiurl = "http://iop-wopiserver:8880/"

[grpc.services.appregistry]
driver = "static"

[grpc.services.appregistry.static.rules]
"text/plain" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.text" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.spreadsheet" = "iop-gateway:19000"
"application/vnd.oasis.opendocument.presentation" = "iop-gateway:19000"

[http.middlewares.providerauthorizer]
driver = "mentix"

[http.middlewares.providerauthorizer.drivers.json]
verify_request_hostname = false

[http.middlewares.providerauthorizer.drivers.mentix]
url = "https://iop.sciencemesh.uni-muenster.de/iop/mentix/cs3"
verify_request_hostname = false
insecure = false
timeout = 10
refresh = 900

[http.services.meshdirectory]

[grpc.services.datatx]
txdriver = "rclone"
# the shares,transfers db file (default: /var/tmp/reva/datatx-shares.json)
#tx_shares_file = ""
# base folder of the data transfers (default: /home/DataTransfers)
#data_transfers_folder = ""

[grpc.services.datatx.txdrivers.rclone]
endpoint = "http://iop-rclone"
auth_user = "very-very-secret"
auth_pass = "very-very-secret"
# the transfers(jobs) db file (default: /var/tmp/reva/datatx-transfers.json)
#file = ""
# check status job interval in milliseconds
job_status_check_interval = 2000
# the job timeout in milliseconds (must be long enough for big transfers!)
job_timeout = 120000
```
