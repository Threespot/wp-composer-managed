<?php
namespace App;

/**
 * OPTIONAL: Pullquote block: prepend a decorative quote icon, strip the leading quote
 * in the quote text, and add a closing quote if one is missing.
 */
// add_filter('render_block_core/pullquote', function (string $block_content): string {
//     // OPTIONAL: Prepend a quote icon using SVG sprite syntax
//     $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 36" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="48" height="36" class="wp-block-pullquote__icon"><use href="#sprite-quote"></use></svg>';
//
//     return preg_replace_callback(
//         '#(<blockquote[^>]*>\s*<p[^>]*>)(.*?)(</p>)#s',
//         static function (array $m) use ($icon): string {
//             $text = $m[2];
//
//             // Strip a leading opening quote (straight or curly)
//             $text = preg_replace('/^(\s*(?:<[^>]+>\s*)*)["\x{201C}]\s*/u', '$1', $text, 1);
//
//             // Add a closing quote if missing
//             if (!preg_match('#["\x{201D}]\s*(?:</?[^>]+>\s*)*$#u', $text)) {
//                 $text = preg_replace('#(\s*(?:</?[^>]+>\s*)*)$#u', "\u{201D}" . '$1', $text, 1);
//             }
//
//             // Prepend the decorative quote icon before <blockquote>
//             return $icon . $m[1] . $text . $m[3];
//         },
//         $block_content
//     );
// }, 10, 1);

/**
 * Upgrade all blocks to ACF Blocks v3
 * @link https://www.advancedcustomfields.com/resources/acf-blocks-v3/
 */
add_filter('acf/blocks/default_block_version', function($version, $block) {
    return 3;
}, 10, 2 );

/**
 * OPTIONAL: Inline editing for ACF blocks (reference — uncomment per block).
 *
 * Lets editors edit fields directly in the block preview. Requires ACF block
 * v3+ (enabled globally above) and WP 6.3+.
 * @link https://www.advancedcustomfields.com/resources/enabling-inline-editing/
 * @link https://www.advancedcustomfields.com/resources/inline-editing-for-acf-blocks/
 *
 * ---------------------------------------------------------------------------
 * OPTION A — Auto inline editing (registration side; this file).
 * Best for simple blocks made mostly of Text / Text Area fields with little or
 * no conditional (@if) markup. ACF auto-detects any element whose only content
 * is a single field's value and makes it editable. Enable per block via the
 * sage-acf-gutenberg-blocks per-block filter (block slug in the hook name):
 * ---------------------------------------------------------------------------
 * add_filter('sage/blocks/<block-slug>/register-data', function (array $data): array {
 *     $data['auto_inline_editing'] = true;
 *     return $data;
 * });
 *
 * Caveats for OPTION A:
 * - WYSIWYG fields are NOT auto text-editable (they support toolbar editing only).
 * - Fields returning arrays (image with return_format id, link/CTA) never inline-edit.
 * - Blocks with per-field @if guards can misbehave because auto mode injects
 *   placeholder values into every field during the preview render. For those,
 *   prefer OPTION B.
 *
 * ---------------------------------------------------------------------------
 * OPTION B — Manual inline editing attributes (markup side; the block PARTIAL,
 * not this file). More reliable for blocks with conditional markup, and the
 * only way to inline-edit a WYSIWYG. Do NOT set auto_inline_editing when using
 * this. Both helpers return '' outside the editor preview, so front-end markup
 * is unchanged. In resources/views/partials/blocks/<slug>.blade.php:
 * ---------------------------------------------------------------------------
 * Text / Text Area (type directly in the preview):
 *   <h2 class="Block-heading" {!! acf_inline_text_editing_attrs('block_heading') !!}>{!! $heading !!}</h2>
 *
 * WYSIWYG and other non-text fields (click a toolbar button -> popover editor):
 *   <div class="Block-body" {!! acf_inline_toolbar_editing_attrs([
 *       [
 *           'field_name' => 'block_body',
 *           'field_label' => 'Edit body',
 *           'field_icon' => '<svg ...>...</svg>', // optional; defaults to the block icon
 *       ],
 *   ]) !!}>@html($body)</div>
 *
 * Note: an @if-guarded empty field renders no element, so it has no inline
 * target until it holds a value — editors add the first value via the sidebar.
 */
