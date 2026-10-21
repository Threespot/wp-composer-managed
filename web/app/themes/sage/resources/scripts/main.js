// SVG sprite plugin
// https://github.com/vbenjs/vite-plugin-svg-icons
import 'virtual:svg-icons-register';

// OPTIONAL: Import images that need to be in the Vite manifest for Blade templates.
// The ?url suffix tells Vite to only export the URL without bundling the image
// import '@images/example-logo.png?url';

// Detect input method in order to hide outlines in an accessible manner
// https://github.com/ten1seven/what-input
import 'what-input';

// Expandable components
import Expandables from './modules/expandables';
new Expandables('#main [data-expands]');
