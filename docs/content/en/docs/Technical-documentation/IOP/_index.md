---
title: "Inter-Operability Platform/Reva"
linkTitle: "IOP"
weight: 10
description: >
  Inter-Operability Platform/Reva
---

## What is the IOP?

The Inter-Operability Platform (IOP) is a service providing a unified standard
interface handling communication of the Site's Sync'n'share system with the
rest of the Science Mesh. It provides services such as establishing data
shares among users (where it handles requests from and to the Science Mesh
and translates them into the Site's sync'n'share system itself).

[Reva](https://reva.link/) is a reference implementation of the IOP. Strictly speaking, you don't
need Reva if your sync'n'share system supports Science Mesh protocols out
of the box. In practice, you need Reva. We sometimes use terms Reva and IOP
interchangeably.

## How to deploy Reva?
Deploying Reva is important step to setup ScienceMesh environment.
1. [Deploy Reva](deployment/), supported way is to Kubernetes.
1. [Configure Reva]({{<ref "docs/Technical-documentation/IOP/Configuration" >}})
   * to define what services are necessary for ScienceMesh environment
   * to talk to your Site's file sync'n'share service
1. [Set up accounting]({{<ref "docs/Technical-documentation/IOP/Accounting-metrics">}})

