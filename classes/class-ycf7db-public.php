<?php
/**
 * The Front-End functionality of the plugin.
 *
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 * @author     Yonox <yonox2008@gmail.com>
 */
class Yonox_Cf7_Db_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the Stylesheets for the front-end of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, YCF7DB_PLUGIN_URL . 'assets/css/yonox-cf7-db-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the front-end of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, YCF7DB_PLUGIN_URL . 'assets/js/yonox-cf7-db-public.js', array( 'jquery' ), $this->version, false );
	}
	
	/**
	 * Capture and save data from Contact Form 7 to database.
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_capture_form( $form_tag )
	{
		global $wpdb;
		$table_name			= $wpdb->prefix . 'ycf7db_submissions';
		$upload_dir			= wp_upload_dir();
		$ycf7db_dirmedia 	= $upload_dir['basedir'] . '/ycf7db_uploads';
		$time_now			= time();

		$cf7form = WPCF7_Submission::get_instance();

		if ( $cf7form )
		{
			$black_list   = array('_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag',
			'_wpcf7_is_ajax_call','cfdb7_name', '_wpcf7_container_post','_wpcf7cf_hidden_group_fields',
			'_wpcf7cf_hidden_groups', '_wpcf7cf_visible_groups', '_wpcf7cf_options','g-recaptcha-response');

			$cf7data		= $cf7form->get_posted_data();
			$cf7files		= $cf7form->uploaded_files();
			$uploaded_files	= array();

			foreach ( $cf7files as $file_key => $file )
			{
				array_push( $uploaded_files, $file_key );
				copy( $file, $ycf7db_dirmedia . '/' . $time_now . '-' . basename($file) );
			}

			$form_data = array();

			foreach ( $cf7data as $key => $value )
			{
				if ( !in_array($key, $black_list) && !in_array($key, $uploaded_files) )
				{
					$tmpVal = $value;

					if ( ! is_array($value) )
					{
						$bl   = array('\"',"\'",'/','\\','"',"'");
						$wl   = array('&quot;','&#039;','&#047;', '&#092;','&quot;','&#039;');

						$tmpVal = str_replace( $bl, $wl, $tmpVal );
					}

					$form_data[$key] = $tmpVal;
				}
				if ( in_array($key, $uploaded_files) )
				{
					$form_data[$key.'_ycf7db_file'] = $time_now.'-'.$value;
				}
			}

			$form_post_id		= absint($form_tag->id());
			$form_title			= sanitize_text_field($form_tag->title());
			$form_values		= mysql_real_escape_string(serialize( $form_data ));
			$submit_date		= current_time('Y-m-d H:i:s');
			$submit_title_page	= get_the_title($cf7form->get_meta('container_post_id'));
			$submit_url_page	= esc_url_raw($cf7form->get_meta('url'));
			$submit_ip			= YCF7DB_Functions::clientIPAddress();
			$submit_browser		= $cf7form->get_meta('user_agent');
			
			$queryParams = $wpdb->prepare( "INSERT INTO $table_name 
				( form_post_id, form_name, form_values, submit_date, submit_title_page, submit_url_page, submit_ip, submit_browser ) 
				VALUES ( %d, %s, %s, %s, %s, %s, %s, %s )", 
				$form_post_id, $form_title, $form_values, $submit_date, $submit_title_page, $submit_url_page, $submit_ip, $submit_browser 
			);

			$wpdb->query( $queryParams );
		}
	}
}
