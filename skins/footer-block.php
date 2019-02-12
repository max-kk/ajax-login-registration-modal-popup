<?php

class SPC_Block_Footer extends SPC_Skin_Base {

    public function __construct() {
        $this->slug = 'footer';
        $this->title = 'Default';

        $this->supports_customizer = true;

        $this->customizer_section_title = 'Footer';
        $this->customizer_section_panel = 'spc_colors';

        parent::__construct();
    }

    public function register_customizer_settings() {

        $this->_register_customizer_setting( "bg", array(
            'default' => '#f7f7f7',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Footer background',
            'type' => 'color',
        ), array(
            '#footer' => array('attribute' => 'background-color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "copyright_color", array(
            'default' => '#808080',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Copyright color',
            'type' => 'color',
        ), array(
            '#footer_logo' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "socials_color", array(
            'default' => '#808080',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Socials color',
            'type' => 'color',
        ), array(
            '#footer_social ul li a' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "border_color", array(
            'default' => '#e31e09',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Top border color',
            'type' => 'color',
        ), array(
            '#footer' => array('attribute' => 'border-top-color','type' => 'css',),
        ) );

    }

}

new SPC_Block_Footer();