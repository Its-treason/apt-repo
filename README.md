# Apt-Repo

**WIP**! Api for creating an Apt-Repository written in PHP.

## Currently working

- Uploading deb Packages using the Web-Ui(`/ui/upload`)
- Adding the Repo to a machine running Debian and installing packages
- Everything being using gpg-keys

## TODO

- Login / Auth for Uploading packages
- User friendly package List
- "File"-Explorer to search through the /dist or /pool folder
- Being able to Delete Packages
- Better pgp private key handling
- More options for storing packages like local Filesystem or S3
- Unittests
- Documentation
- Multiple suites / components

## Tests

### PHPUnit

```
docker-compose -f docker-compose.development.yml run --rm --entrypoint "php vendor/bin/phpunit" php-fpm
```

### PHPCS

```
docker-compose -f docker-compose.development.yml run --rm --entrypoint "php vendor/bin/phpcs" php-fpm
```

#### PHPCBF

```
docker-compose -f docker-compose.development.yml run --rm --entrypoint "php vendor/bin/phpcbf" php-fpm
```
