# Company Versioning API

API для управління версіями даних компаній. Система автоматично відстежує зміни в даних компаній та зберігає історію версій.

## Технології

-   PHP 8.4
-   Laravel 12
-   PostgreSQL 18
-   Docker & Docker Compose
-   Pest

## Встановлення та запуск

### 1. Клонування репозиторію

```bash
git clone git@github.com:programmator/company-versioning-api.git
cd company-versioning-api
```

### 2. Налаштування оточення

Скопіюйте файл `.env.example` в `.env`:

```bash
cp .env.example .env
```

Основні налаштування в `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=company_versioning_api
DB_USERNAME=root
DB_PASSWORD=secret
```

### 3. Запуск через Docker

Запустіть контейнери:

```bash
docker-compose up -d
```

API буде доступне за адресою: **http://localhost:8080**

## Виконання тестів

Запуск всіх тестів:

```bash
docker-compose exec app composer test
```

## API Endpoints

### 1. Створення/оновлення компанії

**POST** `/api/company`

Створює нову компанію або оновлює існуючу (якщо знайдено за ЄДРПОУ).

**Request Body:**

```json
{
    "name": "ТОВ Українська енергетична біржа",
    "edrpou": "37027819",
    "address": "01001, Україна, м. Київ, вул. Хрещатик, 44"
}
```

**Responses:**

Створення нової компанії (статус 200):

```json
{
    "status": "created",
    "company_id": 1,
    "version": 1
}
```

Оновлення існуючої компанії (статус 200):

```json
{
    "status": "updated",
    "company_id": 1,
    "version": 2
}
```

Дублікат (ідентичні дані) (статус 200):

```json
{
    "status": "duplicate",
    "company_id": 1,
    "version": 1
}
```

### 2. Отримання версій компанії

**GET** `/api/company/{edrpou}/versions`

Повертає всі версії компанії за ЄДРПОУ у зворотному хронологічному порядку.

**Параметри URL:**

-   `edrpou` - ЄДРПОУ компанії

**Response (статус 200):**

```json
[
    {
        "name": "ТОВ Українська енергетична біржа - оновлена назва",
        "edrpou": "37027819",
        "address": "01001, Україна, м. Київ, вул. Хрещатик, 44",
        "version": 2,
        "created_at": "2025-11-15 10:00:00"
    },
    {
        "name": "ТОВ Українська енергетична біржа",
        "edrpou": "37027819",
        "address": "01001, Україна, м. Київ, вул. Хрещатик, 44",
        "version": 1,
        "created_at": "2025-11-14 15:30:00"
    }
]
```

**Приклад виклику через curl:**

```bash
curl -X GET http://localhost:8080/api/company/37027819/versions \
  -H "Accept: application/json"
```

## Приклади використання

### Створення нової компанії

```bash
curl -X POST http://localhost:8080/api/company \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "ТОВ Тестова Компанія",
    "edrpou": "12345678",
    "address": "Україна, м. Львів, вул. Тестова, 1"
  }'
```

### Оновлення компанії

```bash
curl -X POST http://localhost:8080/api/company \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "ТОВ Тестова Компанія - нова назва",
    "edrpou": "12345678",
    "address": "Україна, м. Львів, вул. Тестова, 2"
  }'
```

### Перегляд історії версій

```bash
curl -X GET http://localhost:8080/api/company/12345678/versions \
  -H "Accept: application/json"
```

## Корисні команди

### Доступ до контейнера

```bash
docker-compose exec app bash
```

### Перегляд логів

```bash
docker-compose logs -f app
```

### Зупинка контейнерів

```bash
docker-compose down
```

### Очистка бази даних та повторне наповнення

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

## Логіка версіонування

Система автоматично визначає тип операції:

1. **Created** - Компанія з таким ЄДРПОУ не існує, створюється нова компанія з версією 1
2. **Updated** - Компанія існує, але дані відрізняються, створюється нова версія
3. **Duplicate** - Компанія існує з ідентичними даними, нова версія не створюється

Кожна версія містить повний знімок даних компанії на момент створення версії.
