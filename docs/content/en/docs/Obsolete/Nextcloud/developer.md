---
title: "ScienceMesh-Nextcloud Developer Documentation"
linkTitle: "ScienceMesh-Nextcloud Developer Documentation"
weight: 400
description: >
  Documentation for developers of a ScienceMesh-Nextcloud integration.
---

# Developer Documentation

The 'sciencemesh' app for Nextcloud adds API endpoints so that Nextcloud
can serve as a user backend, a shares backend, and a storage backend for revad.

## API Architecture

*This chapter of the developer documentation was the deliverable for work package 2b of the sciencemesh-nextcloud project*

### Invitation flow API

Nextcloud already supports OCM out of the box, but not the invitation-first flow that is used on the ScienceMesh.
There are three systems involved:

* *nextcloud-sender* (Nextcloud server of the sender)
* *revad-sender* (IOP server of the sender)
* *receiver* (the receiver's system; opaque for this part of the flow)

### Obtaining the invite token (from Nextcloud GUI)
Step-by-step, this is how it works for the sender if the sending user uses the Nextcloud GUI to generate the invite token:

1. The sending user uses the GUI of *nextcloud-sender* to request the generation of an invite token.
2. This results in a REST call from *nextcloud-sender* to *revad-sender*:

   - Method: POST
   - Content Type: JSON
   - URL: https://revad-sender/ocm/invitation/generate
   - Request Body: empty
   - Authentication: http basic auth, with the sending user's actual username, but with `<loopback-secret>` as the password (this is because the server-side PHP code does not know the user's real password)
3. Inside revad, this http request triggers a grpc request, including the username and loopback secret
4. The `<loopback-secret>` is checked by reva's Nextcloud-based auth backend (note that both the loopback secret and the user's actual password will grant access)
5. The invite token is generated by reva
6. The token is returned from the grpc call within *revad-sender*
7. The token is returned from the rest call back to *nextcloud-sender*
8. The token is displayed in the GUI, for the sending user to copy and relay out-of-band to the receiving user.

### Obtaining the invite token (from Reva CLI)
Step-by-step, this is how it works for the sender if the sending user uses the Reva CLI to generate the invite token:

1. The sending user uses the CLI client of *revad-sender* to request the generation of an invite token.
2. The reva CLI tool sends a grpc request, including the `<clientID>` and `<clientSecret>`
3. This results in a REST call from *sending-revad* to *sending-nextcloud*:

   - Method: POST
   - Content Type: JSON
   - URL: https://nextcloud-sender/index.php/apps/sciencemesh/api/auth/Authenticate
   - Request Body: e.g. '{"clientID":"einstein","clientSecret":"relativity"}'
   - X-Reva-Secret header: `<shared-secret>`
4. The `<clientID>` and `<clientSecret>`  are checked by reva's Nextcloud-based auth backend (note that both the loopback secret and the user's actual password will grant access)
5. A JSON-serialized CS3 User object is returned.
6. The token is generated by *revad-sender*.
7. The token is returned from the grpc call
8. The token is displayed in the CLI tool, for the sending user to copy and relay out-of-band to the receiving user.

### Accepting the invite token
Step-by-step, this is how it works for the receiver:
There are again three systems involved:

- *sender* (the sender's system; opaque for this part of the flow)
- *revad-receiver* (IOP server of the receiver)
- *nextcloud-receiver* (Nextcloud server of the receiver)

The receiving user either pastes the token into the Nextcloud GUI, or into reva-cli. Authentication works the same way
as on the sending side:

- If coming through the Nextcloud GUI, the Nextcloud-based user manager verifies username + loopback secret
- If coming through the Reva CLI, the Nextcloud-based user manager verifies username + user password

A REST call for 'forward OCM invite' is made from *revad-receiver* to *nextcloud-receiver*. Note that the 'forward' here may feel like a bit of a misnomer, but it probably refers
to the fact that all the receiving revad effectively does is take the token from the message body and launch an almost identical request to it. So it really is a case of "forwarding the message":

 *receiving-user*

 -("Forward this invite acceptance notice!")->

 *receiving-revad*

 -("Here is an invite acceptance notice!")->

 *sending-revad*

A REST call for 'accept OCM invite' made from *nextcloud-receiver* to *sender*. Note that this again feels like a misnomer because *sending-revad* is not ordered to accept the invite, it is just
receiving the information that the receiver has done so.

### Creating the share (sender-side, from Nextcloud GUI)
When the sending user looks for a recipient to share a resource with, and starts typing the recipient identifier, the autocomplete function gathers search results from a number of share providers,
one of which will be ScienceMesh.
The ScienceMesh share provider compares the characters typed so far to names of users who accepted the sender's invite, and provides an autocomplete result.
If the user clicks this, a rest call to https://revad-sender/ocm/send will be made
For authentication, the username and `loopback-secret` will be sent by Nextcloud, and they will loop back to Nextcloud in a user authentication request.

### Creating the share (sender-side, from Reva CLI)
The sender can also call `ocm-share-create` in the Reva CLI using their username and password.
This would then similarly result in a call to the OCM Core share provider service, and the OCM share manager driver, which results in:

- a rest call to *nextcloud-sender* (this time authenticated with username and the user's actual password), to store the sent share on the sender side.

### Creating the share (sender-side, common part)
After that, in both cases, it will trigger a call to the CreateOCMShare method of the OCM share provider
service, and the OCM share manager driver, which results in:

- an `addSentShare` rest call to *nextcloud-sender* (authenticated this time with `X-Reva-Secret`), to store the sent share on the sender side
- an OCM /shares POST to *nextcloud-receiver* (unauthenticated except for the client IP check)

### Creating the share (receiver-side)
When the OCM /shares POST comes in, it triggers a call to the CreateOCMCoreShare method (not "CreateOCMShare method") of the OCM Core service (not "OCM share provider service"),
which also triggers Share function of the OCM share manager driver. So the code paths sound very similar but are subtly different:

- on the sending side it is the *CreateOCMShare* method of the *OCM share provider* service that triggers the Share method of the OCM share manager driver
- on the receiving side it is the *CreateOCMCoreShare* method of the *OCM Core provider* service that triggers the Share method of the OCM share manager driver

The Share method of the OCM share manager driver figures out whether the share was generated locally, and if not, concludes that it must be on the receiving side.
Then, an `addReceivedShare` rest call is made to *nextcloud-sender* (authenticated with `X-Reva-Secret`), to store the sent share on the sender side.


## File sharing API

In a future version we will also implement data transfer shares that trigger an rclone job, but for now,
all received shares just result in a webdav mount on the receiver side.

So when the receiver accesses the resource that was shared with them, the data is actually fetched from the source in real-time.

### Accessing the received share file

When the receiver access the resource through their Nextcloud GUI, the *receiving-nextcloud* will see in its database that the given path is a webdav mount.
It will do a webdav request to *sending-revad*.
This will lead to `GetMD` and `InitiateDownload` calls to *sending-nextcloud*.
The file contents are delivered along the following path:
```
   sending-nextcloud -> sending-revad -> receiving-nextcloud -> webbrowser
```

Similarly, the receiver can access the resource through their Nextcloud WebDAV interface, from their Nextcloud Mobile app, through their Reva CLI or their revad's WebDAV interface.


## Registration flow API

The registration for ScienceMesh is currently still quite a manual process. See the [admin guide](./admin.md).
