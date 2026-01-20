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

function theme_fancybox_scripts() {
    wp_enqueue_style( 'fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css', [], null );
    wp_enqueue_script( 'fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js', [], null, true );
}
add_action( 'wp_enqueue_scripts', 'theme_fancybox_scripts' );



add_action('wp_ajax_load_grid_page', 'load_grid_page_callback');
add_action('wp_ajax_nopriv_load_grid_page', 'load_grid_page_callback');

function load_grid_page_callback() {
  $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

  $args = [
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'posts_per_page' => 24,
    'paged'          => $paged,
  ];

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    echo '<div class="grid-container">';
    echo '<div class="grid-sizer"></div>';
    echo '<div class="gutter-sizer"></div>';

    while ($query->have_posts()) {
      $query->the_post();
      $year = get_field('acf_category_value', get_the_ID());
      $location = get_field('location', get_the_ID());
      $full = wp_get_attachment_image_url(get_the_ID(), 'full');

      echo '<div class="gallery-item" data-id="' . get_the_ID() . '" data-year="' . esc_attr($year) . '" data-location="' . esc_attr($location) . '">';
      echo '<img src="' . esc_url($full) . '" data-full="' . esc_url($full) . '" data-id="' . get_the_ID() . '" data-location="' . esc_attr($location) . '">';
      echo '</div>';
    }

    echo '</div>';

    echo '<div id="grid-pagination">';
    echo paginate_links([
      'total'   => $query->max_num_pages,
      'current' => $paged,
      'format'  => '?paged=%#%',
    ]);
    echo '</div>';
  } else {
    echo '<p>No images found.</p>';
  }

  wp_die(); // Always end AJAX responses
}

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

// === 1️⃣ Register the rewrite rule ===
function photo_donor_rewrite_rule() {
    add_rewrite_rule(
        '^ambasador/([^/]+)/?',
        'index.php?photo_donor_name=$matches[1]',
        'top'
    );
}
add_action('init', 'photo_donor_rewrite_rule');

// === 2️⃣ Register query var ===
function photo_donor_query_vars($vars) {
    $vars[] = 'photo_donor_name';
    return $vars;
}
add_filter('query_vars', 'photo_donor_query_vars');

// === 3️⃣ Load custom template ===
function photo_donor_template_redirect() {
    if (preg_match('/^ambasador\/([^\/]+)\/?$/', trim($_SERVER['REQUEST_URI'], '/'), $matches)) {
        $donor_slug = sanitize_title($matches[1]);
        set_query_var('photo_donor_slug', $donor_slug);
        include get_stylesheet_directory() . '/template-photo-donor.php';
        exit;
    }
}
add_action('template_redirect', 'photo_donor_template_redirect');



// enqueue properly for donor page (and other pages that use the grid)
add_action( 'wp_enqueue_scripts', 'donor_isotope_enqueue' );
function donor_isotope_enqueue() {
    // imagesLoaded
    wp_register_script(
        'imagesloaded',
        'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js',
        ['jquery'],
        null,
        true
    );

    // isotope - depend on imagesLoaded and jquery
    wp_register_script(
        'isotope',
        'https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js',
        ['jquery','imagesloaded'],
        null,
        true
    );
    // wp_add_inline_script(
    //     'main',
    //     'const ajaxurl = "' . esc_url(admin_url('admin-ajax.php')) . '";',
    //     'before'
    // );


    // Only enqueue on pages where you need grid (optional). If you can't detect, remove the conditional.
    $should_enqueue = true;
    // Example conditional: only on donor pages (if your rewrite sets query var 'photo_donor_name')
    if ( function_exists('get_query_var') && get_query_var('photo_donor_name') ) {
        $should_enqueue = true;
    }
    // Or check for page template:
    // if ( is_page_template('template-photo-donor.php') ) $should_enqueue = true;

    if ( $should_enqueue ) {
        wp_enqueue_script('imagesloaded');
        wp_enqueue_script('isotope');

        // enqueue your child theme main.js (adjust filename/path if different)
        wp_enqueue_script(
            'theme-main',
            get_stylesheet_directory_uri() . '/main.js',
            ['jquery','imagesloaded','isotope'],
            null,
            true
        );

        // Optionally localize an ajax URL if main.js needs it
        wp_localize_script('theme-main', 'MyThemeAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    // you can still enqueue nouislider or others if needed
    wp_enqueue_script('nouislider', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js', [], null, true);
    wp_enqueue_style('nouislider-style', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css');
}

add_action('rest_api_init', function () {
    register_rest_route('gallery/v1', '/list', [
        'methods'  => 'GET',
        'callback' => function () {

            $args = [
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => -1,
                'post_mime_type' => 'image',
            ];

            $attachments = get_posts($args);
            $out = [];

            foreach ($attachments as $p) {
                $id = $p->ID;

                // Skip non-gallery images if needed
                // (e.g. logo files that have year=0)
                // But leave this check to you:
                // if ((int)get_field('acf_category_value', $id) === 0) continue;

                $out[] = [
                    'id'       => $id,
                    'year'     => (int) get_field('acf_category_value', $id),
                    'url'      => wp_get_attachment_image_url($id, 'full'),
                    'thumb'    => wp_get_attachment_image_url($id, 'medium'),
                    'large'    => wp_get_attachment_image_url($id, 'large'),
                    'location' => (string) get_field('location', $id),
                    'donor'    => (string) get_field('photo_donor', $id),
                    'meta'     => (string) get_field('meta_description', $id),
                    'title'    => get_post_meta( $id, '_wp_attachment_image_alt', true ),
                ];
            }

            return $out;
        }
    ]);
});

function flyout_menu_shortcode($atts) {
    $atts = shortcode_atts([
        'menu' => '',
        'class' => 'flyout-wp-menu'
    ], $atts);

    return wp_nav_menu([
        'menu' => $atts['menu'],
        'menu_class' => $atts['class'],
        'container' => false,
        'echo' => false
    ]);
}
add_shortcode('flyout_menu', 'flyout_menu_shortcode');

// Enable ACF JSON save & load in child theme
function greeny_acf_json_paths( $paths ) {

    // Remove default path (optional but clean)
    unset($paths[0]);

    $paths[] = get_stylesheet_directory() . '/acf-json';

    return $paths;
}
add_filter('acf/settings/load_json', 'greeny_acf_json_paths');

function greeny_acf_json_save( $path ) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter('acf/settings/save_json', 'greeny_acf_json_save');