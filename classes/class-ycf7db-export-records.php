<?php
/**
 * Export records from DB
 *
 * @link       https://wordpress.org/plugins/yonox-cf7-db
 * @since      1.0.0
 *
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/classes
 */

if ( !defined('ABSPATH') ) {
	exit;
}

use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

$writer = WriterEntityFactory::createXLSXWriter();

$exportPartName = isset($_POST['title_export']) ? sanitize_text_field($_POST['title_export']) : 'YCF7DB_Submits';
$fileName = YCF7DB_PLUGIN_DIR . $exportPartName . '_' . time() . '.xlsx';

$writer->openToBrowser($fileName);

global $wpdb;
$ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions';

$typeExport = isset($_POST['type_export']) ? sanitize_text_field($_POST['type_export']) : null;

if ( $typeExport )
{
	$selectedIds = '';
	
	if ( $typeExport == 'all_forms' )
	{
		$queryFormsGroup = "SELECT form_post_id,form_name FROM $ycf7db_table_name GROUP BY form_post_id";
		$formsGroup = $wpdb->get_results( $queryFormsGroup );
	}
	else if ( $typeExport == 'one_form' )
	{
		$formIDtoExport = absint( $_POST['one_form_id'] ) > 0 ? sanitize_text_field($_POST['one_form_id']) : null;
		if ( $formIDtoExport ) {
			$queryFormsGroup = "SELECT form_post_id,form_name FROM $ycf7db_table_name WHERE form_post_id = %d";
			$formsGroup = $wpdb->get_results( $wpdb->prepare( $queryFormsGroup, $formIDtoExport) );
		} else {
			$formsGroup = array();
		}
	}
	else if ( $typeExport == 'selective_submits' )
	{
		$formIDcurrent = absint( $_POST['current_form_id'] ) > 0 ? sanitize_text_field($_POST['current_form_id']) : null;
		$selectedIds = absint( $_POST['selected_ids'] ) > 0 ? explode(',',sanitize_text_field($_POST['selected_ids'])) : null;
		if ( $formIDcurrent && $selectedIds ) {
			
			$queryFormsGroup = "SELECT form_post_id,form_name FROM $ycf7db_table_name WHERE form_post_id = %d";
			$formsGroup = $wpdb->get_results( $wpdb->prepare( $queryFormsGroup, $formIDcurrent) );
		} else {
			$formsGroup = array();
		}
	}
	
	foreach ( $formsGroup as $formPostName )
	{
		$formsToExport[$formPostName->form_post_id] = $formPostName->form_name;
	}
	
	$countForms = 0;
	
	foreach ( $formsToExport as $formIdToExport => $formName )
	{
		if ( $selectedIds ) {
			$queryDataSelected = '"'.implode('","',$selectedIds).'"';
			$getDataByForm = $wpdb->get_results( "SELECT * FROM $ycf7db_table_name WHERE ID IN($queryDataSelected)" );
		} else {
			$queryDataByForm = "SELECT * FROM $ycf7db_table_name WHERE form_post_id = %d";
			$getDataByForm = $wpdb->get_results( $wpdb->prepare( $queryDataByForm, $formIdToExport ) );
		}
		
		$queryFirstSubmitForm = "SELECT * FROM $ycf7db_table_name WHERE form_post_id = %d";
		$firstSubmitForm = $wpdb->get_row( $wpdb->prepare( $queryFirstSubmitForm, $formIdToExport ), ARRAY_A );
		$formFieldsData = YCF7DB_Functions::getSubmission( $firstSubmitForm['ID'], 'form_fields' );
		
		$countForms++;
		
		if ( $countForms == 1 ) {
			$writer->getCurrentSheet()->setName($formName);
		} else {
			$writer->addNewSheetAndMakeItCurrent();
			$writer->getCurrentSheet()->setName($formName);
		}
		
		$headerRow = [
			WriterEntityFactory::createCell( esc_html__( 'Form Name', 'yonox-cf7-db' ) ),
			WriterEntityFactory::createCell( esc_html__( 'Submit Date', 'yonox-cf7-db' ) ),
		];
		
		$headerFormTags = array();
		foreach ( array_keys($formFieldsData) as $formFieldTag ) {
			$headerFormTags[] = WriterEntityFactory::createCell( $formFieldTag );
		}
		
		$headerWithTags = array_merge( $headerRow, $headerFormTags );
		
		$headerRowEnd = [
			WriterEntityFactory::createCell( esc_html__( 'Page Form', 'yonox-cf7-db' ) ),
			WriterEntityFactory::createCell( esc_html__( 'Page Form URL', 'yonox-cf7-db' ) ),
			WriterEntityFactory::createCell( esc_html__( 'IP Address', 'yonox-cf7-db' ) ),
		];
		
		$headerRowFinal = array_merge( $headerWithTags, $headerRowEnd );
		
		$rowHeaderStyle = (new StyleBuilder())->setFontBold()->build();
		$rowHeaderTitles = WriterEntityFactory::createRow($headerRowFinal,$rowHeaderStyle);
		$writer->addRow($rowHeaderTitles);
		
		foreach ( $getDataByForm as $submitData )
		{
			$sbfCells = [
				WriterEntityFactory::createCell( $submitData->form_name ),
				WriterEntityFactory::createCell( $submitData->submit_date ),
			];
			
			$sbfCellsTagsValues = array();
			foreach ( unserialize($submitData->form_values) as $formFieldValue ) {
				$sbfCellsTagsValues[] = WriterEntityFactory::createCell( $formFieldValue );
			}
			
			$sbfCellsWithValues = array_merge( $sbfCells, $sbfCellsTagsValues );
			
			$sbfCellsEnd = [
				WriterEntityFactory::createCell( $submitData->submit_title_page ),
				WriterEntityFactory::createCell( $submitData->submit_url_page ),
				WriterEntityFactory::createCell( $submitData->submit_ip ),
			];
			
			$sbfCellsFinal = array_merge( $sbfCellsWithValues, $sbfCellsEnd );
			
			$rowFromValues = WriterEntityFactory::createRow($sbfCellsFinal);
			$writer->addRow($rowFromValues);
		}
	}
}

$writer->close();