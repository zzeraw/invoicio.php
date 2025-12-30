# Backend

Все команды ниже выполняются из корня репозитория, если не указано иное.

## Базовый запуск

```bash
docker compose --env-file .env up -d --force-recreate php
```

Запуск контейнера PHP с учетом переменных окружения из `.env`.

```bash
docker compose exec php bash
```

Открыть shell внутри контейнера PHP.

## Зависимости

```bash
docker compose exec php composer install
```

Установить зависимости Composer внутри контейнера.

```bash
docker compose exec php composer update nelmio/api-doc-bundle
```

Обновить пакет `nelmio/api-doc-bundle`.

## База данных

```bash
docker compose exec php bin/console doctrine:migrations:migrate
```

Применить миграции Doctrine.

```bash
docker compose exec postgres psql -U user -d invoicio
```

Подключиться к PostgreSQL (замените `user` и `invoicio` на значения из `.env` при необходимости).

```bash
docker compose up -d --wait postgres_test
```

Поднять тестовую базу данных.

## Кэш и права

```bash
docker compose exec php bin/console cache:clear
```

Очистить кеш приложения от имени пользователя контейнера.

```bash
docker compose exec -w /var/www php composer run-script cache:clear
```

Очистить кеш через Composer-скрипт.

```bash
docker compose exec -u www-data -w /var/www php php bin/console cache:clear
```

Очистить кеш от имени `www-data`, если возникли проблемы с правами.

```bash
docker compose exec -w /var/www php chown -R www-data:www-data var/cache var/log
```

Исправить права на кеш и логи.

```bash
docker compose exec -w /var/www php chown -R www-data:www-data var/cache
```

Исправить права только на кеш.

## JWT ключи

```bash
php bin/console lexik:jwt:generate-keypair
```

Сгенерировать ключи JWT локально (если PHP запущен на хосте).

```bash
docker compose exec php php bin/console lexik:jwt:generate-keypair
```

Сгенерировать ключи JWT внутри контейнера.

## Пользователи

```bash
php bin/console app:user:create admin@example.com secret --role=admin --status=active
```

Создать администратора (команду выполнять локально или внутри контейнера PHP).

## Тесты и качество кода

```bash
./vendor/bin/codecept run
```

Запустить все тесты Codeception (выполнять из каталога `backend`).

```bash
docker compose exec php ./vendor/bin/codecept run
```

Запустить все тесты Codeception внутри контейнера.

```bash
docker compose exec -w /var/www php ./vendor/bin/codecept run invoicebundle
```

Запустить тесты только для `invoicebundle`.

```bash
docker compose exec -w /var/www php ./vendor/bin/codecept run appbundle
```

Запустить тесты только для `appbundle`.

```bash
docker compose exec php ./vendor/bin/phpstan analyse
```

Статический анализ PHPStan.

```bash
docker compose exec php ./vendor/bin/php-cs-fixer fix
```

Автоформатирование PHP-CS-Fixer.

```bash
docker compose exec php ./vendor/bin/phpcs
```

Проверка стиля PHP_CodeSniffer.

## Полезные URL

- http://localhost/api/doc/
- http://localhost/api/health
