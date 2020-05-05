# GOCDB Docker Container
Docker files to provide a containerized version of GOCDB (https://wiki.egi.eu/wiki/GOCDB/Documentation_Index) for the **CS3MESH4EOSC** project.

## Running the containers
Simply build and run the containers via `docker-compose`:

    docker-compose build && docker-compose up -d

This will launch two containers, one for the database and one for the webserver.

### Running in Kubernetes
Simple Kubernetes configuration files are provided in `k8s`. Note that the deployments are configured to never pull images, so the containers have to be built beforehand and made available to Kubernetes; the images have to be named as follows:

- Database: `gocdb-database`
- Webserver: `gocdb-webserver`

Also note that the provided files are only _exemplary_; it is most likely that you will need to modify them according to your needs and cluster configuration.

The provided Minikube deployment instructions (see below) also offers a good starting point to run GOCDB in Kubernetes.

## Configuration
If running the containers via _docker-compose_, no extra configuration is needed; if you need to adjust the webserver configuration, modify the corresponding files in `webserver/config` and rebuild.

If running the containers manually or in Kubernetes, it is necessary to set the environment variable `GOCDB_DATABASE_SERVER` for the GOCDB webserver container to point to the GOCDB database host.

## Minikube deployment

This section describes deploying the GOCDB using [Minikube](https://kubernetes.io/docs/tasks/tools/install-minikube/). The provided k8s files (located in the `k8s` directory) will be used for this and should work out-of-the-box. Note that while running the GOCDB containers in a real Kubernetes cluster will usually require some changes made to the provided files, the given instructions should nonetheless offer a good entry point to get you started.

### Prerequisites

- [Docker](https://www.docker.io)
- [Minikube](https://kubernetes.io/docs/tasks/tools/install-minikube/)
- [Kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl/) (This is actually not needed as a separate installation, as Minikube comes with its own version which we will use; if you intend to deploy GOCDB on a real Kubernetes cluster, though, Kubectl _is_ required.)

To start your Minikube cluster, simply issue the following command:

    minikube start

Wait until the cluster has been created.

### 1. Clone the ScienceMesh repository

The first thing to do is to clone this repository:

    git clone https://github.com/sciencemesh/sciencemesh.git

Now, change to the GOCDB container directory:

    cd sciencemesh/gocdb-container

### 2. Prepare your shell environment

When working with Minikube and Docker containers, you need to let your shell know about Minikube's Docker daemon by issueing the following command:

    eval $(minikube -p minikube docker-env)

When now using the `docker` command, it will use the correct daemon; note that this doesn't require to be run as root (unlike the regular Docker daemon).

### 3. Build the GOCDB containers

If you started your Minikube cluster and prepared your shell to use the Minikube Docker daemon, you can now build the two GOCDB containers in the usual manner:

    docker build -t gocdb-database ./database

    docker build -t gocdb-webserver ./webserver

It is important to name the containers correctly as stated above; otherwise, Kubernetes will not be able to find and use them.

### 4. Deploy GOCDB

Deploying GOCDB in your Minikube cluster is achieved by a simple one-liner:

    minikube kubectl -- create -f ./k8s

This will create a persistent volume claim (to store the database files), two deployments and two services. A good way to check the status of your Minikube cluster is to use its built-in dashboard:

    minikube dashboard

This will launch your web browser and open a dashboard where you can check the status of all entities in your cluster.

### 5. Forward ports

To make the web interface reachable, the last step is to forward the webserver container's port:

    minikube kubectl -- port-forward --address 0.0.0.0 svc/gocdb-webserver 8080:80

This will make the GOCDB available at port 8080 of your system as long as the command is running. Now simply open http://localhost:8080 in your web browser to open the GOCDB web interface. **Enjoy!**

### Cleanup

If you want to remove GOCDB from your Minikube cluster, use the following command:

    minikube kubectl -- delete -f ./k8s

To completely remove the entire Minikube cluster, use:

    minikube delete

This will also remove the previously built Docker images.

## Usage
GOCDB offers a comfortable web frontend to manage the topology of a mesh; it also offers various REST API endpoints to query and modify the topology data.

- The GOCDB frontend can be reached at: [/gocdb](http://localhost/gocdb)
- The public API can be reached at: [/gocdbpi/public](http://localhost/gocdbpi/public)
- The private API can be reached at: [/gocdbpi/private](http://localhost/gocdbpi/private)

For more details about GOCDB, visit the official documentation [here](https://wiki.egi.eu/wiki/GOCDB/Documentation_Index).

## Notes
To make setting up and working with the GOCDB easy, user authentication was removed. This renders some features unusable, like applying user roles, or using protected API methods.

## Contact
The provided container is for testing purposes only. It is neither efficient nor secure. Not every detail of GOCDB was tested. If you encounter any problems, feel free to contact me at [daniel.mueller@uni-muenster.de](mailto:daniel.mueller@uni-muenster.de).
