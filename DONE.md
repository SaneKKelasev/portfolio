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
