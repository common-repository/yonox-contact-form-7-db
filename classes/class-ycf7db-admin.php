<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 * @author     Yonox <yonox2008@gmail.com>
 */

if ( !defined('ABSPATH') ) {
	exit;
}

class Yonox_Cf7_Db_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the Stylesheets for the admin menu icon.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_menu_icon()
	{
		wp_enqueue_style( 'Ycf7db-icon', YCF7DB_PLUGIN_URL . 'assets/css/font-icons/admin-menu-icon/ycf7db.css', array(), $this->version );
	}
	
	/**
	 * Register the Stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( 'Flaticons', YCF7DB_PLUGIN_URL . 'assets/css/font-icons/flaticon.css', array(), $this->version );
		wp_enqueue_style( 'Flaticons2', YCF7DB_PLUGIN_URL . 'assets/css/font-icons/flaticon2.css', array(), $this->version );
		wp_enqueue_style( 'Ftypeicons', YCF7DB_PLUGIN_URL . 'assets/css/font-icons/ftypeicon.css', array(), $this->version );
		wp_enqueue_style( 'DataTables', YCF7DB_PLUGIN_URL . 'assets/DataTables/datatables.min.css', array(), '1.10.18' );
		wp_enqueue_style( $this->plugin_name, YCF7DB_PLUGIN_URL . 'assets/css/yonox-cf7-db-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{	
		$jScriptOpts = YCF7DB_Functions::localizeScripts();
		
		wp_enqueue_script( 'gfonts', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array(), '1.6.26', false );
		wp_enqueue_script( 'DataTables', YCF7DB_PLUGIN_URL . 'assets/DataTables/datatables.min.js', array( 'jquery' ), '1.10.19', true );
		wp_enqueue_script( $this->plugin_name, YCF7DB_PLUGIN_URL . 'assets/js/yonox-cf7-db-admin.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ycf7dbScripts', $jScriptOpts );
	}
	
	/**
	 * Register menu for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_admin_menu()
	{
		global $_wp_last_object_menu;

		$_wp_last_object_menu++;
		
		$ycf7db_page_title = esc_html__( 'Yonox Contact Form 7 DB', 'yonox-cf7-db' );
		
		add_menu_page(
			$ycf7db_page_title,
			esc_html__( 'Yonox CF7 DB', 'yonox-cf7-db' ),
			'administrator',
			'ycf7db_submissions',
			array( $this, 'ycf7db_admin_page_submissions' ),
			'dashicons-ycf7db-mail',
			$_wp_last_object_menu
		);
		
		add_submenu_page(
			'ycf7db_submissions',
			$ycf7db_page_title,
			esc_html__( 'Submissions', 'yonox-cf7-db' ),
			'administrator',
			'ycf7db_submissions'
		);
	}
	
	/**
	 * Register plugin action links.
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_plugin_action_links( $links )
	{
		$ycf7db_opts_link = menu_page_url( 'ycf7db_submissions', false );
		$ycf7db_opts_link = '<a href="' . esc_url($ycf7db_opts_link) . '">' . esc_html__('Submissions', 'yonox-cf7-db') . '</a>';
		
		array_unshift( $links, $ycf7db_opts_link );
		
		return $links;
	}
	
	/**
	 * Load content page for "Submissions" menu.
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_admin_page_submissions()
	{
		require_once YCF7DB_PLUGIN_DIR . 'views/ycf7db-admin-page-submissions.php';
	}
	
	/**
	 * Ajax Retrieve Forms List Grouped by Form from DB
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_list_forms()
	{
		$nonce = isset($_POST['sec_nonce']) ? $_POST['sec_nonce'] : '';
		
		if ( wp_verify_nonce( $nonce, 'get_forms_list_nonce' ) )
		{
			global $wpdb;
			$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';
			$aColumns = array_keys(YCF7DB_Functions::DBTableColumns()['fmlist_dbtable']);
			$sIndexColumn = $aColumns[0];
			
			/*
			 * Group by
			 */
			$groupBy = "GROUP BY " . $aColumns[1];
			
			/*
			 * Paging
			 */
			$sLimit = "";
			if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' ) {
				$sLimit = "LIMIT ".intval( $_POST['start'] ).", " . intval( $_POST['length'] );
			}
			
			/*
			 * SQL queries
			 * Get data to display
			 */
			$sQuery = "SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , " , " " , implode("`, `" , $aColumns)) . "` FROM $ycf7db_table_name $groupBy $sLimit";
			
			$rResult = $wpdb->get_results($sQuery , ARRAY_A);

			$sQuery = " SELECT FOUND_ROWS() ";
			$aResultFilterTotal = $wpdb->get_results($sQuery , ARRAY_N);
			$iFilteredTotal = $aResultFilterTotal[0];

			$total_sQuery = "SELECT * FROM $ycf7db_table_name $groupBy";
			$aResultTotal = count($wpdb->get_results($total_sQuery));
			
			/*
			 * Output
			 */
			$output = array(
				"draw" => intval($_POST['draw']),
				"recordsFiltered" => $iFilteredTotal,
				"recordsTotal" => $aResultTotal,
				"data" => array()
			);
			
			$additionalArrays = array_keys(YCF7DB_Functions::AdditionalColumns()['fmlist_adtable']);
			
			$aColumns = array_merge( $aColumns, $additionalArrays );
			
			foreach ( $rResult as $aRow ) {
				$row = array();
				for ( $i = 0; $i < count($aColumns); $i++ ) {
					
					$row['DT_RowId'] = $aRow[$sIndexColumn];
					
					$form7Exists = get_post_status( $aRow[$sIndexColumn] ) ? '' : ' not-exists';
					
					$countSubsByForm = YCF7DB_Functions::getSubmission($aRow[$sIndexColumn],'count_forms');
					
					$iconFormTitle = '<i class="flaticon2-send'.$form7Exists.' icono-lg va-2 mr-1"></i>';
					$iconViewSubmiss = '<i class="flaticon2-list-3 icono-lg azul mr-2 cursor-pointer tooltipbal" title="'.esc_html__('View Form Submissions', 'yonox-cf7-db').'"></i>';
					$iconDeleteSubmsForm = '<i class="flaticon2-close-cross icono-lg rojo ml-2 cursor-pointer deleteFormSubmits tooltipbal" title="'.esc_html__('Delete Form Submissions', 'yonox-cf7-db').'" id-tr="'.$aRow[$sIndexColumn].'"></i>';
					
					$paramGetIdForm = '&idFormSubms='.$aRow[$sIndexColumn];
					$linkViewSubmPage = admin_url("admin.php?page=ycf7db_submissions".$paramGetIdForm);
					
					if ( $aColumns[$i] == "form_post_id" ) {
						$row[$aColumns[$i]] = '<span class="id_form_nr">'.$aRow[$aColumns[$i]].'</span>';
					}
					else if ( $aColumns[$i] == "form_name" ) {
						$row[$aColumns[$i]] = '<a href="'.esc_url($linkViewSubmPage).'">'.$iconFormTitle.$aRow[$aColumns[$i]].'</a>';
					}
					else if ( $aColumns[$i] == "count_submissions" ) {
						$row[$aColumns[$i]] = '<span class="subm_count_nr tooltipbal" title="'.esc_html__('Count Submissions', 'yonox-cf7-db').'">'.$countSubsByForm.'</span>';
					}
					else if ( $aColumns[$i] == "form_actions" ) {
						$row[$aColumns[$i]] = '<a href="'.esc_url($linkViewSubmPage).'">'.$iconViewSubmiss.'</a>'.$iconDeleteSubmsForm;
					}
				}
				$output['data'][] = $row;
			}
			 
			echo json_encode( $output );
			
			die();
		}
	}
	
	/**
	 * Ajax Retrieve Submissions by Form from DB
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_formsubmits()
	{
		$nonce = isset($_POST['subm_nonce']) ? $_POST['subm_nonce'] : '';
		
		if ( wp_verify_nonce( $nonce, 'get_submits_nonce' ) )
		{
			global $wpdb;
			$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';
			$aColumns = array_keys(YCF7DB_Functions::DBTableColumns()['sblist_dbtable']);
			$sIndexColumn = $aColumns[0];
			
			/*
			 * Paging
			 */
			$sLimit = "";
			if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' ) {
				$sLimit = "LIMIT ".intval( $_POST['start'] ).", " . intval( $_POST['length'] );
			}
			
			/*
			 * Filtering
			 */
			$sWhere = "";
			if ( isset( $_POST['id_form_subms'] ) && intval( $_POST['id_form_subms'] ) ) {
				$ySearch = "";
				if ( isset($_POST['search']['value']) && $_POST['search']['value'] ) {
					$ySearch = " AND form_values LIKE '%".$wpdb->esc_like(sanitize_text_field($_POST['search']['value']))."%' ";
				}
				$sWhere = "WHERE form_post_id = " . $_POST['id_form_subms'] . $ySearch;
			}
			
			/*
			 * SQL queries
			 * Get data to display
			 */
			$sQuery = "SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , " , " " , implode("`, `" , $aColumns)) . "` FROM $ycf7db_table_name $sWhere $sLimit";
			
			$rResult = $wpdb->get_results($sQuery , ARRAY_A);

			$sQuery = " SELECT FOUND_ROWS() ";
			$aResultFilterTotal = $wpdb->get_results($sQuery , ARRAY_N);
			$iFilteredTotal = $aResultFilterTotal[0];

			$total_sQuery = "SELECT * FROM $ycf7db_table_name $sWhere";
			$aResultTotal = count($wpdb->get_results($total_sQuery));
			
			/*
			 * Output
			 */
			$output = array(
				"draw" => intval($_POST['draw']),
				"recordsFiltered" => $iFilteredTotal,
				"recordsTotal" => $aResultTotal,
				"data" => array()
			);
			
			$additionalArrays = array_keys(YCF7DB_Functions::AdditionalColumns()['sblist_adtable']);
			
			$aColumns = array_merge( $aColumns, $additionalArrays );
			
			foreach ( $rResult as $aRow ) {
				$row = array();
				for ( $i = 0; $i < count($aColumns); $i++ ) {
					
					$row['DT_RowId'] = $aRow[$sIndexColumn];
					
					$checkboxRowData = '<div class="custom-control custom-checkbox ml-sm-2">';
					$checkboxRowData .= '<input type="checkbox" name="subm_chkbox[]" class="chkboxsubm custom-control-input d-none" id="submChkBox_'.$aRow[$sIndexColumn].'" id-tr="'.$aRow[$sIndexColumn].'">';
					$checkboxRowData .= '<label class="custom-control-label" for="submChkBox_'.$aRow[$sIndexColumn].'">';
					$checkboxRowData .= '</label></div>';
					
					$statusSubmit = YCF7DB_Functions::getSubmission($aRow[$sIndexColumn], 'submitStatus');
					$submFormFields = YCF7DB_Functions::getSubmission($aRow[$sIndexColumn], 'form_fields');
					
					$classFormName = $statusSubmit == 'read' ? 'fw400' : 'fw700';
					
					$linkFormPage = YCF7DB_Functions::getSubmission($aRow[$sIndexColumn], 'form_page_link');
					
					$iconsActions = '<i class="flaticon-eye icono-lg azul mx-2 cursor-pointer tooltipbal viewSubmitAction" title="'.esc_html__('View Submission', 'yonox-cf7-db').'" id-tr="'.$aRow[$sIndexColumn].'"></i>';
					$iconsActions .= '<i class="flaticon2-trash icono-lg rojo mx-2 cursor-pointer tooltipbal deleteSubmitAction" title="'.esc_html__('Delete Submission', 'yonox-cf7-db').'" id-tr="'.$aRow[$sIndexColumn].'"></i>';
					
					if ( $aColumns[$i] == "fsubm_checkbox" ) {
						$row[$aColumns[$i]] = $checkboxRowData;
					}
					else if ( $aColumns[$i] == "form_name" ) {
						$row[$aColumns[$i]] = '<span class="formName '.$classFormName.' cursor-pointer viewSubmitAction" id-tr="'.$aRow[$sIndexColumn].'">'.$aRow[$aColumns[$i]].'</span>';
					}
					else if ( $aColumns[$i] == "fsubm_fields" ) {
						$row[$aColumns[$i]] = '<span class="subm_count_nr tooltipbal" title="'.esc_html__('Form Fields', 'yonox-cf7-db').'">'.count($submFormFields).'</span>';
					}
					else if ( $aColumns[$i] == "submit_title_page" ) {
						$row[$aColumns[$i]] = '<a href="'.esc_url($linkFormPage).'" target="_blank" class="fw600">'.$aRow[$aColumns[$i]].'</a>';
					}
					// else if ( $aColumns[$i] == "submit_browser" ) {
						// $row[$aColumns[$i]] = '<i class="flaticon2-pie-chart-2 icono-lg azul mx-2"></i>';
					// }
					else if ( $aColumns[$i] == "submit_date" ) {
						$row[$aColumns[$i]] = $aRow[$aColumns[$i]];
					}
					else if ( $aColumns[$i] == "submit_ip" ) {
						$row[$aColumns[$i]] = $aRow[$aColumns[$i]];
					}
					else if ( $aColumns[$i] == "fsubm_actions" ) {
						$row[$aColumns[$i]] = $iconsActions;
					}
				}
				$output['data'][] = $row;
			}
			 
			echo json_encode( $output );
			
			die();
		}
	}
	
	/**
	 * Ajax Create Submit View Modal
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_viewsubmit()
	{
		$nonce = isset($_POST['sviewnonce']) ? $_POST['sviewnonce'] : '';
		$idSubmitValid = isset($_POST['id_submit_view']) ? sanitize_text_field($_POST['id_submit_view']) : '';
		$idSubmView = absint( $idSubmitValid ) > 0 ? $idSubmitValid : false;
		
		if ( wp_verify_nonce( $nonce, 'get_submits_nonce' ) && $idSubmView )
		{
			$tableSubView = '';
			
			$getFormName = YCF7DB_Functions::getSubmission( $idSubmView, 'form_name' );
			$getPageTitle = YCF7DB_Functions::getSubmission( $idSubmView, 'submitPageTitle' );
			$getPageLink = YCF7DB_Functions::getSubmission( $idSubmView, 'form_page_link' );
			$getSubmitDate = YCF7DB_Functions::getSubmission( $idSubmView, 'submitDate' );
			$getSubmitIP = YCF7DB_Functions::getSubmission( $idSubmView, 'submitIp' );
			$getFieldsView = YCF7DB_Functions::getSubmission( $idSubmView, 'form_fields' );
			
			$tableSubView .= '<table class="table display nowrap mt-3 mb-0 table-striped table-borderless table-bordered border-bottom-0 table-hover" style="width:100%;background:#fff;">';
			$tableSubView .= '<thead class="d-none invisible"><tr><th>Field</th><th>Value</th></tr></thead>';
			$tableSubView .= '<tbody>';
			$tableSubView .= '	<tr>';
			$tableSubView .= '		<td style="width:80px;"><span class="fw600 azul">'.esc_html__('Form:', 'yonox-cf7-db').'</span></td>';
			$tableSubView .= '		<td><span class="fw600 gris">'.$getFormName.'</span></td>';
			$tableSubView .= '	</tr>';
			$tableSubView .= '	<tr>';
			$tableSubView .= '		<td style="width:80px;"><span class="fw600 azul">'.esc_html__('Date:', 'yonox-cf7-db').'</span></td>';
			$tableSubView .= '		<td><span class="fw600 gris">'.$getSubmitDate.'</span></td>';
			$tableSubView .= '	</tr>';
			$tableSubView .= '	<tr>';
			$tableSubView .= '		<td style="width:80px;"><span class="fw600 azul">'.esc_html__('Page:', 'yonox-cf7-db').'</span></td>';
			$tableSubView .= '		<td><span class="fw600 gris"><a href="'.$getPageLink.'" target="_blank">'.$getPageTitle.'</a></span></td>';
			$tableSubView .= '	</tr>';
			$tableSubView .= '	<tr>';
			$tableSubView .= '		<td style="width:80px;"><span class="fw600 azul">'.esc_html__('IP:', 'yonox-cf7-db').'</span></td>';
			$tableSubView .= '		<td><span class="fw600 gris">'.$getSubmitIP.'</span></td>';
			$tableSubView .= '	</tr>';
			$tableSubView .= '</tbody>';
			$tableSubView .= '</table>';
			
			$tableSubView .= '<table id="ycf7dbViewSubmit" class="table display nowrap table-striped table-bordered table-hover" style="width:100%;background:#fff;">';
			$tableSubView .= '<thead style="background:#59ce98;color:#fff;text-shadow:1px 1px 1px rgba(0, 0, 0, 0.5);">';
			$tableSubView .= '	<tr>';
			$tableSubView .= '		<th class="border-top-0 border-bottom-0 border-left-0 py-2">'.esc_html__('Field Tag', 'yonox-cf7-db').'</th>';
			$tableSubView .= '		<th class="border-top-0 border-bottom-0 border-right-0 py-2">'.esc_html__('Value', 'yonox-cf7-db').'</th>';
			$tableSubView .= '	</tr>';
			$tableSubView .= '</thead>';
			$tableSubView .= '<tbody>';
			
			$upload_dir = wp_upload_dir();
			$ycf7db_dirmedia = $upload_dir['baseurl'] . '/ycf7db_uploads/';
			
			foreach ( $getFieldsView as $submTag => $submTagValue )
			{
				if ( substr( $submTag, -12) == '_ycf7db_file' ) {
					$submTagValue = '<a href="'.$ycf7db_dirmedia.$submTagValue.'" target="_blank">'.$submTagValue.'</a>';
				}
				$tableSubView .= '	<tr>';
				$tableSubView .= '		<td>'.$submTag.'</td>';
				$tableSubView .= '		<td>'.$submTagValue.'</td>';
				$tableSubView .= '	</tr>';
			}
			
			$tableSubView .= '</tbody>';
			$tableSubView .= '</table>';
			
			// Mark submission as read
			YCF7DB_Functions::markSubmissAsRead( $idSubmView );
			
			echo $tableSubView;
		}
		
		die();
	}
	
	/**
	 * Ajax Delete Submissions from DB
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_delete_records()
	{
		$modeDelRecs = isset($_POST['modeDel']) && $_POST['modeDel'] ? sanitize_text_field($_POST['modeDel']) : '';
		
		if ( $modeDelRecs == 'byForm' )
		{
			$nonce = isset($_POST['formdelnonce']) ? $_POST['formdelnonce'] : '';
			$idFormDelValid = isset($_POST['id_form_delete']) ? sanitize_text_field($_POST['id_form_delete']) : '';
			$idFormDelSubms = absint( $idFormDelValid ) > 0 ? $idFormDelValid : false;
			
			if ( wp_verify_nonce( $nonce, 'get_forms_list_nonce' ) && $idFormDelSubms )
			{
				// Delete rows by form_post_id
				YCF7DB_Functions::deleteSubmits( $idFormDelSubms, $modeDelRecs );
			}
		}
		else if ( $modeDelRecs == 'indivSubmit' )
		{
			$nonce = isset($_POST['fsubdelnonce']) ? $_POST['fsubdelnonce'] : '';
			$idSubmDelValid = isset($_POST['ids_submits_delete']) ? sanitize_text_field($_POST['ids_submits_delete']) : '';
			$idSubmitDelete = $idSubmDelValid ? $idSubmDelValid : false;
			
			if ( wp_verify_nonce( $nonce, 'get_submits_nonce' ) && $idSubmitDelete )
			{
				// Delete submissions individual or selected
				YCF7DB_Functions::deleteSubmits( explode(',',$idSubmitDelete), 'indivSubmit' );
			}
		}

		die();
	}
	
	/**
	 * Ajax Export Submissions from DB
	 *
	 * @since    1.0.0
	 */
	public function ycf7db_export_records()
	{
		if (
			isset($_POST['type_export']) && sanitize_text_field($_POST['type_export']) == 'all_forms' || 
			isset($_POST['type_export']) && sanitize_text_field($_POST['type_export']) == 'one_form'
		) {
			$nonce = isset($_POST['formexportnonce']) ? $_POST['formexportnonce'] : '';
		}
		else if ( isset($_POST['type_export']) && sanitize_text_field($_POST['type_export']) == 'selective_submits' )
		{
			$nonce = isset($_POST['miscexportnonce']) ? $_POST['miscexportnonce'] : '';
		}
		
		if (
			wp_verify_nonce( $nonce, 'get_forms_list_nonce' ) || 
			wp_verify_nonce( $nonce, 'get_submits_nonce' )
		) {
			include_once YCF7DB_PLUGIN_DIR . 'classes/class-ycf7db-export-records.php';
		}
		
		die();
	}
}
