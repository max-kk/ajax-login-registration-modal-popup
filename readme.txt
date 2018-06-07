=== AJAX Login and Registration modal popup ===
Contributors: kaminskym
Tags: login, registration, register, modal, popup, ajax
Requires at least: 4.1
Tested up to: 4.9.5
Requires PHP: 5.4
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easy to integrate modal with Login and Registration features.

== Description ==

Easy to integrate modal with Login and Registration features. Compatible with any theme.

**Features:**

1. Easy to integrate
2. Well customizable
3. 100% responsive
4. Beautiful coded
5. Compatible with other plugins
6. Tested with latest WP version
7. Developer support (via forums or personal via email for PRO users)

**Customization options:**

1. You can add your custom CSS selectors to attach modal
2. All texts/messages can be edited/translated in settings
3. Emails (for registration and lost password) can customized in settings

**Free version compatible with:**

1. [Login LockDown](https://wordpress.org/plugins/login-lockdown/) (limit login attempts count)
2. [WP Facebook Login](https://wordpress.org/plugins/wp-facebook-login/)
3. [WP Foto Vote](https://wp-vote.net/wordpress-voting-plugin/) (photo contest plugin from author of this plugin ☺)

**Roadmap**

* Allow include form to page content (without modal)
* Colors/styles customizer

= PRO features =

* 3 months personal support from developer via Email
* Troubleshooting problems and conflicts with other plugins/themes (1 site)
* Unlimited plugin updates
* Compatibility with other popular plugins (list below)

**The PRO version extra features:**
1. Allow user set custom password (not random generated) during registration
2. Redirect user to specified page after login/registration (for example User Profile)
3. [Request other feature >>](https://maxim-kaminsky.com/shop/contact-me/)

**The PRO version is 100% tested and are compatible with a following plugins:**

1. [Woocommerce](https://wordpress.org/plugins/woocommerce/) (show modal when clicked "Add to cart" in list or single product)
2. [Captcha](https://wordpress.org/plugins/captcha/)
3. [WP reCaptcha Integration](https://wordpress.org/plugins/wp-recaptcha-integration/)
4. [Really Simple CAPTCHA](https://wordpress.org/plugins/really-simple-captcha/)
5. [Captcha bank](https://ru.wordpress.org/plugins/captcha-bank/)
6. [WordPress Social Login](https://wordpress.org/plugins/wordpress-social-login/) (social login buttons below login/register form)
7. [Social Login WordPress Plugin – AccessPress](https://wordpress.org/plugins/accesspress-social-login-lite/) (social login buttons below login/register form)
8. Math Captcha - soon
9. Easy Digital Downloads - soon
10. [Request other plugin >>](https://maxim-kaminsky.com/shop/contact-me/)


[GET A PRO >>](https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ajax-login-registration-modal-popup` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the 'Settings' -> 'Login/Register modal' screen to configure the plugin

== Frequently Asked Questions ==

= How to integrate this plugin to my website? =

Just add class "lrm-login" to the <button> or <a> element for show login tab or "lrm-signup" for registration tab.

= How can I attach modal to menu item? =

Use this tutorial to add class from text above for your menu element - [https://www.lockedowndesign.com/add-css-classes-to-menu-items-in-wordpress/](https://www.lockedowndesign.com/add-css-classes-to-menu-items-in-wordpress/)

= How can I call modal from Javascript? =

= Developer hooks =

*Javascript*

For add your hook when user successful logged in/registered use action "lrm_user_logged_in"
`
jQuery(document).on('lrm_user_logged_in', function(response, $form) {
    // Your JS code
});
`

For call from From JS modal with login tab:

`
jQuery(document).trigger('lrm_show_signup');
`

For call from From JS modal with registration tab:

`
jQuery(document).trigger('lrm_show_login');
`

Example for load Modal after page load (this will work only if user not logged in):

`
jQuery(document).ready(function( $ ){
    $(document).trigger('lrm_show_login');
});
 `

= Login issue with Adminize plugin =

If you have login issue with Adminize plugin - go to Adminize plugin settings and enable option "Allow page access".

== Screenshots ==

1. Login tab
2. Registration tab
3. Lost password tab
4. Admin settings - General
5. Admin settings - Advanced
6. Admin settings - Emails
7. Admin settings - Expressions
8. Integration link or button element
9. Integration to menu item
10. Admin settings - General [PRO]
11. Admin settings - Expressions [PRO]
12. Registration with Password field [PRO]

== Changelog ==

= VER 1.20 - 02/06/2018 =

- Changed "password reset" way: before after password reset request password was immediately changes, now email will be send with change password link
- Warning if Registration is disabled in WP Settings
- Possible Hide First & Last name fields in plugin Settings

==== Update instructions: ====

**Open "EXPRESSIONS" tab and find "Lost password" section**

Replace "Lost your password? Please enter your email address. You will receive mail with new password." with "Lost your password? Please enter your email address. You will receive mail with link to set new password."

**Open "Emails" tab and find "Lost password" section**

Replace "Body" field text with following (or similar):

`Someone has requested a password reset for the following username: {{USERNAME}}' . "\r\n\r\n" .

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address: {{CHANGE_PASSWORD_URL}},`

= VER 1.18 - 30/05/2018 =

- New: added Username field to registration form, to avoid issues if user with equal First and Last exists

= VER 1.17 - 25/05/2018 =

- Fix: fixed issue with modal display in Safari for Windows
- Tweak: scroll to error message, if error happens

= VER 1.16 - 07/05/2018 =

- Tweak: temporary fix for Cache: disable nonce verification if cache enabled

= VER 1.14 - 02/05/2018 =

- Bugfix: doesn't possible to put Html into Terms text field + possible submit form without checking Terms box.
- New: Added ability to change Email format: plain or html

= VER 1.13 - 27/04/2018 =

- Bugfix: On some sites Modal can't work because form html is loaded after script

= VER 1.12 - 25/04/2018 =

- Bugfix: Registration "Terms box" it's displayed even if not disabled in settings
- Tweak: Option "Auto-login user after Registration" replaced with "User must confirm email after registration?"
- Wording tweaks - thanks to @Paul from U2GUIDE.com

= VER 1.11 - 23/04/2018 =

- Notice if newer PRO version exists

= VER 1.10 - 23/04/2018 =

- Added German translation
- Finally implemented support for https://translate.wordpress.org/projects/wp-plugins/ajax-login-and-registration-modal-popup
- "Show" and "Hide" password labels can be translated in admin
- "Expressions" now escaped in admin and public to avoid issues with splashing quotes in FR and other languages
- Other tweaks

= VER 1.05 - 05/04/2018 =

- Added French and Spanish translations (thanks to @EricMangin from u2guide.com)

= VER 1.04 - 30/03/2018 =

- Implemented get-text calls to allow add default translations for all languages. Use https://translate.wordpress.org/projects/wp-plugins/ajax-login-and-registration-modal-popup to translate plugin.
- Make password hidden **** by default

= VER 1.01 - 11/03/2018 =

- Added integration tutorial
- Added `.lrm-show-if-logged-in` and `.lrm-hide-if-logged-in` classes

= VER 1.0 - 03/03/2018 =

Released