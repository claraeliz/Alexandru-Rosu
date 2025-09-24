<?php
/**
 * Plugin Name: ACF Slider Test
 * Description: A slider and grid view with Swiper, ACF year filter (slider only), preview, view toggle, AJAX pagination for grid (chevrons + numbers), and slider image limit.
 * Version: 1.5.1
 * Author: You
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
 

  // Localized ajax_url
  wp_enqueue_script('acf-slider-ajax', plugin_dir_url(__FILE__) . 'acf-slider-ajax.js', ['jquery'], null, true);
  wp_localize_script('acf-slider-ajax', 'acfSliderAjax', [
      'ajax_url' => admin_url('admin-ajax.php'),
  ]);
   wp_enqueue_script('main', '/wp-content/themes/hello-elementor-child/main.js', ['jquery'], null, true);
}

function slider_with_isotope_shortcode() {
  ob_start();

  // Fetch all images with ACF year field
  $args = [
      'post_type'      => 'attachment',
      'post_mime_type' => 'image',
      'post_status'    => 'inherit',
      'posts_per_page' => -1,
      'meta_key'       => 'acf_category_value',
      'orderby'        => 'meta_value_num',
      'order'          => 'ASC',
      'meta_query' => [
      'yr' => [
          'key'     => 'acf_category_value',
          'value'   => [1850, 1989],
          'compare' => 'BETWEEN',
          'type'    => 'NUMERIC',
      ],
  ],
  'orderby' => [ 'yr' => 'ASC' ],

  ];

  $images = get_posts($args);
  if (!$images) {
      return '<p>No images found with ACF year values.</p>';
  }

  // Limit slider to first 60 images
  $slider_limit   = 60;
  $slider_images  = array_slice($images, 0, $slider_limit);

  // Grid initial items — align with AJAX per_page (24)
  $initial_grid_count = 24;
  $total              = count($images);
  $grid_initial       = array_slice($images, 0, $initial_grid_count);

  // Build meta map for preview
  $meta_map = [];
  foreach ($images as $im) {
      $html = get_field('meta_description', $im->ID); // WYSIWYG/HTML
      $meta_map[(string)$im->ID] = $html ? wp_kses_post($html) : '';
  }
  ?>
  

  <script>window.META_BY_ID = <?php echo wp_json_encode($meta_map); ?>;</script>

  <?php
    $partners_background_image = get_field('partners_background_image');
    $partners_title = get_field('partners_title');
    $partners_description = get_field('partners_description');
    $partner_1 = get_field('partner_1');
    $partner_2 = get_field('partner_2');
    $partner_3 = get_field('partner_3');
    $partner_4 = get_field('partner_4');
    $partner_5 = get_field('partner_5');
  ?>

  <section class="partners-wrap simple-parallax" style="--photo:url('<?php echo esc_url($partners_background_image['url']); ?>')">
    <div class="container">
      <div class="title"><?php echo esc_html($partners_title); ?></div>
      <div class="description"><?php echo wp_kses_post($partners_description); ?></div>
      <div class="flex-wrap">
        <div class="partner"><img src="<?php echo esc_url($partner_1['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_2['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_3['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_4['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_5['url']); ?>" alt=""></div>
      </div>
    </div>
  </section>
  <div class="scroll-wrap">
      <div class="scroll-bg show" style="">
        <img src="/wp-content/uploads/2025/09/scroll-down-double-chevrons.svg" width="100%" alt="">
      </div>
  </div>

  <div class="home-wrap">
    <div id="image-preview" class="preview-image">
      <img id="preview-image" src="" alt="">
      <div class="info-box">
        <div class="info-location"></div>
        <div class="info-year"></div>
        <div id="preview-meta" class="meta"></div>
      </div>
      <div class="more-info">
        <div class="inner-wrap">
          <div class="info-icon">
            <span class="default"><img src="/wp-content/uploads/2025/09/more-info-white.png" alt=""></span>
            <span class="hover"><img src="/wp-content/uploads/2025/09/more-info-F6E71D.png" alt=""></span>
          </div>
        </div>
      </div>
    </div>

    <div class="bottom-wrap">
      <div class="slider-container">
        <div class="swiper image-gallery">
          <div class="swiper-wrapper">
            <?php foreach ($slider_images as $image):
                $year     = get_field('acf_category_value', $image->ID);
                $location = get_field('location', $image->ID);
                $thumb    = wp_get_attachment_image_url($image->ID, 'thumbnail');
                $full     = wp_get_attachment_image_url($image->ID, 'large');
            ?>
            <div class="swiper-slide gallery-item" data-id="<?php echo (int)$image->ID; ?>" data-year="<?php echo esc_attr($year); ?>" data-location="<?php echo esc_attr($location); ?>">
              <img src="<?php echo esc_url($thumb); ?>" data-full="<?php echo esc_url($full); ?>" data-id="<?php echo (int)$image->ID; ?>" data-location="<?php echo esc_attr($location); ?>">
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="swiper-controls">
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>
      <div id="range-year"></div>
    </div>

    <div class="grid-container">
      <div class="grid-sizer"></div>
      <div class="gutter-sizer"></div>
      <?php foreach ($grid_initial as $image):
          $year     = get_field('acf_category_value', $image->ID);
          $location = get_field('location', $image->ID);
          $full     = wp_get_attachment_image_url($image->ID, 'full');
      ?>
        <div class="gallery-item" data-id="<?php echo (int)$image->ID; ?>" data-year="<?php echo esc_attr($year); ?>" data-location="<?php echo esc_attr($location); ?>">
          <img src="<?php echo esc_url($full); ?>" data-full="<?php echo esc_url($full); ?>" data-id="<?php echo (int)$image->ID; ?>" data-location="<?php echo esc_attr($location); ?>">
        </div>
      <?php endforeach; ?>
    </div>

    <!-- numeric pagination -->
    <nav id="grid-pagination" class="pagination" aria-label="Gallery pages"></nav>
  </div>

  <script>
  
  </script>
  <?php
  return ob_get_clean();
}

// AJAX handler: grid items filtered by current year range, with paging
function load_more_acf_slider() {
  $per_page = isset($_POST['per_page']) ? max(1, intval($_POST['per_page'])) : 24;
  $page     = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;

  $min_year = isset($_POST['min_year']) ? intval($_POST['min_year']) : 1850;
  $max_year = isset($_POST['max_year']) ? intval($_POST['max_year']) : 1989;

  $offset = ($page - 1) * $per_page;

  $qargs = [
      'post_type'      => 'attachment',
      'post_mime_type' => 'image',
      'post_status'    => 'inherit',
      'posts_per_page' => $per_page,
      'offset'         => $offset,
      'meta_query' => [
          'yr' => [
              'key'     => 'acf_category_value',
              'value'   => [$min_year, $max_year],
              'compare' => 'BETWEEN',
              'type'    => 'NUMERIC',
          ],
      ],
      'orderby' => [ 'yr' => 'ASC' ],
      'no_found_rows'  => false,
      'fields'         => 'ids',
  ];

  $query = new WP_Query($qargs);
  $ids   = $query->posts;

  ob_start();
  if ($ids) {
      foreach ($ids as $id) {
          $year     = get_field('acf_category_value', $id);
          $location = get_field('location', $id);
          $grid_size = 'large';
          $src    = wp_get_attachment_image_url($id, $grid_size);
          $srcset = wp_get_attachment_image_srcset($id, $grid_size);
          $sizes  = '280px';
          $full   = wp_get_attachment_image_url($id, 'full');
          echo '<div class="gallery-item" data-id="'.(int)$id.'" data-year="'.esc_attr($year).'" data-location="'.esc_attr($location).'">';
          echo   '<img src="'.esc_url($src).'" srcset="'.esc_attr($srcset).'" sizes="'.esc_attr($sizes).'" data-full="'.esc_url($full).'" data-id="'.(int)$id.'" data-location="'.esc_attr($location).'" />';
          echo '</div>';
      }
  }
  $html = ob_get_clean();

  $total       = (int) $query->found_posts;
  $total_pages = $total > 0 ? (int) ceil($total / $per_page) : 1;

  wp_send_json([
      'html'          => $html,
      'total'         => $total,
      'current_page'  => $page,
      'total_pages'   => $total_pages,
  ]);
}
