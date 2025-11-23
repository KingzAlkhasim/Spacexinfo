<?php

// Load Child Theme CSS + JS
function my_custom_assets() {
    // Load child theme stylesheet
    wp_enqueue_style( 'child-style', get_stylesheet_uri() );

    // Load JS files
    wp_enqueue_script( 'my-custom-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'), null, true );
    wp_enqueue_script( 'my-custom-function', get_stylesheet_directory_uri() . '/function.js', array('jquery'), null, true );
}
add_action( 'wp_enqueue_scripts', 'my_custom_assets', 20 );


// Load custom CSS ONLY on the signup template
function spacexinfo_custom_styles() {
    if ( is_page_template( 'spacexinfo-signup.php' ) ) {
        ?>
        <style>
            /* YOUR ENTIRE CSS GOES HERE (already included by you) */
        </style>
        <?php
    }
}
add_action( 'wp_head', 'spacexinfo_custom_styles' );