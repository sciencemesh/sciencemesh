---
title: "Health Monitoring"
linkTitle: "Health Monitoring"
weight: 11
description: >
  Information about automated health monitoring in the ScienceMesh project.
---

This section contains details about automated health monitoring in the ScienceMesh project.

### Overview
The ScienceMesh in its entirety can only work if all of its participating sites with their respective services function properly. To ensure this in an automated way, all services are constantly being monitored and checked for their health status through various services running as part of the [Central Component](../central-component).

The two main components used for health monitoring are **Prometheus** and the **Blackbox Exporter**. They are both automatically configured and controlled by _Mentix_, the main service of the Central Component. Below diagram shows how these services are connected:
{{< imgproc "mentix_flow.png" Fit "800x600" >}}
{{< /imgproc >}}

#### Why external checks?
There are many reasons why we perform health checking _from the outside_: First of all, these checks do what a regular user would do, and it is thus important that the checks succeed when run from an external source. Many problems arise due to firewalls or misconfigured ingress servers.

Furthermore, having all checks in a central place makes adding and modifying checks a lot easier. It also ensures that the same checks are run for every site at all times. Last but not least, it gives all participating sites confidence in the reliability and meaningfulness of these checks and their results.

#### What about EFSS-specific checks?
The health monitoring doesn't impose any assumptions on the EFSS system a site uses. The underlying system is, however, indirectly tested through the CS3 APIs and the IOP: If there are issues with your EFSS, these will very likely result in failing checks.

### Prometheus
Prometheus is used to gather various metrics from each site. This includes the total number of users, the number of user groups and the amount of storage used. These metrics are exposed through the IOP (FIXME), which means that its `metrics` HTTP endpoint needs to be accessible from the outside.

Furthermore, Prometheus will also periodically pull metrics from the _Blackbox Exporter_ (_BBE_ for short), which runs in parallel to Prometheus. This in return causes the BBE to run active health checks on every site and its respective services, as explained below.

### Blackbox Exporter
To monitor the health of each site in the ScienceMesh, a [custom fork](https://github.com/sciencemesh/blackbox_exporter) of the [Blackbox Exporter](https://github.com/prometheus/blackbox_exporter) for Prometheus is used. Put simply, the BBE runs a so-called _prober_ to perform a certain check on a provided target when called via a special URL. More technical details about this version of the BBE can be found [here](./bbe).

The general workflow of this active health checking roughly looks like this:
1. In Prometheus, a target rule exists for every check that should be performed
1. Prometheus will scrape (i.e., gather data from) these targets periodically
1. The BBE will thus be called through a URL via Prometheus which contains the actual target of the check, as well as the check to perform itself
    - This causes the BBE to run the specified check on the target (service)
    - The BBE will evaluate the check results and translate them into a format consumable by Prometheus
1. Prometheus will gather these results in form of special metrics and process them further
    - This includes calculating an overall site status as well as the site's availability and reliability

#### Health checks
Currently, only a small number of checks is performed during health monitoring. Below is a list of all checks and what is necessary to pass them:

| Check name | Description | Passing criteria | Critical |
| --- | --- | --- | --- |
| `http_ping` | Checks whether the IOP can be reached via HTTP | IOP URL must be properly configured and reachable from the outside | _Yes_ |
| `iop_login` | Checks whether logging in to the IOP gateway is possible | IOP gRPC port must be properly configured and reachable from the outside; a user with credentials `test/testpass` must be available (this is only temporary and will change in the future) | _Yes_ |

All critical tests must be passed for a site to be considered `Healthy`. Failing uncritical tests will only cause a site to go into `Warning`state.

**Note:** These tests are only temporary. More tests will be added in the future.
