---
title: "ownCloud"
linkTitle: "ownCloud"
weight: 31
description: >
    How to join the ScienceMesh if you're using ownCloud
---


## Install the ScienceMesh app

Go to the ownCloud server apps/ directory (or some other directory used):

```
cd apps/
```

Get the ownCloud ScienceMesh integration app.

You can download the latest version from the application [release page on GitHub](https://github.com/sciencemesh/oc-sciencemesh/releases). Note that you need at least version 1.1.0 of the application.

```
wget https://github.com/sciencemesh/oc-sciencemesh/archive/v1.1.0.tar.gz
tar -xzf v1.1.0.tar.gz
mv oc-sciencemesh sciencemesh
chown -R www-data:www-data sciencemesh
```

In ownCloud open the `~/settings/apps/disabled` page with *Not enabled apps* by an administrator and click `Enable` for the ScienceMesh application.

{{< imgproc oc-settings.png Fit "1024x1024" >}}
ownCloud App settings menu
{{< /imgproc >}}

Once the app is enabled, you can go to `Settings>Additional` and fill the information presented:

{{< imgproc oc-configuration.png Fit "1024x1024" >}}
ScienceMesh application configuration
{{< /imgproc >}}

To register with the ScienceMesh, you will also need an _API key_. First, create a free ScienceMesh account using [this link](https://sciencemesh-test.uni-muenster.de/api/siteacc/register). Once your registration has been approved, you will receive your unique API key via email which you can then enter in the application settings.

After fillling the details of your instance, you can check the internal metric endpoint by accessing the following url:

```
https://<your_instance>/index.php/apps/sciencemesh/internal_metrics
```

{{< imgproc oc-internal-metrics.png Fit "1024x1024" >}}
Internal metrics endpoint
{{< /imgproc >}}


## Install the IOP

You need to download the latest available version of the *revad* binary in the [Github releases page](https://github.com/cs3org/reva/releases).

You need to create a configuration file for the IOP: **metrics.toml**

```
[http]
address = "0.0.0.0:5550"

[http.services.metrics]
metrics_data_driver_type = "xcloud"
metrics_record_interval = 5000
xcloud_instance="http://localhost"
xcloud_pull_interval=60

[http.services.prometheus]
```

You need to put the *xcloud_instance* variable to the url of the ownCloud instance.

**Note: there may be a warning about app integrity. This is due the app not been signed yet.**

Run the IOP with the configuration file:
```
revad -c metrics.toml
```


## Verify that you are connected to the ScienceMesh
Once the IOP and the ScienceMesh are correctly configured, 
you can access the following metrics endpoint:

```
https://<your_instance>/index.php/apps/sciencemesh/metrics
```


{{< imgproc oc-prometheus.png Fit "1024x1024" >}}
Prometheus endpoint working
{{< /imgproc >}}

When your site is authorized in the ScienceMesh, it will appear on the [ScienceMesh map](https://sciencemesh-test.uni-muenster.de/grafana/d/HD3NmHMMk/general-statistics?orgId=1&refresh=30s):
{{< imgproc oc-map.png Fit "1024x1024" >}}
ScienceMesh map
{{< /imgproc >}}
