<?php
/**
 * Plugin Name: ACF Isotope Slider with Media uploads made in Transylvania
 * Description: A slider and grid view with Swiper, ACF year filter (slider only), preview, view toggle, AJAX pagination for grid (chevrons + numbers), and slider image limit.
 * Version: 1.0.0
 * Author: Clara Muranyi
 */

add_shortcode('isotope_image_slider', 'slider_with_isotope_shortcode');
add_action('wp_enqueue_scripts', 'acf_slider_test_scripts');
add_action('wp_ajax_load_more_acf_slider', 'load_more_acf_slider');
add_action('wp_ajax_nopriv_load_more_acf_slider', 'load_more_acf_slider');

function acf_slider_test_scripts() {
  wp_enqueue_style('swiper-bundle-style', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
  wp_enqueue_script('swiper-bundle-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
  wp_enqueue_script('nouislider', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js', [], null, true);
  wp_enqueue_style('nouislider-style', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css');
  wp_enqueue_script('imagesloaded', 'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js', ['jquery'], null, true);
  wp_enqueue_script('isotope', 'https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js', ['imagesloaded'], null, true);

  // Main JS
 wp_enqueue_script(
  'main',
  get_stylesheet_directory_uri() . '/main.js',
  ['jquery', 'imagesloaded', 'isotope'],
  null,
  true
);

  wp_localize_script('main', 'acfSliderAjax', [
    'ajax_url' => admin_url('admin-ajax.php'),
  ]);
}

function slider_with_isotope_shortcode() {
  ob_start();

  // --- Grid Query ---
  $paged = get_query_var('paged') ?: 1;
  $args = [
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => 24,
    'paged'          => $paged,
    'meta_key'       => 'acf_category_value',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'meta_query'     => [[
      'key'     => 'acf_category_value',
      'value'   => [1850, 1989],
      'compare' => 'BETWEEN',
      'type'    => 'NUMERIC',
    ]],
  ];

  $query = new WP_Query($args);
  $images = $query->posts;

  if (!$images) return '<p>No images found with ACF year values.</p>';

 // Fetch *all* images between 1850–1989 (random seed for freshness)
/// FETCH ALL ATTACHMENTS THAT MATCH GALLERY QUERY
$all_images = get_posts(array_merge($args, [
    'posts_per_page'   => -1,
    'paged'            => 1,
    'orderby'          => 'date',
    'suppress_filters' => false,
]));

$all_images = is_array($all_images) ? $all_images : [];

// ==================================================
//   BUILD CLEAN ARRAY FOR THE SLIDER (ALL IMAGES)
// ==================================================

$slider_images = [];

foreach ($all_images as $post) {
    $id   = $post->ID;

    // ACF year field
    $year = (int) get_field('acf_category_value', $id);
    if ($year < 1850 || $year > 1989) {
        continue;
    }

    $meta = wp_get_attachment_metadata($id);
    $upload_dir = wp_upload_dir();

    $slider_images[] = [
        'id'       => $id,
        'year'     => $year,
        'location' => (string) get_field('location', $id),
        'donor'    => (string) get_field('photo_donor', $id),
        'meta'     => (string) get_field('meta_description', $id),
        'thumb'    => wp_get_attachment_image_url($id, 'medium'),
        'full'     => $upload_dir['baseurl'] . '/' . $meta['file'],  // ← REAL FULL SIZE
    ];

}

// Sort ascending by year, then by filename
usort($slider_images, function ($a, $b) {
    if ($a['year'] === $b['year']) {
        return strcmp($a['full'], $b['full']);
    }
    return $a['year'] <=> $b['year'];
});

// Limit total images if needed (OPTIONAL)
$max_total = 50; // or whatever
if (count($slider_images) > $max_total) {
    $slider_images = array_slice($slider_images, 0, $max_total);
}

// ==================================================
//  RANDOM START INDEX FOR JS (use full list length)
// ==================================================
$random_start_index = !empty($slider_images)
    ? array_rand($slider_images)
    : 0;
?>

<script>
  window.SWIPER_RANDOM_START = <?= (int) $random_start_index ?>;
</script>

<?php
// ==================================================
//  META MAP (CORRECTED VERSION)
// ==================================================
$meta_map = [];

foreach ($slider_images as $im) {
    if (empty($im) || empty($im['id'])) {
        continue;
    }
    $html = get_field('meta_description', $im['id']);
    $meta_map[(string) $im['id']] = $html ? wp_kses_post($html) : '';
}
?>

<script>
  window.META_BY_ID = <?= wp_json_encode($meta_map) ?>;
</script>
<?php

    // --- Partners & hero section (untouched) ---
    $partners_background_image = get_field('partners_background_image');
    $alexandru_rosu_text = get_field('alexandru_rosu_text');
    $alexandru_rosu_image = get_field('alexandru_rosu_image');
    $partner_1 = get_field('partner_1');
    $partner_2 = get_field('partner_2');
    $partner_3 = get_field('partner_3');
    $partner_4 = get_field('partner_4');
    $partner_5 = get_field('partner_5');
    $photo_donor = get_field('photo_donor');
    $meta = get_field('meta_description');

  ?>


  <div class="home-wrap">
    <!-- ✅ Preview -->
    <div id="image-preview">
      <img id="preview-image" src="" alt="Preview">

      <div class="more-info">
          <div class="info-icon">
            <img src="/wp-content/uploads/2025/09/more-info-white.png" class="default" alt="more-info-default" />
            <img src="/wp-content/uploads/2025/09/more-info-pink.png" class="hover" alt="more-info-pink" />
          </div>
          
      </div>

      <div class="info-box">
        <span class="close-info-box"></span>
        <div class="info-location"></div>
        <div class="info-year"></div>
        <div class="photo-donor"></div>
        <div class="info-meta"></div>
      </div>

      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>

    <!-- ✅ Swiper -->
   <div class="swiper image-gallery">
   
      <div class="swiper-wrapper">
        <?php foreach ($slider_images as $img) :
            $attachment_id = $img['id']; // or whatever holds the attachment ID
            $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

         ?>
          <div class="swiper-slide gallery-item"
              data-year="<?= esc_attr($img['year']) ?>"
              data-location="<?= esc_attr($img['location']) ?>"
              data-donor="<?= esc_attr($img['donor']) ?>">

              <img src="<?= esc_url($img['thumb']) ?>"
                  data-full="<?= esc_url($img['full']) ?>"
                  data-year="<?= esc_attr($img['year']) ?>"
                  data-location="<?= esc_attr($img['location']) ?>"
                  data-donor="<?= esc_attr($img['donor']) ?>"
                  data-meta="<?= esc_attr(wp_strip_all_tags($img['meta'])) ?>"
                  title="<?= esc_attr( $alt ) ?>"
                  alt="<?= esc_attr( $alt ) ?>">
                  
          </div>
          <?php endforeach; ?>

      </div>
    </div>


    <div id="range-year"></div>

    <!-- ✅ Grid -->
    <div class="grid-container">
      <div class="gallery-wrap">
      

        <?php foreach ($images as $img):
          $year = get_field('acf_category_value', $img->ID);
          $location = get_field('location', $img->ID);
          $full = wp_get_attachment_image_url($img->ID, 'full');
          $thumb = wp_get_attachment_image_url($img->ID, 'medium');
          $large = wp_get_attachment_image_url($img->ID, 'large');
        
        ?>
          <div 
            class="gallery-source"
            data-year="<?= esc_attr($year) ?>"
            data-location="<?= esc_attr($location) ?>"
            data-donor="<?= esc_attr(get_field('donor', $img->ID)) ?>"
            data-thumb="<?= esc_url($thumb) ?>"
            data-full="<?= esc_url($full) ?>"
            data-large="<?= esc_url($large) ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>



    <!-- ✅ Pagination -->
    <?php if ($query->max_num_pages > 1): ?>
      <div id="grid-pagination" class="pagination">
        <?php
        $paged = max(1, get_query_var('paged'));
        if ($paged > 1) {
          echo '<a href="#" class="first" data-page="1">&laquo;&laquo;</a>';
          echo '<a href="#" class="prev" data-page="' . ($paged - 1) . '">&laquo;</a>';
        }
        for ($i = 1; $i <= $query->max_num_pages; $i++) {
          $active = ($i == $paged) ? ' class="active"' : '';
          echo '<a href="#"' . $active . ' data-page="' . $i . '">' . $i . '</a>';
        }
        if ($paged < $query->max_num_pages) {
          echo '<a href="#" class="next" data-page="' . ($paged + 1) . '">&raquo;</a>';
          echo '<a href="#" class="last" data-page="' . $query->max_num_pages . '">&raquo;&raquo;</a>';
        }
        ?>
      </div>
    <?php endif; ?>
  </div>

  <?php
  wp_reset_postdata();
  return ob_get_clean();
}

// ✅ AJAX Grid loader
function load_more_acf_slider() {
  $per_page = isset($_POST['per_page']) ? max(1, intval($_POST['per_page'])) : 24;
  $page     = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
  $min_year = isset($_POST['min_year']) ? intval($_POST['min_year']) : 1850;
  $max_year = isset($_POST['max_year']) ? intval($_POST['max_year']) : 1989;
  $offset   = ($page - 1) * $per_page;

  $query = new WP_Query([
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => $per_page,
    'offset'         => $offset,
    'meta_query'     => [[
      'key'     => 'acf_category_value',
      'value'   => [$min_year, $max_year],
      'compare' => 'BETWEEN',
      'type'    => 'NUMERIC',
    ]],
    'orderby'        => ['meta_value_num' => 'ASC'],
  ]);

  ob_start();
  foreach ($query->posts as $img) {
    $year = get_field('acf_category_value', $img->ID);
    $location = get_field('location', $img->ID);
    $donor = get_field('photo_donor', $img->ID);
    $src = wp_get_attachment_image_url($img->ID, 'large');
    echo '<div class="gallery-item" data-year="'.esc_attr($year).'" data-location="'.esc_attr($location).'">';
    echo '<img src="'.esc_url($src).'" alt="">';
    echo '</div>';
  }
  $html = ob_get_clean();

  wp_send_json([
    'html'          => $html,
    'current_page'  => $page,
    'total_pages'   => $query->max_num_pages,
  ]);
}

// ✅ Donors shortcode (unchanged)
add_shortcode('photo_donors_list', function() {
  global $wpdb;
  $meta_key = 'photo_donor';
  $results = $wpdb->get_results($wpdb->prepare("
    SELECT meta_value AS donor, COUNT(*) AS total
    FROM $wpdb->postmeta
    INNER JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID
    WHERE $wpdb->postmeta.meta_key = %s
      AND $wpdb->postmeta.meta_value <> ''
      AND $wpdb->posts.post_type = 'attachment'
      AND $wpdb->posts.post_mime_type LIKE 'image%%'
      AND $wpdb->posts.post_status = 'inherit'
    GROUP BY meta_value
    ORDER BY total DESC, meta_value ASC
  ", $meta_key));

  if (!$results) return '<p>No photo donors found.</p>';
  $output = '<ul class="photo-donors-list">';
  foreach ($results as $row) {
    $donor = esc_html($row->donor);
    $count = intval($row->total);
    $output .= "<li>{$donor} ({$count})</li>";
  }
  $output .= '</ul>';
  return $output;
});
