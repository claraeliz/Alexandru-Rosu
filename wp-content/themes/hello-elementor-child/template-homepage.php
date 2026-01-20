<?php
/**
 * Template Name: Homepage template
 */

get_header();


?>


<section id="landing">
    <?php

      $landing_logo_1 = get_field('landing_logo_1');
      $landing_logo_2 = get_field('landing_logo_2');
      $landing_logo_3 = get_field('landing_logo_3');
      $partner_1 = get_field('partner_1');
      $partner_2 = get_field('partner_2');
      $partner_3 = get_field('partner_3');
      $partner_4 = get_field('partner_4');
      $center_img = get_field('center_image');
      $parallax_text = get_field('parallax_text');
      $lp_title = get_field('lp_title');
      $lp_subtitle = get_field('lp_subtitle');

    ?>
    
    <div class="sticky-wrap">
        <div class="logo">
          <img src="<?php echo esc_url($landing_logo_1['url']) ?>" alt="">
          <img src="<?php echo esc_url($landing_logo_2['url']) ?>" alt="">
          <img src="<?php echo esc_url($landing_logo_3['url']) ?>" alt="">
        </div>
        <div class="partners">
            <img src="<?php echo esc_url($partner_1['url']) ?>" alt="">
            <img src="<?php echo esc_url($partner_2['url']) ?>" alt="">
            <img src="<?php echo esc_url($partner_4['url']) ?>" alt="">
        </div>
     
        <div class="image">
          <img src="<?php echo esc_url($center_img['url']); ?>" alt="">
        </div>
    </div>

      <div class="title" id="title"><?php echo esc_html($lp_title)?></div>
      <div class="subtitle" id="subtitle"><a href="/arhiva" target="_blank" aria-label="open in same tab"><?php echo esc_html($lp_subtitle)?></a><br><span>1850 - 1989</span></div>
  
   
</section>
<section class="mobile-slider">
     <div class="mob-slider swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($landing_logo_1['url']) ?>" alt="">
                </div> 
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($landing_logo_2['url']) ?>" alt="">
                </div>  
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($landing_logo_3['url']) ?>" alt="">
                </div>
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($partner_2['url']) ?>" alt="">
                </div>
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($partner_3['url']) ?>" alt="">
                </div>
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($partner_4['url']) ?>" alt="">
                </div>
            </div>
        </div>
</section>
<section id="bio" class="d-lg-flex">
  <?php

    $bio_img = get_field('bio_image');
    $parallax_title = get_field('parallax_title');
    $parallax_motto = get_field('parallax_motto');
    $parallax_text = get_field('parallax_text');
    $parallax_bio = get_field('parallax_bio');
    $photographer_1 = get_field('photographer_1');
    $photographer_2 = get_field('photographer_2');
    $photographer_3 = get_field('photographer_3');
    $photographer_4 = get_field('photographer_4');
    $photographer_5 = get_field('photographer_5');
    $photographer_6 = get_field('photographer_6');
    $photographer_7 = get_field('photographer_7');
    $photographer_8 = get_field('photographer_8');
    $background_footer = get_field('background_footer');
    $footer_text = get_field('footer_text');
    $powered_by = get_field('powered_by');
  
   
  ?>
  <div class="inner-wrap">
    <div class="col sticky">
      <div class="img-wrap"><img src="<?php echo esc_url($bio_img['url']); ?>" alt=""></div>
    </div>
    <div class="col scroll-area" id="scrollArea">
      <div class="title"><?php echo esc_html($parallax_title)?></div>
      <div class="motto"><?php echo wp_kses_post($parallax_motto)?></div>
      <div class="description"><?php echo wp_kses_post($parallax_text)?></div>
      <div class="title-wrap">
          <?php 
              $photographers = [];
           

              for ($i = 1; $i <= 8; $i++) {
                  $photographers[$i] = get_field("photographer_$i");
              }
            
              foreach ($photographers as $index => $photographer): ?>

                <?php if (!empty($photographer)): ?>
                  <div class="photographer photographer-<?php echo esc_attr($index); ?>">
                    <?php echo esc_html($photographer); ?>
                  </div>
                <?php endif; ?>

            <?php endforeach; ?>
      </div>
      

    </div>
  </div>
  
</section>
<section id="home-footer" style="background-image: url('<?php echo $background_footer['url'] ?>');">
    <div class="inner-box">
        <div class="inner-text">
                <?php echo wp_kses_post($footer_text)?>
        </div>
    </div>
    <div class="powered-by">
      <div class="text"><a href="https://x01.ro" target="_blank"><span>Powered by</span><img src="<?php echo esc_url($powered_by['url'])?>" alt="Powered by IPSEC"></a></div>
  </div>
</section>

<?php get_footer(); ?>
