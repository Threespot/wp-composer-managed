import { createLogger, defineConfig, mergeConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import mkcert from 'vite-plugin-mkcert';
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons';
import eslint from 'vite-plugin-eslint';
import stylelint from 'vite-plugin-stylelint';
import browserslist from 'browserslist';
import { browserslistToTargets, Features } from 'lightningcss';

import { threespotViteBase } from './threespot-vite-base.js';

const HOST = 'fixme-app-name.lndo.site';

export default mergeConfig(
  threespotViteBase({
    createLogger,
    wordpressPlugin,
    wordpressThemeJson,
    viteStaticCopy,
    createSvgIconsPlugin,
    eslint,
    stylelint,
    browserslist,
    browserslistToTargets,
    Features,
    jqueryPath: 'scripts/lib/jquery-4.0.0.min.js',
  }),
  defineConfig({
    // Pantheon composer-managed (Bedrock) layout: themes live in web/app, so
    // assets are served from /app/... (threespotViteBase assumes /wp-content).
    base: '/app/themes/sage/public/build/',
    plugins: [
      laravel({
        input: [
          'resources/scripts/block-defaults.jsx',
          'resources/scripts/gutenberg.js',
          'resources/scripts/main.js',
          'resources/styles/admin-all.scss',
          'resources/styles/admin-fields.scss',
          'resources/styles/critical.scss',
          'resources/styles/gutenberg.scss',
          'resources/styles/login.scss',
          'resources/styles/main.scss',
        ],
        refresh: [
          'resources/views/**/*.blade.php',
        ],
        url: process.env.APP_URL,
      }),
      mkcert({ hosts: [HOST] }),
    ],
    server: {
      hmr: {
        host: HOST,
      },
    },
  }),
);
