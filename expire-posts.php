<?php

/**
 * Simple Post Auto-Expiry 
 *
 *
 * @since             1.0.0
 * @package           Expire_Posts
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Post Auto-Expiry
 * Description:       This plugin is built to be a starting point for custom auto expiration of posts or custom post types.
 * Version:           1.0.0
 * Author:            Flynn O'Connor
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       expire-posts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_expire_posts() {
	wp_schedule_event( time(), 'daily', 'factors_expiry_event' );
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_expire_posts() {
	wp_clear_scheduled_hook( 'factors_expiry_event' );
}

register_activation_hook( __FILE__, 'activate_expire_posts' );
register_deactivation_hook( __FILE__, 'deactivate_expire_posts' );

add_action( 'factors_expiry_event', 'factors_daily_expiry' );

/**
 * This function goes through all the posts of the type set and 
 * looks for a custom meta field with an expiry date. All posts
 * with a date that has passed will be set to draft. 
 */
function factors_daily_expiry(){
	// change this to target different post types for expiry
	$post_type = array( 'page' ); 
	// changes this to target the metakey with the expiry value. 
	$meta_key = 'expiry_date';
	// change this to use a different date format
	$current_date_format = "Ymd"; 
	
	$expiry_args = array(
		'post_type' => $post_type,
		'posts_per_page' => -1
	);
	
	$potential_expired_posts = get_posts($expiry_args);
	
	foreach($potential_expired_posts as $e ){

		$expiry_date = get_post_meta( $e->ID, $meta_key, true );

		if( !empty( $expiry_date ) ){

			if( $expiry_date < date( $current_date_format ) ){

				$post_args = array(
					'ID' 			=> $e->ID,
					'post_status' 	=> 'draft'
				); 
				wp_update_post( $post_args );
			
			}
		}

	} 

}