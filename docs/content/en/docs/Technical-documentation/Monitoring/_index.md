---
title: "Monitoring"
linkTitle: "Monitoring"
weight: 15
description: >
  Information and setup about ScienceMesh monitoring.
---

This section contains details about monitoring in the ScienceMesh project.

### Central Component
The **[Central Component](./central-component)** is used for managing the central metadata and performing external monitoring.

### Central Database
The mesh metadata is stored in a **[Central Database](./central-database)**.

### Health monitoring
To ensure an overall high quality of service, automated **[health monitoring](./health-monitoring)** is performed.

### Other stuff (FIXME)

Suggested structure:

1. Very basic info about Mesh Architecture, especially relating to the Central Component and EM. Detail info about Central Component Setup and its modules (Mentix, Prometheus, Blackbox exporter, Alertmanager). The Central database and guidelines should be described in the following section.

2. Central Database and its configuration for admins.

3. Health monitoring, what and how is monitored under Mesh infrastructure.

(4). Metrics will be part of sciencemesh-web (graphical interpretation of total number of users, amount of data,...). Anyway here can be described the metrics mechanism for EFSS.

Daniel: Should the CC and CD really be under Monitoring? It should, IMHO, be a level higher.
