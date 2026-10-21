# fixme-app-name

fixme-project-name: a Composer-managed WordPress site hosted on Pantheon.

- **Repo:** https://github.com/Threespot/fixme-app-name (private)
- **Pantheon dashboard:** https://dashboard.pantheon.io/sites/FIXME_pantheon_site_id
- **Live:** https://live-fixme-app-name.pantheonsite.io/
- **Test:** https://test-fixme-app-name.pantheonsite.io/
- **Dev:** https://dev-fixme-app-name.pantheonsite.io/
- **Local:** https://fixme-app-name.lndo.site/

> **Starter / upstream:** this repo was created from [`Threespot/wp-composer-managed`](https://github.com/Threespot/wp-composer-managed), Threespot's Pantheon custom upstream (a fork of Pantheon's [`wordpress-composer-managed`](https://github.com/pantheon-systems/wordpress-composer-managed) with the Threespot base build and theme). Placeholders (`fixme-app-name`, `fixme-project-name`, `fixme-domain`, `FIXME_pantheon_site_id`) are filled in by `composer replace-fixme`. See [Starting a new site](#starting-a-new-site-from-this-upstream) and [Maintaining the upstream](#maintaining-the-upstream) at the end.

## How this project is built

This site uses Pantheon's current tooling, not the deprecated Terminus Build Tools / CircleCI setup used by older Threespot sites.

| Piece | What it does |
|---|---|
| [`Threespot/wp-composer-managed`](https://github.com/Threespot/wp-composer-managed) | The upstream: Pantheon's `wordpress-composer-managed` plus the Threespot base build. A Bedrock layout: WordPress core in `web/wp`, plugins/themes/mu-plugins in `web/app`, config in `config/` and `.env`. |
| [External repository](https://docs.pantheon.io/guides/external-repositories) | GitHub is the only code repo. Pantheon's GitHub App deploys from it. There is no Pantheon git remote to push to. |
| [Integrated Composer](https://docs.pantheon.io/guides/integrated-composer) | Pantheon runs `composer install --no-dev` on every push (`build_step: true`). The root `post-install-cmd` also installs the Sage theme's Composer deps. |
| [Frontend asset builds](https://docs.pantheon.io/frontend-builds) | Pantheon installs the theme's Node deps (Yarn 4 via the committed `.yarn/releases`) and runs `build` in `web/app/themes/sage` on every push (`frontend_build` in `pantheon.yml`). Compiled assets are never committed. |
| [`threespot/wp-base-config`](https://github.com/threespot/wp-base-config) | Threespot's shared mu-plugin config (admin polish, Acorn storage on Pantheon, CPT autoloading, helpers). Loaded by `web/app/mu-plugins/threespot-wp-base-config.php`. |
| [Sage 11](https://roots.io/sage/) + Threespot layer | Theme at `web/app/themes/sage`: Threespot's [wordline `base-build`](https://github.com/Threespot/wordline/tree/base-build) on current Sage 11, plus generic components from nmhc. Vite + SCSS, Yarn 4. |

### Branches and environments

| Git | Pantheon |
|---|---|
| Push or merge to `main` | Builds and deploys to **Dev** |
| Open a pull request | Creates a **Multidev** for the branch |
| `composer deploy:test` | Promotes Dev to **Test** and clears its cache |
| `composer deploy:live` | Promotes Test to **Live** and clears its cache |

The external repository integration doesn't support SFTP mode. All code changes go through git.

## Local development

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop) and [Lando](https://lando.dev/) 3.x
- [Terminus](https://docs.pantheon.io/terminus/install) 4.x, logged in (`terminus auth:whoami`)
- [Composer](https://getcomposer.org/) 2
- Node 24 (see `.tool-versions`, e.g. via [asdf](https://asdf-vm.com/))
- Write access to this repo and membership in the Pantheon site

PHP 8.3 is required. If your host PHP is older, run Composer through Lando (`lando composer …`), which uses PHP 8.3.

### First-time setup

```shell
git clone git@github.com:Threespot/fixme-app-name.git
cd fixme-app-name
cp .env.example .env            # then paste fresh salts from https://roots.io/salts.html
lando start                     # boots containers and runs `composer install` (root + theme)
lando pull --code=none --database=dev --files=dev
cd web/app/themes/sage && yarn install && yarn build
```

Then visit https://fixme-app-name.lndo.site/.

### Theme development (Vite)

The theme's Vite, ESLint, Stylelint and PostCSS configs are symlinks into `vendor/threespot/wp-base-config/dist/`, so run the root `composer install` (or `lando start`) first.

```shell
cd web/app/themes/sage
yarn start         # Vite dev server with HMR at https://fixme-app-name.lndo.site
yarn build         # production build (the same command Pantheon runs)
```

### Premium plugins and Composer credentials

ACF Pro, Admin Columns Pro, Gravity Forms and Object Cache Pro install from their vendors' Composer repositories, which need credentials:

- **Locally:** copy the `auth.json` block from the 1Password note "COMPOSER_AUTH and auth.json for local dev" (Technology vault) to `auth.json` in the repo root. It's gitignored. Run Composer via `lando composer …`.
- **On Pantheon:** the same JSON is the site secret `COMPOSER_AUTH` (type `env`, scope `ic`), which Integrated Composer reads on every build. `OCP_LICENSE` and `ACF_PRO_LICENSE` are site secrets (type `runtime`, scope `web`) read by `config/application.php`. Org-level secrets did not reach this site's builds, so set them per site:

  ```shell
  terminus secret:site:set <site> COMPOSER_AUTH "$(jq -c . auth.json)" --type=env --scope=ic
  terminus secret:site:set <site> OCP_LICENSE <token> --type=runtime --scope=web
  ```
- **GitHub Actions:** the repo secret `COMPOSER_AUTH` (same JSON).

### Adding plugins

Add WordPress.org plugins through Composer from [wp-packages](https://wp-packages.org) (`wp-plugin/<slug>`):

```shell
lando composer require wp-plugin/<slug>
```

Commit `composer.json` and `composer.lock`. Pantheon installs the plugin on deploy.

The Threespot standard plugins live in `upstream-configuration/composer.json` (the upstream's package), so the root `composer.json` stays this site's own. Package repositories (premium vendors, `threespot/wp-base-config`) must stay in the root `composer.json`: Composer ignores repositories declared in dependencies. Don't commit anything in `web/app/plugins/` unless it's a custom plugin that isn't on Composer (and add a `!web/app/plugins/<name>` rule to `.gitignore`).

### Custom post types and taxonomies

`threespot/wp-base-config` autoloads one file per type from:

```
web/app/mu-plugins/
├── custom-post-types/post-types/
└── custom-taxonomies/taxonomies/
```

See the [wp-base-config docs](https://github.com/threespot/wp-base-config#registering-custom-post-types-and-taxonomies).

### Checks and tests

Run from the repo root through Lando (PHP 8.3). These are the same checks GitHub Actions runs on every PR (`.github/workflows/static-checks.yml`):

```shell
lando composer ci          # lint + phpcs + phpstan + phpunit
lando composer lint        # php -l on our tracked PHP in web/app (skips plugins)
lando composer phpcs       # Pantheon-WP-Minimum (phpcs.xml.dist); fails on errors, reports warnings
lando composer phpcbf      # auto-fix what phpcs can
lando composer phpstan     # level 5 with WordPress + ACF stubs (phpstan.neon.dist + baseline)
lando composer unit-test   # PHPUnit + Brain Monkey (tests/Unit)
```

GitHub Actions needs the repo secret `COMPOSER_AUTH` (same JSON as `auth.json`) to install the premium plugins.

### Visual regression

[`@threespot/visual-regression`](https://github.com/threespot/visual-regression) (Playwright). Scenarios live in `visual-regression/scenarios.js`.

```shell
yarn install && yarn vrt:install
yarn vrt:test      # compares Pantheon dev against Lando
yarn vrt:report
```

### Example content

`threespot/wp-fixtures` is a dev dependency: `lando wp threespot fixtures load`, then `lando push --code=none --files=none --database=dev` to share it.

### Logs

`composer collect-logs -- live` downloads the PHP, nginx and MySQL logs for an environment into `logs/` (gitignored).

## Deploying

1. Open a PR. Pantheon builds a Multidev for it; check it there.
2. Merge to `main`. Pantheon builds and deploys to Dev.
3. `lando composer deploy:test` promotes to Test.
4. `lando composer deploy:live` promotes to Live.

## Starting a new site from this upstream

Full guide: awesome-threespot, *Pantheon WordPress Starter*. In short:

```shell
printf '0\n' | terminus site:create <app-name> "<Project Name>" <threespot-upstream> \
  --org=threespot --vcs-provider=github --vcs-org=Threespot \
  --repository-name=<app-name> --visibility=private --skip-clone-repo
# add the site secrets (COMPOSER_AUTH, OCP_LICENSE, ACF_PRO_LICENSE), terminus redis:enable, install WordPress
git clone git@github.com:Threespot/<app-name>.git && cd <app-name>
composer replace-fixme -- --app=<app-name> --name="<Project Name>" --domain=<www.example.org>
lando start
git add -A && git commit -m "Set up <app-name>" && git push
```

This upstream ships **with** a `composer.lock` (unlike Pantheon's): without one, Pantheon's build would have to resolve `threespot/wp-base-config` through the GitHub API, which fails unauthenticated. After an upstream update that changes dependencies, run `lando composer update --lock` and commit.

## Maintaining the upstream

*(Applies to `Threespot/wp-composer-managed` itself; delete this section in site repos if you like.)*

- `default` mirrors Pantheon's upstream; `main` carries the Threespot layer and is what Pantheon serves to sites.
- Pull Pantheon's updates:

  ```shell
  git remote add pantheon https://github.com/pantheon-systems/wordpress-composer-managed.git
  git fetch pantheon && git checkout default && git merge --ff-only pantheon/default && git push origin default
  git checkout main && git merge default    # resolve; keep our deletions of Pantheon's CI/devops files
  ```
- Shared packages go in `upstream-configuration/composer.json`; new premium/VCS repositories go in the root `composer.json`. Then regenerate and commit `composer.lock` (needs `auth.json`; run Composer with a GitHub token so `threespot/*` VCS packages resolve).
- Keep project-specific values as `fixme-*` placeholders (see `scripts/replace-fixme.php`).
