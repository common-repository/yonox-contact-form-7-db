<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://wordpress.org/plugins/yonox-cf7-db
 * @since      1.0.0
 *
 * @package    Yonox_Cf7_Db
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$ycf7db_options = get_option( 'ycf7db_opts' );
		
if ( isset( $ycf7db_options['delete_db_on_uninstall'] ) && $ycf7db_options['delete_db_on_uninstall'] )
{
	global $wpdb;
	$ycf7db_table_name = $wpdb->prefix.'ycf7db_submissions';
	$sql = "DROP TABLE IF EXISTS $ycf7db_table_name";
	$wpdb->query($sql);
	
	delete_option( 'ycf7db_opts' );
}