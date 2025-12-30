# Deploy

## Переменные окружения

Файл `.env` лежит в корне репозитория и используется Docker Compose.

## Запуск стека

```bash
docker compose up -d
```

Поднимает `nginx`, `php`, `postgres`, `frontend` и `postgres_test`.

## Порты по умолчанию

- http://localhost — nginx (HTTP)
- http://localhost:5173 — фронтенд (Vite)
- localhost:5433 — PostgreSQL (основная БД)
- localhost:5434 — PostgreSQL (тестовая БД)
