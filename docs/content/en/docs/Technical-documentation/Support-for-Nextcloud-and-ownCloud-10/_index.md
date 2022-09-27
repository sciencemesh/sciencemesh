---
title: 'Support for Nextcloud and ownCloud 10 - TO BE MERGED WITH IOP SETUP'
linkTitle: 'Support for Nextcloud and ownCloud 10'
weight: 430
description: >
  Configuring support for the invitation workflow in Nextcloud and
  ownCloud 10 (pre-OCIS versions)
---

---

> **_NOTE:_** The following steps documents installation for common k8s deployment environments,
> there may be additional steps required for different deployments.

## Install Nextcloud server

popsat

## Install OwnCloud server

popsat

Both Nextcloud and ownCloud 10 are covered by a procedure described in
<https://sciencemesh-nextcloud.readthedocs.io/en/latest/installation.html>.

There are some caveats in the process one should be aware of, though.

- which version of ownCloud/Nextcloud to use

  Currently:

  - Nextcloud: <https://github.com/pondersource/server/tree/dynamic-shareproviders>
  - ownCloud:
    <https://github.com/pondersource/core/tree/reva-sharees>

  - There is no clear specification of minimum OC/NC version, that can be patched with changes from these Git branches
  - Is there any plan to push the needed changes to upstream NC/OC codebase? Patching official production OC/NC installations with these branches (that are based on a main upstream development branches) is undesirable (hard to do, unsafe, not tested...).

- which is the correct version of the application that really works

  There are following versions:

  1. <https://github.com/pondersource/oc-sciencemesh> (works)
  2. <https://github.com/sciencemesh/nc-sciencemesh> (needs additional steps)
  3. <https://github.com/sciencemesh/oc-sciencemesh> (works, but needs additional steps)

  Version 2. & 3. is missing a `vendor` directory, that can be brought back by running `make` on it (and calling that requires a development environment to be installed beforehand), not mentioned in the original installation procedure.

- necessary changes in helm charts

  A recent version of Revad is needed (official sciencemesh [Charts](https://github.com/sciencemesh/charts/blob/master/iop/Chart.yaml#L41) provides a rather old one). To use the latest version of Reva available, set this value when deploying chart with Helm:

  ```shell
  helm install sciencemesh/iop ... --set gateway.image.tag=latest ...
  ```

- Nextcloud vs. ownCloud 10 specifics

- tricky bits in configs?

  1. an ending `/` is required here:

  ```toml
    #revad.toml
    [http.services.ocmd]
    mesh_directory_url = "https://.../iop/meshdir/"
  ```

  ```sql
  MariaDB []> select * from oc_appconfig where appid = 'sciencemesh';
  ...
  | sciencemesh | meshDirectoryUrl | https://.../iop/meshdir/ |
  ```

  2. mesh provider domains & user's idps
     If you don't use a scheme in those and use the Mentix driver for provider authorizer, you may face problems with requests authorization. This will be fixed with <https://github.com/cs3org/reva/pull/3121>. A temporary fix is to switch to static `json` driver for OCM provider authorizer.

- check ingress rules?
  you must ensure that all `/ocm/` paths reaches the IOP service on your provider domain

- any meaningful procedure for testing?

Changes of the docs necessary elsewhere (possibly):

- is this page <https://developer.sciencemesh.io/docs/technical-documentation/integrations/nextcloud/> completely obsolete?

- Reva deployment and Helm charts?

```

```
