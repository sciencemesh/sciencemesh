---
title: "Accounting Metrics"
linkTitle: "Accounting Metrics"
weight: 
description: >
  Exposing of Accounting Metrics
---

## Accounting Metrics
Accounting requires that statistics concerning site usage, performance, etc. can be collected. For this the IOP exposes certain metrics which, via an http metrics service, can be gathered periodically by external monitoring services.

The precise metrics that can be monitored are defined in the Reva metrics package.

### Available Accounting Metrics
The following accounting metrics are expected to be available, see metrics interface (https://github.com/cs3org/reva/tree/master/pkg/metrics/reader):

1. Number of users per site
2. Number of groups per site
3. Amount of storage per site

It is up to the site to have the above specified metrics figures ready so the metrics service can serve them using one of the metrics drivers. Currently 2 drivers are implemented: 'dummy', 'json'. 

The json driver reads metrics through a json file which needs to be updated continuously by the site to have the latest figures available. The json driver will periodically read the file in order to register the latest figures.

The dummy driver solely exists for demo purposes. It makes the metrics service serve dummy figures. 

Which one of the drivers is used by the site is [configurable](#configurations). 

### Configuration
Below is a metrics configuration file example that explains the possible options.

```
[shared]

[http.services.metrics]
# one of dummy, json.
metrics_data_driver_type = "dummy"
# if left unspecified location /var/tmp/reva/metrics/metricsdata.json is used (for driver type json).
metrics_data_location = ""
# metrics recording interval in milliseconds
metrics_record_interval = 5000
address = ":443"

[http.services.prometheus]
address = ":443"

[http]
address = ":443"
```

Change addresses and ports so that are consistent with your
modification of the
https://developer.sciencemesh.io/docs/technical-documentation/monitoring/accounting-metrics/#configuration
file. All three sections will have the same address.

To choose what driver to use specify the ```metrics_data_driver_type``` value using the driver type name. If the json driver is used the driver expects to find the a metrics figures file in [json format](#metrics-figures-file-format-for-json-driver) at location of your choice, by specifying the ```metrics_data_location``` value. If not specified the default location (/var/tmp/reva/metrics/metricsdata.json) is used.

The metrics recording interval can be set using the ```metrics_record_interval``` flag and has a default recording interval of 5 seconds.

#### Metrics figures file format for json driver
The json driver expects to find a json file in the following exact format (with example values). Each property name reflects the specific metric it concerns:
```
{
    "cs3_org_sciencemesh_site_total_num_users": 7000,
    "cs3_org_sciencemesh_site_total_num_groups": 550,
    "cs3_org_sciencemesh_site_total_amount_storage": 1080001003
}
```
<small>**"cs3_org_sciencemesh_site_total_amount_storage"**</small> is expected to be of type ```byte```.

This file is read periodically (```metrics_record_interval``` [configuration](#configurations) flag) by the json driver. 

<u>It is the site's responsibility to update this file periodically with the latest figures.</u>

Considering Kubernetes deployment, the way to inject a file into the container is something like
```
helm upgrade .... iop sciencemesh/iop -f values.yaml --set-file gateway.configFiles.metrics\\.json=metrics.json
```
In more detail, let's have metrics.json containing
```
# cat metrics.json
{
    "cs3_org_sciencemesh_site_total_num_users": 20742,
    "cs3_org_sciencemesh_site_total_num_groups": 165,
    "cs3_org_sciencemesh_site_total_amount_storage": 386887921664
}
```
When deploying the IOP using Helm, add
```
--set-file gateway.configFiles.metrics\\.json=metrics.json
```
Helm will then write it to the config map of the IOP
```
kubectl get configmaps iop-gateway-config -o json | jq '.data."metrics.json"' | jq -r
```
and you can edit the file using
```
kubectl edit configmaps iop-gateway-config 
```
or alternatively, just run helm upgrade with new contents.

### Formats and Exporting
The metrics package uses [OpenCensus](https://opencensus.io/) for implementation of the statistical data types and exporting of the metrics in [Prometheus](https://prometheus.io/) exposition format. 

A response of the http metrics service to a single GET metrics request contains all defined metrics.
