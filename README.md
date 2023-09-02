# How to Deploy Laravel 10 to Cloud Run GCP using Cloud Build

## App/Project Configuration
### Folder Structure
```
kena-mental-project/
├─ docker/
│  ├─ nginx.conf
│  ├─ startup.sh
├─ src/
│  ├─ your laravel project.../
│  ├─ ...
├─ cloudbuild.yaml
├─ Dockerfile
```

### Dockerfile Setup
You can copy from my setup. Note the Dockerfile, nginx.conf and startup.sh from the docker folder. Or you can get it [here](https://github.com/harshalone/deploy-laravel-9-on-google-cloud-run). Customize with your project setup!

## Cloud Build Configuration
### cloudbuild.yaml Setup
You can copy from my [cloudbuil.yaml](https://github.com/dhillen-bp/kena-mental-deploy/blob/main/cloudbuild.yaml) setup and Customize with your project setup!.

## Deploy with Cloud Build
### Create Trigger
1. Open the [GCP Console](https://console.cloud.google.com) and go to Cloud Build
2. Create a Trigger and fill it like [my example](https://ibb.co/V9M3byb) or according to your setup.
3. Before pressing the Create button, add Substitution variables like [this](https://ibb.co/yFTPgST). Or you can add other variables you need.
4. Additionally, for service-account-key.json you need to convert it to string as _SERVICE_ACCOUNT_KEY value. You can change it at [here](https://jsonformatter.org/json-stringify-online) or another Json Stringify.
5. After that, run the trigger on the button. And go to History to see if the trigger was successfully executed like [this](https://ibb.co/mvjnjKg).

### Check Cloud Run
1. Open Cloud Run and check the services that have been deployed with Cloud Build.
2. You will get the url of the Laravel application that you have deployed. You can also check your logs and services details like [this](https://ibb.co/dgrkn9W).

#### Learning References
- https://alemsbaja.hashnode.dev/how-to-deploy-a-laravel-application-on-google-cloud-run-using-cloud-build-with-build-command-continuous-integration-and-deployment
- https://www.youtube.com/watch?v=00UqiF4hqNw