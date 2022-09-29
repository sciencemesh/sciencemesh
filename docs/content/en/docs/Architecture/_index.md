---
title: "Architecture and Implementation"
linkTitle: "Architecture and Implementation"
weight: 200
description: >
  Overall architecture of the Science Mesh and how those components are implemented
---

When joining the Science Mesh as a site, it is useful to understand the
architecture of the system on conceptual level.

**FIXME**

Later you will see how the
components [are actually
implemented](../technical-documentation/central-component).

The Science Mesh consists of its participating sites running Enterprise File Sync and Share (EFSS) services and of a so-called [Central Component]({{< ref "docs/Technical-documentation/Central-Component" >}}). From a logical point of view, the Central Component is responsible for providing the few global services of the Science Mesh. It does not imply that the Central Component is a single physical entity, it well may be distributed across several Science Mesh partners. 
The interface between a Science Mesh node and the Science Mesh’s core operational infrastructure is what is called an Executive Module (EM).
