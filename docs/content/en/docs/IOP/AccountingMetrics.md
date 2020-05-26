---
title: "Accounting Metrics"
linkTitle: "Accounting Metrics"
weight: 
description: >
  Exposing of Accounting Metrics
---

## Accounting Metrics
Accounting requires that statistics concerning site usage, performance, etc. can be collected. For this the IOP exposes certain metrics which, via an http metrics service, can be gathered periodically by external monitoring services.

![? leuk plaatje ? (site metrics scripts / metrics pkg / http metrics service)](https://github.com/sciencemesh/sciencemesh/tree/master/docs/content/en/docs/IOP/accounting-metrics.png "Accounting Metrics Service")

##### _When the http metrics service is requested for metrics it calls for metrics through the metrics package API. For each metric the metrics package connects to an underlying layer that provides the actual metrics' values._

The precise metrics that need to be monitored are defined in a metrics package.

### Available Accounting Metrics
The following accounting metrics are available in the metrics package:

1. Number of users per site
2. Number of groups per site
3. Amount of storage per site

### Formats and Exporting
The metrics package uses [OpenCensus](https://opencensus.io/) for implementation of the statistical data types and exporting of the metrics in [Prometheus](https://prometheus.io/) exposition format. 

A response of the http metrics service to a single GET metrics request contains all defined metrics.
