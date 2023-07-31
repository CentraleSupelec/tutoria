# UPS Tutorat

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
Make sure the database name and ports are accurate in the `DATABASE_URL`. Otherwise, change the variable accordingly to the database definition in `docker-compose.yml`.
```
DATABASE_URL="postgresql://<POSTGRES_USER>:<POSTGRES_PASSWORD>@postgres-test:<PORT>/<POSTGRES_DB>?serverVersion=15&charset=utf8"
```

### Copy php fixer into a local file for customisation needs (optional)

```
cp -f .php-cs-fixer.dist.php .php-cs-fixer.php
```

### Map localhost to a hostname accepted by CS

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

## Other useful commands

* Run the php fixer
```
docker-compose exec -it php vendor/bin/php-cs-fixer fix -v --dry-run
```

* Run rector
```
docker-compose exec -it php vendor/bin/rector process --dry-run
```

* Execute migrations:
```
docker-compose exec -it php bin/console app:sync-migrate
```

* Create an administrator:
```
docker-compose exec php symfony console app:create-admin
```
