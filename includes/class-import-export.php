<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;
/**
 * Import/Export settings
 *
 * @since      2.03
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_Import_Export_Manager {

    public static function init() {
        add_action('wp_ajax_lrm_import', array(__CLASS__, 'AJAX_process_import'));
        add_action('wp_ajax_lrm_export', array(__CLASS__, 'AJAX_process_export'));
    }

    /**
     * Register settings
     * @param \underDEV\Utils\Settings $settings_class
     * @throws Exception
     */
    public static function register_settings( $settings_class ) {

        $SECTION = $settings_class->add_section( __( 'Import/Export', 'ajax-login-and-registration-modal-popup' ), 'import/export', false );

	    $SECTION->add_group( __( 'Export', 'ajax-login-and-registration-modal-popup' ), 'export' )
	            ->add_field( array(
		            'slug'        => 'export',
		            'name'        => __('Export following sections:', 'ajax-login-and-registration-modal-popup' ),
		            'default'     => true,
		            'render'      => array( LRM_Settings::get(), '_render__text_section' ),
		            'sanitize'    => '__return_false',
		            'addons' => array('section_file'=>'export'),
	            ) )
		    ->description( __( 'Here you could simply export and import your plugin settings for backup or migrate to the another website.', 'ajax-login-and-registration-modal-popup' ) );


        $SECTION->add_group( __( 'Import', 'ajax-login-and-registration-modal-popup' ), 'import' )

            ->add_field( array(
                'slug'        => 'import',
                'name'        => __('Import following sections:', 'ajax-login-and-registration-modal-popup'),
                'default'     => true,
                'render'      => array( LRM_Settings::get(), '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'import'),
            ) );

    }

    public static function AJAX_process_export(  ) {

    	if ( empty($_GET['_nonce']) || ! wp_verify_nonce($_GET['_nonce'], 'lrm_run_export') ) {
    		wp_send_json_error( 'Invalid nonce!' );
	    }

    	if ( ! current_user_can('manage_options') ) {
    		wp_send_json_error( 'Not allowed!' );
	    }

	    if ( empty($_GET['sections']) ) {
		    wp_send_json_error( 'No sections are selected!' );
	    }

    	$sections = $_GET['sections'];
	    $export_string = '';

	    $section_data = [];
	    $sections_data = [];
    	foreach ( $sections as $section ) {
		    $section = sanitize_text_field($section);

		    $section_data = get_option( 'lrm_' . $section );

		    if ( $section_data ) {
			    $sections_data[$section] = $section_data;
		    }
	    }

    	if ( $sections_data ) {
		    $export_string = json_encode( $sections_data );
	    }

    	wp_send_json_success( $export_string );
    }

    public static function AJAX_process_import(  ) {

    	if ( empty($_POST['_nonce']) || ! wp_verify_nonce($_POST['_nonce'], 'lrm_run_import') ) {
    		wp_send_json_error( 'Invalid nonce!' );
	    }

	    if ( ! current_user_can('manage_options') ) {
		    wp_send_json_error( 'Not allowed!' );
	    }

	    if ( empty( trim($_POST['sections_import']) ) || empty($_POST['sections']) ) {
		    wp_send_json_error( 'Import string is empty or no sections are selected!' );
	    }

    	$sections = $_POST['sections'];
	    $sections_import = json_decode( trim(stripslashes($_POST['sections_import'])), true );

	    if ( JSON_ERROR_NONE !== json_last_error() ) {
		    wp_send_json_error( 'Json parse error: ' . json_last_error_msg() );
	    }

	    $section_data = [];
	    $sections_data = [];

    	foreach ( $sections as $section ) {
		    $section = sanitize_text_field($section);

		    // Skip if no setting in Import string
		    if ( !isset($sections_import[$section]) ) {
		    	continue;
		    }

		    $section_data = get_option( 'lrm_' . $section );

		    if ( $section_data ) {
			    $section_data = array_merge( $section_data, $sections_import[$section] );
			    update_option( 'lrm_' . $section, $section_data );
		    } else {
		    	add_option( 'lrm_' . $section, $sections_import[$section] );
		    }
	    }

    	wp_send_json_success();
    }

}
