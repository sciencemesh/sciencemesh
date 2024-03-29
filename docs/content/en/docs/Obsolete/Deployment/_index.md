---
title: "Deployment"
linkTitle: "Deployment"
weight: 15
description: >
  How to deploy the IOP/Reva
---

OBSOLETE

[Reva](https://reva.link) is a reference implementation of the inter-operability platform that serves as a communication layer between a sync'n'share system and the Science Mesh.

This section discusses deployment of Reva. There are several options, local
deployment on a single computer, Minikube for testing of a Kubernetes
install on a local machine, and finally, Kubernetes as the recommended way
of Reva deployment. Make your choice. Note that just plain deployment is
described here, the IOP must also be configured, which will be the next
step.

## Local deployment
Deploy invidual components one by one in your local computer. This approach is the best to learn how the different services connect to each other.
Follow the [local deployment guide](local).

## Minikube deployment
Having a fully fledged Kubernetes cluster is a task on its own. This section provides a Minikube setup using the same specifications that will be consumed by a production cluster, so you can start hacking in your own computer.
Follow the [Minikube deployment guide](minikube).

## Kubernetes deployment
Is still the official way and it is described in the active documentation.
This is a placeholder for obsolete pages.

