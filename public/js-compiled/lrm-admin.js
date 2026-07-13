+(function ($) {

	jQuery(".lrm-run-export").click(function(event) {
		event.preventDefault();

		var submitBtn = this;
		submitBtn.innerHTML += '<span class="spinner is-active"></span>';

		var sectionsToProcess = [];
		var sectionsCheckbox = document.querySelectorAll(".lrm_export_sections_checkbox");
		for ( var N=0; N < sectionsCheckbox.length; N++ ) {
			if ( sectionsCheckbox[N].checked ) {
				sectionsToProcess.push( sectionsCheckbox[N].value );
			}
		}

		jQuery.get(
			  LRM_ADMIN.ajax_url,
			  { action: "lrm_export", sections: sectionsToProcess, _nonce: jQuery(submitBtn).data("nonce") },
			  function(resp){
				  if ( resp.success ) {
				  	$(".lrm-export-string-wrap").show();
				 	$("#lrm-export-string").val( resp.data );
				  } else {
					  if ( resp.data ) {
						  alert(resp.data);
					  } else {
						  alert("Export error!");
					  }
				  }
				  jQuery(submitBtn).find(".spinner").remove();
			  }
		);

		return false;
	});

	jQuery(".lrm-run-import").click(function(event) {
		event.preventDefault();

		var submitBtn = this;
		submitBtn.innerHTML += '<span class="spinner is-active"></span>';

		var sectionsToProcess = [];
		var sectionsCheckbox = document.querySelectorAll(".lrm_import_sections_checkbox");
		for ( var N=0; N < sectionsCheckbox.length; N++ ) {
			if ( sectionsCheckbox[N].checked ) {
				sectionsToProcess.push( sectionsCheckbox[N].value );
			}
		}

		jQuery.post(
			  LRM_ADMIN.ajax_url,
			  { action: "lrm_import", sections: sectionsToProcess, sections_import: $("#lrm-import-string").val(), _nonce: jQuery(submitBtn).data("nonce") },
			  function(resp){
				  if ( resp.success ) {
					  alert("Import complete!");
				  } else {
					  if ( resp.data ) {
						  alert(resp.data);
					  } else {
						  alert("Import error!");
					  }
				  }
				  jQuery(submitBtn).find(".spinner").remove();
			  }
		);

		return false;
	});

	/**
	 * =======================================
	 * REDIRECTS JS
	 * =======================================
	 */
	$(document).on("change", ".redirect-to", function(e) {
		show_or_hide_url_input(this);
	});


	function show_or_hide_url_input(input) {
		if ( input.value == "url" ) {
			$(input).parent().find(".redirect-url").show();
			$(input).parent().find(".redirect-page").hide();
		} else if ( input.value == "page" ) {
			$(input).parent().find(".redirect-url").hide();
			$(input).parent().find(".redirect-page").show();
		} else {
			$(input).parent().find(".redirect-url").hide();
			$(input).parent().find(".redirect-page").hide();
		}
	}

	function bulk_show_or_hide_redirect_inputs(input) {
		$(".redirect-to").each(function (k, el) {
			show_or_hide_url_input(el);
		});
	}

	bulk_show_or_hide_redirect_inputs();

	$(".js-lrm-add-new-redirect-rule").click(function () {

		//var rows_count = $(".lrm-redirects-field__roles-wrap").length;
		var $new_row = $(
			  $(".js-lrm-repeater-tpl[data-name='" + $(this).data("name") + "']").html().split('%key%').join( "0" )
		);
		var $wrap = $(this).parent().find(".lrm-repeater-field__roles-wrap");
		$wrap.prepend( $new_row );

		$new_row.find( ".pretty-select" ).selectize();

		bulk_show_or_hide_redirect_inputs();

		reorder_redirects( $wrap );
		// 1232
	});

	$(document).on("click", ".js-lrm-delete-row",function (e) {
		e.preventDefault();
		if ( confirm("Are you sure to delete this row?") ) {
			var parent = $(this).closest(".lrm-repeater-field__roles-wrap");
			$(this).closest(".lrm-repeater-field__row").fadeOut(500).remove();

			reorder_redirects( parent );
		}
	});
	// 13223444444444444444444

	$( ".lrm-repeater-field__roles-wrap" ).sortable({
		//containment: ".lrm-redirects-field__row",
		handle: ".js-lrm-sort-row",
		items: ".lrm-repeater-field__row",
		update: function( evt, ui ) {
			//console.log(ui);

			reorder_redirects( ui.item.parent() );
		},
	});

	function reorder_redirects( $wrap ) {

		$wrap.find(".lrm-repeater-field__row").each(function(ID, el) {
			var current_KEY = $(el).data("key");
			$(el).data("key", ID);

			$(el).find("input[name],select[name]").each(function(k, input) {
				var new_input_name = $(input).attr("name").replace("["+current_KEY+"]", "["+ID+"]");

				$(input).attr("name", new_input_name);
			});
		});

	}

})(jQuery);