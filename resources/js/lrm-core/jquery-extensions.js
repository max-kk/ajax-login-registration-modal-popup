//credits http://css-tricks.com/snippets/jquery/move-cursor-to-end-of-textarea-or-input/
jQuery.fn.putCursorAtEnd = function () {
	return this.each(function () {
		// If this function exists...
		if (this.setSelectionRange) {
			// ... then use it (Doesn't work in IE)
			// Double the length because Opera is inconsistent about whether a carriage return is one character or two. Sigh.
			var len = jQuery(this).val().length * 2;
			jQuery(this).trigger('focus');
			this.setSelectionRange(len, len);
		} else {
			// ... otherwise replace the contents with itself
			// (Doesn't work in Google Chrome)
			jQuery(this).val(jQuery(this).val());
		}
	});
};

/**
 * @since 2.02
 *
 * @param url
 * @param options
 * @returns {*}
 */
jQuery.cachedScript = function( url, options ) {
	// Allow user to set any option except for dataType, cache, and url
	options = jQuery.extend( options || {}, {
		dataType: "script",
		cache: true,
		url: url
	});

	// Use $.ajax() since it is more flexible than $.getScript
	// Return the jqXHR object so we can chain callbacks
	return jQuery.ajax( options );
};
