<?php
/**
 * Template for showing all photos donated by a specific donor.
 * Accessible via /ambasador/{donor-slug}/
 */

get_header();

$donor_slug = get_query_var('photo_donor_name');
$donor_name = ucwords(str_replace('-', ' ', $donor_slug)); // Convert slug to readable name

echo '<div class="photo-donor-page container">';
echo '<h1 class="page-title">Photos donated by ' . esc_html($donor_name) . '</h1>';


// Query all attachments that have this donor in the ACF field
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$args = [
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'posts_per_page' => 12, // show 12 per page (adjust as needed)
    'paged'          => $paged,
    'meta_query'     => [
        [
            'key'     => 'photo_donor',
            'value'   => $donor_name,
            'compare' => 'LIKE',
        ],
    ],
];

$query = new WP_Query($args);

if ($query->have_posts()) {
    echo '<div class="grid-container">';
    echo  '<div class="gallery-wrap">';
    while ($query->have_posts()) {
        $query->the_post();
        $img_url = wp_get_attachment_image_url(get_the_ID(), 'full');
        $img_alt = get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true);
        $year = get_field('acf_category_value', get_the_ID());

        echo '<div class="gallery-item">';
        echo '<a href="' . esc_url(wp_get_attachment_url(get_the_ID())) . '" target="_blank">';
        echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($img_alt) . '">';
        echo '</a>';
        if ($year) {
            echo '<div class="meta-desc">' . esc_html($year) . '</div>';
        }
        echo '</div>';
    }
    echo '<nav id="grid-pagination" class="pagination" aria-label="Gallery pages"></nav>';
    echo '</div>';
} else {
    echo '<p>No photos found for this donor.</p>';
}

wp_reset_postdata();

echo '</div></div>';

get_footer();
