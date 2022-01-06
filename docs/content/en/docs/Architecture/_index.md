---
title: "Architecture"
linkTitle: "Architecture"
weight: 15
description: >
  A short description of the architecture of the Science Mesh   
---

Architecture

## Architecture

The Science Mesh consists of its participating Sites running EFSS services and a so-called Central Component. From a logical point of view, the Central Component is responsible for providing the few global services of the Science Mesh. However, it is by no means necessary that all those services run on a single node of the Science Mesh - they may be distributed across several Science Mesh partners. The interface between a Mesh node and the Mesh’s core operational infrastructure is what we call an Executive Module (EM).

The figure below displays the conceptual architecture: The Central Component of the Science Mesh includes the monitoring and accounting infrastructure as well as the Central Database and Helpdesk. The monitoring infrastructure runs probes which return results to the monitoring infrastructure as to if the services running at the Sites are operational. Similarly, the accounting infrastructure collects usage metrics from Sites. The Central Database provides the EMs running within Sites with Science Mesh topology information.

![picture of architecture]("/static/architecture.png")

Topology information about the Science Mesh is contained in the Central Database. This includes Site names, endpoints, services running at the Sites and further meta-information. Executive Modules consume this information and use it to perform service and user discovery for the users (similar to WAYF service in identity federations). Those discovery processes are the cornerstone of the Science Mesh, they serve to establish trust relationship between Users that are used for data sharing and application access. Executive Modules also enforce sharing policies implemented at the Sites, i.e. which type of data sharing is permitted and what the requirements to access applications are.

Various functionalities of the Sites are periodically monitored. Accounting data is collected as well, containing gross aggregated usage statistics such as the number of shares and users, as well as the amount of storage used. None of the collected data can be traced back to an individual user.

The Science Mesh is therefore a “share-nothing” infrastructure where all Sites can function independently of each other. They do not depend on the Central Component to provide basic functionality for the users. If the monitoring or accounting services go offline, the main service is not affected, and only monitoring/accounting operations will be stopped. If the Central Database goes offline, the EMs cannot be updated. While the information within the EMs may be stale, the EMs remain functional. Again, this is not critical as expected update intervals of the Science Mesh topology metadata are in days rather than minutes. Since the Central Database will be based on a relational database management system, it will be possible to opt for a geo-redundant solution with high availability, if it is deemed that downtime is not tolerable.



