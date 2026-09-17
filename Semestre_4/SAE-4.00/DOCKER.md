# Docker

This project can now be started with Docker Compose.

## Prerequisites

- Docker Desktop
- Docker Compose

## Startup

```powershell
docker compose up --build
```

Exposed services:

- PHP/Apache application: http://localhost:8080
- MySQL: localhost:3307

Configuration applied inside the containers:

- PHP_VERSION=8.5 (build argument for the Apache/PHP image)
- DB_HOST=db
- DB_PORT=3306
- DB_NAME=social_media_awards
- DB_USER=sma_user
- DB_PASS=sma_password

## Database

On first startup, the MySQL container creates the social_media_awards database and then automatically loads the schema from database/schema.sql.

MySQL data is stored in the Docker volume named mysql_data.

To temporarily build another supported PHP branch, override the build argument:

```powershell
docker compose build --build-arg PHP_VERSION=8.4 web
```

## Shutdown

```powershell
docker compose down
```

To also remove the local MySQL data from the container:

```powershell
docker compose down -v
```

## Important Note

The project's environment loader now lets already defined environment variables take precedence over config/.env. This keeps local secrets out of the image while still injecting a database configuration adapted to Docker.