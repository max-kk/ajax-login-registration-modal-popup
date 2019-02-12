<?php
/**
 * Setup menus in WP admin.
 *
 * @category Admin
 * @since    1.34
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LRM_Admin_Menus {
    function __construct() {
        add_action('admin_init', array($this, 'add_nav_menu_meta_boxes'));
    }

    public function add_nav_menu_meta_boxes() {
        add_meta_box(
            'lrm_nav_links',
            __('Login & Registration modal', 'ajax-login-and-registration-modal-popup' ),
            array( $this, 'nav_menu_links'),
            'nav-menus',
            'side',
            'low'
        );
    }

    public function nav_menu_links() {?>
        <div id="posttype-lrm" class="posttypediv">
            <div id="tabs-panel-lrm-login" class="tabs-panel tabs-panel-active">
                <ul id ="lrm-login-checklist" class="categorychecklist form-no-clear">
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> Login Link
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php _e('Login', 'ajax-login-and-registration-modal-popup' ); ?>">
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#login">
                        <input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="lrm-login lrm-hide-if-logged-in">
                    </li>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> Register Link
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php _e('Register', 'ajax-login-and-registration-modal-popup' ); ?>">
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#register">
                        <input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="lrm-register lrm-hide-if-logged-in">
                    </li>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> Logout Link (PRO Only)
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php _e('Log-out', 'ajax-login-and-registration-modal-popup' ); ?>">
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php echo site_url('/?lrm_logout=1'); ?>">
                        <input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="lrm-show-if-logged-in">
                    </li>
                </ul>
            </div>
            <p class="button-controls">
        			<span class="list-controls">
        				<a href="<?php echo admin_url( "nav-menus.php?page-tab=all&amp;selectall=1#posttype-lrm" ); ?>" class="select-all"><?php _e( 'Select All' ); ?></a>
        			</span>
                <span class="add-to-menu">
                    <input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu' ); ?>" name="add-post-type-menu-item" id="submit-posttype-lrm">
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
    <?php }
}