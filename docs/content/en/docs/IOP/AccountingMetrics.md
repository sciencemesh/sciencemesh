---
title: "Accounting Metrics"
linkTitle: "Accounting Metrics"
weight: 
description: >
  Exposing of Accounting Metrics
---

## Accounting Metrics
Accounting requires that statistics concerning site usage, performance, etc. can be collected. For this the IOP exposes certain metrics which, via an http metrics service, can be gathered periodically by external monitoring services.

The precise metrics that need to be monitored are defined in a metrics package.

### Available Accounting Metrics
The following accounting metrics are available in the metrics package:

1. Number of users per site
2. Number of groups per site
3. Amount of storage per site

### Formats and Exporting
The metrics package uses [OpenCensus](https://opencensus.io/) for implementation of the statistical data types and exporting of the metrics in [Prometheus](https://prometheus.io/) exposition format. 

A response of the http metrics service to a single GET metrics request contains all defined metrics.
