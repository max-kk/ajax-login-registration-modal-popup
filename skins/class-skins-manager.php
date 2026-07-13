<?php

/**
 * Class LRM_Skins
 * @since 2.00
 */
class LRM_Skins extends WP_Skins_Manager_Abstract {

    protected static $instance;

    /*
     * Loads default skins
     */
    public function load_defaults()
    {
        $defaults = ['Default'];

        foreach ($defaults as $skin_one) {
            $skin_name = 'LRM_Skin_' . $skin_one;
            new $skin_name();
        }

        parent::load_defaults();
    }

    /*
     * Loads default skins
     */
    public function load_custom_skins( $skins )
    {
        foreach ($skins as $skin_one) {
            $skin_name = 'LRM_Skin_' . $skin_one;
            new $skin_name();
        }
    }

    /**
     * Ensure at least default skins are loaded before reading registry-dependent data.
     *
     * @return void
     */
    protected function ensure_skins_loaded() {
        if ( empty( $this->skins ) ) {
            $this->load_defaults();
        }
    }

    /**
     * Return list of available skins.
     *
     * @return array
     */
    public function get_list() {
        $this->ensure_skins_loaded();
        return parent::get_list();
    }

    /**
     * Enqueue skin CSS
     */
    public function load_current_skin_assets() {
        $this->ensure_skins_loaded();
        $skin_slug = $this->get_current_skin_slug();
        $skin = $this->get( $skin_slug );

        $base_url = $skin->get_url('') ? $skin->get_url('') : LRM_URL . 'skins/';
        wp_enqueue_style( 'lrm-modal-skin', $base_url  . $skin_slug .'/skin.css', ['lrm-modal'], LRM_ASSETS_VER );

        // Allow a skin load custom assets
        $skin->assets();
        $skin->_enqueue_output_customized_css();
    }

    /**
     * @return string
     */
    public function get_current_skin_slug() {
        $skin_slug = $this->get_customizer_preview_skin_slug();

        if ( ! $skin_slug ) {
            $skin_slug = lrm_setting('skins/skin/current');
        }

        if ( ! $skin_slug ) {
            $skin_slug = 'default';
        }

        if ( ! $this->is_registered( $skin_slug ) ) {
            $skin_slug = 'default';
        }

        return apply_filters('lrm/skins/current', $skin_slug);
    }

    /**
     * In Customizer preview, use the current unsaved value from `customized` payload.
     * This prevents one-step lag when switching skin select control.
     *
     * @return string
     */
    protected function get_customizer_preview_skin_slug() {
        if ( ! is_customize_preview() ) {
            return '';
        }

        if ( empty( $_REQUEST['customized'] ) ) {
            return '';
        }

        $customized_raw = wp_unslash( $_REQUEST['customized'] );
        $customized = json_decode( $customized_raw, true );

        if ( ! is_array( $customized ) ) {
            return '';
        }

        if ( empty( $customized['lrm_skins[skin][current]'] ) ) {
            return '';
        }

        return sanitize_key( $customized['lrm_skins[skin][current]'] );
    }

    /**
     * @return self
     */
    public static function instance(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }
    /**
     * @return self
     */
    public static function i(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }

}
