# Social Media Awards

Social Media Awards is a classic PHP web application used to manage a social-media awards platform with public browsing, candidate applications, voting flows, results pages, and administration screens.

The current repository also includes:
- a Docker-based local development stack
- a MySQL schema bootstrap
- Composer-based PSR-4 autoloading and PHPUnit test suites
- a public front controller for MVC-style extensionless routes

## Current Scope

Main functional areas:
- public pages served through public/index.php with routes such as /categories, /nominees, /results, /about, /contact and /inscription
- candidate space under views/candidate/
- voter space under views/user/
- administration pages under views/admin/
- service, model and controller layers under app/

Technical characteristics:
- PHP 8.3-8.5 application without a full-stack framework
- MySQL database with procedures, triggers and seed data in database/schema.sql
- front-end assets in assets/css, assets/js and assets/images
- Docker Compose stack with Apache, MySQL and phpMyAdmin
- Composer PSR-4 autoloading for App\ and Tests\ namespaces

## Repository Structure

```text
.
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── Services/
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
├── config/
│   ├── .env
│   ├── .env.exemple
│   ├── database.php
│   ├── Env.php
│   ├── permissions.php
│   ├── routes.php
│   ├── paths.php
│   ├── upload.php
│   └── session.php
├── database/
│   └── schema.sql
├── docker/
│   ├── apache/
│   └── mysql/
├── public/
│   ├── index.php
│   └── uploads/
├── views/
│   ├── admin/
│   ├── candidate/
│   ├── partials/
│   ├── public/
│   ├── user/
│   └── login.php
├── tests/
├── composer.json
├── composer.lock
├── Dockerfile
├── docker-compose.yml
├── phpunit.xml
├── index.php
└── README.md
```

## Recommended Local Run

Docker is the standard local execution path for this project.

### Start The Stack

First start or rebuild after Docker changes:

```bash
docker compose up -d --build
```

Regular start:

```bash
docker compose up -d
```

### Stop The Stack

```bash
docker compose down
```

### Services

- Web application: http://localhost:8080
- MySQL: localhost:3307
- phpMyAdmin: http://localhost:8081

### Docker Database Credentials

- Database: social_media_awards
- User: sma_user
- Password: sma_password
- Root user: root
- Root password: root

The database container uses a persistent Docker volume named mysql_data.

## Database Bootstrap

The canonical SQL schema is stored in database/schema.sql.

In Docker, the MySQL service is initialized through:
- database/schema.sql
- docker/mysql/init/10-load-schema.sh

phpMyAdmin is connected to the Docker MySQL service and can be used for local inspection and administration.

## Environment Configuration

For local non-Docker usage:

1. Copy config/.env.exemple to config/.env
2. Fill in the required values

Main variables:
- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASS
- DB_CHARSET

Important behavior:
- config/Env.php prefers runtime environment variables over values from config/.env
- this means Docker-injected variables override the local file inside the container

## Development Notes

- User-facing text displayed on web pages must remain in French.
- Non-user-facing technical text such as comments, docblocks, technical logs and documentation can be in English.
- Public uploads are stored under public/uploads/.

## Useful Checks

Focused PHP syntax check:

```bash
php -l path/to/file.php
```

Validate the Docker Compose configuration:

```bash
docker compose config
```

Regenerate the optimized Composer autoloader:

```bash
composer dump-autoload -o
```

Generate minified CSS and JavaScript assets:

```bash
composer assets:minify
```

Run the automated tests:

```bash
composer test
```

Show running containers:

```bash
docker compose ps
```
