var LRM = LRM ? LRM : {};

//jQuery(document).ready(function($) {
// jQuery(document).ready(
/** @var $ jQuery */
+(function($) {

	if ( $('.lrm-user-modal').length > 0 ){
		lrm_init()
	} else {
		setTimeout(function() {
			lrm_init();
		}, 1200);
	}

	function lrm_init() {
		var $formModal = $('.lrm-user-modal'),
			  $formLogin = $formModal.find('#lrm-login'),
			  $formSignup = $formModal.find('#lrm-signup'),
			  $formForgotPassword = $formModal.find('#lrm-reset-password'),
			  $formModalTab = $('.lrm-switcher'),
			  $tabLogin = $formModalTab.children('li').eq(0).children('a'),
			  $tabSignup = $formModalTab.children('li').eq(1).children('a'),
			  $forgotPasswordLink = $formLogin.find('.lrm-form-bottom-message a'),
			  $backToLoginLink = $formForgotPassword.find('.lrm-form-bottom-message a'),
			  loader_html = $("#tpl-lrm-button-loader").html();

		$(document).on('lrm_show_signup', signup_selected);

		$(document).on('lrm_show_signin', login_selected);
		$(document).on('lrm_show_login', login_selected);

		setTimeout(function () {
			if (LRM.selectors_mapping.login) {
				$(LRM.selectors_mapping.login)
					  .off("click")
					  .on('click', function (event) {
						  event.preventDefault();
						  $(document).trigger('lrm_show_login');
						  return false;
					  });
			}
			if (LRM.selectors_mapping.register) {
				$(LRM.selectors_mapping.register)
					  .off("click")
					  .on('click', function (event) {
						  event.preventDefault();
						  $(document).trigger('lrm_show_signup');
						  return false;
					  });
			}
		}, 300);

		//$("form.cart").on('submit', signup_selected);

		//open sign-up form
		$(document).on('click', '.lrm-signup', signup_selected);
		$(document).on('click', '.lrm-register', signup_selected);
		//open login-form form
		$(document).on('click', '.lrm-signin', login_selected);
		$(document).on('click', '.lrm-login', login_selected);

		$(document).on('click', '#lrm-login .lrm-form-message a', function (event) {
			event.preventDefault();
			forgot_password_selected();
		});

		//close modal
		$formModal.on('click', function (event) {
			if ($(event.target).is($formModal) || $(event.target).is('.lrm-close-form')) {
				$formModal.removeClass('is-visible');
			}
		});
		//close modal when clicking the esc keyboard button
		$(document).keyup(function (event) {
			if (event.which == '27') {
				$formModal.removeClass('is-visible');
			}
		});

		//switch from a tab to another
		$formModalTab.on('click', function (event) {
			event.preventDefault();
			( $(event.target).is($tabLogin) ) ? login_selected(event, true) : signup_selected(event, true);
		});

		//hide or show password
		$('.hide-password').on('click', function () {
			var togglePass = $(this),
				  passwordField = togglePass.parent().find('input');

			( 'password' == passwordField.attr('type') ) ? passwordField.attr('type', 'text') : passwordField.attr('type', 'password');
			( togglePass.data("hide") == togglePass.text() ) ? togglePass.text(togglePass.data("show")) : togglePass.text(togglePass.data("hide"));
			//focus and move cursor to the end of input field
			passwordField.putCursorAtEnd();
		});

		//show forgot-password form
		$forgotPasswordLink.on('click', function (event) {
			event.preventDefault();
			forgot_password_selected();
		});

		//back to login from the forgot-password form
		$backToLoginLink.on('click', function (event) {
			event.preventDefault();
			login_selected(event, true);
		});

		function login_selected(event, is_system_call) {
			// if (LRM.is_user_logged_in) {
			// 	return true;
			// }

			if ( "undefined" != typeof(is_system_call) || !is_system_call ) {
				LRM.redirect_url = "";
				if ( $(this).hasClass("lrm-redirect") ) {
					LRM.redirect_url = $(this).attr("href");
				}
			}

			$formModal.addClass('is-visible');
			$formLogin.addClass('is-selected');
			$formSignup.removeClass('is-selected');
			$formForgotPassword.removeClass('is-selected');
			$tabLogin.addClass('selected');
			$tabSignup.removeClass('selected');

			if (event) {
				event.preventDefault();
			}
			return false;
		}

		function signup_selected(event, is_system_call) {
			// if (LRM.is_user_logged_in) {
			// 	return true;
			// }

			if ( "undefined" != typeof(is_system_call) || !is_system_call ) {
				LRM.redirect_url = "";
				if ( $(this).hasClass("lrm-redirect") ) {
					LRM.redirect_url = $(this).attr("href");
				}

			}

			$formModal.addClass('is-visible');
			$formLogin.removeClass('is-selected');
			$formSignup.addClass('is-selected');
			$formForgotPassword.removeClass('is-selected');
			$tabLogin.removeClass('selected');
			$tabSignup.addClass('selected');

			if (event) {
				event.preventDefault();
			}
			return false;
		}

		function forgot_password_selected() {
			// if (LRM.is_user_logged_in) {
			// 	return true;
			// }

			$formLogin.removeClass('is-selected');
			$formSignup.removeClass('is-selected');
			$formForgotPassword.addClass('is-selected');
			return false;
		}

		$(document).on('submit', '.lrm-form', function (event) {
			event.preventDefault();

			if ( LRM.is_customize_preview ) {
				alert( "Not possible submit form in Preview Mode!" );
				return;
			}

			var $form = $(event.target);

			// Fix for ACF PRO plugin
			if ( $form.data("action") == "registration" && $form.find("#acf-form-data").length > 0 && acf.validation.active ) {
				if ( "yes" !== $form.data("lrm-acf-validated") ) {
					return;
				}
				// Reset validation flag
				$form.data("lrm-acf-validated", "no");
			}

			$form.find(".has-error").removeClass("has-error")
				  .next("span").removeClass("is-visible");

			$form.find("button[type='submit']").prepend(loader_html);

			$form.find(".lrm-form-message").html("");


			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: LRM.ajax_url,
				data: $form.serialize(),
				success: function (response) {
					$form.find(".lrm-button-loader").remove();

					if ( response.data.message ) {
						if ( !response.data.for ) {
							$form.find(".lrm-form-message").html(response.data.message);

							if (!response.success) {
								$form.find(".lrm-form-message").addClass("lrm-is-error");
							}

							$(".lrm-user-modal").animate({scrollTop: 80}, 400);
						} else {
							$form.find('input[name="' + response.data.for + '"]').addClass('has-error')
								  .next('span').html(response.data.message).addClass('is-visible');
							$form.find(".lrm-form-message").removeClass("lrm-is-error").html("");

						}
					}

					// If user Logged in After Login or Registration
					// If Email Verify after Registration enabled - we skip this
					if (response.success && response.data.logged_in) {
						LRM.is_user_logged_in = true;
						$(document).trigger('lrm_user_logged_in', [response, $form]);

						if (LRM.reload_after_login) {
							window.location.reload();
						}
					}

					$(document).trigger('lrm_pro/maybe_refresh_recaptcha');
				}
				// error: function(jqXHR, textStatus, errorThrown) {
				// 	$form.find(".lrm-button-loader").remove();
				//
				// 	alert("An error occurred, please contact with administrator... \n\rFor more details look at the console (F12 or Ctrl+Shift+I, Console tab)!");
				//
				// 	if (window.console == undefined) {
				// 		return;
				// 	}
				// 	console.log('statusCode:', jqXHR.status);
				// 	console.log('errorThrown:', errorThrown);
				// 	console.log('responseText:', jqXHR.responseText);
				// }
			});
		});

	}

	// ajaxSetup is global, but we use it to ensure JSON is valid once returned.
	$.ajaxSetup( {
		dataFilter: function( raw_response, dataType ) {
			// We only want to work with JSON
			if ( 'json' !== dataType ) {
				return raw_response;
			}

			if ( lrm_is_valid_json( raw_response ) ) {
				return raw_response;
			} else {
				// Attempt to fix the malformed JSON
				var maybe_valid_json = raw_response.match( /{"success.*}/ );

				if ( null === maybe_valid_json ) {
					console.log( 'Unable to fix malformed JSON' );
				} else if ( lrm_is_valid_json( maybe_valid_json[0] ) ) {
					console.log( 'Fixed malformed JSON. Original:' );
					console.log( raw_response );
					raw_response = maybe_valid_json[0];
				} else {
					console.log( 'Unable to fix malformed JSON' );
				}
			}

			return raw_response;
		}
	} );


	function lrm_is_valid_json ( raw_json ) {
		try {
			var json = $.parseJSON( raw_json );

			return ( json && 'object' === typeof json );
		} catch ( e ) {
			return false;
		}
	}

//});
})(jQuery);


//credits http://css-tricks.com/snippets/jquery/move-cursor-to-end-of-textarea-or-input/
jQuery.fn.putCursorAtEnd = function() {
	return this.each(function() {
    	// If this function exists...
    	if (this.setSelectionRange) {
      		// ... then use it (Doesn't work in IE)
      		// Double the length because Opera is inconsistent about whether a carriage return is one character or two. Sigh.
      		var len = jQuery(this).val().length * 2;
      		this.focus();
      		this.setSelectionRange(len, len);
    	} else {
    		// ... otherwise replace the contents with itself
    		// (Doesn't work in Google Chrome)
			jQuery(this).val(jQuery(this).val());
    	}
	});
};