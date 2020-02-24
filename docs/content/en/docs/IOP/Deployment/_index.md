---
title: "Deployment"
linkTitle: "Deployment"
weight: 15
description: >
  How to deploy the IOP?
---

## Deployment
The ScienceMesh IOP consists of various components, being the core one [Reva](https://reva.link). Reva is an inter-operability platform that bridge the gap between cloud storages and application providers.

## Local deployment
Deploy invidual components one by one in your local computer. This approach is the best to learn how the different services connect to each other.

## Minikube deployment
The official mode of deployment for the platform will be to use Kubernetes and Helm charts.
Having a fully fledged Kubernetes cluster is a task on its own, so for the time being we provide a local Minikube deployment using the same specifications that will be consumed by a production Kubernetes cluster, so you can hack in your own computer.
