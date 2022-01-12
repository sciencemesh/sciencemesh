---
title: "How to join ScienceMesh"
linkTitle: "How to join ScienceMesh"
weight: 100
description: >
    The steps to join the ScienceMesh
---

To join the ScienceMesh, there are several formal and technical steps. If you encounter any problems during this process or have some general questions, feel free to contact the ScienceMesh administration here (FIXME).
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
later on. We want sites to join the ScienceMesh and not be deterred bij a mountain of bureaucracy.

-->
Here is a brief overview of the steps necessary to join the ScienceMesh project:

1. First you need to setup:
    - the [Inter Operability Platform]({{< ref "docs/Technical-documentation/IOP" >}}) (IOP)
    - the [health monitoring]({{< ref "docs/Technical-documentation/monitoring/Health-Monitoring" >}})
    - the [accounting metrics]({{< ref "docs/Technical-documentation/monitoring/Accounting-Metrics" >}}) collection    

   for your site. You may find information on how to setup these components by following the links above.

1. Get into contact with a ScienceMesh representative by using this [form]({{< ref "docs/How to join ScienceMesh/OnlineForm" >}})
1. After that, a ScienceMesh representative will get back to you, detailing the next steps to get your site up and running. 
1. Your site is then added to our central database, effectively integrating it into the ScienceMesh.
    - This includes appearing on all ScienceMesh dashboards and being actively monitored for proper health.
1. In order to maintain your site's information in the central database, you will need to create an administrative account for our central database and request proper accesss rights.
    - More information will be provided by a ScienceMesh representative after your site has joined the ScienceMesh.


<!--
1. Read the technical and legal documentation which can be found here (FIXME).
1. Get into contact with the ScienceMesh administration by using this online form (FIXME).
1. After reviewing your request, an administrator will get back to you, detailing the next steps; these include:
    - Getting remaining details about your site, especially technical ones like your IOP address for health monitoring
    - Performing initial compatibility and quality tests
    - FIXME...
1. Once all prerequisites have been met, you need to agree to and sign our OLAs/SLAs (FIXME).
1. Your site is added to our central database, effectively integrating it into the ScienceMesh.
    - This includes appearing on all ScienceMesh dashboards and being actively monitored for proper health.
1. In order to maintain your site's information, you will need to create an administrative account for our central database and request proper accesss rights.
    - More information will be provided by an administrator after your site has joined the ScienceMesh.

-->

<!--

This stuff should go to FIXME1 I think.

### Checklists (FIXME)
Below you'll find some quick checklists to help you get your site ready for joining the ScienceMesh.

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

#### Before joining the ScienceMesh
- Read this (FIXME) introductory document that details the technical requirements to join the ScienceMesh
- Install the IOP (Reva) as explained here (FIXME)
- Configure the IOP and connect it to your EFSS as explained here (FIXME)
- Run some self-tests as explained here (FIXME)
- FIXME...
-->
