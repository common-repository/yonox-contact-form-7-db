<?php
/**
 * Plugin Core Functions
 *
 * This class defines all plugin core functions
 *
 * @since      1.0.0
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 * @author     Yonox <yonox2008@gmail.com>
 */

if ( !defined('ABSPATH') ) {
	exit;
}

class YCF7DB_Functions
{
	/**
	 * Table Columns Names from DB.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   array
	 */
	public static function DBTableColumns()
	{
		$DBTable_columns = array(
			'fmlist_dbtable' => array(
				'form_post_id'	=> array( 'title' => esc_html__('ID', 'yonox-cf7-db'),			'data' => 'form_post_id',	'orderable' => false, 'searchable' => false, 'width' => '24' ),
				'form_name'		=> array( 'title' => esc_html__('Form Name', 'yonox-cf7-db'),	'data' => 'form_name',		'orderable' => false, 'className' => 'title-link' )
			),
			'sblist_dbtable' => array(
				'ID'				=> array(),
				'form_name'			=> array( 'title' => esc_html__('Form Name', 'yonox-cf7-db'),	'data' => 'form_name',			'orderable' => false ),
				'submit_title_page'	=> array( 'title' => esc_html__('Page Form', 'yonox-cf7-db'),	'data' => 'submit_title_page',	'orderable' => false ),
				'submit_date'		=> array( 'title' => esc_html__('Submit Date', 'yonox-cf7-db'),	'data' => 'submit_date',		'orderable' => false, 'className' => 'text-center' ),
				// 'submit_browser'	=> array( 'title' => esc_html__('Browser', 'yonox-cf7-db'),		'data' => 'submit_browser',		'orderable' => false, 'className' => 'text-center' ),
				'submit_ip'			=> array( 'title' => esc_html__('IP Address', 'yonox-cf7-db'),	'data' => 'submit_ip',			'orderable' => false, 'className' => 'text-center' )
			)
		);
		return $DBTable_columns;
	}
	
	/**
	 * Additional Columns.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   array
	 */
	public static function AdditionalColumns()
	{
		$Additional_columns = array(
			'fmlist_adtable' => array(
				'count_submissions'	=> array(
					'title' => esc_html__('Submissions', 'yonox-cf7-db'),
					'data' => 'count_submissions',
					'orderable' => false,
					'searchable' => false,
					'className' => 'text-center',
					'width' => '100'
				),
				'form_actions' => array(
					'title'			=> esc_html__('Actions', 'yonox-cf7-db'),
					'data'			=> 'form_actions',
					'orderable'		=> false,
					'searchable'	=> false,
					'className'		=> 'text-center',
					'width'			=> '100'
				)
			),
			'sblist_adtable' => array(
				'fsubm_checkbox' => array(
					'title'			=> '<div class="custom-control custom-checkbox ml-2"><input type="checkbox" class="custom-control-input d-none" id="submChkBoxRoot"><label class="custom-control-label" for="submChkBoxRoot"></label></div>',
					'data'			=> 'fsubm_checkbox',
					'orderable'		=> false,
					'searchable'	=> false,
					'className'		=> 'text-center',
					'width'			=> '20'
				),
				'fsubm_fields' => array(
					'title'			=> esc_html__('Fields', 'yonox-cf7-db'),
					'data'			=> 'fsubm_fields',
					'orderable'		=> false,
					'searchable'	=> false,
					'className'		=> 'text-center'
				),
				'fsubm_actions' => array(
					'title'			=> esc_html__('Actions', 'yonox-cf7-db'),
					'data'			=> 'fsubm_actions',
					'orderable'		=> false,
					'searchable'	=> false,
					'className'		=> 'text-center'
				)
			)
		);
		return $Additional_columns;
	}
	
