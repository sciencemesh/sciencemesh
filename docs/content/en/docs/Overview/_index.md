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
to access applications running in the infrastructure without prior detailed
knowledge of technical details like which systems their colleagues use and
what are their user identities in them.

The sites independently operate Enterprise File Synchronisation and Sharing (EFSS) systems. In order for the systems to share data, they use the [Open Cloud Mesh protocol](https://wiki.geant.org/display/OCM/Open+Cloud+Mesh) and [CS3 API](https://cs3org.github.io/cs3apis/). The protocol and the API handle accessing and exchanging data, but they solve neither discovery of user identites nor establishing trust between EFSS systems, that are necessary to make such a sharing practical for the administrators and the users. 

Those two main layers are therefore added by the Science Mesh:
- the Science Mesh is an infrastructure with metadata describing its
  sites and services (so that adding a site does not require configuring
  all other sites in the infrastructure individually)
- handling user identities is quite hidden from the users who may use any
  standard way of textual communication such as e-mails or instant
  messaging to establish trust with their colleagues to share data with
  them, i.e. by sending user-friendly invitations.

The ScienceMesh is an instance of such a federation.

# Who is Eligible to Join the ScienceMesh

The ScienceMesh is agnostic what type of data and user community is served
by a particular site, be it large or small, academic or otherwise. On the
other hand, certain minimal requirements both in policies as well as
quality of service are set for the sites to operate within the
infrastructure in order to achieve good quality of service for the users as
a whole. Those requirements in practice limit the sites to ones operated
professionally by an institution or by an ((inter)national)
e-infrastructure.

It is not to say that a tiny EFSS instance run by a small faculty
department is not allowed to join the infrastructure, but the overhead of
doing so would not be reasonable. The infrastructure is open to everybody
complying with the requirements.

On the other hand, it is up to a particular site what type of operations
will be accepted by the site and permitted to its users.

