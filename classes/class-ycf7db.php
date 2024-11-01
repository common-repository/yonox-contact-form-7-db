<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * front-end hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 * @author     Yonox <yonox2008@gmail.com>
 */
class Yonox_Cf7_Db
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Yonox_Cf7_Db_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the front-end of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if ( defined( 'YCF7DB_VERSION' ) ) {
			$this->version = YCF7DB_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		
		$this->plugin_name = 'yonox-cf7-db';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Yonox_Cf7_Db_Loader. Orchestrates the hooks of the plugin.
	 * - Yonox_Cf7_Db_i18n. Defines internationalization functionality.
	 * - YCF7DB_Functions. Defines all core functions.
	 * - Yonox_Cf7_Db_Admin. Defines all hooks for the admin area.
	 * - Yonox_Cf7_Db_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-i18n.php';
		
		/**
		 * The class responsible for all core functions
		 */
		require_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-functions.php';
		
		/**
		 * The class responsible for Spout (*.xlsx) exports
		 */
		require_once YCF7DB_PLUGIN_DIR . 'Spout/Autoloader/autoload.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the front-end of the site.
		 */
		require_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-public.php';

		$this->loader = new Yonox_Cf7_Db_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Yonox_Cf7_Db_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Yonox_Cf7_Db_i18n();
		
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Yonox_Cf7_Db_Admin( $this->get_plugin_name(), $this->get_version() );

		// Register menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ycf7db_admin_menu' );
		
		// Register plugin action links
		$this->loader->add_filter( 'plugin_action_links_' . YCF7DB_PLUGIN_BASENAME, $plugin_admin, 'ycf7db_plugin_action_links', 10, 1 );
		
		// Admin Menu Icon Font
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_menu_icon' );
		
		// Load styles and scripts only into plugin pages
		if ( isset($_GET['page']) && substr($_GET['page'], 0, 7) == 'ycf7db_' )
		{
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		}
		
		// Register Ajax Actions
		$this->loader->add_action( 'wp_ajax_ycf7db_list_forms', $plugin_admin, 'ycf7db_list_forms' );
		$this->loader->add_action( 'wp_ajax_ycf7db_formsubmits', $plugin_admin, 'ycf7db_formsubmits' );
		$this->loader->add_action( 'wp_ajax_ycf7db_viewsubmit', $plugin_admin, 'ycf7db_viewsubmit' );
		$this->loader->add_action( 'wp_ajax_ycf7db_export_records', $plugin_admin, 'ycf7db_export_records' );
		$this->loader->add_action( 'wp_ajax_ycf7db_delete_records', $plugin_admin, 'ycf7db_delete_records' );
	}

	/**
	 * Register all of the hooks related to the front-end functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Yonox_Cf7_Db_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wpcf7_before_send_mail', $plugin_public, 'ycf7db_capture_form' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    Yonox_Cf7_Db_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
