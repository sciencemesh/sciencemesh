---
title: "Science Mesh Operators"
linkTitle: "Science Mesh Operators"
weight: 1000
description: >
  Documentation for Science Mesh Operators
---

# Science Mesh Helpdesk

Helpdesk of the Science Mesh serves operators of Science Mesh sites. Note
that user support remains at the sites; end user requests should generally
never be directly handled by the Science Mesh.

To contact the helpdesk, e-mail to helpdesk@sciencemesh.io. You will
receive an automated answer with assigned ticket number.

## Helpdesk Staff 101

This section is relevant if you are an administrator of the Request Tracker
queue called ScienceMesh-helpdesk.

The helpdesk is implemented as a Request Tracker queue and operated by
CESNET. Mail aliases are
- helpdesk (at) sciencemesh.io, if you reply to a mail from the system, the
  reply gets forwareded to ticket requestor; new mail to this address
  creates a new ticket,
- helpdesk-c (at) sciencemesh.io and helpdesk-com (at) sciencemesh.io for comments,
  i.e. internal communication among helpdesk staff related to the ticket,
  but not the ticket requestor.

In general, if you just "reply" to a mail from the queue, it is understood
as a communication related to the ticket (don't mess with MessageId and
X-RT headers).

Web interface of the system: https://rt.cesnet.cz/. You *MUST* be
registered (see below) to have access to the administrating interface of
the queue. Logging in, qou will be greeted by "Where are you from" page.
When logging in, in general, choose eduGAIN (among last items in the
"Direct links" list) and search for the organisation you used for
registration.

## Getting Access to the RT Queue

How to get access to the administration of the ScienceMesh-helpdesk RT
queue.

Note that mails to above mentioned addresses can be sent by anybody.

The administration interface of the queue is accessible to people in
Virtual Organisation Science Mesh Administrators (VO_sciencemesh) in
CESNET's Perun system (https://einfra.cesnet.cz/allfed/).

You need to apply to for membership in the organisation. Use
https://einfra.cesnet.cz/allfed/registrar/?vo=VO_sciencemesh&locale=en.
This is eduGAIN-enabled, so search for your home organisation identity
provider. If you don't have access to a suitable eduGAIN identity, search
for "CESNET" in the search bar: you will be offered "social identities"
such as Google, Linkedin, Github, Orcid, and others.

Your application will be manually reviewed and you will be assigned to
appropriate groups.

Note that after you are set up by the Virtual Organisation admin, allow for
up to 2 hours for propagation of your access rights to the RT system
itself.

## Managing the Virtual Organisation

This is only useful for virtual organisation administrators.

Applications for membership are most easily (dis)approved via links in
email notifications about the applications. Application requests are sent
to the RT queue. Kindly approve only people you expect and know; note that
nearly anybody from the whole Internet can apply.

Approving the application by itself doesn't do much, it just puts the
identity into VO_sciencemesh. There are two relevant groups in the virtual
organisation:
- RT-ScienceMesh-helpdesk, those identities have access to RT web interface
  and can thus manage tickets. They *WILL NOT* receive all mail
  communication from the RT queue, though.
- RT-ScienceMesh-helpdesk-watchers, those identities will receive mail from
  the queue.
Under normal circumstances, the mesh operator should be put into both those
groups. Note that changes in group membership may take up to 2 hours to be
propagated into the RT system itself.

(Technically, there is also group "members", it contains all members of the
virtual organisation.)

## Special Needs, Problem Solving

When everything goes blue side down with the RT Queue and/or user
management there, you can contact:
- du-support (at) cesnet.cz (storage guys who typically know what to do and
  most likely know you as well)
- support (at) cesnet.cz (support guys who know everything, but probably
  don't know you ;)).

Mail aliases in sciencemesh.io are managed by CERN.

