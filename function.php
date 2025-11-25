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
    } // end if
}
add_action( 'wp_head', 'spacexinfo_custom_styles' );

/* * AUTOMATIC GOOGLE TRANSLATOR (10 LANGUAGES) */
function add_google_translate_global() {
    ?>
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'es,ar,fr,de,zh-CN,ru,pt,ja,ko,hi',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
      }, 'google_translate_element');
    }
    </script>

    <!-- Use explicit https to avoid mixed-content issues -->
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <style>
        #google_translate_element {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(10, 14, 39, 0.9);
            padding: 10px;
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 135, 0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .goog-te-gadget {
            font-family: 'Segoe UI', sans-serif !important;
            font-size: 0 !important;
        }

        .goog-te-gadget .goog-te-combo {
            padding: 8px 12px;
            border-radius: 6px;
            background: #0a0e27;
            color: #fff !important;
            border: 1px solid rgba(255,255,255,0.2);
            font-weight: 600;
            cursor: pointer;
        }

        .goog-te-gadget .goog-te-combo option {
            background: #0a0e27;
            color: white;
        }

        /* Hide Google Top Bar */
        body { top: 0 !important; }
        .goog-te-banner-frame { display: none !important; }
        .goog-logo-link { display: none !important; }
        .goog-te-gadget img { display: none !important; }
    </style>

    <div id="google_translate_element"></div>
    <?php
}
add_action('wp_footer', 'add_google_translate_global');
