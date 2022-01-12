---
title: "Central Database"
linkTitle: "Central Database"
weight: 11
description: >
  Information about the Central Database used for the ScienceMesh project.
---

This section contains details about the **GOCDB**, the central database used for storing the mesh metadata of the ScienceMesh project.

### Overview
All metadata of the ScienceMesh is stored in a central database, which is part of the [Central Component](../central-component). This data includes information about every participating site and its various services.

To manage this data, an instance of the **[GOCDB](https://github.com/GOCDB/gocdb)** is used. The GOCDB is a grid configuration database developed by [EGI](https://www.egi.eu). Below you will find a brief overview of how to gain access to the ScienceMesh GOCDB instance, as well as where the information regarding your site can be found. If you are looking for a more extensive GOCDB documentation, click [here](https://wiki.egi.eu/wiki/GOCDB).

### Gaining access
The central database can be found [here](https://gocdb.sciencemesh.uni-muenster.de). In order to access the GOCDB, you first need to create a ScienceMesh account using [this link](https://iop.sciencemesh.uni-muenster.de/iop/siteacc/account?path=register). You will be presented a simple form where you can create your account:
{{< imgproc "register-account.png" Fit "600x450" >}}
{{< /imgproc >}}

The various fields should be self-explanatory. The most important one is the `ScienceMesh Site` field; this also means that you can only register an account for your site _after_ it has been added to the central database by a ScienceMesh administrator.

After creating your account, you can log in to the account panel [here](https://iop.sciencemesh.uni-muenster.de/iop/siteacc/account/?path=login). In the panel, which you can see below, click on the `Request GOCDB access` button. A form will appear where you can send the request, including comments on why you need access, to the ScienceMesh administration.
{{< imgproc "account-panel.png" Fit "600x450" >}}
{{< /imgproc >}}

A ScienceMesh administrator will review your request and eventually grant you access to the central GOCDB instance. Use your main account credentials to log in.

### Requesting administrative rights for your site
The entry page of the GOCDB will look something like this:
{{< imgproc "gocdb.png" Fit "600x450" >}}
{{< /imgproc >}}

By default, you will only be able to view the mesh metadata but won't be able to modify anything. To gain write permissions for your site, follow these steps:
1. Under `Browse`, click on `Sites`
1. Click on the entry for your site
1. Scroll down until you find the `Users` box
1. Click on `Request role`:
    {{< imgproc "request.png" Fit "600x450" >}}
    {{< /imgproc >}}
1. From the roles drop-down menu, select `Site administrator`:
    {{< imgproc "role.png" Fit "600x450" >}}
    {{< /imgproc >}}
1. After submitting, a ScienceMesh administrator will review your request and grant you write access to your site

### Keeping your data up-to-data
It is your obligation as a site administrator to keep the information stored in the central database up-to-date. This mainly concerns the general site information that can be found under `Browse > Site`, as well as your site services that can be found under `Browse > services`.

When updating your data, it is absolutely necessary to keep the overall structure of your metadata as-is. This means that you MAY NOT add or remove site or service properties; you may modify them to match your actual data, of course. The same holds true for additional service endpoints.

For more details about the data stored in the database and what all these properties and service endpoints mean, click [here](./gocdb).
