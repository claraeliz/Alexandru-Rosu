<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style','hello-elementor-header-footer' ) );
         wp_enqueue_script('jquery');
      wp_enqueue_script('isotope', 'https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js', ['jquery'], null, true);
    wp_enqueue_script('nouislider', 'https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js', [], null, true);
    wp_enqueue_style('nouislider-style', 'https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.css');

    wp_enqueue_style('fancybox-style', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css');
    wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js', [], null, true);
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 20 );

// END ENQUEUE PARENT ACTION

//function to add custom media field
function custom_media_add_media_custom_field( $form_fields, $post ) {
    $field_value = get_post_meta( $post->ID, 'custom_media_style', true );

    $form_fields['custom_media_style'] = array(
        'value' => $field_value ? $field_value : '',
        'label' => __( 'Class' ),
        'helps' => __( 'Enter your class' ),
        'input'  => 'textarea'
    );

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'custom_media_add_media_custom_field', null, 2 );

//save your custom media field
function custom_media_save_attachment( $attachment_id ) {
    if ( isset( $_REQUEST['attachments'][ $attachment_id ]['custom_media_style'] ) ) {
        $custom_media_style = $_REQUEST['attachments'][ $attachment_id ]['custom_media_style'];
        update_post_meta( $attachment_id, 'custom_media_style', $custom_media_style );

    }
}
add_action( 'edit_attachment', 'custom_media_save_attachment' );


