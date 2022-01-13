---
title: "Deployment"
linkTitle: "Deployment"
weight: 15
description: >
  How to deploy the IOP?
---

The ScienceMesh IOP consists of various components, being the core one [Reva](https://reva.link). Reva is an inter-operability platform that bridge the gap between cloud storages and application providers.

## Local deployment
Deploy invidual components one by one in your local computer. This approach is the best to learn how the different services connect to each other.

## Minikube deployment
Having a fully fledged Kubernetes cluster is a task on its own. This section provides a Minikube setup using the same specifications that will be consumed by a production cluster, so you can start hacking in your own computer.

## Kubernetes deployment
The official way of deploying the IOP platform are its [Helm charts](https://sciencemesh.github.io/charts/). They provide an abstraction over the different Kubernetes entities required to configure and run the IOP on your cluster.
