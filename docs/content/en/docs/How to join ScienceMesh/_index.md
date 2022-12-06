---
title: "How to join Science Mesh"
linkTitle: "How to join Science Mesh"
weight: 100
description: >
    The steps to join the Science Mesh
---

This documentation is intended for an administrator of a sync-and-share site.

To join the Science Mesh, there are several formal and technical steps. For the sake of testing, you can start with Technical steps then once you are done with testing you can continue with the formal steps. If you encounter any problems during this process or have some general questions, feel free to [contact the ScienceMesh administration](../support/). 

## General notes on site deployment
1. If you prefer to understand the environment of the Science Mesh, how the components interact and what part of the systems you need to deploy, we suggest to get familiar with the [overall architecture of the Science Mesh and how the components are implemented](../architecture/).
1. Following lists of technical and formal steps could be performed mostly in parallel, each individual list should nevertheless be performed in the order stated below.
1. We indicate suitable versions of components in this document.

## Technical steps
1. In order to join the ScienceMesh, you as the operator of a site are expected to run one of supported EFSS (Enterprise File Sync and Share Systems). Firstly you need to deploy desired EFSS in your environment. Currently is Sciencemesh support implemented in forks of several EFSSs with added sharing applications, specifically [Nextcloud](https://github.com/pondersource/server/tree/sciencemesh) and/or [ownCloud](https://github.com/pondersource/core/tree/sciencemesh). Use versions (branches) linked here.

	You should use official documentation of [Nextcloud](https://nextcloud.com/install/) or [ownCloud](https://doc.owncloud.com/docs/next/) respectively to deploy your testing instance with ScienceMesh patch/support.

   (Note: distribution production versions will be supplied as patches against stable ownCloud and Nextcloud installations.)

1. Next step is to install and set up the [Reva IOP (interoperability platform)]({{< ref "docs/Technical-documentation/IOP" >}}) acting as an Executive Module of your EFSS instance in the ScienceMesh. Reva allows you to use all available API to share various resources within ScienceMesh (data, apps,...).
   
   Note: Reva version >=1.19 AND <2.0 is necessary. Avoid Reva 2.x as this is for OCIS only and will not work with ownCloud10 nor Nextcloud.

1. Then install an [integration application](../technical-documentation/iop/iop-nextcloud-owncloud10-integrations) that provides an interface between your EFSS and Reva.

1. Next step is to set up operational stuff. Your site needs to be [registered in the Central Database]({{< ref "docs/Technical-documentation/Central-Database" >}}) where metadata about your site and applications running there are stored. A Science Meshrepresentative will create initial entries for your site, but from there on, it will be your responsibility to keep these entries up to date. The Central Database will also serve you as the source of metadata about other meshed sites, e.g. for the Mesh Directory service (similar to “Where Are You From” or WAYF in identity federations). 

1. [Monitoring and accounting]({{< ref "docs/Technical-documentation/Monitoring" >}}) needs to be set up. Note this is not a replacement of your standard tools for operating your infrastructure but an addition; all information collected by ScienceMesh monitoring and accounting is strictly related to ScienceMesh operations, collecting just very high-level and aggregated information.

1. The last (optional) step is to enable the [Optional IOP/Reva Integrations]({{< ref "docs/Technical-documentation/IOP/IOP-Optional-Configs" >}}) such as CodiMD or collaborative tools (such as text processors and spreadsheets). Services intended to be accessible through the Science Mesh should be configured here.


## Formal steps

1. Get into contact with a ScienceMesh representative [by email, ideally providing necessary information](../science-mesh-governance-and-operations/firstcontactinfo/).

1. After that, a ScienceMesh representative will get back to you, detailing the next steps to get your site into the mesh.

1. [Formal steps]({{< ref "docs/Science-mesh-governance-and-operations" >}}) include declaring compliance with policies, appointing a representative into the governance structure of the infrastructure etc. As of January 2022, the infrastructure is to be formally established, so this part of the procedure will be covered later. It is neverheless recommended to get familiar with the proposed structure, as this is the best time to comment on it.

<!--
FIXME: I have asked Kuba and Pedro for an email addres helpdesk@sciencemesh.io
-->

<!--
From the Site Admission Procedure

Technical requirements
1. Each service must offer a way to be testable and verifiable from the outside by the
Operational Team to support automated testing.
1. The service MUST offer the required endpoints to perform these tests.
2. An account MUST be created in order to facilitate these tests.
3. These endpoints MUST be protected by some kind of authorisation mechanism.
4. It is only necessary to open these endpoints to the Operational Team; the Operational
Team will inform the site administrators in advance what IPs these tests will come from.
2. The service MUST support the Up-Test: This test will probe the service to see if it is up and
running, i.e. whether it can respond to a simple query on its endpoint.
3. An endpoint to collect accounting metrics MUST be provided.
1. This endpoint MUST be protected by some kind of authorisation mechanism.
2. It is only necessary to open this endpoint to the Operational Team; the Operational Team
will inform the site administrators in advance from what IPs the accounting metrics will
be collected.

Site admission procedure
The following steps need to be undertaken to join the Science Mesh:
1. A representative of the applicant Site signs the Science Mesh Policy Declaration and
presents it to the Operational Team together with the necessary contact information.
2. The applicant Site MUST make sure that it conforms with the requirements in section
“Requirements for Sites joining the Science Mesh”.
3. The Operational Team verifies that the applicant conforms with the requirements in section
“Requirements for Sites joining the Science Mesh”. If the applicant does not conform with
the requirements, the Operational Team MUST describe reasons and SHOULD give a
recommendation how to remedy.
4. The Operational Team informs the Science Mesh Steering Group about a new Site.
45. The Operational Team performs registration of the applicant Site to the Science Mesh. This
includes registration in the Central Database and making sure that accounting metrics are
collected and the Site monitoring tests are running.
-->

<!--

For the CS3 workshop, the governance boards and the OT will not be in place.
Therefore let us just focus on the technical part and leave the paperwork for
later on. We want sites to join the Science Mesh and not be deterred bij a mountain of bureaucracy.

-->


<!--
1. Read the technical and legal documentation which can be found here (FIXME).
1. Get into contact with the Science Mesh administration by using this online form (FIXME).
1. After reviewing your request, an administrator will get back to you, detailing the next steps; these include:
    - Getting remaining details about your site, especially technical ones like your IOP address for health monitoring
    - Performing initial compatibility and quality tests
    - FIXME...
1. Once all prerequisites have been met, you need to agree to and sign our OLAs/SLAs (FIXME).
1. Your site is added to our central database, effectively integrating it into the Science Mesh.
    - This includes appearing on all Science Mesh dashboards and being actively monitored for proper health.
1. In order to maintain your site's information, you will need to create an administrative account for our central database and request proper accesss rights.
    - More information will be provided by an administrator after your site has joined the Science Mesh.

-->

<!--

This stuff should go to FIXME1 I think.

### Checklists (FIXME)
Below you'll find some quick checklists to help you get your site ready for joining the Science Mesh.

#### General requirements (FIXME)
- Have this...
- And that...
- Legal stuff...
- FIXME...

#### Basic technical requirements
- Supported EFSS systems: _ownCloud 10/OCIS_, _Nextcloud_ (Version?), _Seafile_ (Version?)
- IOP (Reva) installed, configured and running next to your EFSS system
- EFSS and IOP must be accessible from the outside (see here (FIXME))
- FIXME...

#### Before joining the Science Mesh
- Read this (FIXME) introductory document that details the technical requirements to join the Science Mesh
- Install the IOP (Reva) as explained here (FIXME)
- Configure the IOP and connect it to your EFSS as explained here (FIXME)
- Run some self-tests as explained here (FIXME)
- FIXME...
-->
