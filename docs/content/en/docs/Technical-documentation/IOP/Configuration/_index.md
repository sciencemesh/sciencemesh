---
title: 'Configuration'
linkTitle: 'Configuration'
weight: 80
description: >
  How to configure Reva IOP to provide Science Mesh services
---

To get an understanding on how to create, use and manage your two Reva configurations,
please refer to the [official Reva documentation](https://reva.link/docs/getting-started/beginners-guide/).

> NOTE: The following configuration sections are meant to be put into the main Reva configuration file (`revad.toml`), unless explicitly stated otherwise.

> **IMPORTANT**: values marked with `$...$` are meant to be changed to something more meaningful

If you are joining the ScienceMesh with an ownCloud "OCIS" system, then follow the instructions below.
If you are joining with an ownCloud 10 or Nextcloud system then you can also use the [sciencemesh1.toml](https://github.com/cs3org/ocm-test-suite/blob/main/servers/revad/sciencemesh1.toml), [sciencemesh2.toml](https://github.com/cs3org/ocm-test-suite/blob/main/servers/revad/sciencemesh2.toml), and [sciencemesh3.toml](https://github.com/cs3org/ocm-test-suite/blob/main/servers/revad/sciencemesh3.toml) for your three required revad processes. Make sure to replace all 'your.revad.com' and 'your.efss.com' strings in each of them with your actual revad and efss host names!
* replace all occurrences of `your.revad.com` with your revad internet-facing hostname
* replace all occurrences of `your.effs.com` with your Nextcloud/ownCloud internet-facing hostname
* replace '/etc/revad/tls/revanc1.crt' and '/etc/revad/tls/revanc1.key' strings to point to a valid TLS certificate (or remove these lines if you use an http offloading proxy in front of your revad http port)
* if necessary, change all occurrences of /index.php/apps/sciencemesh/ to the correct path if it's different (e.g. remove the "index.php/")
* IMPORTANT FOR SECURITY: change the 'shared-secret-1' and 'machine-api-key' strings to random ones!
* you can test your config by putting the three files into a folder and from there run something like `../reva/cmd/revad/revad --dev-dir .` - a Helm chart for running the three revad processes will hopefully be available soon!
* replace all occurrences of "shared-secret-1" with the shared secret that you configured in the 'ScienceMesh' app's admin settings on your EFSS (see https://developer.sciencemesh.io/docs/technical-documentation/efss-deployment/ )
