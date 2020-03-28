###This tutorial shows how to deploy Reva and Phoenix on a local Kubernetes cluster as a helm chart.

####1. Install *kubectl* and *minikube*
````
https://kubernetes.io/docs/tasks/tools/install-kubectl/
https://kubernetes.io/docs/tasks/tools/install-minikube/
````
####2. Launch *minikube*
````
minikube start
````
####3. Enable *helm*
````
minikube addons enable helm-tiller
````
####4. Switch to your minikube's docker*
````
eval $(minikube docker-env) 
````
In case of using Windows:
````
minikube docker-env
````
####5. Build images
````
cd sciencemesh/k8s
docker build -t revad -f Dockerfile.revad .
docker build -t phoenix -f Dockerfile.phoenix .
````
####6. Run *helm* chart
````
helm install reva-phoenix ./reva-phoenix
````
####7. Forward ports
````
kubectl port-forward svc/revad-svc 20080:20080 & kubectl port-forward svc/phoenix-svc 8300:8300 &
````
####7. Access *phoenix* login page in browser at localhost:8300