---
title: "General Architecture"
linkTitle: "General Architecture"
weight: 200
description: >
  A short description of the architecture of the Science Mesh   
---

When joining the Science Mesh as a site, it is useful to understand the
architecture of the system on conceptual level. Later you will see how the
components [are actually
implemented](../central-component-implementation).

The Science Mesh consists of its participating sites running Enterprise File Sync and Share (EFSS) services and of a so-called Central Component. From a logical point of view, the Central Component is responsible for providing the few global services of the Science Mesh. It does not imply that the Central Component is a single physical entity, it well may be distributed across several Science Mesh partners. 
The interface between a Science Mesh node and the Science Mesh’s core operational infrastructure is what is called an Executive Module (EM).

The picture below displays the conceptual architecture: the Central Component of the Science Mesh includes [health monitoring and accounting metrics collection]({{< ref "docs/Technical-documentation/Monitoring" >}}) infrastructure as well as the [Central Database]({{< ref "docs/Technical-documentation/Central-Database" >}}) keeping Science Mesh metadata.
The central [helpdesk]({{< ref "docs/Support" >}}) is included for completeness. 
The [health monitoring]({{< ref "docs/Technical-documentation/Monitoring/Health-Monitoring" >}}) infrastructure runs probes which return results to the monitoring infrastructure as to if the services running at the sites are operational.
Similarly, the [accounting metrics]({{< ref "docs/Technical-documentation/Monitoring/Accounting-Metrics" >}}) infrastructure collects usage metrics, like the total number of user or the total amount of data stored, from sites.
The Central Database provides the EMs running within sites with Science Mesh topology information.

{{< imgproc architecture.jpg Resize "x640" >}}
Architecture of the Science Mesh
{{< /imgproc >}}


Topology information contained in the Central Database includes site names, endpoints, services running at the sites and further meta-information. Executive Modules consume this information and use it to perform service and user discovery for the users (similar to WAYF service in identity federations). Those discovery processes are the cornerstone of the Science Mesh, they serve to establish trust relationship between Users that are used for data sharing and application access.

Executive Modules also enforce sharing policies implemented at the sites, i.e. which type of data sharing is permitted and what the requirements to access applications are. Note that sharing policies are thus local to the nodes and managed by them.

The Science Mesh does not critically depend on the Central Component being available. The Science Mesh is therefore a “share-nothing” infrastructure where all sites can function independently of each other. They do not depend on the Central Component to provide basic functionality for the users. If the health monitoring or accounting metrics services go offline, the main service is not affected, and only health monitoring and accounting metrics collection operations will be stopped.
If the Central Database goes offline, the EMs cannot be updated. While the information within the EMs may become stale, the EMs remain functional. Again, this is not critical as expected update intervals of the Science Mesh topology metadata are in days rather than minutes.
