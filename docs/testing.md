# Testing

This project uses Codeception for tests and Alice for fixtures.

## Test database (Docker)

The test database runs in Docker, uses a named volume, and loads DDL from `backend/migrations/schema` on init.

Start the test database (defined in the main compose file):

```sh
docker compose up -d --wait postgres_test
```

Stop it when you're done:

```sh
docker compose down
```

## Running tests

Install dev dependencies in `backend/` first:

```sh
composer install
```

Run Codeception:

```sh
cd backend
./vendor/bin/codecept run
```

The test bootstrap rebuilds the schema from SQL files in `backend/migrations/schema` on each run.
