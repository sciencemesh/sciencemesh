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

The following accounting metrics are available in the metrics package (https://github.com/cs3org/reva/tree/master/pkg/metrics):

1. Number of users per site
2. Number of groups per site
3. Amount of storage per site

### Formats and Exporting
The metrics package uses [OpenCensus](https://opencensus.io/) for implementation of the statistical data types and exporting of the metrics in [Prometheus](https://prometheus.io/) exposition format. 

A response of the http metrics service to a single GET metrics request contains the current/latest values from the metrics data source of all defined metrics.

## How to Configure
Two data source configurations exist, JSON file data source and dummy data. To specify what data source to use you must specify the `metrics_data_driver_type` key in metrics.toml to have the value `json` or `dummy`. 

Example of JSON file data source configuration:
```
metrics_data_driver_type = "json"
``` 
This will make the metrics module read the actual metrics data from a JSON file. Without further configuration the data file will be read from location `/var/tmp/reva/metrics/metricsdata.json` which obviously must exist. To configure a custom file location use the `metrics_data_location` key as follows: 
```
metrics_data_driver_type = "json"
metrics_data_location = "... path to your JSON metrics data file ..."
```

For each metrics request the file will be read and it is __up to the site to update the metrics values in the file__.

The JSON metrics data file must be of the following format (with example values):
```
{
    "cs3_org_sciencemesh_site_total_num_users": 7000,
    "cs3_org_sciencemesh_site_total_num_groups": 550,
    "cs3_org_sciencemesh_site_total_amount_storage": 1080001003
}
```
The key names specify the accounting metrics as explained in [Available Accounting Metrics](#available-accounting-metrics) in that same order.

##### Dummy metrics configuration
When `metrics_data_driver_type` is set to `dummy` random dummy metrics values are automatically created for each metrics request. This can be used to test the prometheus service. The `metrics_data_location` key is not needed here.