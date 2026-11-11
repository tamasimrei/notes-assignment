# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

A notes app with tagging: a Symfony 6.4 JSON API (PHP 8.5, Doctrine ORM, MariaDB) at the repo root, plus a React frontend built with Vite in `notes-frontend/`. Everything runs through Docker Compose.

## Running

```sh
docker compose up -d              # web (PHP/nginx) :8080, db (MariaDB) :3306, node (React dev server) :3000
docker compose logs
docker compose down --rmi all -v  # full teardown
```

- The `web` container runs `composer install` on startup, and the `node` container runs `npm install && npm start`. The source is bind-mounted, so code changes show up without rebuilding.
- `DATABASE_URL` must be `mysql://root:Pas_W0rd@db:3306/tm-notes?serverVersion=10.8.3-MariaDB`. Put it in `.env`, which is not committed. `CORS_ALLOW_ORIGIN` is also read from the environment by `config/packages/nelmio_cors.yaml`.
- **The schema is managed by Doctrine migrations** in `migrations/`. The `web` container runs `doctrine:migrations:migrate` on startup, after the `db` healthcheck passes. For a schema change, update the entities, then generate a migration with `php bin/console doctrine:migrations:diff` and review it. Load sample data with `php bin/console doctrine:fixtures:load` (`src/DataFixtures/AppFixtures.php`). MariaDB data is kept in `./var/lib/mysql`.
- The frontend's API base URL `http://localhost:8080/api` is hardcoded in `notes-frontend/src/Notes/index.jsx` and `notes-frontend/src/Tags/index.jsx`.

## Tests

The backend has two separate test suites. Run them inside the `web` container, for example with `docker compose exec web ...`:

```sh
vendor/bin/phpunit                                       # tests/ — functional WebTestCase tests that hit /api/* through KernelBrowser
vendor/bin/phpunit tests/Controller/NoteControllerTest.php
vendor/bin/phpunit --filter testCreateNote
vendor/bin/phpspec run                                   # spec/ — unit specs for Entity/Repository/Service/Controller
vendor/bin/phpspec run spec/Service/NoteServiceSpec.php
```

- The PHPUnit tests use a real database. In the `test` environment, Doctrine adds `_test` to the database name (`tm-notes_test`). Build it from the entity mapping and don't load fixtures into it: the fixture tags (`foo`, `bar`, `qux`) collide with the tests.
  ```sh
  php bin/console doctrine:database:create --env=test --if-not-exists
  php bin/console doctrine:schema:create --env=test
  ```
- Logging goes through Monolog (`config/packages/monolog.yaml`). In the test environment, logs go to `var/log/test.log`, so the exceptions that error tests trigger on purpose (`Uncaught PHP Exception ...`) appear there and not in PHPUnit's output. Dev logs go to `var/log/dev.log`, and prod logs go to stderr as JSON, where `docker compose logs` shows them.
- Controller tests check error responses through the real error handling (exception → `JsonApiProblemNormalizer` → JSON) with `assertJsonErrorResponse()`. Don't use `catchExceptions(false)` for that, because it skips the normalizer.
- Some controller tests depend on earlier ones (`testCreateNote` returns data that later tests use), so running one of those alone with `--filter` may not work.

The frontend has no tests yet.

## Backend architecture

The request flow is controller → service → repository. Every class in `src/` except `Entity/` is an autowired service.

- **Controllers** (`src/Controller/`) declare routes with attributes (`#[Route]`, plus `#[MapEntity(id: 'noteId')]` to map `{noteId}` to an entity). They extend `AbstractApiController`. Request bodies are deserialized straight into entities with the Symfony Serializer. Updates pass `object_to_populate` so PUT/PATCH modify the existing entity.
- **Error handling convention:** controllers throw HTTP exceptions whose *message is a JSON string* `{code, message, errors[]}`, built by helpers such as `createJsonNotFoundException`. `src/Serializer/JsonApiProblemNormalizer.php` normalizes Symfony's `FlattenException` by decoding that message back into the response body. Exceptions that don't carry a JSON message fall back to the status code and status text. New error responses should follow the same pattern. Validation failures return 422, with `errors` listing `{field, message}` entries from `AbstractEntityService::validate()`.
- **Services** (`src/Service/`) extend `AbstractEntityService`, which holds the shared validation. `NoteService::saveNote` replaces submitted tags with existing `Tag` entities when they match (looked up through `TagService::find`) before persisting. `Note` ↔ `Tag` is a ManyToMany relationship that cascades persist, merge and refresh, so new tags are created implicitly.
- **Repositories** provide `persist()` and `remove()` helpers that the services call.

## Frontend

The frontend is React 18 with react-bootstrap and axios, built and served by Vite (`notes-frontend/vite.config.js`; the dev server runs on port 3000). Components are `.jsx` files, and the HTTP root is `notes-frontend/public/` (Vite `root`): `public/index.html` loads `/src/index.jsx`, which an alias maps to `notes-frontend/src/`. Builds copy the rest of `public/` (favicon, manifest, robots.txt) into `build/` unchanged. Vite installs platform-specific native binaries, and `node_modules` is installed inside the Alpine `node` container, so run npm commands in that container (`docker compose exec node npm ...`), not on the host. The feature folders `Notes/`, `Tags/` and `Alert/` (an alert context provider) live under `notes-frontend/src/`, and the shell is in `App/`.
