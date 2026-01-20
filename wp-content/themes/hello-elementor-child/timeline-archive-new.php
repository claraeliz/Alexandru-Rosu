<?php
/*
Template Name: Timeline archive new
Template Post Type: post, page, event
*/
// Page code here...

// Get the selected terms from ACF (multi-select taxonomy field)
// Inside a template (e.g., page.php, single.php)
get_header();


echo do_shortcode('[isotope_image_slider]');

// var_dump( gd_info() );
// var_dump( extension_loaded('imagick') );
$meta = wp_get_attachment_metadata(559); // replace with your image ID
echo "<pre>";
print_r($meta);
echo "</pre>";
exit;



get_footer();



		