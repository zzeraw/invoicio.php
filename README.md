Invoicio (PHP)
-------------------

Приложение для удобного выставления счетов фрилансером

---

## Инструкция по развертыванию проекта локально

### 1. Установить Docker и Docker Compose

Для работы с проектом локально необходимо установить Docker и Docker Compose.

Проект был развернут успешно на следующей конфигурации:

- Docker version 28.0.4
- Docker Compose version v2.34.0

Скорее всего запустится и на других версиях.

Docker Compose обязательно должен быть не ниже 2 версии.

### 2. Скопировать проект из Github

В первую очередь:

- добавить свой SSH-ключ в Github

!!! ВАЖНО !!!

Если у вас винда, то ПЕРЕД клонированием репозитория нужно сделать такую настройку обработки конца строк:

```bash
git config --global core.autocrlf input
```

Опционально:

```bash
git config user.name "John Doe"
git config user.email johndoe@example.com
```

* [https://github.com/zzeraw/invoicio.php](https://github.com/zzeraw/invoicio.php)

Склонируйте код в папку проекта:

```bash
https://github.com/zzeraw/invoicio.php
```

Переходим в папку проекта:

---


docker compose exec php bin/console cache:clear
docker compose exec php composer update nelmio/api-doc-bundle
docker compose exec php bin/console doctrine:migrations:migrate
docker compose --env-file .env up -d --force-recreate php
docker compose exec php bash
docker compose exec postgres psql -U user -d invoicio


./vendor/bin/codecept run


docker compose exec php composer install

docker compose exec php ./vendor/bin/codecept run


docker compose up -d --wait postgres_test
cd backend && ./vendor/bin/codecept run








docker compose exec php ./vendor/bin/codecept run
docker compose exec php ./vendor/bin/phpstan analyse
docker compose exec php ./vendor/bin/php-cs-fixer fix
docker compose exec php ./vendor/bin/phpcs



http://localhost/api/doc/
http://localhost/api/health