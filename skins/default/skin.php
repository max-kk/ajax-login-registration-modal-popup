<?php

class LRM_Skin_Default extends LRM_Skin_Base {

    public function __construct() {
        $this->slug = 'default';
        $this->title = 'Default';

        // Keep Default skin customizer settings always registered.
        // In PRO this section is shown in the LRM panel; in standalone Free
        // it remains harmless even if Customizer panel wiring differs.
        $this->supports_customizer = true;
        $this->customizer_section_title = '[skin] Default';

        parent::__construct();
    }

    public function register_customizer_settings() {

        $this->_register_customizer_setting( "open_modal", array(
            'default' => '1',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            //'sanitize_callback' => 'sanitize_hex_color',
            'type_class' => 'LRM_Pro_WP_Customize_Control_Button',

            'label'      => __( 'Display modal for customize', 'ajax-login-and-registration-modal-popup' ),
            'description'=> __( 'Open modal >>', 'ajax-login-and-registration-modal-popup' ),

            'type' => 'button',
        ) );

        $this->_register_customizer_setting( 'palette', array(
            'default' => self::default_palette_with_legacy(),
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => array( __CLASS__, 'sanitize_palette' ),
            'label' => 'Palette',
            'type' => 'select',
            'choices' => array(
                'default' => 'Default',
                'light' => 'Luminous Light',
                'dark' => 'Luminous Dark',
            ),
        ), array(
            ':root' => array(
                'attribute' => '--lrm-default-palette',
                'type' => 'css',
                'callback' => array( __CLASS__, 'palette_css_callback' ),
            ),
        ) );

        $this->_register_customizer_setting( "btn_color", array(
            'default' => self::default_with_legacy( 'btn_color', '#ffffff' ),
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => array( __CLASS__, 'sanitize_btn_color' ),

            'label' => 'Buttons color',
            'type' => 'color',
        ), array(
            '.lrm-form a.button,.lrm-form button,.lrm-form button[type=submit],.lrm-form #buddypress input[type=submit],.lrm-form input[type=submit]' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "btn_bg", array(
            'default' => self::default_with_legacy( 'btn_bg', '#2f889a' ),
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => array( __CLASS__, 'sanitize_btn_bg' ),

            'label' => 'Buttons background color',
            'type' => 'color',
        ), array(
            '.lrm-form a.button,.lrm-form button,.lrm-form button[type=submit],.lrm-form #buddypress input[type=submit],.lrm-form input[type=submit]' => array('attribute' => 'background-color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "text_color", array(
            'default' => self::default_with_legacy( 'text_color', '#343642' ),
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => array( __CLASS__, 'sanitize_text_color' ),

            'label' => 'Body text color',
            'type' => 'color',
        ), array(
            '.lrm-user-modal-container' => array('attribute' => 'color','type' => 'css'),
        ) );

    }

    public static function sanitize_palette( $palette ) {
        return in_array( $palette, array( 'default', 'light', 'dark' ), true ) ? $palette : 'default';
    }

    public static function sanitize_btn_color( $value ) {
        return self::sanitize_color_token( $value, 'btn_color' );
    }

    public static function sanitize_btn_bg( $value ) {
        return self::sanitize_color_token( $value, 'btn_bg' );
    }

    public static function sanitize_text_color( $value ) {
        return self::sanitize_color_token( $value, 'text_color' );
    }

    /**
     * Resolve default palette with migration from legacy luminous options/skin slugs.
     *
     * @return string
     */
    protected static function default_palette_with_legacy() {
        $legacy_palette = get_option( 'lrm_luminous__palette', null );
        if ( in_array( $legacy_palette, array( 'light', 'dark' ), true ) ) {
            return $legacy_palette;
        }

        $skins = get_option( 'lrm_skins', array() );
        $legacy_skin = isset( $skins['skin']['current'] ) ? sanitize_key( $skins['skin']['current'] ) : '';

        if ( in_array( $legacy_skin, array( 'luminous_dark', 'default_dark' ), true ) ) {
            return 'dark';
        }

        if ( in_array( $legacy_skin, array( 'luminous_light', 'default_light' ), true ) ) {
            return 'light';
        }

        return 'default';
    }

    /**
     * Keep migrated defaults from legacy luminous keys.
     *
     * @param string $token
     * @param string $fallback
     *
     * @return string
     */
    protected static function default_with_legacy( $token, $fallback ) {
        $legacy_value = get_option( 'lrm_luminous__' . $token, null );
        if ( null !== $legacy_value && '' !== $legacy_value ) {
            return $legacy_value;
        }

        return $fallback;
    }

    /**
     * Keep color options non-empty: if user clears a picker, restore palette default.
     *
     * @param string $value
     * @param string $token
     *
     * @return string
     */
    protected static function sanitize_color_token( $value, $token ) {
        $sanitized = sanitize_hex_color( $value );
        if ( ! empty( $sanitized ) ) {
            return $sanitized;
        }

        $palette = self::sanitize_palette( get_option( 'lrm_default__palette', self::default_palette_with_legacy() ) );
        $defaults = self::get_palette_color_defaults( $palette );

        return isset( $defaults[ $token ] ) ? $defaults[ $token ] : '#000000';
    }

    protected static function get_palette_color_defaults( $palette ) {
        if ( 'dark' === $palette ) {
            return array(
                'btn_bg' => '#b0c8f0',
                'btn_color' => '#183152',
                'text_color' => '#e0e3e8',
            );
        }

        if ( 'light' === $palette ) {
            return array(
                'btn_bg' => '#0059bb',
                'btn_color' => '#ffffff',
                'text_color' => '#181c23',
            );
        }

        return array(
            'btn_bg' => '#2f889a',
            'btn_color' => '#ffffff',
            'text_color' => '#343642',
        );
    }

    protected static function get_palette_tokens( $palette ) {
        if ( 'default' === $palette ) {
            return array(
                'surface' => '#ffffff',
                'surface_2' => '#d2d8d8',
                'surface_3' => '#f7f7f7',
                'border' => '#d2d8d8',
                'muted' => '#809191',
                'input_bg' => '#f7f7f7',
                'input_focus_bg' => '#ffffff',
                'focus_ring' => 'inset 0 1px 1px rgba(0,0,0,.25)',
                'link' => '#2f889a',
                'checkbox_bg' => '#ffffff',
                'selected_tab_bg' => '#ffffff',
                'selected_tab_text' => '#505260',
                'radius' => '.25em',
                'container_shadow' => 'none',
                'input_font_weight' => '300',
                'border_width' => '2px',
            );
        }

        if ( 'dark' === $palette ) {
            return array(
                'surface' => '#1c2024',
                'surface_2' => '#262a2f',
                'surface_3' => '#31353a',
                'border' => '#44474e',
                'muted' => '#c4c6cf',
                'input_bg' => '#262a2f',
                'input_focus_bg' => '#31353a',
                'focus_ring' => '0 0 0 3px rgba(176, 200, 240, 0.25)',
                'link' => '#b0c8f0',
                'checkbox_bg' => '#101418',
                'selected_tab_bg' => '#1c2024',
                'selected_tab_text' => '#e0e3e8',
                'radius' => '12px',
                'container_shadow' => '0 12px 32px rgba(24, 28, 35, 0.12)',
                'input_font_weight' => '400',
                'border_width' => '1px',
            );
        }

        return array(
            'surface' => '#ffffff',
            'surface_2' => '#e6e8f3',
            'surface_3' => '#ebedf9',
            'border' => '#c1c6d7',
            'muted' => '#414754',
            'input_bg' => '#f1f3fe',
            'input_focus_bg' => '#ebedf9',
            'focus_ring' => '0 0 0 3px rgba(0, 89, 187, 0.16)',
            'link' => '#0059bb',
            'checkbox_bg' => '#ffffff',
            'selected_tab_bg' => '#ffffff',
            'selected_tab_text' => '#181c23',
            'radius' => '12px',
            'container_shadow' => '0 12px 32px rgba(24, 28, 35, 0.12)',
            'input_font_weight' => '400',
            'border_width' => '1px',
        );
    }

    public static function palette_css_callback( $attribute_value, $attribute_value_src ) {
        $palette = self::sanitize_palette( $attribute_value_src );
        $tokens = self::get_palette_tokens( $palette );

        return sprintf(
            ':root{--lrm-default-surface:%1$s;--lrm-default-surface-2:%2$s;--lrm-default-surface-3:%3$s;--lrm-default-border:%4$s;--lrm-default-muted:%5$s;--lrm-default-input-bg:%6$s;--lrm-default-input-focus-bg:%7$s;--lrm-default-focus-ring:%8$s;--lrm-default-link:%9$s;--lrm-default-checkbox-bg:%10$s;--lrm-default-selected-tab-bg:%11$s;--lrm-default-selected-tab-text:%12$s;--lrm-default-radius:%13$s;--lrm-default-container-shadow:%14$s;--lrm-default-input-font-weight:%15$s;--lrm-default-border-width:%16$s;}',
            $tokens['surface'],
            $tokens['surface_2'],
            $tokens['surface_3'],
            $tokens['border'],
            $tokens['muted'],
            $tokens['input_bg'],
            $tokens['input_focus_bg'],
            $tokens['focus_ring'],
            $tokens['link'],
            $tokens['checkbox_bg'],
            $tokens['selected_tab_bg'],
            $tokens['selected_tab_text'],
            $tokens['radius'],
            $tokens['container_shadow'],
            $tokens['input_font_weight'],
            $tokens['border_width']
        );
    }
}
