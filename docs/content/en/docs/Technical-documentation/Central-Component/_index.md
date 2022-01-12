---
title: "Central Component"
linkTitle: "Central Component"
weight: 10
description: >
  The Central Component of the ScienceMesh project.
---

This section contains details about the so-called _Central Component_ used for managing the central metadata and performing external monitoring in the ScienceMesh project.

### Overview
The _Central Component_ (_CC_ for short) consists of various interconnected services that are responsible for managing the mesh metadata, as well as taking care of health monitoring and alerting. Below image shows a diagram of these services, which will be described in brief in the following sections:
{{< imgproc "mentix_flow.png" Fit "800x600" >}}
{{< /imgproc >}}

#### Mentix
To connect the various components of the CC, a service called Mentix (short for _Mesh Entity Exchanger_) is used. Mentix is responsible for gathering site and service metadata from the **GOCDB** (see below) and exporting it to other services, both external and internal ones. Mentix can thus be seen as the bridge between the mesh topology information provider and all services that require this information.

#### GOCDB
The **GOCDB** forms the central database of the mesh and is used to store all metadata about the various sites participating in the ScienceMesh. It is used by several other services, especially within the Central Component, to gather information about the different entities (sites, services, etc.) in the mesh. The GOCDB is described in more detailed in a dedicated section which can be found here (FIXME).

#### Prometheus
To gather all metrics and health information about the mesh and its individual sites, a **[Prometheus](https://prometheus.sciencemesh.uni-muenster.de)** instance is used. As can be seen in the above diagram, it is automatically configured by Mentix: Whenever the mesh topology has been modified, all targets which are to be monitored are updated as well.

#### Blackbox Exporter
To perform health checks on the remote sites and collect this information through Prometheus, a service called **[Blackbox Exporter](https://bbe.sciencemesh.uni-muenster.de)** is used; details about the fork used for the ScienceMesh project can be found [here](../health-monitoring/bbe). Just like the central Prometheus instance, it is automatically configured by Mentix. The Blackbox Exporter periodically runs various so-called probes for every service which check them for proper functionality and expose the results in a form consumable by Prometheus.

#### Alertmanager
To inform administrative users about issues that were detected through monitoring, an **[Alertmanager](https://alerts.sciencemesh.uni-muenster.de)** instance is used. This ensures that administrators can react swiftly to any arising problems.

#### Grafana
All information collected by Prometheus is presented to the user through various **[Grafana](https://grafana.sciencemesh.uni-muenster.de)** dashboards.
