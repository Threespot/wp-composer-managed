<?php
namespace App;

/**
 * Shims for functions imported from threespot/wp-base-config
 * https://github.com/Threespot/wp-base-config/
 */
function append_icon(...$args) {
	return \Threespot\Wp\Helpers\append_icon(...$args);
}

function bis_srcset(...$args) {
	return \Threespot\Wp\Helpers\bis_srcset(...$args);
}

function bytes_to_human_size(...$args) {
	return \Threespot\Wp\Helpers\bytes_to_human_size(...$args);
}

function clean_wysiwyg_markup(...$args) {
	return \Threespot\Wp\Helpers\clean_wysiwyg_markup(...$args);
}

/**
 * DEPRECATED: Only use if necessary in legacy codebases
 *
 * @deprecated Use bis_srcset() instead.
 */
function fly_srcset(...$args) {
	return \Threespot\Wp\Helpers\fly_srcset(...$args);
}

function format_video_iframe(...$args) {
	return \Threespot\Wp\Helpers\format_video_iframe(...$args);
}

function get_custom_post_types(...$args) {
	return \Threespot\Wp\Helpers\get_custom_post_types(...$args);
}

function get_img_alt(...$args) {
	return \Threespot\Wp\Helpers\get_img_alt(...$args);
}

function get_primary_term(...$args) {
	return \Threespot\Wp\Helpers\get_primary_term(...$args);
}

function img_tag(...$args) {
	return \Threespot\Wp\Helpers\img_tag(...$args);
}

function is_external(...$args) {
	return \Threespot\Wp\Helpers\is_external(...$args);
}

function nowrap(...$args) {
	return \Threespot\Wp\Helpers\nowrap(...$args);
}

function obfuscate(...$args) {
	return \Threespot\Wp\Helpers\obfuscate(...$args);
}

function strip_p_tags(...$args) {
	return \Threespot\Wp\Helpers\strip_p_tags(...$args);
}

function strip_quotes(...$args) {
	return \Threespot\Wp\Helpers\strip_quotes(...$args);
}

function svg(...$args) {
	return \Threespot\Wp\Helpers\svg(...$args);
}

function trim_excerpt(...$args) {
	return \Threespot\Wp\Helpers\trim_excerpt(...$args);
}
