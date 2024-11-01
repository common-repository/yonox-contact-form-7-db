"use strict";

// Class Definition
var YCF7DBscripts = function ($) {

	var dbFormsTable;
	var dbSubmitsTable;
	var YformExport;
	
	var yAjaxUrl = ycf7dbScripts.ajaxUrl;
	var dTablesLang = ycf7dbScripts.dTableLanguage;
	var varsDBForms = ycf7dbScripts.yDBFormsOpts;
	var varsDBFSubs = ycf7dbScripts.yDBFSubmOpts;
	
	var loadGoogleFonts = function () {
		WebFont.load({
			google: {
				"families": ["Varela Round:400", "Open Sans:400,600,700"]
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	};
	
	var registerTooltips = function () {
		$('.tooltipbal').tooltip({
			container: '.dataTables_wrapper'
		});
	}
	
	var registerDelRecordsModal = function () {
		$('#ycf7dbFormsTable').on('click', 'i.deleteFormSubmits', function () {
			var idTr = $(this).attr('id-tr');
			$('#deleteRecordsModal .icon-box').html('<i class="flaticon2-delete"></i>');
			$('#deleteRecordsModal input#idFormTr').val(idTr);
			$('#deleteRecordsModal').modal('show');
		});
		$('#ycf7dbSubmissTable').on('click', 'i.deleteSubmitAction', function () {
			var idsbTr = $(this).attr('id-tr');
			$('#deleteRecordsModal .icon-box').html('<i class="flaticon2-trash icono-lg"></i>');
			$('#deleteRecordsModal input#actionType').val('individualDelete');
			$('#deleteRecordsModal input#idSubmitTr').val(idsbTr);
			$('#deleteRecordsModal').modal('show');
		});
	}
	
	var resetDelRecordsModal = function () {
		$('#loaderDelRecs').hide();
		$('#flexContentDelRecs').show();
		$('#deleteRecordsModal button').removeAttr('disabled');
	}
	
	var resetViewSubmitModal = function () {
		$('#flexContentSubmissionData').html('');
		$('#viewSubmissionModal .modal-header, #flexContentSubmissionData').hide();
		$('#loaderSubmissionData').show();
	}
	
	var actionDeleteRecords = function ( delMode, aData ) {
		$('#deleteRecordsModal button').attr('disabled','disaled');
		$('#flexContentDelRecs').hide();
		$('#loaderDelRecs').fadeIn(200);
		$.ajax({url: yAjaxUrl, method: "POST", cache: false, data: aData
		}).done( function (response) {
			// console.log(response);
		});
	}
	
	var createExportIframe = function () {
		$('<iframe>', { name: 'YExporterIFrame' })
		.hide()
		.appendTo('.ycf7db-container');
	}
	
	var formExporter = function ( type_form, mix_id ) {
		YformExport = $('<form>', { name: 'YformExporter', action: yAjaxUrl, method: 'POST', target : 'YExporterIFrame'}).appendTo('.ycf7db-container');
		$('<input>').attr({ type: 'hidden', name: 'action', value: 'ycf7db_export_records' }).appendTo(YformExport);
		if ( type_form === 'exportForms' ) {
			var nonceExportRecs = varsDBForms.ajaxData;
			$('<input>').attr({ type: 'hidden', name: 'formexportnonce', value: nonceExportRecs.sec_nonce }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'title_export', value: 'YCF7DB_All_Submits' }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'type_export', value: 'all_forms' }).appendTo(YformExport);
		}
		if ( type_form === 'exportOneForm' ) {
			var nonceExportRecs = varsDBForms.ajaxData;
			$('<input>').attr({ type: 'hidden', name: 'formexportnonce', value: nonceExportRecs.sec_nonce }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'title_export', value: 'YCF7DB_One_Form' }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'type_export', value: 'one_form' }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'one_form_id', value: mix_id }).appendTo(YformExport);
		}
		if ( type_form === 'exportMisc' ) {
			var nonceExportRecs = varsDBFSubs.ajaxData;
			var idFormSelected = $('input#currentFormView').val();
			$('<input>').attr({ type: 'hidden', name: 'miscexportnonce', value: nonceExportRecs.subm_nonce }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'title_export', value: 'YCF7DB_Selected_Submits' }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'type_export', value: 'selective_submits' }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'current_form_id', value: idFormSelected }).appendTo(YformExport);
			$('<input>').attr({ type: 'hidden', name: 'selected_ids', value: mix_id }).appendTo(YformExport);
		}
	}
	
	var registerActionsSubmitsModal = function () {
		var fSubsAjaxData = varsDBFSubs.ajaxData;
		$('#ycf7dbSubmissTable').on('click', '.viewSubmitAction', function (evt) {
			evt.preventDefault();
			var idSubmTr = $(this).attr('id-tr');
			resetViewSubmitModal();
			$('#viewSubmissionModal').modal('show');
			$.ajax({
				url: yAjaxUrl,
				method: "POST",
				dataType: "html",
				cache: false,
				data: { action: "ycf7db_viewsubmit", sviewnonce: fSubsAjaxData.subm_nonce, id_submit_view: idSubmTr }
			}).done( function (response) {
				$('#flexContentSubmissionData').html(response);
			});
			setTimeout( function () {
				$('#loaderSubmissionData').hide();
				$('#viewSubmissionModal .modal-header').show();
				$('#flexContentSubmissionData').slideDown();
				$('tr#'+idSubmTr+' span.formName').removeClass('fw700').addClass('fw400');
			},500);
		});
		$('#delRecsFormBtn').on('click', function () {
			var typeModalAction = $('#deleteRecordsModal input#actionType').val();
			if ( typeModalAction === 'selectedDelete' ) {
				var idSubmTrDel = $('#deleteRecordsModal input#selectedIds').val();
			} else {
				var idSubmTrDel = $('#deleteRecordsModal input#idSubmitTr').val();
			}
			var nonceDelRec = varsDBFSubs.ajaxData;
			var ajxData = { action: "ycf7db_delete_records", modeDel: 'indivSubmit', fsubdelnonce: nonceDelRec.subm_nonce, ids_submits_delete: idSubmTrDel };
			actionDeleteRecords( 'delIndivSubmit', ajxData);
			setTimeout(function(){
				$('#deleteRecordsModal').modal('hide');
				dbSubmitsTable.draw();
				setTimeout(function(){
					resetDelRecordsModal();
				},500);
			},1000);
		});
	}
	
	var registerSubmsCheckBoxes = function () {
		var chkSelected = [];
		var fillObjChecked = function () {
			var chkSelected = $('input.chkboxsubm:checked').map(function(){
				return $(this).attr('id-tr');
			}).get();
			$('input#selectedIds').val(chkSelected);
		}
		//select all checkboxes
		$("#submChkBoxRoot").on('change', function () {
			$(".chkboxsubm").prop('checked', $(this).prop("checked"));
			fillObjChecked();
		});
		$('.chkboxsubm').live('change', function () {
			if(false == $(this).prop("checked")){
				$("#submChkBoxRoot").prop('checked', false);
			}
			if ($('.chkboxsubm:checked').length == $('.chkboxsubm').length ){
				$("#submChkBoxRoot").prop('checked', true);
			}
			fillObjChecked();
		});
	}
	
	var generateListFormsTable = function () {
		
		var TLFiconSearch = '<div id="listTableSearch" class="d-inline">' + 
			'<i class="flaticon2-search-1 icono-lg mr-3 gris cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>' + 
		'<div class="dropdown-menu dropdown-menu-right p-2 shadow border-0">' + 
			'<div class="input-group">' + 
				'<input id="searchListForms" name="searchListForms" type="search" class="form-control-sm rounded" placeholder="search..."/>' + 
			'</div>' + 
		'</div></div>';
		
		var TLFiconRefresh = '<i class="flaticon2-reload icono-lg mr-3 gris cursor-pointer" id="refreshListTable"></i>';
		
		var TLFiconDownload = '<div id="DdownMenuDownload" class="d-inline">' + 
			'<i class="flaticon2-gear icono-lg mr-2 gris cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>' + 
		'<div class="dropdown-menu dropdown-menu-right shadow border-0 rounded-0 pt-0 mt-0">' + 
			'<div class="dropdown-header blanco px-3 py-2 fw600" style="background:#6b9fe0;">Export All Records</div>' + 
			'<div class="dropdown-divider mt-0"></div>' +
			'<a id="excExpAllForms" class="dropdown-item pl-2" href="#"><i class="ftypeicon-xlsx mx-2"></i>Export All to Excel</a>' +  
		'</div></div>';
		
		dbFormsTable = $('#ycf7dbFormsTable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			language: dTablesLang,
			ajax: {
				url: yAjaxUrl,
				type: "POST",
				data: varsDBForms.ajaxData
			},
			columns: varsDBForms.columns,
			order: [],
			dom: '<"row ycf7dbFormsTable-top-row mt-1"<"col-7 d-flex justify-content-start"i><"col-5 d-flex justify-content-end"<"ycf7dbTableLFMenu">>>rtp<"clear">',
			deferRender: true,
			drawCallback: function( settings ) {
				registerTooltips();
				// console.log(settings); // Debug Table Options
			},
			initComplete: function( settings, json ) {
				// console.log(json); // Debug Return Data
			} 
		});
		
		$("div.ycf7dbTableLFMenu").html( TLFiconRefresh + TLFiconDownload );
		
		$('#listTableSearch').on('show.bs.dropdown', function () {
			setTimeout( function () {
				$('input#searchListForms').focus();
			},100);
		});
		
		$('input#searchListForms').keyup( function () {
			dbFormsTable.search( $(this).val() ).draw();
		});
		
		$('#refreshListTable').on('click', function (evt) {
			evt.preventDefault();
			dbFormsTable.draw();
		});
		
		$('#excExpAllForms').on('click', function (evt) {
			evt.preventDefault();
			formExporter('exportForms');
			$('#exportRecsModal').modal('show');
			setTimeout(function () {
				YformExport.submit();
				setTimeout(function () {
					YformExport.remove();
					$('#exportRecsModal').modal('hide');
				},400);
			},800);
		});
		
		$('#delRecsFormBtn').on('click', function () {
			var trIDform = $('#deleteRecordsModal input#idFormTr').val();
			var nonceDelRecs = varsDBForms.ajaxData;
			var ajxData = { action: "ycf7db_delete_records", modeDel: 'byForm', formdelnonce: nonceDelRecs.sec_nonce, id_form_delete: trIDform };
			actionDeleteRecords( 'formSubmits', ajxData);
			setTimeout(function(){
				$('#deleteRecordsModal').modal('hide');
				dbFormsTable.draw();
				setTimeout(function(){
					resetDelRecordsModal();
				},500);
			},1000);
		});
	}
	
	var generateDBSubmitsTable = function () {
		
		var TSBiconSearch = '<div id="mDdownSearch" class="d-inline">' + 
			'<i class="flaticon2-search-1 icono-lg mr-3 gris cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>' + 
		'<div class="dropdown-menu dropdown-menu-right p-2 shadow border-0">' + 
			'<div class="input-group">' + 
				'<input id="searchInSubmits" name="searchInSubmits" type="search" class="form-control-sm rounded" placeholder="'+dTablesLang.sSearch+'..."/>' + 
			'</div>' + 
		'</div></div>';
		
		var TSBiconRefresh = '<i class="flaticon2-reload icono-lg mr-3 gris cursor-pointer" id="refreshSubmsTable"></i>';
		
		var TSBiconDownload = '<div id="mDdownMenu" class="d-inline">' + 
			'<i class="flaticon2-gear icono-lg mr-2 gris cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>' + 
		'<div class="dropdown-menu dropdown-menu-right shadow border-0 rounded-0 pt-0 mt-0">' + 
			'<div class="dropdown-header blanco px-3 py-2 fw600" style="background:#59ce98;">Bulk Actions</div>' + 
			'<div class="dropdown-divider mt-0"></div>' +  
			'<a id="excExpAllbyForm" class="dropdown-item pl-2" href="#"><i class="ftypeicon-xlsx mx-2"></i>Export Form</a>' + 
			'<div class="dropdown-divider"></div>' + 
			'<a id="excExpSelected" class="dropdown-item pl-2 azul" href="#"><i class="ftypeicon-xlsx mx-2"></i>Export Selected</a>' + 
			'<a id="delSelected" class="dropdown-item pl-2 rojo" href="#"><i class="flaticon2-trash icono-md mx-2"></i>Delete Selected</a>' + 
		'</div></div>';
		
		dbSubmitsTable = $('#ycf7dbSubmissTable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			language: dTablesLang,
			ajax: {
				url: yAjaxUrl,
				type: "POST",
				data: varsDBFSubs.ajaxData
			},
			columns: varsDBFSubs.columns,
			order: [],
			dom: '<"row ycf7dbSubmissTable-top-row mt-1 mb-2"' + 
					'<"col-7 d-flex justify-content-start"i><"col-5 d-flex justify-content-end"<"ycf7dbTableSubmtsMenu">>>rt' + 
				'<"row ycf7dbSubmissTable-bottom-row mt-4"<"col-md-2 pl-4 d-flex justify-content-center justify-content-md-start"l>' + 
					'<"col-md-10 pr-4 d-flex justify-content-center justify-content-md-end"p>><"clear">',
			deferRender: true,
			drawCallback: function( oSettings ) {
				registerTooltips();
				$("#submChkBoxRoot").prop('checked', false);
				$('input#selectedIds').val('');
				
				if (oSettings._iDisplayLength == -1 || oSettings._iDisplayLength > oSettings.fnRecordsDisplay())
				{
					$('.ycf7dbSubmissTable-bottom-row div').hide();
				} else {
					$('.ycf7dbSubmissTable-bottom-row div').fadeIn("slow");
				}
				// console.log(oSettings); // Debug Table Options
			},
			initComplete: function( settings, json ) {
				// console.log(json); // Debug Return Data
			}
		});
		
		$("div.ycf7dbTableSubmtsMenu").html( TSBiconSearch + TSBiconRefresh + TSBiconDownload );
		
		$('#mDdownSearch').on('show.bs.dropdown', function () {
			setTimeout( function () {
				$('input#searchInSubmits').focus();
			},100);
		});
		
		registerSubmsCheckBoxes();
		
		$('input#searchInSubmits').keyup( function () {
			dbSubmitsTable.search( $(this).val() ).draw();
		});
		
		$('#refreshSubmsTable').on('click', function (evt) {
			evt.preventDefault();
			dbSubmitsTable.draw();
		});
		
		$('#excExpAllbyForm').on('click', function (evt) {
			evt.preventDefault();
			var currentFormId = $('#currentFormView').val();
			formExporter('exportOneForm',currentFormId);
			$('#exportRecsModal').modal('show');
			setTimeout(function () {
				YformExport.submit();
				setTimeout(function () {
					YformExport.remove();
					$('#exportRecsModal').modal('hide');
				},400);
			},800);
		});
		
		$('#excExpSelected').on('click', function (evt) {
			evt.preventDefault();
			var idsSelected = $('input#selectedIds').val();
			if ( idsSelected.length > 0 ) {
				formExporter('exportMisc',idsSelected);
				$('#exportRecsModal').modal('show');
				setTimeout(function () {
					YformExport.submit();
					setTimeout(function () {
						YformExport.remove();
						$('#exportRecsModal').modal('hide');
					},400);
				},800);
			} else {
				alert('Nothing Selected...');
			}
		});
		
		$('#delSelected').on('click', function (evt) {
			evt.preventDefault();
			var idsSelected = $('input#selectedIds').val();
			if ( idsSelected.length > 0 ) {
				$('#deleteRecordsModal input#actionType').val('selectedDelete');
				$('#deleteRecordsModal .icon-box').html('<i class="flaticon2-trash icono-lg"></i>');
				$('#deleteRecordsModal').modal('show');
			} else {
				alert('Nothing Selected...');
			}
		});
	}

	return {
		init: function () {
			loadGoogleFonts();
			createExportIframe();
			generateListFormsTable();
			registerDelRecordsModal();
			generateDBSubmitsTable();
			registerActionsSubmitsModal();
		}
	};
}(jQuery);

// Class Initialization
jQuery(document).ready(function () {
	YCF7DBscripts.init();
});
