# Roadmap

PortfolioHub развивается как небольшой fullstack pet-project на Laravel, Inertia и Vue. Цель roadmap — добавлять реальные продуктовые возможности и выбирать архитектуру по сложности задачи, а не по шаблону.

## Принципы

- Использовать Laravel-конвенции и добавлять Repository/Service/DTO/Action только там, где они реально упрощают код, фиксируют контракт или изолируют бизнес-сценарий.
- Проектировать БД через constraints, foreign keys, indexes и реальные запросы приложения.
- Держать controllers тонкими.
- Использовать Form Requests при появлении пользовательского ввода.
- Отдавать frontend-контракты через Resources.
- Использовать транзакции для операций, где несколько изменений должны быть атомарными.
- Покрывать существенные backend-фичи feature tests.
- Перед commit запускать:

```bash
composer validate --strict
vendor/bin/pint --test
php artisan test
npm run build
```

## Порядок Развития

### 1. Страница Детального Проекта

Пользователь видит отдельную страницу проекта по slug: большую галерею, описание, стек, ссылки, задачу/решение/результат.

Backend:
- добавить route `GET /projects/{project:slug}`;
- добавить controller для show-страницы;
- добавить `ProjectDetailResource`;
- eager-load `images` и `technologies`;
- не показывать проекты без `published_at`.

БД:
- на первом шаге можно использовать текущие поля;
- если контента не хватает, добавить nullable-поля `problem`, `solution`, `result`, `role`, `started_at`, `finished_at`.

Frontend:
- новая Inertia-страница `Projects/Show.vue`;
- ссылка с карточки проекта на detail page;
- переиспользовать галерею.

Tests:
- published project доступен;
- draft project недоступен;
- корректный Inertia component;
- props содержат images, technologies и ссылки.

Ценность для Laravel: высокая.

### 2. Каталог Всех Проектов

Пользователь видит отдельную страницу со всеми опубликованными проектами, а главная остаётся витриной избранных/последних.

Backend:
- добавить route `GET /projects`;
- отдельный index controller;
- сортировка по `published_at DESC`;
- pagination или ограниченная выборка.

БД:
- текущий индекс `published_at` подходит.

Frontend:
- страница `Projects/Index.vue`;
- список/сетка карточек;
- empty state.

Tests:
- published проекты отображаются;
- drafts не отображаются;
- порядок корректный;
- pagination/limit работает.

Ценность для Laravel: высокая.

### 3. Фильтрация По Технологиям

Пользователь фильтрует проекты по Laravel, Vue, MySQL и другим технологиям.

Backend:
- query param `technology`;
- фильтр через `whereHas('technologies')`;
- передавать список технологий для фильтра.

БД:
- pivot уже есть;
- индекс по `technology_id` уже есть.

Frontend:
- chips/tabs технологий;
- активное состояние фильтра;
- Inertia reload с query params;
- empty state для пустой выдачи.

Tests:
- фильтр возвращает только подходящие проекты;
- drafts не попадают;
- неизвестный slug обрабатывается предсказуемо.

Ценность для Laravel: очень высокая.

### 4. Поиск По Проектам

Пользователь ищет проекты по названию и описанию.

Backend:
- query param `search`;
- простой поиск по `title` и `description`;
- при объединении с фильтрами держать запрос читаемым.

БД:
- сначала без fulltext;
- fulltext index рассматривать только если появится реальная необходимость.

Frontend:
- search input;
- loading state при Inertia-переходе;
- сброс поиска.

Tests:
- поиск по названию;
- поиск по описанию;
- поиск не показывает drafts;
- поиск работает вместе с фильтром технологии.

Ценность для Laravel: средняя-высокая.

### 5. Расширенная Модель Проекта

Пользователь видит проект как case study: роль, задача, решение, результат, период.

Backend:
- обновить model fillable;
- расширить resource;
- обновить seeders/factories.

БД:
- добавить nullable-поля `role`, `problem`, `solution`, `result`, `started_at`, `finished_at`;
- при необходимости добавить `sort_order`.

Frontend:
- блоки case study на detail page;
- аккуратное отображение пустых полей.

Tests:
- поля приходят во frontend props;
- даты форматируются стабильно;
- старое поведение published/draft сохраняется.

Ценность для Laravel: высокая.

### 6. Контактная Форма

