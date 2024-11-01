<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 * @author     Yonox <yonox2008@gmail.com>
 */
class Yonox_Cf7_Db_Activator
{
	/**
	 * Check if dependency is installed and activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ))
		{
			wp_die(
				'Please install and activate <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank"><strong>Contact Form 7</strong></a> plugin. <br>
				<a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>'
			);
		}
		else
		{
			// Create database if not exists.
			global $wpdb;
			$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';
			$ycf7db_query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $ycf7db_table_name ) );

			if ( ! $wpdb->get_var( $ycf7db_query ) == $ycf7db_table_name )
			{
				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE $ycf7db_table_name (
					ID bigint(20) NOT NULL AUTO_INCREMENT,
					form_post_id bigint(20) NOT NULL,
					form_name text NOT NULL,
					form_values longtext NOT NULL,
					submit_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					submit_status varchar(20) DEFAULT 'unread' NOT NULL,
					submit_title_page text NOT NULL,
					submit_url_page text NOT NULL,
					submit_ip varchar(50) NOT NULL,
					submit_browser text NOT NULL,
					PRIMARY KEY  (ID)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}
			
			$upload_dir = wp_upload_dir();
			$ycf7db_dirmedia = $upload_dir['basedir'] . '/ycf7db_uploads';
			
			if ( ! file_exists( $ycf7db_dirmedia ) )
			{
				wp_mkdir_p( $ycf7db_dirmedia );
			}
			
			// Register default options
			$ycf7db_default_opts = get_option('ycf7db_opts');
			
			if ( ! $ycf7db_default_opts )
			{
				$ycf7db_default_opts = array(
					'delete_db_on_uninstall' => false
				);
				add_option('ycf7db_opts', $ycf7db_default_opts);
			}
		}
	}
}
