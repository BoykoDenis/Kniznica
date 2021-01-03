# Kniznica

## Back-end

## Front-end

## Docker

### Windows Notes

This cluster tested on Windows WSL2 (Ubuntu) + Docker 3.0 environment.

Please follow the instruction at https://docs.docker.com/docker-for-windows/wsl.

### Developing environment

The docker-compose.dev.yml file overrides the standard docker compose
settings (in docker-compose.dev.yml) with development environment parameters

Run docker compose with the following parameters to build container cluster in development mode.

```bash
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```
#### Back-end

Docker mount application directory to .docker/backend/src directory.
So you can modify files in this directory - the changes will appear
in docker container immediately.

#### Database

MySQL will store DB files in directory .docker/mysql.dev/storage.
Connect to DB using the following credentials
- host: 127.0.0.1
- db name: see DB_DATABASE var in .env file
- db user: see DB_USERNAME var in .env file
- password: see DB_PASSWORD var in .env file

Because DB files stored outside of container any container action (delete, rebuild)
will not affect on DB content.

#### Logs

In development mode backend stores logs to the following directories

- Apache: .docker/backend/log/apache2
- PHP: .docker/backend/log/php


### Production

Run docker compose with the following parameters to build container cluster in production mode.

```bash
docker-compose up -d
```