Пользователь отправляет имя, email и сообщение через форму связи.

Backend:
- `ContactMessageController@store`;
- Form Request validation;
- сохранение в БД;
- flash success response.

БД:
- таблица `contact_messages`;
- поля `name`, `email`, `message`, `read_at`, timestamps;
- индекс по `created_at`.

Frontend:
- Inertia form helper;
- validation errors;
- processing state;
- success state.

Tests:
- validation errors;
- successful submit creates row;
- response содержит success state.

Ценность для Laravel: высокая.

### 6.1. Демонстрационная Админка

Пользователь-владелец входит в защищённую панель и управляет проектами и сообщениями.

Backend:
- стандартный Laravel session auth;
- protected routes под `/admin`;
- dashboard controller;
- project CRUD controller;
- contact message admin controller;
- Form Request для project create/update;
- Action с transaction для сохранения project + technologies + images.

БД:
- использовать существующие `users`, `projects`, `technologies`, `project_images`, `contact_messages`;
- дополнительных таблиц не нужно.

Frontend:
- login page;
- admin layout;
- dashboard;
- projects index/form;
- contact messages index/show.

Tests:
- guest redirect на login;
- login/logout;
- admin видит dashboard;
- create/update project;
- mark contact message as read.

Ценность для Laravel: очень высокая.

### 7. SEO И Meta

Пользователь напрямую это почти не видит, но страницы получают нормальные title/description.

Backend:
- передавать meta props;
- для detail page брать данные из проекта.

БД:
- можно начать без новых полей;
- позже добавить `meta_title`, `meta_description`.

Frontend:
- использовать Inertia head;
- разные title для home/catalog/detail.

Tests:
- минимально проверить title/meta для detail page.

Ценность для Laravel: средняя.

### 8. Улучшение Seed Data

Пользователь видит несколько реалистичных проектов, а фильтры и каталог выглядят живыми.

Backend:
- расширить сидеры несколькими проектами;
- сохранить идемпотентность.

БД:
- без новых таблиц, если не добавлены поля case study.

Frontend:
- без обязательных изменений.

Tests:
- обычно не тестировать сидеры подробно;
- при сложной логике проверить идемпотентность.

Ценность для Laravel: средняя.

## Что Пока Не Добавлять Без Реальной Необходимости

- Docker.
- Kubernetes.
- Redis.
- RabbitMQ.
- Очереди.
- Микросервисы.
- PHPStan/Larastan.
- Крупные зависимости.
- Админку до появления понятного сценария управления данными.

Архитектурные слои не запрещены. Если задача становится сложнее простого controller + model + resource, можно и нужно выносить логику в подходящий слой:

- Form Request — для пользовательского ввода и валидации.
- Service/Action — для бизнес-сценария, транзакций или нескольких связанных моделей.
- Repository или query object — для повторяемых сложных выборок.
- DTO — для устойчивого контракта данных между слоями.
- Model method/scope — для поведения или запроса, который естественно принадлежит модели.

## Файлы, Которые Нужно Смотреть Перед Новой Задачей

Общее состояние:
- `README.md`
- `DONE.md`
- `ROADMAP.md`
- `composer.json`
- `package.json`
- `.github/workflows/deploy.yml`

Routes и bootstrap:
- `routes/web.php`
- `bootstrap/app.php`
- `resources/views/app.blade.php`

Backend:
- `app/Http/Controllers/HomeController.php`
- `app/Http/Resources/ProjectCardResource.php`
- `app/Models/Project.php`
- `app/Models/ProjectImage.php`
- `app/Models/Technology.php`

База данных:
- `database/migrations/*projects*`
- `database/migrations/*project_images*`
- `database/migrations/*technologies*`
- `database/migrations/*project_technology*`
- `database/factories/ProjectFactory.php`
- `database/factories/ProjectImageFactory.php`
- `database/seeders/ProjectSeeder.php`
- `database/seeders/TechnologySeeder.php`

Frontend:
- `resources/js/Pages/Home/Index.vue`
- `resources/js/Components/Project/Card.vue`
- `resources/js/Components/Project/Gallery.vue`
- `resources/js/Components/Project/TechnologyBadge.vue`
- `resources/css/app.css`

Tests:
- `tests/Feature/HomePageTest.php`
- `phpunit.xml`
