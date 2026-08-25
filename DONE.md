# Done

Этот файл фиксирует уже выполненную работу, чтобы не возвращаться к одному и тому же cleanup без причины.

## Базовый Cleanup Для Public Code Review

Выполнено:
- проект переведён из Laravel boilerplate в оформленный PortfolioHub;
- удалён неиспользуемый `resources/views/welcome.blade.php`;
- удалён boilerplate unit test `tests/Unit/ExampleTest.php`;
- README полностью заменён на техническое описание проекта на русском;
- обновлены package metadata в `composer.json`;
- обновлён `APP_NAME` в `.env.example`;
- добавлен собственный SVG favicon;
- root title зафиксирован как `PortfolioHub`.

## Backend

Выполнено:
- `ProjectCardResource` отдаёт `repository_url`;
- сохранён существующий контракт `images` и `technologies`;
- `HomeController` загружает проекты с eager loading `images` и `technologies`;
- запрос главной показывает только опубликованные проекты;
- сортировка идёт по `published_at DESC`;
- главная ограничена 6 проектами.

## База Данных И Factories

Выполнено:
- `ProjectImageFactory` генерирует storage-relative path вида `projects/tests/*.webp`;
- `sort_order` в factory больше не создаёт очевидный конфликт при нескольких изображениях;
- `ProjectSeeder` использует обычный import `Technology`;
- из `DatabaseSeeder` удалён стандартный закомментированный boilerplate;
- сидеры демо-проекта сохраняют идемпотентность.

## Frontend

Выполнено:
- главная страница получила цельный premium dark style;
- карточка проекта визуально усилена;
- CTA для сайта и исходного кода стали заметнее;
- галерея позволяет выбрать все изображения, переданные backend;
- добавлены понятные состояния для активного изображения;
- базовые theme colors вынесены в `resources/css/app.css`;
- hero слегка уплотнён, чтобы карточка была видна раньше.

## Tests

Выполнено:
- `HomePageTest` использует `withoutVite()`;
- `php artisan test` работает без предварительного `npm run build`;
- тестируется HTTP 200;
- тестируется Inertia component `Home/Index`;
- тестируется наличие published project;
- тестируется отсутствие draft project;
- тестируется сортировка по `published_at DESC`;
- тестируется limit 6;
- тестируется `repository_url`;
- тестируются структуры `images` и `technologies`.

## CI/CD

Выполнено:
- workflow запускается на pull request и push в `main`/`develop`;
- есть отдельный tests job;
- staging deploy привязан к `develop`;
- production deploy привязан к `main`;
- в tests job добавлен `vendor/bin/pint --test`;
- deployment logic не переписывалась сверх необходимости.

## Проверки

Перед последним commit успешно проходили:

```bash
composer validate --strict
vendor/bin/pint --test
php artisan test
npm run build
```

## Commits

- `4309915 refactor: improve project quality and test coverage`
- `02e3655 style: polish portfolio landing page`

## Что Не Делали

- Не добавляли Docker.
- Не добавляли PHPStan/Larastan.
- Не добавляли Repository/Service/DTO слои там, где текущая простая логика этого не требовала.
- Не добавляли auth/admin panel.
- Не добавляли новые крупные зависимости.
- Не меняли deploy pipeline сверх добавления Pint check.

## Roadmap Progress

### 1. Страница Детального Проекта

Сделано:
- добавлен route `GET /projects/{project:slug}`;
- добавлен controller для detail page;
- добавлен `ProjectDetailResource`;
- detail page показывает только published проекты;
- draft projects возвращают 404;
- detail page получает images, technologies, ссылки и case-study поля;
- добавлена Inertia-страница `Projects/Show.vue`;
- карточки проектов ведут на detail page;
- добавлены feature tests для detail page.

### 2. Каталог Всех Проектов

