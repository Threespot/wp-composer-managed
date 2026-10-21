# CLAUDE.md

Project-specific context for this theme. The `README.md` is the upstream Roots Sage marketing readme and does not describe how this fork is wired up.

## General Behavior

When answering WordPress/PHP questions, provide a direct answer first before exploring the codebase. Only investigate files if the user asks for project-specific context or if the question genuinely requires it.

## Code Style / Conventions

Never introduce Unicode smart quotes or curly quotes in any code file (PHP, JS, SCSS, Blade, JSON, etc.). Always use straight quotes (' and "). Do not alter existing comments solely to replace curly quotes with straight quotes — only avoid introducing new ones. A PostToolUse hook in `.claude/settings.json` scans added lines after every Edit and will block if any contain curly quotes.

**CSS class naming — modified BEM.** Component root is PascalCase (`RelatedContent`); elements are `Block-element` with a **single hyphen**, never the chained `Block__parent__child` form. Keep class names as short as possible: drop an intermediate ancestor name when it isn't needed for clarity or specificity, or when the element would still make sense if the markup were later moved out of that wrapper. Prefer:

```html
<div class="RelatedContent">
  <ul class="RelatedContent-list">
    <li class="RelatedContent-item">
      <a class="RelatedContent-link">Example</a>
    </li>
  </ul>
</div>
```

not the fully-chained `RelatedContent__list__item__link`. Standard BEM modifier syntax (`Block-element--modifier`) still applies.

## What this is

A Threespot fork of [Roots Sage 11](https://roots.io/sage/) (current branch: `sage-11`). Sage provides Blade templating + Vite + Acorn; this theme layers Threespot's shared WordPress conventions on top via the `threespot/wp-base-config` package.

The `style.css` theme header still says `fixme-project-name` / `fixme-domain` — replace per-site. The Vite HMR host in `vite.config.js` is also `fixme-app-name.lndo.site` (Lando local dev) and must be set per-site.

## Toolchain

- PHP `>=8.2`
- Node `22.18.0` (see `.tool-versions`)
- Yarn `4.14.1` (Berry, configured via `.yarnrc.yml` + `.yarn/`). `.tool-versions` lists `yarn 1.22.22` for asdf shim only — actual package manager is Yarn 4.
- Composer for PHP deps; Acorn boots from `functions.php`.

## The `threespot/wp-base-config` integration (load-bearing)

Most shared frontend/admin behavior lives in the `threespot/wp-base-config` Composer package — it is **not** in this repo's `vendor/` directory. It is installed at the **site** level (Bedrock-style layout) and loaded as an MU-plugin.

The theme reaches it via relative symlinks four levels up from the theme root:

```
.stylelintrc.cjs       -> ../../../../vendor/threespot/wp-base-config/dist/.stylelintrc.cjs
eslint.config.js       -> ../../../../vendor/threespot/wp-base-config/dist/eslint.config.js
postcss.config.cjs     -> ../../../../vendor/threespot/wp-base-config/dist/postcss.config.cjs
.pxtorem-config.cjs    -> ../../../../vendor/threespot/wp-base-config/dist/.pxtorem-config.cjs
threespot-vite-base.js -> ../../../../vendor/threespot/wp-base-config/dist/vite-base.js
```

This means:
- The theme expects to live at `web/app/themes/<theme>/` (Pantheon composer-managed; `wp-content` on older Bedrock builds) inside a Bedrock-style site where `vendor/threespot/wp-base-config/` exists at the site root.
- The symlinks will appear broken if you open this directory in isolation. That is expected — the package is provided by the parent site, not the theme.
- All Vite/PostCSS/ESLint/Stylelint configs require `NODE_OPTIONS=--preserve-symlinks` (already set on every npm script in `package.json`). Do not strip it.

The package supplies (non-exhaustive):
- Admin polish, login styling, ACF promo hiding, WP Activity Log overrides
- The PHP helper namespaces `Threespot\Wp\Helpers\*` and `Threespot\Wp\Sage\*` (`vite_asset_url`, `inline_critical_css`, `register_blade_directives`)
- A filter-based config surface — the theme wires URLs in via `add_filter('threespot/...')` calls in `functions.php`

If a Blade template calls something like `{!! App\svg(...) !!}`, the implementation is the shim in `app/legacy-helpers.php`, which forwards to `\Threespot\Wp\Helpers\*`. **New code should call `\Threespot\Wp\Helpers\*` directly** — the `App\*` shims exist only for backward compatibility.

## What the theme owns vs. what the package owns

| Concern | Lives in |
| --- | --- |
| Per-site image sizes (`IMAGE_SIZES` global) | `app/image-sizes.php` |
| Enqueue of `main.scss` / `main.js` / inlined `critical.scss` | `app/theme-assets.php` |
| `App\*` backward-compat shims | `app/legacy-helpers.php` |
| Acorn provider registration | `app/Providers/ThemeServiceProvider.php` |
| Blade views, SCSS, JS modules | `resources/` |
| ACF field groups | `acf-json/` (JSON sync target) |
| Defer policy, jQuery override, admin/login styling, oEmbed cleanup, helpers | `threespot/wp-base-config` (NOT this repo) |

Note: script `defer` is handled by the package's `AssetConfig`. `functions.php` opts `main-js` into `type="module"` (which excludes IE11) via the `threespot/assets/module_script_handles` filter — do not also add a `defer` attribute manually.

## Vite entry points

Defined in `vite.config.js`:
- `resources/scripts/main.js`, `resources/scripts/gutenberg.js`
- `resources/styles/main.scss`, `resources/styles/critical.scss`, `resources/styles/admin.scss`, `resources/styles/login.scss`, `resources/styles/gutenberg.scss`

`critical.scss` is inlined into `<head>` by `inline_critical_css()` (from the package), not enqueued.

The Vite asset URL helper is `Threespot\Wp\Sage\vite_asset_url()`. All `wp_enqueue_*` calls and the `threespot/.../*_url` filters resolve URLs through it so dev/HMR vs. built/hashed paths work transparently.

## Scripts

```
yarn start          # vite dev server (HMR over the mkcert host in vite.config.js)
yarn build          # production build into public/build
yarn lint:css       # stylelint over resources/**/*.{css,scss}
yarn lint:js        # eslint over resources/scripts/**/*.{js,jsx}
yarn translate      # generate .pot + update .po files
yarn translate:compile  # build .mo + JSON translations
```

All run with `NODE_OPTIONS=--preserve-symlinks` so the linked shared configs resolve correctly.

PHP formatting: `laravel/pint` (`vendor/bin/pint`).

## Per-site setup checklist

When cloning this theme into a new site, the items that need replacing:
1. `style.css` theme header — `fixme-project-name`, `fixme-domain`
2. `vite.config.js` — `HOST = 'fixme-app-name.lndo.site'`
3. `app/image-sizes.php` — confirm `IMAGE_SIZES` and `add_image_size(...)` calls match the project
4. `functions.php` `threespot/admin/is_internal_user` filter if internal-user detection should differ from `@threespot.com`
5. The parent Bedrock site must have `threespot/wp-base-config` installed at `<site>/vendor/threespot/wp-base-config/` for the symlinks to resolve

## ACF

- Block definitions use `mwdelaney/sage-acf-gutenberg-blocks` — block Blade files live under `resources/views/blocks/` (auto-registered).
- ACF field groups are JSON-synced into `acf-json/`. Edits made through the WP admin write here; commit the resulting JSON.

## Building custom ACF blocks

Blocks are auto-registered by `mwdelaney/sage-acf-gutenberg-blocks` (a theme Composer dep): it scans `resources/views/blocks/*.blade.php`, parses the metadata header comment, and registers `acf/<filename-slug>`. There is no manual `acf_register_block_type` call. Use `related-content` as a reference implementation.

**Before building a new component or block, ask the requester two things:**
- **Styles**: whether to write the full component styles or just scaffold the SCSS file (write out the class names / empty rules only). The requester often prefers to write the styles themselves.
- **Icon**: offer a few block-icon options to choose from rather than picking one. Prefer a [Dashicon](https://developer.wordpress.org/resource/dashicons/) slug (e.g. `Icon: megaphone`) before reaching for an inline SVG from a 3rd-party open-source set; if the codebase already uses an icon set, pick the new icon from that same set for consistency.

To add a block named `<slug>` (kebab-case filename → block `acf/<slug>`):

1. **Block view** — `resources/views/blocks/<slug>.blade.php`. Opens with a metadata header comment (`Title`, `Description`, `Category: threespotblock`, `Icon`, `Keywords`, `Mode: preview`, `SupportsAnchor: true`, `SupportsMultiple: true`, `SupportsAlign: false`). Add a `PostTypes` line only to *restrict* availability; omit it to allow all types including CPTs. Below the header, `get_field()` each field into a variable, then `@include('partials.blocks.<slug>', [...])`. `$block` is available in the partial automatically (Blade `@include` inherits parent scope).
2. **Partial** — `resources/views/partials/blocks/<slug>.blade.php`, holds the markup. Wrap in `@if (is_admin() || <required-field>)` so the editor always previews but empty blocks skip on the front end. The root element carries `class="<Pascal> {{ $block ? $block['classes'] : '' }}" @if($block) id="{{ $block['id'] }}" @endif`.
3. **Field group JSON** — in `acf-json/`, set the `location` value to `acf/<slug>`. Prefer editing fields in the admin and committing the resulting JSON.

**Conventions**
- **Field names: prefix with the block slug** — `large_promo_heading`, not `heading`. Keeps `get_field()` calls unambiguous across blocks. Match the local PHP variable names to the field names.
- **Markup**: modified BEM off the block name (see Code Style / Conventions above) — `.LargePromo`, `.LargePromo-heading`, etc.
- **Styles**: `resources/styles/components/_<slug>.scss` starting with `@use "@styles/globals" as *;`, `@forward`ed (alphabetically) in `components/_index.scss`.

**ACF field defaults** (apply unless told otherwise):
- **Layout**: for a block with a few text/link fields plus an image, wrap the text/link fields in an ACFE Column field at width `8/12` and the image in a Column at width `4/12`.
- **Short description text** (e.g. `*_description`): use a **WYSIWYG** field, not a textarea, so editors get basic styling and links. Settings (via the ACF Extended plugin): height `90`, allowed elements `p,strong,b,em,i,a[href]`, toolbar "Very Simple", and "Show Media Upload Buttons" disabled.
- **Label placement**: Settings → Presentation → set to **Top aligned**.
- **Instructions**: leave Presentation → Instructions **empty** — the requester adds these themselves when needed.
- **Content-picker fields** (relationship, post object, page link, etc.): always filter to **published** content only.
- **Stylized field variants**: always enable them (e.g. "Stylised Button" in repeater fields).

**Verify before done**: create a test page with the block populated, then screenshot desktop + mobile. ACF local JSON loads automatically, so a new block works without a DB sync.

## Conventions

- 2-space indent everywhere except `*.php` (4 spaces). Blade and `resources/views/**.php` are 2 spaces. Single quotes (see `.editorconfig`).
- Don't add a new `App\<helper>()`; either use the package's `Threespot\Wp\Helpers\*` directly or, if a shim is genuinely needed for an existing template, add it to `app/legacy-helpers.php` matching the existing pattern.
