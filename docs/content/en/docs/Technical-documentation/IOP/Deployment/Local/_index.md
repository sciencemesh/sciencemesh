---
title: "Local Deployment"
linkTitle: "Local"
weight: 15
description: >
  How to deploy ScienceMesh fetching individual components?
---

## Prerequisites

Make sure that you have **make**, **git**, **wget**, **tar**, **gcc** and **sudo** is installed. Further, you have to install **go** for Reva and Phoenix and **yarn** for Phoenix.

### Installing Go

Check https://golang.org/dl/ for the latest version of go. Install it and make sure that **go** is in your **$PATH**.

### Installing Yarn

For CentOS8, you can do this by:

```
curl --silent --location https://dl.yarnpkg.com/rpm/yarn.repo | sudo tee /etc/yum.repos.d/yarn.repo
dnf install yarn -y
```

There is a dependency on ***nodejs 10*** when building Phoenix. Make sure that that is installed.

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



