---
title: "Local Deployment"
linkTitle: "Local"
weight: 15
description: >
  How to deploy ScienceMesh fetching individual components?
---

## Reva
Install Reva following the official documentation: [Install Reva](https://reva.link/docs/getting-started/install-reva/).

## ownCloud Phoenix
ownCloud Phoenix is not a required component for the IOP however is a nice add-on as it allows to interact with the IOP from a nice UI.
To install Phoenix from the sources do:

```
git clone https://github.com/owncloud/phoenix
cd phoenix
git checkout v0.4.0
yarn install-all
yarn dist
```

## Configure Reva
If you want to configure Reva to run locally with Phoenix you can follow this tutorial: https://reva.link/docs/tutorials/phoenix-tutorial/ 



