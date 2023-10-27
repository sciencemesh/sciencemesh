---
title: "Reva Deployment into Kubernetes"
linkTitle: "Reva Deployment into Kubernetes"
weight: 16
description: >
  Deploy the Reva/IOP on your Kubernetes cluster.
---


The official way of deploying the IOP platform are its [Helm charts](https://sciencemesh.github.io/charts/). They provide an abstraction over the different Kubernetes entities required to configure and run the IOP on your cluster.

## Prerequisites

- You must add the `sciencemesh` helm repo to your client sources:

<div class="artifacthub-widget" data-url="https://artifacthub.io/packages/helm/sciencemesh/iop" data-theme="light" data-header="false" data-responsive="true"><blockquote><p lang="en" dir="ltr"><b>iop</b>: ScienceMesh IOP is the reference Federated Scientific Mesh platform</p>&mdash; Open in <a href="https://artifacthub.io/packages/helm/sciencemesh/iop">Artifact Hub</a></blockquote></div><script async src="https://artifacthub.io/artifacthub-widget.js"></script>

```bash
helm repo add sciencemesh https://sciencemesh.github.io/charts/
```

- We'll be deploying a reva daemon using the [example config](https://github.com/cs3org/reva/blob/master/examples/sciencemesh/sciencemesh.toml). **We need to tweak a few values to appropriate ones according to your deployment**.

```bash
wget -q https://github.com/cs3org/reva/blob/master/examples/sciencemesh/sciencemesh.toml

```
1. In the example config you need to edit the section denoted as **[vars]**. Replace `your.revad.org` with your actual domain. 
2. If you have a Kubernetes deployment with an ingress and a route:
  - Set the `external_reva_endpoint` var to your actual externally-visible route to reva.
  - In the `[http.services.ocmprovider]` section, set a `webdav_root` to include your route, e.g. `/iop/remote.php/dav/ocm/`
3. Replace `your.efss.org` with the actual endpoint of your EFSS system.
4. Define appropriate secrets in the [vars] section: the `efss_shared_secret` must match the `oc_appconfig.configvalue` in your EFSS DB for `oc_appconfig.app_id` = `sciencemesh`
5. Provide appropriate SSL full chain certificate and key files in the [http] section

If you want to terminate the SSL connection to reva at your reverse proxy system (e.g. at your Kubernetes ingress), then you can configure reva to use http instead. For that, you need to follow these steps:
1. Remove the `certfile` and `keyfile` entries from the [http] section.
1. Replace the https port `443` with a port number of your choice everywhere you find it
1. Make sure all `https`-served endpoints (including `datagateway`) are adapted accordingly

## Configure and deploy IOP
To deploy IOP via `helm` we need to configure couple more things.

1. Metrics, for further detail please visit [Metrics page](https://developer.sciencemesh.io/docs/technical-documentation/monitoring/accounting-metrics/#metrics-figures-file-format-for-json-driver)

We have to prepare the file named `metrics.json`. Here we demonstrate a statically prepared file containing elementary metrics needed for Sciencemesh monitoring.
```
{
    "cs3_org_sciencemesh_site_total_num_users": 15222,
    "cs3_org_sciencemesh_site_total_num_groups": 100,
    "cs3_org_sciencemesh_site_total_amount_storage": 386887921664
}
```
2. Persitency of the operational data (accepted invites etc.).

We have to setup PVC into `values.yaml` as follows. Details to setup PVC are described in the [following section](#enabling-and-configuring-persistency).
```
---
gateway:
    persistentVolume:
           enabled: true
           existingClaim: 'data-iop-prod'
```
Then we can simply run the following command to deploy our IOP.
```
helm -n <your-namespace> install --set-file gateway.configFiles.revad\\.toml=sciencemesh.toml iop sciencemesh/iop --set gateway.image.repository=cs3org/revad --set gateway.image.tag=v1.26.0 -f values.yaml --set-file gateway.configFiles.metrics\\.json=metrics.json
```
If you perform any changes then you can upgrade your deployment using `helm upgrade` command.
```
helm -n cs3 <your-namespace> upgrade --set-file gateway.configFiles.revad\\.toml=sciencemesh.toml iop sciencemesh/iop --set gateway.image.repository=cs3org/revad --set gateway.image.tag=v1.26.0 -f values.yaml --set-file gateway.configFiles.metrics\\.json=metrics.json
```


## Enabling and configuring persistency

In case you need to keep the data stored on the `storage` service `root`, across version upgrades and restarts of an IOP deployment, you will need to enable data persistency through a [Kubernetes Persistent Volume](https://kubernetes.io/docs/concepts/storage/persistent-volumes) (PV). This is done by using a Persistent Volume Claim (PVC). By default, persistency is disabled for convenience as it involves setting up a `StorageClass`, having an available [driver](https://kubernetes.io/docs/concepts/storage/persistent-volumes/#types-of-persistent-volumes) for your storage infrastructure, etc.

The [`cs3org/revad` chart](https://github.com/cs3org/charts/tree/master/revad) provides two methods to attach a volume to an IOP deployment:

### Persistent Volume Claim auto-provision

When `persistentVolume.enabled=true` alone is passed, helm generates and installs PVC manifest by relying on some cluster and chart preset defaults. This option is especially useful to quickly deploy the IOP for the first time, without spending too much effort in the storage configurations.

For a full reference on the different `persistentVolume` configurations available, refer to the [chart parameters list](https://github.com/cs3org/charts/tree/master/revad#configuration).

### Reusing a pre-existing Persistent Volume Claim

This option is key when rolling an upgrade in the cluster while keeping all the data from a previous version. Here's a really simple PVC manifest and the workflow to create and consume it from the charts.

```bash
cat << EOF > pvc.yaml
kind: PersistentVolumeClaim
apiVersion: v1
metadata:
  name: data-iop-prod
spec:
  accessModes:
    - ReadWriteMany
  resources:
    requests:
      storage: 1G
  storageClassName: standard
EOF

kubectl apply -f pvc.yaml

# Note the 'Unbound' status for the PVC as there's still no deployment exercising the claim
kubectl get pvc
NAME                 STATUS   VOLUME                                     CAPACITY   ACCESS MODES   STORAGECLASS   AGE
data-iop-prod             Unbound  pvc-fddca20b-69a4-43ec-ad12-6d4e2bd4a433   1Gi        RWO            standard       1d20h

helm upgrade -i iop sciencemeshcharts/iop \
  --set gateway.persistentVolume.enabled=true \
  --set gateway.persistentVolume.existingClaim=data-iop-prod

# Get the PV provisioned by the claim
kubectl get pv
NAME                                       CAPACITY   ACCESS MODES   RECLAIM POLICY   STATUS   CLAIM                        STORAGECLASS   REASON   AGE
pvc-fddca20b-69a4-43ec-ad12-6d4e2bd4a433   1Gi        RWO            Delete           Bound    default/iop-data             standard                1d20h
```

If the PVC was auto-provisioned by a previous release, you'll need to pass its name (i.e. `<release-name>-gateway`) as `persistentVolume.existingClaim`, as part of the `helm upgrade` command.


## Configuring IOP ingress

To configure the two ingress resources that expose the IOP endpoints (GRPC and HTTP), we'll just need to pass a few values into a `custom-ingress.yaml` file. For instance, a configuration for a cluster running the [nginx-ingress controller](https://kubernetes.github.io/ingress-nginx/) would be similar to:

```bash
cat << EOF > custom-ingress.yaml
gateway:
  ingress:
    enabled: true
    services:
      grpc:
        hostname: <hostname>
        path: /
        annotations:
          kubernetes.io/ingress.class: nginx
          nginx.ingress.kubernetes.io/ssl-redirect: "true"
          nginx.ingress.kubernetes.io/backend-protocol: "GRPC"
        tls:
          - secretName: <keypair>
            hosts:
              - <hostname>
      http:
        hostname: <hostname>
        path: /iop(/|$)(.*)
        annotations:
          kubernetes.io/ingress.class: nginx
          nginx.ingress.kubernetes.io/ssl-redirect: "true"
          nginx.ingress.kubernetes.io/use-regex: "true"
          nginx.ingress.kubernetes.io/rewrite-target: /$2
          nginx.ingress.kubernetes.io/proxy-body-size: 200m
        tls:
          - secretName: <keypair>
            hosts:
              - <hostname>
EOF
```

- `<hostname>` is the domain you'll use to expose the IOP publicly.
- (Optional) The `<keypair>` is a Kubernetes tls secret created from the `.key` and `.crt` files. It can be omitted together with both `tls` sections to expose the services without TLS-termination. Also note that the secret must be present in the cluster before deploying the IOP:

```bash
kubectl create secret tls <keypair> --key=tls.key --cert=tls.crt
```

Once all this is done, we can carry on with the deployment by running:

```bash
helm upgrade -i iop sciencemesh/iop \
  --set-file gateway.configFiles.revad\\.toml=standalone.toml \
  --set-file gateway.configFiles.users\\.json=users-cern.json \
  --set-file gateway.configFiles.ocm-providers\\.json=providers.demo.json \
  -f custom-ingress.yaml
```

## Testing the deployment

**FIXME: to be moved into configuration**

You can easily test your deployment is reachable outside the cluster by running the `reva` cli and `curl` against the exposed services:

```bash
# Configure the REVA cli client to connect to your GRPC service:
reva configure
host: <hostname>:443
config saved in /.reva.config

# Log-in using any of the users provided in gateway.configFiles.users.json
reva login -list
Available login methods:
- basic

reva login basic
username: ishank
password: ishankpass
OK

# HTTP: Query the Prometheus metrics endpoint:
curl https://<hostname>/iop/metrics
```

## Configuration

After deployment, continue by [configuring Reva]({{<ref "docs/Technical-documentation/IOP/Configuration" >}}).


