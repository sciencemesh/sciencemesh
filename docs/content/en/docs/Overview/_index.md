---
title: "Overview"
linkTitle: "Overview"
weight: 10
description: >
  ScienceMesh in a Nutshell
---

# What is ScienceMesh

ScienceMesh is an infrastructure of independent data storage sites
providing sync-and-share services. It is a loose federation
allowing users of the sites to share and transfer data among the sites and
to access applications runnig in the infrastructure without prior detailed
knowledge of technical details like which systems their colleagues use and
what are their user identities in them.

The sites independently operate Enterprise File Synchronisation and Sharing (EFSS) systems. In order for the systems to share data, they use the [Open Cloud Mesh protocol](https://wiki.geant.org/display/OCM/Open+Cloud+Mesh). The protocol handles accessing and exchanging data, but it solves neither discovery of user identites nor establishing trust between EFSS systems. Those are two main layer added by the Science Mesh:
- the Science Mesh is an infrastructure with metadata describing its
  partners (so that adding a site does not require configuring all other
  sites in the infrastructure separately)
- handling user identities is quite hidden from the users who may use any
  standard way of textual communication such as e-mails or instant
  messaging to establish trust with their colleagues to share data with
  them.

The ScienceMesh is an instance of such a federation.

# Who is Eligible to Join the ScienceMesh

The ScienceMesh is agnostic what type of data and user community is served
by a particular site, be it large or small, academic or otherwise. On the
other hand, certain minimal requirements both in policies as well as
quality of service are set for the sites to operate within the
infrastructure. Those in practice limit the sites to ones operated
professionally by an institution or by an ((inter)national)
e-infrastructure.

It is not to say that a tiny EFSS instance run by a small faculty
department cannot join the infrastructure, but the overhead of doing so
would not be reasonable.

