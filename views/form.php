<!--
<?php
/**
 * @version 1.06
 * Changelog:
 *  1.06: Moved to separate files, added full width button style & option to hide the tabs
 *  1.05: $role_silent and $role apply
 *  1.04: font icons classes, removed labels text, added "aria-label", fixed registration password placeholder
 *  1.03: added password confirmation field
 *  1.02: added "redirect_url" field
*/

defined( 'ABSPATH' ) || exit;

/** @var bool $is_inline */
/** @var string $role */
/** @var bool $role_silent */

/** @var string $default_tab "login"/"register"/"lost-password" */
$fields_required = ('both' === lrm_setting('advanced/validation/type')) ? 'required' : '';
$icons_class = lrm_setting('skins/skin/icons');
$icons_class = $icons_class === 'svg' ? $icons_class : $icons_class  . ' lrm-is-font';
$extra_main_class = 'lrm-btn-style--' . lrm_setting('skins/skin/btn_style');
//echo lrm_setting('advanced/validation/type');

$fieldset_submit_class = 'fieldset--' . lrm_setting('skins/skin/btn_style');

$users_can_register = apply_filters('lrm/users_can_register', get_option("users_can_register") );

?>
-->
<div class="lrm-main lrm-font-<?= $icons_class; ?> <?php echo !$is_inline ? 'lrm-user-modal' : 'lrm-inline is-visible'; ?> <?= esc_attr($extra_main_class); ?>" <?php echo !$is_inline ? 'style="visibility: hidden;"' : ''?>> <!-- this is the entire modal form, including the background -->
<!--<div class="lrm-user-modal" style="visibility: hidden;">  this is the entire modal form, including the background -->

    <div class="lrm-user-modal-container"> <!-- this is the container wrapper -->
        <div class="lrm-user-modal-container-inner"> <!-- this is the container wrapper -->

            <?php
            if ( ! lrm_setting('skins/skin/hide_tabs') ) {
                require "form-parts/tabs.php";
            }
            require "form-parts/login.php";
            require "form-parts/register.php";
            require "form-parts/lost-password.php";
            ?>

        </div> <!-- lrm-user-modal-container -->
        <a href="#0" class="lrm-close-form" title="<?php echo lrm_setting('messages/other/close_modal'); ?>">
            <span class="lrm-ficon-close"></span>
        </a>

    </div> <!-- lrm-user-modal-container -->

</div> <!-- lrm-user-modal -->