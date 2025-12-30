Invoicio (PHP)
-------------------

Приложение для удобного выставления счетов фрилансером

---

## Общие требования

- Docker version 28.0.4
- Docker Compose version v2.34.0

Не ниже 2 версии должен быть Docker Compose.

Скорее всего запустится и на других версиях.

## Клонирование репозитория

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
git clone https://github.com/zzeraw/invoicio.php
```

## Документация по запуску и развертыванию

- `frontend/README.md` — запуск фронтенда
- `backend/README.md` — запуск бэкенда, миграции, тесты и сервисные команды
- `deploy/README.md` — запуск стека через Docker Compose
