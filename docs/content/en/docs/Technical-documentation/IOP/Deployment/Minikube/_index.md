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

### Launch minikube and configure the helm-tiller add-on

```bash
minikube start
minikube addons enable helm-tiller
```

> **Note:** If it is the first time you're running minikube, take a look to the [different VM drivers](https://kubernetes.io/docs/setup/learning-environment/minikube/#specifying-the-vm-driver) available for the `--driver' flag to pick the best one for your system.

### Deploy REVA and Phoenix using helm charts

You can find more details on how this example works in the [REVA tutorials](https://reva.link/docs/tutorials/phoenix-tutorial/). For the time being, you can install an example chart for ownCloud Phoenix by cloning this repo and running:

```bash
git clone https://github.com/sciencemesh/sciencemesh.git

helm install phoenix sciencemesh/k8s/phoenix
```

Once this is done, you can install and plug-in the IOP by fetching the sciencemesh charts from the [official helm repo](https://sciencemesh.github.io/charts/) and overriding some `revad` settings:

```bash
helm repo add sciencemesh https://sciencemesh.github.io/charts/

helm install iop \
  --set revad.workingDir=/go/src/github/cs3org/reva/examples/oc-phoenix \
  --set revad.args="{-dev-dir,.}" \
  sciencemesh/iop
```

Afterwards, you can verify the deployments and their readyness by running:

```bash
kubectl get deployments -o wide
NAME                 READY   UP-TO-DATE   AVAILABLE   AGE     CONTAINERS          IMAGES                SELECTOR
iop-revad            1/1     1            1           50s     revad               cs3org/revad:v0.1.0   app.kubernetes.io/instance=iop,app.kubernetes.io/name=revad
phoenix-deployment   1/1     1            1           57s     phoenix-container   owncloud/phoenix      app=phoenix
```

### Forward ports

In order to access the Phoenix web UI from your browser, you'll need to create some port-forwards to the services runninng in the cluster:

```bash
kubectl port-forward svc/iop-revad 20080:20080 & \
kubectl port-forward svc/phoenix-svc 8300:8300 &
```

Now just browse to [`http://localhost:8300`](http://localhost:8300) to verify everything is up and running. Try to log in by using username `einstein` and password `relativity`.

### Cleanup

Once done testing, you can quickly tear down the deployment by running:

```bash
helm delete iop
helm delete phoenix
# If you want to remove the local cluster as well:
minikube delete
```

