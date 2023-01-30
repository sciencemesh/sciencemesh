---
title: "Architecture and Implementation"
linkTitle: "Architecture and Implementation"
weight: 100
description: >
  Overall architecture of the Science Mesh and how those components are implemented
---

When joining the Science Mesh as a site, it is useful to understand the
architecture of the system on conceptual level.

There is a [Central Component](central-component-architecture) that handles
metadata of the Science Mesh together with monitoring and accounting. In
addition to the conceptual architecture, we also describe how the [Central
Component is implemented](central-component-implementation).

The Central Component is a source of data for the [Mesh Directory
Service](mesh-directory-service) that provides a user interface in the
invitation workflow to let the user reveal which system should be used to
establish sharing trust.

---

If unsure where to go next, read about the [General Architecture](central-component-architecture).
