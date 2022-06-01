# Skills Service Provider(SSP) API Service

## Following 3 branches are protected and among them develop branch is the default branch:
```shell
master
staging
develop
```

```master``` branch is only being used for production release.
Code of master branch has not been used. We are using container image from staging branch to deploy to production.
Common configurations should be placed in the ```.env.example``` file.

## Development Environment
Sensitive information like different type of credentials for development environment have to be configured in the file named ```deploy/values.dev.yaml```.

In order to release in the development environment, we have to switch to ```develop``` branch and after that we have to increase the value of the property ```devBuildImageVersion``` in ```deploy/version.yaml```.

After that we have to write a commit message ```RELEASE = 'php artisan migrate'``` if we want to do any database migration after the release. But, if we don't
want to run any command after the release then the commit message will be ```RELEASE = 'php artisan list'```.

## Staging Environment
Sensitive information like different type of credentials for staging environment have to be configured in the file named ```deploy/values.staging.yaml```.

In order to release in the staging environment, we have to switch to ```staging``` branch and after that  we have to increase the value of the property ```stagingBuildImageVersion``` in ```deploy/version.yaml```.

After that we have to write a commit message ```RELEASE = 'php artisan migrate'``` if we want to do any database migration after the release. But, if we don't
want to run any command after the release then the commit message will be ```RELEASE = 'php artisan list'```.

## Production Environment
Sensitive information like different type of credentials for production environment have to be configured in the file named ```deploy/values.production.yaml```.

In order to release in the production environment, we have to switch to ```master``` branch and after that  we have to set successful version of the staging environment value of ```stagingBuildImageVersion``` and
make a git push. This will successfully release the desired version of staging environment container image to production environment.

## Initial Setup

```shell
git clone git_project_url
cd project_folder
```

#### Please update to your machine with php version >= 8.0, also uncomment into php.ini file extension=shmop, extension=php_sockets.dll,extension=soap if already up to date please ignore this configuration.

```shell
composer install // to update and install all require pugin
```

#### .env file updated for credentials 

```shell
composer require flipbox/lumen-generator  // to active artisan cli run this command

php artisan migrate:fresh --seed  // to migrate and seed data into database

php artisan serve //default port wise to start this project run this command

php -S localhost:8005 -t public //custom port wise to start this project run this command
```



After deploying at the first time, seeder files have to be run by running the following command:

```php artisan db:seed```

#### RabbitMQ consume command. This following command has to be run in the background as a daemon service.
```shell
php artisan queue:work --queue=institute.course.enrollment.q,institute.batch.calender.q,institute.db.sync.q
```




# Licensing & Copyright

Copyright 2022 @a2i, Bangladesh

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.






