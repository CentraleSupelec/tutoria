# TutorIA

TutorIA is an open-source project that lets you create and manage tutoring slots, and allows students to sign up for them.

<p align="center">

[![Go to the french presentation](https://img.shields.io/badge/Go%20to%20the%20french%20presentation-961a3d?logo=readthedocs&link=https%3A%2F%2Fcentralesupelec.github.io%2Ftutoria%2F)](https://centralesupelec.github.io/tutoria/)

[![Picture](docs/assets/etudiants.png)](https://centralesupelec.github.io/tutoria/)

</p>

## Requirements

* Git
* Docker / docker-compose

## Local installation

### Copy environments file into local ones

* Environment variables
```
cp -f .env .env.local
```

* Environment variables for testing
```
cp -f .env.test .env.test.local
```

Change LTI_KEYS_BASE_FOLDER_PATH to '/app' in your .env.local and .env.test.local

Make sure the database name and ports are accurate in the `DATABASE_URL`. Otherwise, change the variable accordingly to the database definition in `docker-compose.yml`.
```
DATABASE_URL="postgresql://<POSTGRES_USER>:<POSTGRES_PASSWORD>@postgres-test:<PORT>/<POSTGRES_DB>?serverVersion=15&charset=utf8"
```

### Copy php fixer into a local file for customisation needs (optional)

```
cp -f .php-cs-fixer.dist.php .php-cs-fixer.php
```

### Map localhost to a hostname accepted by your CAS

```
echo "127.0.0.1 tutorat-local.paris-saclay.fr" | sudo tee -a /etc/hosts
```

### Add a pre-commit logic (optional)

Run this command to execute `pre-commit.sh` each time a commit is created:
```
ln -s ../../bin/pre-commit.sh .git/hooks/pre-commit
```

### Run the containers

```
docker-compose up --build -d
```

### Install bundles

```
docker-compose exec php composer install
```

### Update the testing database

The previous command will automatically run the not already executed migrations. But the testing database needs to be updated accordingly. To do so run this command:
```
docker-compose exec -it php sh tests/init-test-database.sh
```

## URLs

### Local

* Web server : https://tutorat-local.paris-saclay.fr/
* Admin dashboard : https://tutorat-local.paris-saclay.fr/admin/dashboard
* Profiler : https://tutorat-local.paris-saclay.fr/_profiler/

## Testing

Command to run all tests:
```
docker-compose exec -it php vendor/bin/phpunit
```

To run a given testing directory:
```
docker-compose exec -it php vendor/bin/phpunit tests/<SUBDIRECTORY_NAME>
```

## LTI Integration

Moodle URL :

```
http://localhost:9180/
```

* Create Moodle as a Platform :

    * Audience : http://localhost:9180

    * Oidc Authentication Url : http://localhost:9180/mod/lti/auth.php

* Create an LTI Integration specifying Moodle as a Platform (previously created)

* Important Intergration parameters :

    * Client ID : from the External Tool parameters created in Moodle

    * Platform Jwks Url : http://moodle:8080/mod/lti/certs.php (Notice that the base URL is different from Moodle URL, because it will be called inside the docker container)

    * Deployment IDs/Default Deplyment ID : from the External Tool parameters created in Moodle

Add Tutorat as an External Tool in Moodle:

* Go to tool configuration panel :

```
http://localhost:9180/mod/lti/toolconfigure.php
```

* Click on "configure a tool manually"

    * Tool Name : Choose your tool name

    * Tool URL : https://tutorat-local.paris-saclay.fr/lti/launch

    * LTI version : 1.3

    * Public key type : Key Set URL

    * Public keyset : https://localhost.centralesupelec.fr/jwks/tutorIaKeySet.json

    * Initiate login URL : https://tutorat-local.paris-saclay.fr/lti/oidc/initiation

    * Redirection URLs : https://tutorat-local.paris-saclay.fr/lti/launch

    * Default launch container : New Window

    * After creating the External Tool, you can click on the list icon on the created tool to get some other useful information. Example :

```
Platform ID: http://localhost:9180
Client ID: Ezt2pDP3XQit2ZE
Deployment ID: 2
Public keyset URL: http://localhost:9180/mod/lti/certs.php
Access token URL: http://localhost:9180/mod/lti/token.php
Authentication request URL: http://localhost:9180/mod/lti/auth.php
```

* Select an activity or a course in Moodle -> Click on "Add an activity or resource" -> External Tool

    * Preconfigured tool : Select the External Tool that you created

    * You can click on "Show more", then there is "Custom parameters" field where you can send additional information to the External Tool

## Other useful commands

* Run the php fixer
```
docker-compose exec -it php vendor/bin/php-cs-fixer fix -v --dry-run
```

* Run rector
```
docker-compose exec -it php vendor/bin/rector process --dry-run
```

 * Générer une migration :
```
docker-compose exec php symfony console make:migration
```

* Execute migrations:
```
docker-compose exec -it php bin/console app:sync-migrate
```

* Create an administrator:
```
docker-compose exec php symfony console app:create-admin
```

## Documentation

The documentation is available on our [Github project's page](https://centralesupelec.github.io/tutoria).

To edit the documentation, you can serve it localy : `docker run --rm -it -p 8000:8000 -v ${PWD}:/docs squidfunk/mkdocs-material`

The Github's CI will deploy it once merged.
