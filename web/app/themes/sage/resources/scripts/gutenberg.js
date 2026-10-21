//------------------------------------------------------------------------
// Custom block editor overrides
//------------------------------------------------------------------------
import domReady from '@wordpress/dom-ready';
import { select, subscribe } from '@wordpress/data';
import { unregisterBlockStyle, unregisterBlockVariation } from '@wordpress/blocks';
import { setLocaleData } from '@wordpress/i18n';
// Import Threespot default overrides
import '@threespot-base-config/gutenberg-defaults.js';

// Override core editor strings (the "default" text domain). The key must match
// the source string exactly, including punctuation. If this text changes in a
// future release this will no longer work.
setLocaleData({
  'Display a plus icon next to the accordion header.': ['NOTE: This setting is intentionally ignored by the current theme.'],
}, 'default');

// Unregister default block styles
// Note: Run this JS in the browser console to see the full block styles list
//
//   wp.blocks.getBlockTypes()
//     .filter(b => b.styles?.length)
//     .forEach(b => console.log(b.name, b.styles.map(s => s.name)));
//
const stylesToUnregister = [
  // ['core/button', 'outline'],
  ['core/image', 'rounded'],
  ['core/quote', 'plain'],
  ['core/separator', 'dots'],
  ['core/separator', 'wide'],
  // ['core/site-logo', 'rounded'],
  // ['core/table',     'stripes'],
];

// Unregister block variations
// Note: Run this JS in the browser console to see the full block variants list
//
//   wp.blocks.getBlockTypes()
//     .map(b => ({ name: b.name, variations: wp.blocks.getBlockVariations(b.name) }))
//     .filter(b => b.variations?.length)
//     .forEach(b => {
//       console.log(b.name);
//       b.variations.forEach(v => {
//         console.log('  -', v.name, v.scope ? `(${v.scope.join(', ')})` : '(all)');
//       });
//     });
//
const variationsToUnregister = [
  // ['core/group', 'group-grid'],
  // ['core/group', 'group-row'],
  // ['core/group', 'group-stack'],
];

domReady(() => {
  // DOM ready can fire before core blocks finish registering, so calling
  // unregisterBlockStyle()/unregisterBlockVariation() directly is a race.
  // subscribe() runs on every wp.data store change; we poll the block
  // registry until each style/variation is present, then unregister it.
  // Once both lists are empty, unsubscribe.
  const remainingStyles = [...stylesToUnregister];
  const remainingVariations = [...variationsToUnregister];

  const unsubscribe = subscribe(() => {
    // Unregister custom styles
    for (let i = remainingStyles.length - 1; i >= 0; i--) {
      const [block, style] = remainingStyles[i];
      const styles = select('core/blocks').getBlockStyles(block);

      if (styles?.some((s) => s.name === style)) {
        unregisterBlockStyle(block, style);
        remainingStyles.splice(i, 1);
      }
    }

    // Unregister custom variations
    for (let i = remainingVariations.length - 1; i >= 0; i--) {
      const [block, variation] = remainingVariations[i];
      const variations = select('core/blocks').getBlockVariations(block);

      if (variations?.some((v) => v.name === variation)) {
        unregisterBlockVariation(block, variation);
        remainingVariations.splice(i, 1);
      }
    }

    if (remainingStyles.length === 0 && remainingVariations.length === 0) {
      unsubscribe();
    }
  });

  // OPTIONAL: Register custom styles
  // wp.blocks.registerBlockStyle('core/button', {
  //   name: 'example',
  //   label: 'Exmaple',
  // });
});

// Sage 10 boilerplate
/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
import.meta.webpackHot?.accept(console.error);
