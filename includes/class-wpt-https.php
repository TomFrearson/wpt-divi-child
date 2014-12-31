<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPT_HTTPS class.
 *
 * @class 		WPT_HTTPS
 * @version		1.0
 * @package		WPTechCentre/Classes
 * @category	Class
 * @author 		WPTechCentre
 */
class WPT_HTTPS {

	/**
	 * Hook in our HTTPS functions if we're on the frontend. This will ensure any links output to a page (when viewing via HTTPS) are also served over HTTPS.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			
			// HTTPS urls with SSL on
			$filters = array( 'post_thumbnail_html', 'wp_get_attachment_url', 'wp_get_attachment_image_attributes', 'wp_get_attachment_url', 'option_stylesheet_url', 'option_template_url', 'script_loader_src', 'style_loader_src', 'template_directory_uri', 'stylesheet_directory_uri', 'site_url' );

			foreach ( $filters as $filter ) {
				add_filter( $filter, 'WPT_HTTPS::force_https_url' );
			}
			
			add_filter( 'page_link', array( $this, 'force_https_page_link' ), 10, 2 );
			add_action( 'template_redirect', array( $this, 'force_https_template_redirect' ) ); //force HTTPS
			add_action( 'template_redirect', array( $this, 'unforce_https_template_redirect' ) ); //unforce HTTPS
		}
	}

	/**
	 * force_https_url function.
	 *
	 * @param mixed $content
	 * @return string
	 */
	public static function force_https_url( $content ) {
		if ( is_ssl() ) {
			if ( is_array( $content ) )
				$content = array_map( 'WPT_HTTPS::force_https_url', $content );
			else
				$content = str_replace( 'http:', 'https:', $content );
		}
		return $content;
	}

	/**
	 * Force a post link to be SSL if needed
	 *
	 * @param  string $post_link
	 * @param  object $post
	 * @return string
	 */
	public function force_https_page_link( $link, $page_id ) {
		if ( in_array( $page_id, array( get_option( 'wpt_checkout_page_id' ), get_option( 'wpt_myaccount_page_id' ) ) ) ) {
			$link = str_replace( 'http:', 'https:', $link );
		} elseif ( get_option('wpt_unforce_ssl_checkout') == 'yes' ) {
			$link = str_replace( 'https:', 'http:', $link );
		}
		return $link;
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url
	 */
	public function force_https_template_redirect() {
		if ( ! is_ssl() && (
			is_page( 'signup-for-our-standard-plan' ) || 
			is_page( 'signup-for-our-professional-plan' ) || 
			is_page( 'signup-for-our-enterprise-plan' ) || 
			is_page( 'paypal' ) || 
			is_page( 'payment-confirmed' ) 
			) ) {

			if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^http://|', 'https://', $_SERVER['REQUEST_URI'] ) );
				exit;
			} else {
				wp_safe_redirect( 'https://' . ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI'] );
				exit;
			}
		}
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url
	 */
	public function unforce_https_template_redirect() {
		if ( is_ssl() && $_SERVER['REQUEST_URI'] && 
			! is_page( 'signup-for-our-standard-plan' ) && 
			! is_page( 'signup-for-our-professional-plan' ) && 
			! is_page( 'signup-for-our-enterprise-plan' ) && 
			! is_page( 'paypal' ) && 
			! is_page( 'payment-confirmed' ) 
			) {

			if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^https://|', 'http://', $_SERVER['REQUEST_URI'] ) );
				exit;
			} else {
				wp_safe_redirect( 'http://' . ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI'] );
				exit;
			}
		}
	}
}

new WPT_HTTPS();
