<?php

/**
 * Base Skins class
 *
 * Allows to set default params
 *
 * @since      2.00
 */
abstract class LRM_Skin_Base extends WP_Skin_Base_Abstract
{
    /**
     * Init
     */
    public function __construct()
    {
        // _customizer _config
        $this->output_handle = 'lrm-modal-skin';
        $this->output_position = 'wp_enqueue_scripts';
        $this->output_priority = 11;

        $this->customizer_slug = 'lrm_' . $this->slug . '__';

        $this->customizer_section_panel = 'lrm_panel';
        $this->output_css_prefix = '';

        LRM_Skins::i()->register($this->slug, $this);

        parent::__construct();
    }
}