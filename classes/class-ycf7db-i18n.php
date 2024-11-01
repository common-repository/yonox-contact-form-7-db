<?php
/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 * @author     Yonox <yonox2008@gmail.com>
 */
class Yonox_Cf7_Db_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'yonox-cf7-db',
			false,
			dirname( dirname( YCF7DB_PLUGIN_BASENAME ) ) . '/languages/'
		);
	}
}
