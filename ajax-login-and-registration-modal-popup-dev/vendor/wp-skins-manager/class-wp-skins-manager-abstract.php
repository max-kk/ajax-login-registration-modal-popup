<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Skins manager
 *
 * @author     Maxim K <woo.order.review@gmail.com>
 */
abstract class WP_Skins_Manager_Abstract
{
    // Local Cacher
    protected $type;
    
    /** @var WP_Skin_Base[] $skins */
    protected $skins = array();

    function register($skin, $class)
    {
        $this->skins[$skin] = $class;
    }

    /**
     * Check is following Skin Slug is registered
     * @param $skin
     * @return bool
     */
    function is_registered($skin)
    {
        return $skin && isset($this->skins[$skin]);
    }

    /**
     * Call skin function
     * @param $skin
     * @param $action
     * @param mixed $param1
     *
     * @return mixed
     */
    function call( $skin, $action, $param1 = false )
    {
        if ( isset($this->skins[$skin]) ) {
            return $this->skins[$skin]->$action( $param1 );
        }
        return $param1;
    }

    /**
     * @return bool|WP_Skin_Base[]
     */
    function get_skins()
    {
        if (isset($this->skins)) {
            return $this->skins;
        }
        return false;
    }

    /**
     * @param string $skin
     * @return WP_Skin_Base
     *
     * @throws Exception
     */
    function get($skin)
    {
        if (isset($this->skins[$skin])) {
            return $this->skins[$skin];
        }
        throw new \Exception( "Skin '{$skin}' isn't registered!" );
    }

    function get_list()
    {
        $list = array();

        foreach ($this->skins as $skin_name => $skin_class) {
            $list[$skin_name] = $skin_class->get_title();
        }
        return $list;
    }

    function locate($skin, $file)
    {
        if (isset($this->skins[$skin])) {
            // False or Path
            return $this->skins[$skin]->get_path($file);
        }
        return false;
    }

    function locate_url($skin, $file)
    {
        if (isset($this->skins[$skin])) {
            // False or Path
            return $this->skins[$skin]->get_url($file);
        }
        return false;
    }

    /**
     * Include default skins
     */
    function load_defaults()
    {

    }
}