# Apt-Repo

Virtual Apt-Repository written in PHP

## Features

- Uploading Debian-Packages using the Web-Ui (`/ui/upload`)
- Everything gpg-keys secured
- Multiple Suites / Codenames
- Package list

## TODO

- S3 Support
- Documentation

## Development

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
