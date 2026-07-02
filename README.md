# plack.app

A Slack-style team chat app built with Laravel and React. You sign up, spin up a workspace, and organize conversations into channels.

plack.app was built live over two days on stream. Want to see it come together from an empty folder? Here are the recordings:

- Day 1 — https://www.youtube.com/watch?v=zq4vexfE2zs
- Day 2 — https://www.youtube.com/watch?v=gPHN3wHdP8Q

## What it does

- **Workspaces** — every user can create and own workspaces. Each one gets a `general` channel out of the box so there's somewhere to talk from the start.
- **Channels** — create, rename, and delete channels inside a workspace. Everything lives at readable slug URLs like `/workspaces/acme/channels/general`.
- **Owner-only access** — a workspace belongs to the person who made it. If you're not the owner, you get a 404 and never even learn it exists.
- **Accounts done properly** — registration, login, email verification, password resets, two-factor auth, and profile/appearance settings all come from Laravel Fortify.

## Tech stack

- **Laravel 13** on **PHP 8.5**, with [Inertia v3](https://inertiajs.com) gluing the backend to the frontend (no separate API layer).
- **React 19** + TypeScript, styled with **Tailwind v4** and shadcn/Radix components.
- **Fortify** for auth, **Wayfinder** for typed route helpers on the frontend, and **[Essentials](https://github.com/nunomaduro/essentials)** for stricter Laravel defaults.
- **SQLite** by default, so there's no database server to set up. Sessions, the queue, and the cache all live in the database too.
- **Bun** as the package manager and **vite-plus** as the bundler.

## Getting started

You'll need:

- **PHP 8.5+**
- **[Composer](https://getcomposer.org)**
- **[Bun](https://bun.sh)**
- A coverage driver like **[Xdebug](https://xdebug.org/docs/install)** or **pcov** if you plan to run the test suite (it enforces 100% coverage).

Clone it and run the setup script:

```bash
git clone https://github.com/nunomaduro/plack.app.git
cd plack.app

# Installs deps, creates .env, generates a key, runs migrations, builds the frontend
composer setup
```

Then start everything with one command:

```bash
composer dev
```

That boots the Laravel server, a queue worker, live log output, and the Vite dev server together. Your app is at http://localhost:8000.

> The first `bun install` also downloads Chromium for the browser tests, so give it a minute the first time.

## Everyday commands

```bash
composer dev     # run the app (server + queue + logs + vite)
composer test    # the full check: linting, types, static analysis, and tests
composer lint    # auto-fix code style (Rector + Pint for PHP, vp for JS/TS)
```

`composer test` is the same gate CI runs, and it's strict on purpose. If you want to run just one slice:

```bash
composer test:unit           # Pest tests, 100% code coverage required
composer test:types          # PHPStan at max level + tsc
composer test:type-coverage  # 100% type coverage required
composer test:lint           # style check without fixing anything
```

Browser tests run through Pest's browser plugin (backed by Playwright) and live in `tests/Browser`.

## How the code is organized

The backend keeps controllers thin and pushes the real work into small, single-purpose **Action** classes under `app/Actions` (for example, creating a workspace and its `general` channel happens in one transactional action). Read-heavy list queries live in `app/Queries`.

On the frontend, pages are Inertia components in `resources/js/pages`, and links to backend routes go through the typed helpers Wayfinder generates, so a renamed route becomes a TypeScript error instead of a broken link.

A heads-up if you're new to the repo: it's opinionated about quality. 100% test and type coverage, PHPStan at max level, and strict style rules are all enforced by `composer test`, so expect the tooling to push back until things are tidy.

## Credits

plack.app grew out of the [Laravel Starter Kit (Inertia & React)](https://github.com/nunomaduro/laravel-starter-kit-inertia-react), built live on stream. Released under the [MIT license](https://opensource.org/licenses/MIT).