	/**
	 * Datatables Language.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array
	 */
	public static function DataTableTranslation()
	{
		$dtLang = null;
		$langCode = null;
		$locale = get_locale();
        $i18nDir = YCF7DB_PLUGIN_DIR . 'assets/DataTables/i18n/';
		
		if ( substr( $locale, 2, 1 ) == '_' ) {
			$langCode = substr($locale, 0, 2);
		}
		else if ( substr( $locale, 3, 1 ) == '_' ) {
			$langCode = substr($locale, 0, 3);
		}
		
		if ( is_readable( $i18nDir . $locale . '.json' ) )
		{
            $dtLang = json_decode(file_get_contents($i18nDir . $locale . '.json'));
        }
		else if ( $langCode && is_readable( $i18nDir . $langCode . '.json' ) )
		{
			$dtLang = json_decode(file_get_contents($i18nDir . $langCode . '.json'));
		}
		else
		{
			$dtLang = array(
				"sEmptyTable"		=> esc_html__( "No data available in table", 'yonox-cf7-db' ),
				"sInfo"				=> esc_html__( "Showing _START_ to _END_ of _TOTAL_ entries", 'yonox-cf7-db' ),
				"sInfoEmpty"		=> esc_html__( "Showing 0 to 0 of 0 entries", 'yonox-cf7-db' ),
				"sInfoFiltered"		=> esc_html__( "(filtered from _MAX_ total entries)", 'yonox-cf7-db' ),
				"sLengthMenu"		=> esc_html__( "Show _MENU_ entries", 'yonox-cf7-db' ),
				"sLoadingRecords"	=> esc_html__( "Loading...", 'yonox-cf7-db' ),
				"sProcessing"		=> esc_html__( "Processing...", 'yonox-cf7-db' ),
				"sSearch"			=> esc_html__( "Search:", 'yonox-cf7-db' ),
				"sZeroRecords"		=> esc_html__( "No matching records found", 'yonox-cf7-db' ),
				"oPaginate"			=> array(
					"sFirst"	=> esc_html__( "First", 'yonox-cf7-db' ),
					"sLast"		=> esc_html__( "Last", 'yonox-cf7-db' ),
					"sNext"		=> esc_html__( "Next", 'yonox-cf7-db' ),
					"sPrevious"	=> esc_html__( "Previous", 'yonox-cf7-db' )
				),
				"oAria"				=> array(
					"sSortAscending"	=> esc_html__( ": activate to sort column ascending", 'yonox-cf7-db' ),
					"sSortDescending"	=> esc_html__( ": activate to sort column descending", 'yonox-cf7-db' )
				)
			);
		}
		return $dtLang;
	}
	
	/**
	 * Admin Localize Scripts.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array
	 */
	public static function localizeScripts()
	{
		$nonceListForms = wp_create_nonce('get_forms_list_nonce');
		$nonceSubmForms = wp_create_nonce('get_submits_nonce');
		
		$fmListDBcolumns = self::DBTableColumns()['fmlist_dbtable'];
		$fmListADcolumns = self::AdditionalColumns()['fmlist_adtable'];
		$sbListDBcolumns = self::DBTableColumns()['sblist_dbtable'];
		$sbListADcolumns = self::AdditionalColumns()['sblist_adtable'];
		
		$locale = get_locale();
        $i18nDir = YCF7DB_PLUGIN_DIR . 'assets/DataTables/i18n/';
		
		$loclzScripts = array(
			'ajaxUrl'		=> admin_url( 'admin-ajax.php' ),
			'yDBFormsOpts'	=> array(
				'columns' => array(
					$fmListDBcolumns['form_post_id'],
					$fmListDBcolumns['form_name'],
					$fmListADcolumns['count_submissions'],
					$fmListADcolumns['form_actions']
				),
				'ajaxData' => array(
					'action' => 'ycf7db_list_forms',
					'sec_nonce' => $nonceListForms
				)
			),
			'yDBFSubmOpts'	=> array(
				'columns' => array(
					$sbListADcolumns['fsubm_checkbox'],
					$sbListDBcolumns['form_name'],
					$sbListADcolumns['fsubm_fields'],
					$sbListDBcolumns['submit_title_page'],
					$sbListDBcolumns['submit_date'],
					// $sbListDBcolumns['submit_browser'],
					$sbListDBcolumns['submit_ip'],
					$sbListADcolumns['fsubm_actions']
				),
				'ajaxData' => array(
					'action' => 'ycf7db_formsubmits',
					'subm_nonce' => $nonceSubmForms,
					'id_form_subms' => $_GET['idFormSubms']
				)
			),
			'dTableLanguage' => self::DataTableTranslation()
		);
		return $loclzScripts;
	}
	
