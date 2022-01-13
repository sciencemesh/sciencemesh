---
title: "Kubernetes Deployment"
linkTitle: "Kubernetes"
weight: 16
description: >
  Deploy and configure the IOP on your cluster.
---

## Prerequisites

- You must add the `sciencemesh` helm repo to your client sources:

<div class="artifacthub-widget" data-url="https://artifacthub.io/packages/helm/sciencemesh/iop" data-theme="light" data-header="false" data-responsive="true"><blockquote><p lang="en" dir="ltr"><b>iop</b>: ScienceMesh IOP is the reference Federated Scientific Mesh platform</p>&mdash; Open in <a href="https://artifacthub.io/packages/helm/sciencemesh/iop">Artifact Hub</a></blockquote></div><script async src="https://artifacthub.io/artifacthub-widget.js"></script>

```bash
helm repo add sciencemesh https://sciencemesh.github.io/charts/
```

- We'll be deploying a reva daemon using the [standalone example config](https://github.com/cs3org/reva/blob/master/examples/standalone/standalone.toml). We just need to tweak a couple of keys in this config for reva to use the right values:

```bash
wget -q https://raw.githubusercontent.com/cs3org/reva/master/examples/standalone/standalone.toml

# Example edits for the CERN deployment:
sed -i '/^\[grpc.services.gateway\]/a datagateway = "https://sciencemesh.cernbox.cern.ch/iop/datagateway"' standalone.toml
sed -i '/^\[grpc.services.storageprovider\]/a data_server_url = "https://sciencemesh.cernbox.cern.ch/iop/data"' standalone.toml
```

| key                                                                                                                             | value                                                          |
|---------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------|
| [`grpc.services.gateway.datagateway`](https://reva.link/docs/config/grpc/services/gateway/#datagateway)                         | Set to our externally-accessible Data Gateway (`/datagateway`) |
| [`grpc.services.storageprovider.data_server_url`](https://reva.link/docs/config/grpc/services/storageprovider/#data_server_url) | Points to the external endpoint for the Data Server (`/data`)  |

- All the mesh providers and some dummy users per-provider have been specified in the [ocm-partners example](https://github.com/cs3org/reva/tree/master/examples/ocm-partners). We can fetch these files to later pass them to helm by running:

```bash
wget -q https://raw.githubusercontent.com/cs3org/reva/master/examples/ocm-partners/providers.demo.json
# Get the CERN users, for instance:
wget -q https://raw.githubusercontent.com/cs3org/reva/master/examples/ocm-partners/users-cern.json
```

- To simplify things, we will rely on a pre-deployed [nginx-ingress](https://kubernetes.github.io/ingress-nginx/deploy/) controller. The `nginx.ingress.kubernetes.io/backend-protocol: "GRPC"` annotation can be supplied to expose GRPC services in a very easy way.

## Configuring an IOP deployment

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
  name: iop-data
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
iop-data             Unbound  pvc-fddca20b-69a4-43ec-ad12-6d4e2bd4a433   1Gi        RWO            standard       1d20h

helm upgrade -i iop sciencemeshcharts/iop \
  --set gateway.persistentVolume.enabled=true \
  --set gateway.persistentVolume.existingClaim=iop-data

# Get the PV provisioned by the claim
kubectl get pv
NAME                                       CAPACITY   ACCESS MODES   RECLAIM POLICY   STATUS   CLAIM                        STORAGECLASS   REASON   AGE
pvc-fddca20b-69a4-43ec-ad12-6d4e2bd4a433   1Gi        RWO            Delete           Bound    default/iop-data             standard                1d20h
```

If the PVC was auto-provisioned by a previous release, you'll need to pass its name (i.e. `<release-name>-gateway`) as `persistentVolume.existingClaim`, as part of the `helm upgrade` command.

## Next Steps

Once you have your deployment up and running, consider one of these tutorials:
