---
title: "Minikube Deployment"
linkTitle: "Minikube"
weight: 15
description: >
  How to deploy the ScienceMesh IOP using Minikube?
---

This tutorial demonstrates how to deploy both REVA and Phoenix on a local Minikube cluster with a single helm chart.

## Prerequisites

- [kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl/)
- [minikube](https://kubernetes.io/docs/tasks/tools/install-minikube/)
- [helm](https://helm.sh/docs/intro/install/)

Clone the repo:

```bash
git clone https://github.com/sciencemesh/sciencemesh.git
```

### Launch minikube and configure the helm-tiller add-on

```bash
minikube start
minikube addons enable helm-tiller
```

> **Note:** If it is the first time you're running minikube, take a look to the [different VM drivers](https://kubernetes.io/docs/setup/learning-environment/minikube/#specifying-the-vm-driver) available for the `--driver' flag to pick the best one for your system.

### Deploy REVA and Phoenix using the helm chart

```bash
helm install reva-phoenix ./reva-phoenix
```

And verify the deployments are ready by running:

```bash
kubectl get deployments -o wide
NAME                 READY   UP-TO-DATE   AVAILABLE   AGE   CONTAINERS          IMAGES             SELECTOR
phoenix-deployment   1/1     1            1           3m    phoenix-container   owncloud/phoenix   app=phoenix
revad-deployment     1/1     1            1           3m    revad-container     cs3org/revad       app=revad
```

### Forward ports

In order to access the Phoenix web UI from your browser, you'll need to create some port-forwards to the services runninng in the cluster:

```bash
kubectl port-forward svc/revad-svc 20080:20080 & \
kubectl port-forward svc/phoenix-svc 8300:8300 &
```

Now just browse to [`http://localhost:8300`](http://localhost:8300) to verify everything is up and running. Try to log in by using username `einstein` and password `relativity`.

### Cleanup

Once done testing, you can quickly tear down the deployment by running:

```bash
helm delete reva-phoenix
# If you want to remove the local cluster as well:
minikube delete
```

