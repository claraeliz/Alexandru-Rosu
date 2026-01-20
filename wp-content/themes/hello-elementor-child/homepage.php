<?php
/*
Template Name: Homepage
Template Post Type: post, page, event
*/


get_header();


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
?>

  <section class="partners-wrap simple-parallax">
    <div class="bg-img"><img src="<?php echo esc_url($partners_background_image['url']); ?>" alt=""></div>
    <div class="height-vh">
      <div class="flex-wrap">
        <div class="partner"><img src="<?php echo esc_url($partner_1['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_2['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_3['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_4['url']); ?>" alt=""></div>
        <div class="partner"><img src="<?php echo esc_url($partner_5['url']); ?>" alt=""></div>
      </div>
      <div class="al-text"><img src="<?php echo esc_url($alexandru_rosu_text['url']); ?>" alt=""></div>
    </div>
    <div class=""><div class="al-portrait"><img src="<?php echo esc_url($alexandru_rosu_image['url']); ?>" alt=""></div></div>
  </section>

<?php 

get_footer();



		