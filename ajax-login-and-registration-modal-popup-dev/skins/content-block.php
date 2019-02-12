<?php

class SPC_Block_Content extends SPC_Skin_Base {

    public function __construct() {
        $this->slug = 'content';
        $this->title = 'content';

        $this->supports_customizer = true;

        $this->customizer_section_title = 'Content';
        $this->customizer_section_panel = 'spc_colors';

        parent::__construct();
    }

    public function register_customizer_settings() {

        $this->_register_customizer_setting( "bg", array(
            'default' => '#1c2e41',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Content background',
            'type' => 'color',
        ), array(
            'body' => array('attribute' => 'background','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "text_color", array(
            'default' => '#b6c5d5',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Text color',
            'type' => 'color',
        ), array(
            '.wrap' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "links_color", array(
            'default' => '#5C8BC0',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Links color',
            'type' => 'color',
        ), array(
            'a' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "links_hover_color", array(
            'default' => '#23527c',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Hover/Focus link color',
            'type' => 'color',
        ), array(
            'a:hover, a:focus' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "h1_color", array(
            'default' => '#ffffff',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Heading color',
            'type' => 'color',
        ), array(
            'h1.title, .accordion h4' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "faq_border_color", array(
            'default' => '#ffffff',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'FAQ active heading color',
            'type' => 'color',
        ), array(
            '.accordion.active h4' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "faq_border_color", array(
            'default' => '#e31e09',
            'setting_type' => 'theme_mod',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'FAQ active border color',
            'type' => 'color',
        ), array(
            '.accordion.active, .accordion:hover' => array('attribute' => 'border-color','type' => 'css',),
        ) );

    }

}

new SPC_Block_Content();