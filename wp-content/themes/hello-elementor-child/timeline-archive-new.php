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

get_footer();



		