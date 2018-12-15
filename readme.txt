=== AJAX Login and Registration modal popup ===
Contributors: kaminskym
Tags: login, registration, register, modal, popup, ajax
Requires at least: 4.1
Tested up to: 4.9.6
Requires PHP: 5.4
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easy to integrate modal with Login and Registration features.

== Description ==

Easy to integrate modal with Login and Registration features. Compatible with any theme.

[DEMO >>](https://demo.maxim-kaminsky.com/lrm/)

**Features:**

1. Easy to integrate
2. Well customizable
3. 100% responsive
4. Beautiful coded
5. Compatible with other plugins
6. Tested with latest WP version
7. Developer support (via forums or personal via email for PRO users)
7. Supports WP Customizer (in PRO)

**Customization options:**

1. You can add your custom CSS selectors to attach modal
2. All texts/messages can be edited/translated in settings
3. Emails (for registration and lost password) can customized in settings

**Free version compatible with:**

1. [Login LockDown](https://wordpress.org/plugins/login-lockdown/) (limit login attempts count)
2. [WP Facebook Login](https://wordpress.org/plugins/wp-facebook-login/)
3. [WP Foto Vote contests](https://wp-vote.net/wordpress-voting-plugin/) (photo contest plugin from author of this plugin ☺)
4. [All In One WP Security & Firewall](https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/) (tested with "Renamed Login Page")
5. [Eonet Manual User Approve](https://wordpress.org/plugins/eonet-manual-user-approve/): review user before they an sign in - [tutorial](https://trello.com/c/72l3LfC7/3-how-to-manually-review-new-users-registrations)
6. [WPML](https://wpml.org/): Multi-language support - [tutorial](https://trello.com/c/trX6b6xR/4-multi-language-support-via-wpml)
7. [s2member](https://wordpress.org/plugins/s2member/) plugin: tweaks for login process

**Roadmap**

* Allow include form to page content (without modal)
* Colors/styles customizer [partially implemented via WP Customizer]
* Documentation and Videos
* ?WooCommerce Login/Registration form integration
* Registration Form builder

= PRO features =

* 6 months personal support from developer via Email
* Troubleshooting problems and conflicts with other plugins/themes (1 site)
* Unlimited plugin updates
* Compatibility with other popular plugins (list below)

**The PRO version extra features:**

1. Allow user set custom password (not random generated) during registration
2. Redirect user to specified page after login/registration/logout (for example to the User Profile)
3. User verification via click on the link in registration email
4. Email only registration - hide username filed from registration form
5. Customize buttons color in [WP Customizer](https://trello.com/c/RXvJzKIQ/7-how-to-customize-form-colors)
6. [Request other feature >>](https://maxim-kaminsky.com/shop/contact-me/)

**The PRO version is 100% tested and are compatible with a following plugins:**

1. **[Woocommerce](https://wordpress.org/plugins/woocommerce/)** (show modal when clicked "Add to cart" in list or single product or in Cart when click "Process to Checkout")
2. [WooCommerce Sensei](https://woocommerce.com/products/sensei/) (fix for Login process)
3. **[WP reCaptcha Integration](https://wordpress.org/plugins/wp-recaptcha-integration/)**
4. **[Invisible reCaptcha](https://wordpress.org/plugins/invisible-recaptcha/)** - [tutorial](https://trello.com/c/SYR5pXxn/8-how-to-set-up-invisible-recaptcha)
5. **[BuddyPress](https://wordpress.org/plugins/buddypress/)** ([replace default registration form with BuddyPress one >>](https://monosnap.com/file/3RNMa7Wl3EYWidw9znAJbgJ5QVL7oy))
6. [Captcha](https://wordpress.org/plugins/captcha/)
7. [Really Simple CAPTCHA](https://wordpress.org/plugins/really-simple-captcha/)
8. [Captcha bank](https://ru.wordpress.org/plugins/captcha-bank/)
9. [WordPress Social Login](https://wordpress.org/plugins/wordpress-social-login/) (social login buttons below login/register form)
10. [Social Login WordPress Plugin – AccessPress](https://wordpress.org/plugins/accesspress-social-login-lite/) (social login buttons below login/register form)
11. [Jetpack - SSO login](https://jetpack.com/support/sso/) [Wordpress.com login button >>](https://monosnap.com/file/4Na5FYYONRj79jnLBmQFK3hjnMJQDR)
12. [WC Vendors & WC Vendors Pro](https://wordpress.org/plugins/wc-vendors/) [Apply to become vendor checkbox >>](https://monosnap.com/file/TmpY4bYTHwF36ouN6fGpdjKZi5k3jz)
13. Math Captcha - soon
14. Easy Digital Downloads - soon
15. [Request other plugin >>](https://maxim-kaminsky.com/shop/contact-me/)

[GET PRO >>](https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/)

[PRO DEMO >>](https://demo.maxim-kaminsky.com/lrm/pro/)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ajax-login-registration-modal-popup` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the 'Settings' -> 'Login/Register modal' screen to configure the plugin

== Frequently Asked Questions ==

= How to integrate this plugin to my website? =

Just add class `lrm-login` to the `<button>` or `<a>` element for show login tab or `lrm-signup` for registration tab.

Example: `<a href="/wp-login.php" class="lrm-login">Login</a>`

= How can I attach modal to menu item? =

Use this tutorial to add class from text above for your menu element - [https://www.lockedowndesign.com/add-css-classes-to-menu-items-in-wordpress/](https://www.lockedowndesign.com/add-css-classes-to-menu-items-in-wordpress/)

= Class "lrm-hide-if-logged-in" is not working in GeneratePress theme? =

[https://www.wpbeginner.com/plugins/how-to-easily-add-custom-css-to-your-wordpress-site/](Add this custom css):
`
body.logged-in li.lrm-hide-if-logged-in a { display: none; }
body.logged-in li [class*='lrm-hide-if-logged-in'] a { display: none; }
`

Thanks to Kash Monsefi for a report.

= How can I add log out link/button? =

Please read this post: https://wordpress.org/support/topic/logout-link-8/#post-10180543

= How can I call modal from Javascript? =

Look "Developer hooks" section below.

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

== Known issues ==

- With Theme my login (TML) plugin (3 Password fields on the Create Account tab, if enable password field in LRM and TML)

== Changelog ==

= VER 1.41 - 02/11/2018 =

- New option - use WooCommerce emails templates, so your emails will looks the same (useful for WC stores)
- Tweaks for password managers

= VER 1.40 - 10/10/2018 =

- Tweak for https://wordpress.org/plugins/eonet-manual-user-approve/ (stop reset password during user approval)
- A lot of JS & CSS updates - so please test your modal forms design and functionality after update!!
- Inline mode with shortcode [lrm_form default_tab="login"], where default_tab can be set as 'login', 'register', 'lost-password'

= VER 1.37 - 06/10/2018 =

- Added new email - for admin about new user registration (please note - in case of using Social login this email will be not triggered)
- Added html template field to simplify email templates customization - https://trello.com/c/OX5IIUEr/10-how-to-style-email-templates

= VER 1.36 - 05/10/2018 =

- Tweaks for s2member plugin + tweaks that can fix possible issues wih any other plugins that tried to redirect after login
- Fixed a bug with the "Forgot Password" link in modal

= VER 1.35 - 20/09/2018 =

- Fixes for a Polylang plugin

= VER 1.34 - 18/09/2018 =

- Tweaks for PRO compatibility with the [Invisible reCaptcha](https://wordpress.org/plugins/invisible-recaptcha/) plugin- [tutorial](https://trello.com/c/SYR5pXxn/8-how-to-set-up-invisible-recaptcha)
- Tweaks for CSS & JS selectors for make them working even with prefixes, like "divi-lrm-login", that can be added by some themes in menus
- Added [predefined list](https://monosnap.com/file/TsOHDJZR4HzlgkmiZVvPmxkkGPpPH1) of menu items

= VER 1.33 - 07/09/2018 =

- Small tweaks for LRM Pro to allow the user create account with email only
- Return user ID after login/registration to JS
- Full compatibility with [WPML](https://wpml.org/) plugin for [multi-language support](https://trello.com/c/trX6b6xR/4-multi-language-support-via-wpml)

= VER 1.32 - 01/09/2018 =

- Small tweaks for LRM Pro BuddyPress integration - option to disable BuddyPress form

= VER 1.31 - 27/08/2018 =

- Small tweaks for LRM Pro BuddyPress integration

= VER 1.30 - 15/08/2018 =

- Since this version if New Users Registration is Off in settings - Registration Tab will be hidden

= VER 1.29 - 09/08/2018 =

- Loading spinner html moved from php to JS to avoid issues with the W3C Total Cache plugin

= VER 1.28 - 01/08/2018 =

- Added filter "lrm/mails/registration/is_need_send" that allows stop sending registration email
- Partial Russian translation added (thanks to @raccoon72)

= VER 1.27 - 30/07/2018 =

- Integrated auto-updater for PRO version

= VER 1.26 - 26/07/2018 =

- Small admin instructions tweaks
- Optionally you can disable Browser validation (in Advanced section)

= VER 1.25 - 21/07/2018 =

- Fixed Critical issue if PRO version is installed and version < 1.17

= VER 1.24 - 20/07/2018 =

- Fixed password reset issues with WooCommerce installed
- Fixed issue with slashed quotes after saving in Emails section
- Minor fixes

= VER 1.23 - 30/06/2018 =

- Fixed issues with HTTPS and Login (when try open to /wp-admin/ wordpress require re-login).

= VER 1.22 - 18/06/2018 =

- Message about disabled user registration now displayed only on Plugin settings page (not site-wide)
- Fixes for "All In One WP Security & Firewall" plugin

= VER 1.21 - 14/06/2018 =

- Fixed issue with Reset password: not possible use username to reset, only email

= VER 1.20 - 10/06/2018 =

- Warning if New Users Registration is disabled in WP Settings
- Possible Hide First & Last name fields (Registration Form) in plugin Settings
- Small settings fix: (default "true" values for checkboxes is added in a wrong way)
- Changed "password reset" way: before after password reset request password was immediately changed, now email will be send with change password link

= Update instructions from 1.1x to 1.20: =

**Open "EXPRESSIONS" tab and find "Lost password" section**

Replace
**"Lost your password? Please enter your email address. You will receive mail with new password."**
with
**"Lost your password? Please enter your email address. You will receive mail with link to set new password."**

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