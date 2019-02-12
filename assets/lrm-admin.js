+(function ($) {


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
			  $(".js-lrm-redirects-tpl[data-name='" + $(this).data("name") + "']").html().split('%key%').join( "0" )
		);
		var $wrap = $(this).parent().find(".lrm-redirects-field__roles-wrap");
		$wrap.prepend( $new_row );

		$new_row.find( ".pretty-select" ).selectize();

		bulk_show_or_hide_redirect_inputs();

		reorder_redirects( $wrap );
		// 1232
	});

	$(document).on("click", ".js-lrm-delete-row",function (e) {
		e.preventDefault();
		if ( confirm("Are you sure to delete this row?") ) {
			var parent = $(this).closest(".lrm-redirects-field__roles");
			$(this).closest(".lrm-redirects-field__row").fadeOut(500).remove();

			reorder_redirects( parent );
		}
	});
	// 13223444444444444444444

	$( ".lrm-redirects-field__roles-wrap" ).sortable({
		//containment: ".lrm-redirects-field__row",
		handle: ".js-lrm-sort-row",
		items: ".lrm-redirects-field__row",
		update: function( evt, ui ) {
			console.log(ui);

			reorder_redirects( ui.item.parent() );
		},
	});

	function reorder_redirects( $wrap ) {

		$wrap.find(".lrm-redirects-field__row").each(function(ID, el) {
			var current_KEY = $(el).data("key");
			$(el).data("key", ID);

			$(el).find("input[name],select[name]").each(function(k, input) {
				var new_input_name = $(input).attr("name").replace("["+current_KEY+"]", "["+ID+"]");

				$(input).attr("name", new_input_name);
			});
		});

	}

})(jQuery);