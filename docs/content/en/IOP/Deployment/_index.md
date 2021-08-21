---
title: "Deployment"
linkTitle: "Deployment"
weight: 15
description: >
  How to deploy the IOP?
---

The ScienceMesh IOP consists of various components, the core one being [Reva](https://reva.link). Reva is an inter-operability platform that bridges the gap between cloud storage and application providers.

## Local deployment
Deploy individual components one by one on your local computer. In order to learn how the different services connect to each other, this approach is a good starting point.

## Minikube deployment
Having a fully fledged Kubernetes cluster is a task on its own. This section provides a Minikube setup using the same specifications that will be consumed by a production cluster, so you can start hacking on your own computer.

## Kubernetes deployment
The official way to deploy the IOP platform is via its [Helm charts](https://sciencemesh.github.io/charts/). They provide an abstraction over the different Kubernetes entities required to configure and run the IOP on your cluster.
