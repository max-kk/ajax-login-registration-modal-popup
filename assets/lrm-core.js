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

	function is_mobile_or_tablet() {
		var check = false;

		(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);

		return check;
	}

	function lrm_init() {
		// var $formModal = $('.lrm-user-modal'),
		// 	  $formLogin = $formModal.find('#lrm-login'),
		// 	  $formSignup = $formModal.find('#lrm-signup'),
		// 	  $formForgotPassword = $formModal.find('#lrm-reset-password'),
		// 	  $formModalTab = $('.lrm-switcher'),
		// 	  $tabLogin = $formModalTab.children('li').eq(0).children('a'),
		// 	  $tabSignup = $formModalTab.children('li').eq(1).children('a'),
		// 	  $forgotPasswordLink = $formLogin.find('.lrm-form-bottom-message a'),
		// 	  $backToLoginLink = $formForgotPassword.find('.lrm-form-bottom-message a'),

	  	var loader_html = '<span class="lrm-button-loader"> <svg version="1.1" id="L4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 40" enable-background="new 0 0 0 0" xml:space="preserve"> <circle fill="#ffffff" stroke="none" cx="30" cy="20" r="6"> <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.1"/> </circle> <circle fill="#ffffff" stroke="none" cx="50" cy="20" r="6"> <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.2"/> </circle> <circle fill="#ffffff" stroke="none" cx="70" cy="20" r="6"> <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.3"/> </circle> </svg></span>';

		$(document).on('lrm_show_signup', signup_selected);

		$(document).on('lrm_show_signin', login_selected);
		$(document).on('lrm_show_login', login_selected);

		setTimeout(function () {
			if (LRM.selectors_mapping.login) {
				$(LRM.selectors_mapping.login)
					  .off("click")
					  .on('click', function (event) {
						  event.preventDefault();
						  $(document).trigger('lrm_show_login', [event]);
						  return false;
					  });
			}
			if (LRM.selectors_mapping.register) {
				$(LRM.selectors_mapping.register)
					  .off("click")
					  .on('click', function (event) {
						  event.preventDefault();
						  $(document).trigger('lrm_show_signup', [event]);
						  return false;
					  });
			}
		}, 300);

		//$("form.cart").on('submit', signup_selected);

		var handle_event = is_mobile_or_tablet() ? 'touchend' : 'click';

		//open sign-up form
		$(document).on('click', '.lrm-signup', signup_selected);
		$(document).on(handle_event, '[class*="lrm-register"]', signup_selected);
		$(document).on('click', '.lrm-switch-to--register', signup_selected);

		//open login-form form
		$(document).on('click', '.lrm-signin', login_selected);
		$(document).on(handle_event, '[class*="lrm-login"]', login_selected);
		$(document).on('click', '.lrm-switch-to--login', login_selected);

		$(document).on('click', '.lrm-login .lrm-form-message a,.lrm-switch-to--reset-password', function (event) {
			event.preventDefault();
			forgot_password_selected(event);
		});

		//close modal
		$('.lrm-user-modal').on('click', function(event){
			if ($(event.target).is('.lrm-user-modal') || $(event.target).is('.lrm-close-form')) {
				$(this).removeClass('is-visible');
			}
		});
		//close modal when clicking the esc keyboard button
		$(document).keyup(function (event) {
			if (event.which == '27') {
				$(".lrm-user-modal").removeClass('is-visible');
			}
		});

		//switch from a tab to another
		// $formModalTab.on('click', function (event) {
		// 	event.preventDefault();
		// 	( $(event.target).is($tabLogin) ) ? login_selected(event, true) : signup_selected(event, true);
		// });

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
		// $forgotPasswordLink.on('click', function (event) {
		// 	event.preventDefault();
		// 	forgot_password_selected(event);
		// });
		//
		// //back to login from the forgot-password form
		// $backToLoginLink.on('click', function (event) {
		// 	event.preventDefault();
		// 	login_selected(event, true);
		// });

		function login_selected(event, event_orig) {
			// if (LRM.is_user_logged_in) {
			// 	return true;
			// }

			/**
			 * @since 1.34
			 * this - clicked element
			 */
			$(document).triggerHandler("lrm/before_display/login", this, event);

			var $formModal = $(event.target).closest(".lrm-main");

			if ( ! $formModal.length ) {
				LRM.redirect_url = "";
				if ( !event_orig ) {
					var el = event.target ? event.target : this;
				} else {
					var el = event_orig.target;
				}
				if ( el && $(el).hasClass("lrm-redirect") ) {
					LRM.redirect_url = $(el).attr("href");
				}
			}

			if ( ! $formModal.length ) {
				$formModal = $(".lrm-user-modal");
			}

			// var $formModal = $('.lrm-user-modal'),
			// 	  $formLogin = $formModal.find('.lrm-login'),
			// 	  $formSignup = $formModal.find('.lrm-signup'),
			// 	  $formForgotPassword = $formModal.find('.lrm-reset-password'),
			// 	  $formModalTab = $('.lrm-switcher'),
			// 	  $tabLogin = $formModalTab.children('li').eq(0).children('a'),
			// 	  $tabSignup = $formModalTab.children('li').eq(1).children('a'),
			// 	  $forgotPasswordLink = $formLogin.find('.lrm-form-bottom-message a'),
			// 	  $backToLoginLink = $formForgotPassword.find('.lrm-form-bottom-message a'),
			//
			$formModal.addClass('is-visible');
			$formModal.find('.lrm-signin-section').addClass('is-selected');
			$formModal.find('.lrm-signup-section').removeClass('is-selected');
			$formModal.find('.lrm-reset-password-section').removeClass('is-selected');
			$formModal.find('.lrm-switcher').children('li').eq(0).children('a').addClass('selected');
			$formModal.find('.lrm-switcher').children('li').eq(1).children('a').removeClass('selected');

			if (event) {
				event.preventDefault();
			}
			return false;
		}

		function signup_selected(event, event_orig) {
			// if (LRM.is_user_logged_in) {
			// 	return true;
			// }

			/**
			 * @since 1.34
			 * this - clicked element
			 */
			$(document).triggerHandler("lrm/before_display/registration", this, event);

			// $formModal.addClass('is-visible');
			// $formLogin.removeClass('is-selected');
			// $formSignup.addClass('is-selected');
			// $formForgotPassword.removeClass('is-selected');
			// $tabLogin.removeClass('selected');
			// $tabSignup.addClass('selected');

			var $formModal = $(event.target).closest(".lrm-main");


			if ( ! $formModal.length ) {
				LRM.redirect_url = "";
				if ( !event_orig ) {
					var el = event.target ? event.target : this;
				} else {
					var el = event_orig.target;
				}
				if ( el && $(el).hasClass("lrm-redirect") ) {
					LRM.redirect_url = $(el).attr("href");
				}
			}

			if ( ! $formModal.length ) {
				$formModal = $(".lrm-user-modal");
			}

			$formModal.addClass('is-visible');
			$formModal.find('.lrm-signin-section').removeClass('is-selected');
			$formModal.find('.lrm-signup-section').addClass('is-selected');
			$formModal.find('.lrm-reset-password-section').removeClass('is-selected');
			$formModal.find('.lrm-switcher').children('li').eq(0).children('a').removeClass('selected');
			$formModal.find('.lrm-switcher').children('li').eq(1).children('a').addClass('selected');

			if (event) {
				event.preventDefault();
			}
			return false;
		}

		function forgot_password_selected(event) {
			// if (LRM.is_user_logged_in) {
			// 	return true;
			// }

			/**
			 * @since 1.34
			 * this - clicked element
			 */
			$(document).triggerHandler("lrm/before_display/forgot_password", this, event);

			var $formModal = $(event.target).closest(".lrm-main");

			if ( ! $formModal.length ) {
				$formModal = $(".lrm-user-modal");
			}

			$formModal.addClass('is-visible');
			$formModal.find('.lrm-signin-section').removeClass('is-selected');
			$formModal.find('.lrm-signup-section').removeClass('is-selected');
			$formModal.find('.lrm-reset-password-section').addClass('is-selected');

			// $formLogin.removeClass('is-selected');
			// $formSignup.removeClass('is-selected');
			// $formForgotPassword.addClass('is-selected');
			return false;
		}

		$(document).on('submit', '.lrm-form', lrm_submit_form);

		function lrm_submit_form (event) {
			if ( LRM.is_customize_preview ) {
				alert( "Not possible to submit form in Preview Mode!" );
				return;
			}
			var $form = $(event.target);

			event.preventDefault();

			if ( $(document).triggerHandler('lrm/do_not_submit_form', $form) ) {
				return false;
			}

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

					// $form.data("action") for get
					$(document).triggerHandler('lrm/ajax_response', [response, $form, $form.data("action")]);

					// If user Logged in After Login or Registration
					// If Email Verify after Registration enabled - we skip this
					if (response.success && response.data.logged_in) {
						LRM.is_user_logged_in = true;
						$(document).triggerHandler('lrm_user_logged_in', [response, $form, $form.data("action")]);

						if (LRM.reload_after_login) {
							window.location.reload();
						}
					}

					$(document).triggerHandler('lrm_pro/maybe_refresh_recaptcha');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$form.find(".lrm-button-loader").remove();

					alert("An error occurred, please contact with administrator... \n\rFor more details look at the console (F12 or Ctrl+Shift+I, Console tab)!");

					if (window.console == undefined) {
						return;
					}
					console.log('statusCode:', jqXHR.status);
					console.log('errorThrown:', errorThrown);
					console.log('responseText:', jqXHR.responseText);
				}

			});

			return false;
		}

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