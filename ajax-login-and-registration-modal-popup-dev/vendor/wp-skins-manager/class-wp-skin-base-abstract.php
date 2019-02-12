<?php

/**
 * Uses for extends Contest themes functionality
 *
 * Add ability themes more beauty add custom assets
 * and set some params, like support custom leaders block, etc
 *
 * @author     Maxim K <woo.order.review@gmail.com>
 */
abstract class WP_Skin_Base_Abstract extends WP_Skins_Customizer_Abstract
{
    protected $slug;
    protected $title;

    protected $path = '';
    protected $url = '';

    protected $api_version;

    /**
     * Init
     */
    public function __construct()
    {
        $this->init_customizer();

        $this->_enqueue_output_customized_css();
    }

    /**
     * _init theme (add actions, hooks, etc)
     */
    public function init() {
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_path($file)
    {

        return $this->path ? trailingslashit($this->path) . $file : '';
    }

    public function get_url($file)
    {
        return $this->url ? trailingslashit($this->url) . $file : '';
    }

    /**
     * _global _assets
     */
    public function assets(){ }

    /**
     * _filter _shortcode _args before passing to _template
     *
     * @param array $args
     * @return array
     */
    public function filter_args( $args = array() ){
        return $args;
    }
}