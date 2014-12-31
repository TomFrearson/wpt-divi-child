<?php
/**
 * WPTechCentre
 *
 * Functions for WPTechCentre.
 *
 * @author      WPTechCentre
 * @version     1.0
 */

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Template redirect - if we end up on a page ensure it has the correct http/https url
 */
locate_template( 'includes/class-wpt-https.php', true, true );

/**
 * Load scripts and styles
 */ 
if( !function_exists( 'wpt_load_scripts' ) ) {
	function wpt_load_scripts() {
		wp_enqueue_style(
			'wpt_forms',
			get_stylesheet_directory_uri() . '/css/forms.css'
		);
		wp_enqueue_script(
			'wpt_skype',
			get_stylesheet_directory_uri() . '/js/skype-uri.js',
			false,
			true
		);
		/*wp_enqueue_script(
			'wpt_hangout',
			'//apis.google.com/js/platform.js',
			false,
			true
		);*/
		wp_enqueue_script(
			'wpt_sitecontrol',
			get_stylesheet_directory_uri() . '/js/getsitecontrol.js',
			false,
			true
		);
	}
}
add_action('wp_enqueue_scripts', 'wpt_load_scripts');

/**
 * Custom blog & archive page thumbnail sizes
 */
function wpt_blog_thumbnail_width( $width ) {
	if( !is_single() ) {
		return 150;
	} else {
		return $width;
	}
}
add_filter( 'et_pb_blog_image_width', 'wpt_blog_thumbnail_width');
add_filter( 'et_pb_index_blog_image_width', 'wpt_blog_thumbnail_width');

function wpt_blog_thumbnail_height( $height ) {
	if( !is_single() ) {
		return 150;
	} else {
		return $height;
	}
}
add_filter( 'et_pb_blog_image_height', 'wpt_blog_thumbnail_height');
add_filter( 'et_pb_index_blog_image_height', 'wpt_blog_thumbnail_height');

/**
 * Twitter Widget Pro hide follower count
 */
function twp_hide_follower_count( $attributes ) {
	if( !empty( $attributes['class'] ) && 'twitter-follow-button' == $attributes['class'] )
		$attributes['data-show-count'] = 'false';
	
	return $attributes;
}
add_filter( 'widget_twitter_link_attributes', 'twp_hide_follower_count' );

?>
