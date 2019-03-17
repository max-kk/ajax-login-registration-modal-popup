<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.41
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_Deactivator {

    /**
     * Remove data from database when plugin deactivates
     *
     * @since    1.41
     */
    public static function deactivate() {

        if ( lrm_setting('advanced/uninstall/remove_all_data') ) {

            delete_option("lrm_general");
            delete_option("lrm_advanced");
            delete_option("lrm_messages");
            delete_option("lrm_mails");
            delete_option("lrm_beg_message");
            delete_option("lrm-forms-init");

            delete_option("lrm_general_pro");
            delete_option("lrm_auto_trigger");
            delete_option("lrm_integrations");
            delete_option("lrm_messages_pro");
            delete_option("lrm_pro_version");

            if ( lrm_is_pro('1.60') ) {
                LRM_API_Manager::instance()->uninstall();
            }

        }

    }
}
