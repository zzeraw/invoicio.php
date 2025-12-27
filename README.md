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

