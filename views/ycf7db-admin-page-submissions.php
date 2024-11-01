<?php
/**
 * Content page for "Submissions" admin page
 *
 * @link       https://wordpress.org/plugins/yonox-cf7-db
 * @since      1.0.0
 *
 * @package    Yonox_Cf7_Db
 * @subpackage Yonox_Cf7_Db/views
 */

if ( !defined('ABSPATH') ) {
	exit;
}
?>
<div class="wrap">
	<div class="ycf7db-container">
		
		<?php global $wpdb; ?>
		<?php $ycf7db_table_name = $wpdb->prefix . 'ycf7db_submissions'; ?>
		<?php $ycf7db_rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $ycf7db_table_name"); ?>
		
		<?php if ( $ycf7db_rowcount > 0 ) { ?>
			
			<?php if ( isset($_GET['idFormSubms']) && absint($_GET['idFormSubms']) ) { ?>
				<?php $queryTitleForm = "SELECT * FROM $ycf7db_table_name WHERE form_post_id = %d GROUP BY form_post_id"; ?>
				<?php $titleForm = $wpdb->get_results( $wpdb->prepare( $queryTitleForm, absint($_GET['idFormSubms']) ) ); ?>
				<?php $cform7Exists = get_post_status( $titleForm[0]->form_post_id ) ? 'azul' : ' not-exists'; ?>
				<div class="row">
					<div class="col">
						<div class="card border-0 rounded-0" style="max-width:none;padding:unset;margin-top:10px;">
							<div class="card-body">
								<div class="title_form_submissions">
									<i class="flaticon2-send icono-lg mr-1 <?php echo $cform7Exists; ?>"></i>
									<h4 style="font-size:1.2em;"><?php echo $titleForm[0]->form_name; ?></h4>
								</div>
								<table id="ycf7dbSubmissTable" class="table table-striped table-hover" style="width:100%;background:#fff;"></table>
							</div>
						</div>
					</div>
					<input type="hidden" id="currentFormView" value="<?php echo $titleForm[0]->form_post_id; ?>">
				</div>
			<?php } else { ?>
				<div class="row">
					<div class="col">
						<div class="card border-0 rounded-0" style="max-width:none;padding:unset;margin-top:10px;">
							<div class="card-body">
								<table id="ycf7dbFormsTable" class="table table-striped table-hover listFormsTable" style="width:100%;background:#fff;"></table>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		
		<!-- Delete Records Modal -->
		<div class="modal fade" id="deleteRecordsModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="deleteRecordsModal" aria-hidden="true">
		  <div class="modal-dialog modal-delrecs" role="document">
			<div class="modal-content border-0 shadow-lg mt-5">
				<div class="modal-header d-block">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	
				</div>
				<div class="modal-body pt-0">
					<div id="loaderDelRecs" class="my-3 text-center" style="display:none;">
						<p><?php echo esc_html__('Deleting Records', 'yonox-cf7-db'); ?></p>
						<div class="ycf7db-loader">
							<div class="bola1"></div>
							<div class="bola2"></div>
							<div class="bola3"></div>
						</div>
					</div>
					<div id="flexContentDelRecs">
						<div class="icon-box">
							<i class="flaticon2-delete"></i>
						</div>				
						<h4 class="modal-title"><?php echo esc_html__('Are you sure?', 'yonox-cf7-db'); ?></h4>
						<p class="text-center">
							<?php echo esc_html__('Do you really want to delete these records?', 'yonox-cf7-db'); ?><br>
							<?php echo esc_html__('This process cannot be undone.', 'yonox-cf7-db'); ?>
						</p>
					</div>
				</div>
				<div id="footer-delrecs" class="modal-footer d-block">
					<input type="hidden" id="actionType" value="">
					<input type="hidden" id="idFormTr" value="">
					<input type="hidden" id="selectedIds" value="">
					<input type="hidden" id="idSubmitTr" value="">
					<button type="button" class="btn btn-info mb-2" data-dismiss="modal"><?php echo esc_html__('Cancel', 'yonox-cf7-db'); ?></button>
					<button type="button" class="btn btn-danger mb-2 delActionBtn" id="delRecsFormBtn"><?php echo esc_html__('Delete', 'yonox-cf7-db'); ?></button>
				</div>
			</div>
		  </div>
		</div>
		
		<!-- View Submission Modal -->
		<div class="modal fade" id="viewSubmissionModal" tabindex="-1" role="dialog" aria-labelledby="viewSubmissionModal" aria-hidden="true">
		  <div class="modal-dialog modal-submiss-data modal-lg" role="document">
			<div class="modal-content border-0 shadow-lg mt-5">
				<div class="modal-header pb-1">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	
				</div>
				<div class="modal-body pt-0 px-0 px-lg-3" style="overflow-x:auto;">
					<div id="loaderSubmissionData" class="my-5 text-center">
						<p><?php echo esc_html__('Loading Data...', 'yonox-cf7-db'); ?></p>
						<div class="ycf7db-loader">
							<div class="bola1"></div>
							<div class="bola2"></div>
							<div class="bola3"></div>
						</div>
					</div>
					<div id="flexContentSubmissionData"  style="display:none;"></div>
				</div>
					<input type="hidden" id="idSubmissTr" value="">
			</div>
		  </div>
		</div>
		
		<!-- Create Download File Modal -->
		<div class="modal fade" id="exportRecsModal" tabindex="-1" role="dialog" aria-labelledby="exportRecsModal" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content border-0 shadow-lg mt-5">
				<div class="modal-body pt-0 px-0 px-lg-3" style="overflow-x:auto;">
					<div id="loaderExportData" class="my-5 text-center">
						<p class="text-secondary"><?php echo esc_html__('Downloading Files...', 'yonox-cf7-db'); ?></p>
						<div class="ycf7db-loader">
							<div class="bola1"></div>
							<div class="bola2"></div>
							<div class="bola3"></div>
						</div>
					</div>
				</div>
			</div>
		  </div>
		</div>
			
		<?php } else { ?>
		
			<div class="row">
				<div class="col">
					<div class="card border-0" style="max-width:none;margin-top:10px;">
						<div class="card-body">
							<div style="max-width:400px;margin:0 auto;">
								<img src="<?php echo YCF7DB_PLUGIN_URL . 'assets/img/envelope-sleeping.gif'; ?>" style="width:100%;height:100%;">
							</div>
							<h2 style="color:#f98080;margin-bottom:20px;margin-top:20px;text-align:center;font-size:26px;">
								<?php echo esc_html__( 'Not found submissions for now', 'yonox-cf7-db' ); ?>
							</h2>
						</div>
					</div>
				</div>
			</div>
			
		<?php } ?>
		
	</div>
</div>
