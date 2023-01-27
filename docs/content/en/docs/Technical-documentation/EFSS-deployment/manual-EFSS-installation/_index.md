---
title: "How to Install EFSS Manually"
linkTitle: "How to Install EFSS Manually"
weight: 200
description: >
    Manual installation of EFSS
---

We provide two ways of deploying EFSS:

  * installing manually using standard ownCloud 10 or Nextcloud installations, patching and installing the integration application,
  * or deploying directly from the github repository. This option is suitable for developers only and is not recommended for production use.

## Manual installation of Science Mash enabled EFSS using patches

This procedure leads to the same result as the official Kubernetes deployment, it is just performed manually.

1. Deploy ownCloud 10 and or Nextcloud to your infrastructure, following
   the official documentation of [Nextcloud](https://nextcloud.com/install/) or [ownCloud](https://doc.owncloud.com/docs/next/) respectively.

1. Patch the installation with:
   - ownCloud 10:
     (https://patch-diff.githubusercontent.com/raw/owncloud/core/pull/40577.patch)
   - Nextcloud:
     (https://patch-diff.githubusercontent.com/raw/nextcloud/server/pull/36228.patch)

   (suitable patch options could be, e.g. `patch -p1 -t --forward --no-backup-if-mismatch < ...patch...` that should be performed in the folder where the EFSS is installed, typically something like /var/www/owncloud)

1. Install the integration application. Go the the EFSS installation
   directory, and perform
   - ownCloud 10:
     ```
     git clone -b v0.1.0 https://github.com/pondersource/oc-sciencemesh sciencemesh
     cd sciencemesh
     make
     ```
   - Nextcloud:
     ```
     git clone -b v0.1.0 https://github.com/pondersource/nc-sciencemesh sciencemesh
     cd sciencemesh
     make
     ```

1. FIXME register the application


## Manual installation of Science Mesh enabled EFSS for developers

Beware: those are alternative technical steps for developers, they are **not recommended** for production use and are **not supported** by the Science Mesh. You've been warned, this can bite you and kill your cat.

Repositories linked from this subsection already contain necessary patches,
and those repositories mostly track ownCloud 10 and Nextcloud git masters.
Have we mentioned it wasn't suitable for production?

1. In order to join the ScienceMesh, you as the operator of a site are expected to run one of supported EFSS (Enterprise File Sync and Share Systems). Firstly you need to deploy desired EFSS in your environment. Currently is Sciencemesh support implemented in forks of several EFSSs with added sharing applications, specifically [Nextcloud](https://github.com/pondersource/server/tree/sciencemesh) and/or [ownCloud](https://github.com/pondersource/core/tree/sciencemesh). Use versions (branches) linked here.

	You should use official documentation of [Nextcloud](https://nextcloud.com/install/) or [ownCloud](https://doc.owncloud.com/docs/next/) respectively to deploy your testing instance with ScienceMesh patch/support.

1. Then install an [integration application](../technical-documentation/iop/iop-nextcloud-owncloud10-integrations) that provides an interface between your EFSS and Reva.


1. FIXME register the application

