---
title: "Central Database"
linkTitle: "Central Database"
weight: 11
description: >
  Information about the Central Database used for the Science Mesh project.
---

This section contains details about the **GOCDB**, the central database used for storing the mesh metadata of the Science Mesh project.

## Overview
All metadata of the Science Mesh is stored in a central database, which is part of the [Central Component](../central-component). This data includes information about every participating site and its various services.

To manage this data, an instance of the **[GOCDB](https://github.com/GOCDB/gocdb)** is used. The GOCDB is a grid configuration database developed by [EGI](https://www.egi.eu). Below you will find a brief overview of how to gain access to the Science Mesh GOCDB instance, as well as where the information regarding your site can be found. The GOCDB also comes with [detailed documentation](https://wiki.egi.eu/wiki/GOCDB).

To register a Science Mesh site, you first need to get access to the database and create a record for your Site there. As registration requires human approval, you are advised to do this step first. In the later stage of the deployment, Site endpoints will configured (both in the Site's IOP as well as in the Central Database). Here we describe the first part, i.e. gaining access and registering a site.

## Gaining access
[The central database is available through a web interface](https://gocdb.sciencemesh.uni-muenster.de).

**Note:** Account registration is only available _after_ your site has been added to the mesh
by the Science Mesh administrator.

In order to access the GOCDB, you first need to [register a Science Mesh Site Administrator Account](https://iop.sciencemesh.uni-muenster.de/iop/siteacc/account?path=register). You will be presented a simple form where you can create your account:
{{< imgproc "register-account.png" Fit "600x450" >}}
{{< /imgproc >}}

The various fields should be self-explanatory. The most important one is the `Science Mesh Operator` field; choose your institution/company here.

After creating your account, you can log in [to the account panel](https://iop.sciencemesh.uni-muenster.de/iop/siteacc/account/?path=login). In this panel, which you can see below, click on the `Request GOCDB access` button. A form will appear where you can send the request, including comments on why you need access, to the Science Mesh administration.
{{< imgproc "account-panel.png" Fit "600x450" >}}
{{< /imgproc >}}

A Science Mesh administrator will review your request and eventually grant you access to the central GOCDB instance. Use your main account credentials to log in.

## Requesting administrative rights for your sites
The entry page of the GOCDB will look something like this:
{{< imgproc "gocdb.png" Fit "600x450" >}}
{{< /imgproc >}}

By default, you will only be able to view the mesh metadata but won't be able to modify anything. To gain write permissions for your data, follow these steps:
1. Under `Browse`, click on `NGIs`; _please note that an operator is called NGI in GOCDB_
1. Click on the entry for your operator
1. Scroll down until you find the `Users` box
1. Click on `Request role`:
    {{< imgproc "request.png" Fit "600x450" >}}
    {{< /imgproc >}}
1. From the roles drop-down menu, select `NGI Operations Manager`:
    {{< imgproc "role.png" Fit "600x450" >}}
    {{< /imgproc >}}
1. After submitting, a Science Mesh administrator will review your request and grant you write access to your data

## Setup of your ScienceMesh instance in GOCDB
To set up your ScienceMesh instance in GOCDB you need to setup all elementary components as follows.

First you need to define **Site** by click on the **Add Site** button in GOCDB interface. For each ScienceMesh instance (in GOCDB referred as **Site** object) you have to define two elementary **Service Types**. First is **REVAD** and second is **EFSS** Service Type. All objects described in this section are editable using edit icon in the right-top corner. Although you have to have appropriate permissions granted by **Operation Manager**. Setup of user permissions can be found in the lef menu **User Status** -> **Manage Roles**.

### Site object
The Site can be created by clicking on the **Add Site button** in the left menu. In the particular **Site** object detail, see below, you can find all elementary information you need to fill in. Especially in the right-up the pannel named **Project Data**. There you can define the value of **Infrastructure** attribute (Production | Test). This attribute is taken into account to distinguish the status of your infrastructure for other services, e.g. MeshDirectory service. Below in the **Site** object detail you have to add both services defined below (REVAD, EFSS) to the displayed **Site**, see below. While creating the **Service**s below you is necessary to choose **Hosting Site** value corresponding to your Site created in this step.

{{< imgproc "sm-site.png" Fit "600x450" >}}
{{< /imgproc >}}

### REVAD service
In the following screenshot you can see the exemplary setup of the REVAD service.

Please pay extra attention to setup correctly all **Service Endpoints** which are being used for monitoring and other services. Of course **the URLs depend on your own ScienceMesh instance setup!**
{{< imgproc "revad-service.png" Fit "600x450" >}}
{{< /imgproc >}}

### EFSS service
In the following screenshot you can see the exemplary setup of the EFSS service.
Please pay extra attention to setup correctly all **Service Endpoints**. Particularly **EFSS_WEBAPP** and **EFSS_STATUS**. Of course **the URLs depend on your own ScienceMesh instance setup!**
{{< imgproc "efss-service.png" Fit "600x450" >}}
{{< /imgproc >}}


## Keeping your data up-to-date
It is your obligation as a site administrator to keep the information stored in the central database up to date. This mainly concerns the general sites' information that can be found under `Browse > Site`, as well as your sites' services that can be found under `Browse > Services`.

When updating your data, it is absolutely necessary to keep the overall structure of your metadata as-is. This means that you MAY NOT add or remove site or service properties; you may modify them to match your actual data, of course. The same holds true for additional service endpoints.

For more details about the data stored in the database and what all these properties and service endpoints mean, we provide a [detailed description of its structure](./gocdb).
