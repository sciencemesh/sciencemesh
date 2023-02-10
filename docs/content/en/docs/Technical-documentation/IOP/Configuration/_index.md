---
title: 'Configuration'
linkTitle: 'Configuration'
weight: 80
description: >
  How to configure Reva IOP to provide Science Mesh services
---

To get an understanding on how to create, use and manage Reva configuration,
please refer to the [official Reva documentation](https://reva.link/docs/getting-started/beginners-guide/).

> NOTE: The following configuration sections are meant to be put into the main Reva configuration file (`revad.toml`), unless explicitly stated otherwise.

> **IMPORTANT**: values marked with `$...$` are meant to be changed to something more meaningful

If you are joining the ScienceMesh with an ownCloud "OCIS" system, then follow the instructions below.
If you are joining with an ownCloud 10 or Nextcloud system then you can also use [this template](https://github.com/cs3org/ocm-test-suite/blob/main/servers/revad/sciencemesh.toml) and
* replace all occurrences of `your.revad.com` with your revad internet-facing hostname
* replace all occurrences of `your.effs.com` with your Nextcloud/ownCloud internet-facing hostname
* if necessary, change all occurrences of /index.php/apps/sciencemesh/ to the correct path if it's different (e.g. remove the "index.php/")
* replace all occurrences of "shared-secret-1" with the shared secret that you configured in the 'ScienceMesh' app's admin settings on your EFSS (see https://developer.sciencemesh.io/docs/technical-documentation/efss-deployment/ )
