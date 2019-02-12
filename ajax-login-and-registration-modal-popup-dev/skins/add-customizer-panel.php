<?php

add_action('customize_register', function ($wp_customize ) {

    /* @var WP_Customize_Manager $wp_customize */

    //		/**
    //		 * Add our Header & Navigation Panel
    //		 */
    $wp_customize->add_panel( 'spc_colors',
        array(
            'title' => __( 'Theme palette' ),
            //'description' => esc_html__( 'Adjust your Header and Navigation sections.' ), // Include html tags such as

            'priority' => 30, // Not typically needed. Default is 160
            'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
            'theme_supports' => '', // Rarely needed
            'active_callback' => '', // Rarely needed
        )
    );

} , 9 );