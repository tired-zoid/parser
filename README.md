# Log Parser & Analytics для Nginx

Веб-приложение на базе Yii2 для парсинга и анализа логов веб-сервера Nginx.

## Функциональность

### Парсинг логов
- Извлечение IP-адреса, даты/времени, URL, User-Agent
- Парсинг User-Agent: операционная система, архитектура (x86/x64), браузер
- Консольная команда для загрузки данных

### Аналитика и визуализация
- График 1: Количество запросов по дням (линейный график)
- График 2: Топ-3 самых популярных браузера (столбчатая диаграмма)
- Таблица: Статистика по дням с сортировкой

### Фильтрация
- По диапазону дат (не более 1 года)
- По операционной системе
- По архитектуре (x86/x64)

### Сортировка таблицы
- По всем колонкам: дата, количество запросов, топ URL, топ браузер
- По возрастанию/убыванию

## Технологии

- PHP 7.4+ / Yii2 Framework (Basic template)
- MySQL 5.7+ / MariaDB
- Chart.js - для графиков
- WhichBrowser/Parser - для парсинга User-Agent
- Git - система контроля версий

## Установка

### 1. Клонирование репозитория

```bash
git clone https://github.com/tired_zoid/parser.git
cd parser
```

## 2. Установка зависимостей

```bash
composer install
```

## 3. Настройка базы данных

Создайте базу данных MySQL:

```sql
CREATE DATABASE parser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Настройте подключение в `config/db.php`:

```php
<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=parser',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
```

## 4. Применение миграций

```bash
php yii migrate
```
# Использование

## Загрузка логов

```bash
php yii/log/parse /path/to/nginx/access.log
```

Пример:

```bash
php yii/log/parse /var/log/nginx/access.log
```

## Запуск веб-приложения

```bash
php yii serve
```

Откройте в браузере:

```text
http://localhost:8080
```