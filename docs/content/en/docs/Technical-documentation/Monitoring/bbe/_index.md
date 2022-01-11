---
title: "Blackbox Exporter"
linkTitle: "Blackbox Exporter"
weight: 15
description: >
  Information about the special fork of the Blackbox Exporter used for the ScienceMesh project
---

## Health monitoring with the Blackbox Exporter
To monitor the health of each site in the ScienceMesh, a [custom fork](https://github.com/sciencemesh/blackbox_exporter) of the [Blackbox Exporter](https://github.com/prometheus/blackbox_exporter) for Prometheus is currently used.

Put simply, the BBE runs a so-called _prober_ to perform a certain check on a provided target when called via URL:
```
https://bbe.sciencemesh.uni-muenster.de/probe?target=google.com&module=nagios_test
```
The response is the result of this check in the form of various Prometheus metrics.

This means that by periodically scraping a BBE target, Prometheus can be used to (indirectly) perform _active probing_ of target sites: Whenever Prometheus calls the BBE target, the BBE in return starts the prober to perform its checks and provides the results in a format consumable by Prometheus.

### Nagios probes
Nagios probes can be used to perform checks on specific targets; these can include simple pings, authentication tests or more advanced checks. There are several advantages when using Nagios probes to perform health checks:

- Nagios is widely used, so switching to another system later is easy
- Many Nagios probes exist, so the need to write custom checks is minimized
- No library is necessary to write these probes; in many cases, a probe comes as a simple bash script

#### Writing custom probes
Nagios probes do not require a special library to be used. Instead, they follow two simple conventions (we are leaving out support for performance data here, as it is currently not needed in the ScienceMesh project):

- The return code of the probe determines its status:
  - `0` = Success
  - `1` = Warning
  - `2` = Error
  - `3` = Unknown
- The _first_ line of output (to `stdout`) is considered as the probe's status message; any further output is considered as additional information

When writing a probe for the ScienceMesh project, these additional rules apply:

- A probe can be anything executable; it doesn't matter if it is a Go binary or Shell script - but it should not have any dependencies on external libraries to keep deployment simple
- Nagios probes are controlled solely via commandline arguments; they should never use any kind of configuration file or similar
- The host (i.e., the target to be checked) is a _mandatory_ commandline argument (e.g, `--host`)
- If a check requires authentication on the target, the credentials should also be passed via commandline arguments; if this is not possible or wanted, use **test/testpass** for the time being (note that this is likely to change in the future!)

Some further guidelines also apply:

- A probe should only perform small and quick tests; it's better to have many small probes than one big one
- Probes should not be too talkative; the first line of output is the most important one, the rest should help admins to find the reason for failed checks

#### Support for Nagios probes in the BBE
The original BBE does not support Nagios probes. This is why a [custom fork](https://github.com/sciencemesh/blackbox_exporter) has been created that allows probers of a new `nagios` type that will launch an external Nagios probe and convert its results into Prometheus metrics. More details can be found in above repository.