	/**
	 * Get ip client.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    string
	 */
	public static function clientIPAddress() {
        $clientIp = '';
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    } else if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        $clientIp = $ip;
                    }
                }
            }
        }
        return $clientIp;
    }
	
	/**
	 * Get submissions data from DB.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    mixed
	 */
	public static function getSubmission( $idSubmit = null, $queryType = 'count_forms' )
	{
		$queryResults = '';
		
		if ( absint( $idSubmit ) > 0 )
		{
			global $wpdb;
			$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';
			$query = "SELECT * FROM $ycf7db_table_name WHERE ID = %d";
			$ycf7dbData = $wpdb->get_results( $wpdb->prepare( $query,$idSubmit ) );
			
			if ( $queryType === 'count_forms' )
			{
				$queryCountForm = "SELECT COUNT(*) FROM $ycf7db_table_name WHERE form_post_id = %d";
				$queryResults = $wpdb->get_var( $wpdb->prepare( $queryCountForm,$idSubmit ) );
			}
			elseif ( $queryType === 'form_fields' )
			{
				$queryResults = unserialize($ycf7dbData[0]->form_values);
			}
			elseif ( $queryType === 'form_name' )
			{
				$queryResults = $ycf7dbData[0]->form_name;
			}
			elseif ( $queryType === 'form_page_link' )
			{
				$queryResults = $ycf7dbData[0]->submit_url_page;
			}
			elseif ( $queryType === 'submitPageTitle' )
			{
				$queryResults = $ycf7dbData[0]->submit_title_page;
			}
			elseif ( $queryType === 'submitStatus' )
			{
				$queryResults = $ycf7dbData[0]->submit_status;
			}
			elseif ( $queryType === 'submitDate' )
			{
				$queryResults = $ycf7dbData[0]->submit_date;
			}
			elseif ( $queryType === 'submitIp' )
			{
				$queryResults = $ycf7dbData[0]->submit_ip;
			}
		}
		return $queryResults;
	}
	
	/**
	 * Mark submission as read.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public static function markSubmissAsRead( $idSubmiss = null )
	{
		if ( absint( $idSubmiss ) > 0 )
		{
			global $wpdb;
			$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';
			$query = "UPDATE $ycf7db_table_name SET submit_status = 'read' WHERE ID = %d";
			$markSbmAsRead = $wpdb->query( $wpdb->prepare( $query, $idSubmiss ) );
		}
	}
	
	/**
	 * Delete Form Submissions from DB.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public static function deleteSubmits( $idRecDel = null, $modeDel = null )
	{
		if ( absint( $idRecDel ) > 0 )
		{
			global $wpdb;
			$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';
			
			if ( $modeDel == 'byForm' )
			{
				$query = "DELETE FROM $ycf7db_table_name WHERE form_post_id = %d";
				$deleteByForm = $wpdb->query( $wpdb->prepare( $query, $idRecDel ) );
			}
			else if ( $modeDel == 'indivSubmit' )
			{
				foreach ( $idRecDel as $idSubmDel )
				{
					$query = "DELETE FROM $ycf7db_table_name WHERE ID = %d";
					$deleteSelective = $wpdb->query( $wpdb->prepare( $query, $idSubmDel ) );
				}
			}
		}
	}
}
