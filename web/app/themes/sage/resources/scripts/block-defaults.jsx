//------------------------------------------------------------------------
// Custom block-editor field overrides
//
// Threespot defaults (heading text-wrap + margins, list hide-bullets,
// column sticky toggle) live in threespot/wp-base-config and are imported
// below. Add project-specific block attribute/control filters underneath
// the import using your own namespace, e.g.
// addFilter('blocks.registerBlockType', 'my-site/extra-attrs', ...).
//
// https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/
//------------------------------------------------------------------------
import '@threespot-base-config/block-defaults.jsx';