Сделано:
- добавлен route `GET /projects`;
- добавлен controller каталога;
- каталог показывает published проекты;
- сортировка идёт по `published_at DESC`;
- добавлена pagination-структура;
- добавлена Inertia-страница `Projects/Index.vue`;
- главная получила ссылку на каталог;
- добавлены feature tests для каталога.

### 3. Фильтрация По Технологиям

Сделано:
- добавлен query param `technology`;
- фильтр реализован через `whereHas('technologies')`;
- список технологий передаётся в каталог;
- frontend показывает technology chips с активным состоянием;
- добавлены feature tests для фильтра.

### 4. Поиск По Проектам

Сделано:
- добавлен query param `search`;
- поиск работает по `title` и `description`;
- поиск комбинируется с текущей структурой каталога;
- frontend показывает search input с Inertia-переходом;
- добавлены feature tests для поиска.

### 5. Расширенная Модель Проекта

Сделано:
- добавлены case-study поля проекта: `role`, `problem`, `solution`, `result`, `started_at`, `finished_at`, `sort_order`;
- обновлены `Project` fillable/casts;
- обновлены factories;
- обновлены seeders;
- detail page отображает задачу, решение и результат.

### 6. Контактная Форма

Сделано:
- добавлена таблица `contact_messages`;
- добавлена модель `ContactMessage`;
- добавлен `StoreContactMessageRequest`;
- добавлен `ContactMessageController`;
- форма сохраняет сообщение в БД;
- frontend показывает validation errors, processing state и success message;
- добавлены feature tests для успешной отправки и validation errors.

### 7. SEO И Meta

Сделано:
- home, catalog и detail pages получают `meta`;
- Vue-страницы используют Inertia `Head`;
- title и description отличаются по странице.

### 8. Улучшение Seed Data

Сделано:
- сидер PortfolioHub расширен case-study полями;
- добавлены дополнительные демо-проекты `TaskFlow` и `MetricBoard`;
- сидер остаётся идемпотентным через `updateOrCreate`;
- technology sync сохранён.

## Product Polish And Admin

### Detail Page И Каталог

Сделано:
- detail page визуально отделён от карточки проекта;
- первый экран detail page стал больше похож на case study;
- добавлены даты начала и завершения проекта;
- блок фильтров каталога выровнен;
- поиск и technology chips стали выглядеть как единая панель.

### Auth И Admin Skeleton

Сделано:
- добавлен login/logout на стандартном Laravel session auth;
- добавлена protected admin area под `/admin`;
- добавлен admin layout;
- добавлен dashboard с метриками;
- добавлены последние контактные сообщения на dashboard;
- shared Inertia props теперь содержат текущего пользователя.

### Admin Project CRUD

Сделано:
- добавлен список проектов в админке;
- добавлено создание проекта;
- добавлено редактирование проекта;
- добавлено удаление проекта;
- добавлен `ProjectRequest` для validation;
- добавлен `SaveProjectAction`;
- сохранение проекта, технологий и изображений выполняется в transaction;
- технологии синхронизируются через many-to-many;
- изображения пересобираются атомарно.
- добавлена загрузка изображений проекта из админки;
- загруженные файлы сохраняются в `storage/app/public/projects/{slug}`;
- в БД сохраняются storage-relative paths, совместимые с публичной галереей и сидерами;
- добавлен feature test на upload изображения проекта.
- добавлен флаг защищённого проекта для публичного demo-доступа;
- защищённые проекты нельзя открыть на редактирование, обновить или удалить через backend;
- список проектов показывает защищённые записи в режиме просмотра;
- админский интерфейс русифицирован без смешения английских labels;
- из формы изображений убрано ручное поле storage path, вместо него показывается upload и превью.

### Admin Contact Messages

Сделано:
- добавлен список сообщений;
- добавлен просмотр сообщения;
- добавлено действие mark as read;
- dashboard считает unread messages.

### Tests

Сделано:
- добавлены tests для login/logout/admin access;
- добавлены tests для admin project index/create/update;
- добавлены tests для admin contact messages и mark as read.
