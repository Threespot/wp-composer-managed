# CLAUDE.md

Guidance for Claude Code when working in this repository.

## What this repo is

A Pantheon-hosted WordPress site built on Threespot's `wp-composer-managed` upstream (a fork of Pantheon's `wordpress-composer-managed`, Roots Bedrock layout) with a Sage 11 theme carrying the Threespot layer (from `Threespot/wordline@base-build` plus generic components from nmhc). GitHub is the only code repository: Pantheon's **external repository** integration deploys from it. Pantheon builds the site itself on every push:

- **Integrated Composer** runs `composer install --no-dev` (`build_step: true` in `pantheon.yml`). The root `post-install-cmd` then runs `composer theme:install`, which installs the Sage theme's own Composer deps (Sage's `functions.php` requires `web/app/themes/sage/vendor/autoload.php`).
- **Frontend asset builds** run the theme's `build` script in `web/app/themes/sage` (`frontend_build` in `pantheon.yml`, Node 24). The theme uses **Yarn 4**: `yarn.lock` plus the committed `.yarn/releases/yarn-4.14.1.cjs` (`yarnPath` in `.yarnrc.yml`).

There is no CircleCI, no Build Tools and no Pantheon git remote. Do not add any of them, and never commit `vendor/`, `web/wp/`, Composer-installed plugins or `web/app/themes/sage/public/build/`.

## Layout

- `web/wp/`: WordPress core (Composer, never edited, not committed)
- `web/app/`: content dir (`WP_CONTENT_DIR`). Plugins/themes/mu-plugins live here, **not** `web/wp-content`
- `web/app/mu-plugins/threespot-wp-base-config.php`: loads `threespot/wp-base-config` (shared Threespot config: admin, assets, blocks, Acorn storage on Pantheon, CPT/taxonomy autoloading)
- `web/app/themes/sage/`: Sage 11 theme with the Threespot layer (Acorn, Blade, Vite, SCSS in `resources/styles`, JS in `resources/scripts`). Its lint/Vite configs are symlinks into `vendor/threespot/wp-base-config/dist/`, so run the root `composer install` before building. Its own `composer.json`, `package.json` and `CLAUDE.md`
- `config/application.php` + `config/application.pantheon.php`: Bedrock config. Local values come from `.env` (copy `.env.example`)
- `pantheon.yml`: site overrides (PHP, MariaDB, HTTPS, build step, frontend build). `pantheon.upstream.yml` holds upstream defaults; don't edit it.
- `private/scripts/helpers.sh`: upstream helper that symlinks `web/wp/*` into `web/` after install

## Common commands

```bash
lando start                                       # runs composer install (root + theme) on start
lando pull --code=none --database=dev --files=dev
lando composer require wp-plugin/<slug>           # WordPress.org plugins via wp-packages.org (Threespot standard set lives in upstream-configuration/composer.json)
lando wp …                                        # WP-CLI
cd web/app/themes/sage && yarn start              # Vite HMR (https, host fixme-app-name.lndo.site)
cd web/app/themes/sage && yarn build              # same build Pantheon runs
lando composer deploy:test                        # promote dev -> test, clear cache
lando composer deploy:live                        # promote test -> live, clear cache
lando composer ci                                 # lint + phpcs + phpstan + phpunit (same as GitHub Actions)
```

`phpstan-baseline.neon` holds known third-party/upstream findings only. After changing Blade layouts locally, `lando wp acorn optimize:clear` if you see stale views.

Premium plugins (ACF Pro, Admin Columns Pro, Gravity Forms, Object Cache Pro) need Composer credentials: a gitignored `auth.json` locally, the site-level Pantheon secret `COMPOSER_AUTH` (type `env`, scope `ic`) for Pantheon builds, and the `COMPOSER_AUTH` repo secret for GitHub Actions. License constants come from `$threespot_setting()` in `config/application.php` (env, then `pantheon_get_secret()`, then legacy `secrets.json`).

PHP 8.3 is required (Sage 11 / Acorn 6). If host PHP is older, run Composer via `lando composer`.

## Deploy flow

PR → Pantheon Multidev (automatic). Merge/push to `main` → Dev (automatic). Dev → Test → Live via Terminus (`deploy:test` / `deploy:live`). External-repo sites do not support SFTP mode.

## Code style

Never introduce Unicode smart/curly quotes in code files outside of comments. Always use straight quotes.
