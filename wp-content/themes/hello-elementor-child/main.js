
jQuery(document).ready(function($) {
  /* ============================
     DOM & STATE
  ============================ */
  const $swiper    = $('.swiper.image-gallery');
  const $preview   = $('#image-preview');
  const $range     = $('#range-year');
  const $grid      = $('.grid-container');
  const $wrap      = $grid.find('.gallery-wrap');
  const $btnSlider = $('.slider-view');
  const $btnGrid   = $('.grid-view');

  const PAGE_SIZE  = 20;
  const IMAGE_LIMIT = 50;

  let gridData   = [];
  let currentPage = 1;
  let swiper      = null;
  let ALL_IMAGES  = [];
  let SLIDES      = [];   // current swiper items

  $grid.hide();

  window.SLIDES = [];

  const client_width = document.documentElement.clientWidth;

  $('.view-toggle').addClass('hide');

  if ($('.home-wrap').length) {
    $('body').addClass('archive');
    $('.view-toggle').addClass('show').removeClass('hide');
  }

   $('header').removeClass('hide-header');

   
  function renderMobileSwiper() {
    var swiperMob = new Swiper('.mob-slider', {
        loop: true,
        slidesPerView: 2,
        spaceBetween: 10,
        autoplay: {
            delay: 5000,
        }
    });
  }


  if($('body').hasClass('home')) {

    const images = document.querySelectorAll('.partners img');
    const logo_images = document.querySelectorAll('.logo img');
    let current = 0;
    let l_current = 0;
    if (!images.length || !logo_images.length) return;


    images[current].classList.add('active');
    logo_images[l_current].classList.add('active');

    setInterval(() => {
        images[current].classList.remove('active');
        current = (current + 1) % images.length;
        images[current].classList.add('active');


        logo_images[l_current].classList.remove('active');
        l_current = (l_current + 1) % logo_images.length;
        logo_images[l_current].classList.add('active');

    }, 5000); // 5 seconds

    const stickyText = document.querySelector('#bio .motto');
    const count = document.querySelectorAll('#bio [class^="photographer"]').length;

    
    const scrollArea = document.querySelector('.scroll-area');

    const maxTranslate = window.innerWidth; // start off-screen

    const updatePosition = () => {
      const rect = scrollArea.getBoundingClientRect();
      const viewportHeight = window.innerHeight;

      // Progress from 0 → 1 as section scrolls
      const progress = Math.min(
        Math.max((viewportHeight - rect.top) / viewportHeight, 0),
        1
      );

      const translateX = maxTranslate * (1 - progress);
      if(window.innerWidth > 1024) {
          stickyText.style.transform = `translateX(${translateX}px)`;
      }
     
      
    };

    window.addEventListener('scroll', updatePosition, { passive: true });
    window.addEventListener('resize', updatePosition);

    // Initial position
    if(client_width >= 1024) {
       updatePosition();
    } else {
        renderMobileSwiper(); 
    }
  
  }


  const section = document.getElementById('scrollArea');

  function isInView(el) {
    const rect = el.getBoundingClientRect();
    return (
      rect.top <= window.innerHeight  &&
      rect.bottom >= 0
    );
  }
  


  if(client_width >= 1024) {
    if($('body').hasClass('home')) {
        window.addEventListener('scroll', () => {
    
        if (isInView(section)) {
          const photographers = $('.photographer');
          const scrollArea = document.querySelector('.scroll-area');
          const rect = scrollArea.getBoundingClientRect();
          const viewportHeight = window.innerHeight;
          

          const maxTranslate = window.innerWidth; // start off-screen
          

          const progress = Math.min(
            Math.max((viewportHeight - rect.top) / viewportHeight, 0),
            1
          );

          const translateX = maxTranslate * (1 - progress);
            

          photographers.each(function(index) {
            let my_this= this;

            setTimeout(function() {
                my_this.style.transform = `translateX(${translateX}px)`;
            }, (200 * index));
            
          });
          

          const firstSection = document.querySelector('#landing');
          const f_rect = firstSection.getBoundingClientRect();
          const sectionBottom = f_rect.bottom - 200 + window.scrollY;

          
          
          if (window.scrollY >= sectionBottom) {
            if($('header').hasClass('hide-header')) {
                //
            } else {
                $('header').addClass('hide-header');
            }

          }
        
          
        }

      });
    }
      
    
  }
    

  if(client_width >= 1024) {
    if($('body').hasClass('home')) {
      addEventListener("scrollend", () => {
  
      setTimeout(() =>{
        $('header').removeClass('hide-header');
      }, 2000);
       
    })

    const firstSection = document.querySelector('#landing');

      let lastScrollY = window.scrollY;

      window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;
        const sectionHeight = firstSection.offsetHeight;

        const scrollingUp = currentScrollY < lastScrollY;
        const reachedFirstSection = currentScrollY <= sectionHeight;

        if (scrollingUp && reachedFirstSection) {
          $('header').removeClass('hide-header');
        }

        lastScrollY = currentScrollY;
      });
    }
   
  }
 


    const wrapper = '.menu-wrapper';
    const trigger = '.openMenu';
    const panel   = '.flyOut';
    const closeBtn = '.closeMenu';

    // OPEN menu
    $(document).on("click", trigger, function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        $(wrapper).addClass('menu-open');
        $(trigger).addClass('active');
        $(panel).addClass('open');
    });

    // CLOSE menu — click close icon
    $(document).on("click", closeBtn, function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        $(wrapper).removeClass('menu-open');
        $(trigger).removeClass('active');
        $(panel).removeClass('open');
    });

    // CLOSE menu — ESC key
    $(document).on('keydown', function(e) {
        if (e.key === "Escape") {
            $(wrapper).removeClass('menu-open');
            $(trigger).removeClass('active');
            $(panel).removeClass('open');
        }
    });

    // CLOSE menu — click outside panel
    $(document).on('click', function(e) {
        if (!$(e.target).closest(wrapper + ' ' + panel).length &&
            !$(e.target).closest(wrapper + ' ' + trigger).length) {

            $(wrapper).removeClass('menu-open');
            $(trigger).removeClass('active');
            $(panel).removeClass('open');
        }
    });  


  /* ============================
     UTILS
  ============================ */

  function normalizeUrl(u) {
    if (!u) return '';
    try { u = decodeURIComponent(u); } catch (e) {}
    u = u
      .toLowerCase()
      .replace(/^https?:\/\//, '')
      .replace(/^www\./, '')
      .replace(/[?#].*$/, '');

    const filename = u.split('/').pop() || '';

    return filename
      .replace(/-\d{2,4}x\d{2,4}(?=\.(jpg|jpeg|png|gif|webp|avif)$)/i, '')
      .replace(/(\.|-)?scaled(?=\.(jpg|jpeg|png|gif|webp|avif)$)/i, '')
      .replace(/\.(jpg|jpeg|png|gif|webp|avif)$/i, '')
      .replace(/[_\s]+/g, '-');
  }

  function dedupe(items) {
    const map = new Map();
    for (const i of items || []) {
      const url = (i && (i.full || i.thumb)) || '';
      const key = normalizeUrl(url);
      if (!key) continue;

      const current = map.get(key);
      const isResized = /(-\d{2,4}x\d{2,4}|-scaled|\.(scaled))/i.test(url);

      // prefer non-resized originals
      if (!current || isResized === false) {
        map.set(key, i);
      }
    }
    return Array.from(map.values());
  }

  /* ============================
     COLLECT INITIAL HTML IMAGES
  ============================ */


// 1) Read original HTML ONCE (with correct donor/location/meta)
$('.image-gallery .swiper-slide').each(function () {
  const $slide = $(this);
  const $img   = $slide.find('img');

  // --- YEAR HANDLING ---
  let year = null;

  // raw year from attributes
  let yearAttr =
    $img.attr('data-year') ||
    $slide.attr('data-year') ||
    '';

  yearAttr = (yearAttr || '').toString().trim();

  if (yearAttr) {
    // grab the first 4-digit number in case it’s like "1865 ?" or "1862-1863"
    const m = yearAttr.match(/\d{4}/);
    if (m) {
      const y = parseInt(m[0], 10);
      if (Number.isFinite(y) && y > 0) {
        year = y;
      }
    }
  }

  // fallback: try to read year from filename if we still don't have one
  if (!Number.isFinite(year)) {
    const src = $img.attr('src') || $img.data('full') || '';
    const fallbackYear = extractYearFromUrl(src);
    if (Number.isFinite(fallbackYear) && fallbackYear > 0) {
      year = fallbackYear;
    }
  }

  // still nothing? keep the slide anyway, just log it
  if (!Number.isFinite(year)) {
    console.warn('Slide without usable year; keeping metadata anyway', {
      yearAttr,
      slide: this
    });
    year = null;
  }

 // --- METADATA ---
const location =
  $img.attr('data-location') ||
  $slide.attr('data-location') ||
  '';

const donor =
  $img.attr('data-donor') ||
  $slide.attr('data-donor') ||
  '';

const title= $img.attr('title');

const meta = $img.attr('data-meta') || '';

const src = $img.attr('src') || $img.data('full') || '';
const key = normalizeUrl(src);

const obj = {
  id: ALL_IMAGES.length,
  year,           // may be a real year, or null
  location,
  donor,
  meta,
  title,
  thumb: src,
  full: $img.data('full') || src,
  large: $img.data('large') || src,
  source: 'html', // mark origin
  key             // normalized filename for matching
};


ALL_IMAGES.push(obj);

});

  window.ALL_IMAGES = ALL_IMAGES;


  ALL_IMAGES = dedupe(ALL_IMAGES);

  /* ============================
     MERGE REST (years only)
  ============================ */
  async function mergeAllSources() {
    let combined = [...ALL_IMAGES];  // HTML is the truth

    try {
      const res = await fetch('/wp-json/gallery/v1/list', { cache: 'no-store' });
      if (res.ok) {
        const data = await res.json();

        const restItems = data
          .filter(d => Number.isFinite(d.year) && d.year !== 0)
          .map(d => {
            const key = normalizeUrl(d.url);
            return {
              id: d.id,
              year: d.year,
              full: d.url,
              title: d.title,
              large: d.large || d.url,
              thumb: d.thumb || d.url,
              location: d.location || "",
              donor: d.donor || "",
              meta: d.meta || "",
              source: 'rest',
              key
            };
          });


        for (const r of restItems) {
          const htmlExisting = combined.find(i => i.key === r.key);
          if (!htmlExisting) {
            combined.push(r); // only add if HTML didn’t already have it
          }
        }

      }
    } catch (e) {
      console.warn("REST merge skipped:", e);
    }

    ALL_IMAGES = combined;
    ALL_IMAGES.forEach((img, index) => { img.id = index; });
    window.ALL_IMAGES = ALL_IMAGES; // make sure it’s global for console debug
    // console.log('AFTER MERGE ALL_IMAGES:', ALL_IMAGES);

  }

  /* ============================
     YEAR RANGE
  ============================ */
  function getYearRange() {
    let min = 0, max = 9999;
    if ($range && $range[0] && $range[0].noUiSlider) {
      const v = $range[0].noUiSlider.get().map(x => parseInt(x, 10));
      min = v[0]; max = v[1];
    }
    return [min, max];
  }

  /* ============================
     PREVIEW HOVER
  ============================ */
  $('#image-preview').hover(
    function () { $('.more-info').addClass('show'); },
    function () { $('.more-info').removeClass('show'); }
  );
  
  $('.more-info').on('click', function () {
    $(this).siblings('.info-box').addClass('transform');
    $(this).addClass('hide');
  });
  



    $('.info-box').on('click', function() {
      $(this).removeClass('transform');
      $('.more-info').removeClass('hide');
  });

  /* ============================
     SWIPER & PREVIEW
  ============================ */

  function renderSlides(items) {
    
    SLIDES = items.slice();
    window.SLIDES = items.slice();

    const html = SLIDES.map((item, index) => `
      <div class="swiper-slide gallery-item" data-idx="${index}">
        <img 
          src="${item.thumb}" 
          data-idx="${index}" 
          data-full="${item.full}" 
          data-large="${item.large}"
          data-year="${item.year || ''}"
          data-location="${item.location || ''}"
          data-donor="${item.donor || ''}"
          data-meta="${(item.meta || '').replace(/"/g, '&quot;')}"
          title="${item.title}"
          alt="${item.title}"
        >
      </div>
    `).join('');


    $('.image-gallery .swiper-wrapper').html(html);

    const firstImg = document.querySelector('.image-gallery .swiper-slide img');

  }

    function updatePreviewByIndex(idx) {
      const slide = swiper?.slides?.[idx];
      if (!slide) return;

      const img = slide.querySelector('img');
      if (!img) return;

      const full = img.getAttribute('data-full') || img.src || '';

     
      $('#preview-image').attr('src', full);

     
      $('.info-year').text(img.dataset.year || '');
      $('.info-location').text(img.dataset.location || '');
      $('.photo-donor').text(
        img.dataset.donor ? ('Ambasador: ' + img.dataset.donor) : ''
      );
      $('.info-meta').html(img.dataset.meta || '');
    }



  function initSwiper({ startIndex = 0, centerOnce = false } = {}) {
  
    if (swiper) {
      swiper.destroy(true, true);
      swiper = null;
    }

    swiper = new Swiper('.image-gallery', {
      slidesPerView: 'auto',
      spaceBetween: 10,
      loop: false,
      centeredSlides: !!centerOnce,
      centeredSlidesBounds: !!centerOnce,
      centerInsufficientSlides: !!centerOnce,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      on: {
        init() {
          const maxIdx = SLIDES.length - 1;
          const idx = Math.max(0, Math.min(startIndex, maxIdx));
        setTimeout(() => {
            updatePreviewByIndex(idx);
        }, 0);
          $('.swiper-slide').removeClass('highlight');
          const $slide = $(this.slides[idx]);
          $slide.addClass('highlight');


          this.slideTo(idx, 0, false);
          requestAnimationFrame(() => updatePreviewByIndex(idx));
        }
      }
    });

 
    swiper.on('slideChange', function () {
     
      const idx = swiper.activeIndex;
      $('.swiper-slide').removeClass('highlight');
      $(swiper.slides[idx]).addClass('highlight');
      updatePreviewByIndex(idx);
    });

    $(document)
      .off('click.swpr')
      .on('click.swpr', '.image-gallery .swiper-slide img', function (e) {
        e.preventDefault();
        const idx = parseInt($(this).data('idx'), 10);
        if (Number.isFinite(idx) && idx >= 0 && idx < SLIDES.length) {
          swiper.slideTo(idx);
          $('.swiper-slide').removeClass('highlight');
          $(swiper.slides[idx]).addClass('highlight');
          updatePreviewByIndex(idx);
        }
      });
  }

  function applyYearToSwiper(min, max) {
   
    const filteredLimited = ALL_IMAGES
      .filter(i => Number.isFinite(i.year) && i.year >= min && i.year <= max)
      .sort((a, b) => a.year - b.year || (a.full || '').localeCompare(b.full || ''))
      .slice(0, IMAGE_LIMIT);

    // 🔥 MAKE SLIDES THE SINGLE SOURCE OF TRUTH
    SLIDES = filteredLimited.slice();
    window.SLIDES = SLIDES;

    // Rebuild swiper using SLIDES
    renderSlides(SLIDES);
    initSwiper({ startIndex: 0, centerOnce: false });

    // Update preview for the first slide
    if (SLIDES.length > 0) {
        updatePreviewByIndex(0);
    } else {
        $('#preview-image').attr('src', '');
        $('.info-location, .info-year, .photo-donor, .info-meta').text('');
    }

}



  /* ============================
     GRID VIEW
  ============================ */

  function renderGridPage(page) {
    const totalPages = Math.ceil(gridData.length / PAGE_SIZE);
    currentPage = Math.max(1, Math.min(page, totalPages));

    const start = (currentPage - 1) * PAGE_SIZE;
    const slice = gridData.slice(start, start + PAGE_SIZE);

    const html = slice.map(i => `
      <div class="gallery-item" data-year="${i.year || ''}">
        <a href="${i.full}" data-fancybox="gallery" data-caption="${i.title}" >
          <img src="${i.full}" alt="${i.title}" title="${i.title}">
        </a>
      </div>
    `).join('');

    $wrap.html(html);
  }

  Fancybox.bind("[data-fancybox='gallery']", {
    // options
  });

  function renderGridPagination() {
    const $p = $('#grid-pagination');
    if (!$p.length) return;

    const totalPages = Math.ceil(gridData.length / PAGE_SIZE);
    if (totalPages <= 1) {
      $p.empty();
      return;
    }

    const makeBtn = (p, label, cls='') =>
      `<a href="#" class="${cls}" data-page="${p}">${label}</a>`;

    let html = '';
    const prev = Math.max(1, currentPage - 1);
    const next = Math.min(totalPages, currentPage + 1);

    html += makeBtn(1, '&laquo;', 'first');
    html += makeBtn(prev, '&lsaquo;', 'prev');

    for (let i = 1; i <= totalPages; i++) {
      html += makeBtn(i, i, i === currentPage ? 'active' : '');
    }

    html += makeBtn(next, '&rsaquo;', 'next');
    html += makeBtn(totalPages, '&raquo;', 'last');

    $p.html(html);
  }

  $(document).on('click', '#grid-pagination a', function (e) {
    e.preventDefault();
    const page = parseInt($(this).data('page'), 10) || 1;
    renderGridPage(page);
    renderGridPagination();
    const el = $grid.get(0);
    if (el && el.scrollIntoView) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  function extractYearFromUrl(url) {
    if (!url) return null;
    try { url = decodeURIComponent(url); } catch(e) {}
    const name = url.split('/').pop() || '';
    const matches = name.match(/\b(1[5-9]\d{2}|20\d{2})\b/g);
    if (!matches || !matches.length) return null;
    return parseInt(matches[matches.length - 1], 10);
  }

  async function buildGrid() {
    // Use the same year range as the slider
    const [min, max] = getYearRange();

    // Use the already-merged ALL_IMAGES (HTML + REST)
    const filtered = (ALL_IMAGES || [])
      .filter(i => Number.isFinite(i.year) && i.year >= min && i.year <= max);

    // Reuse dedupe + sorting logic so filenames normalize the same way
    const deduped = dedupe(filtered).sort((a, b) =>
      a.year - b.year || (a.full || '').localeCompare(b.full || '')
    );

    if (!deduped.length) {
      $wrap.html('<p class="no-results">No images found for this range.</p>');
      $('#grid-pagination').empty();
      return;
    }

    gridData = deduped;
    currentPage = 1;
    renderGridPage(currentPage);
    renderGridPagination();
  }


  /* ============================
     YEAR SLIDER
  ============================ */
  const rangeEl = document.getElementById('range-year');
  if (rangeEl && !rangeEl.noUiSlider) {
    noUiSlider.create(rangeEl, {
      start: [1850, 1989],
      connect: true,
      range: { min: 1850, max: 1989 },
      step: 1,
      tooltips: true,
      format: { to: v => Math.round(v), from: v => Number(v) }
    });

  rangeEl.noUiSlider.on('change', function () {
    const [min, max] = getYearRange();

    // rebuild slides for the selected year range
    applyYearToSwiper(min, max);

    // after slides are recreated, ensure preview + highlight match slide 0
    setTimeout(() => {
        if (swiper && SLIDES.length > 0) {
            swiper.slideTo(0, 0, false);   // select first slide
            updatePreviewByIndex(0);        // update preview image

            $('.swiper-slide').removeClass('highlight');
            $(swiper.slides[0]).addClass('highlight');
        }
    }, 0);

    // grid mode update
    if ($('body').hasClass('grid')) {
        buildGrid();
    }
  });


  }

  /* ============================
     VIEW TOGGLES
  ============================ */
  $btnGrid.on('click', async function () {
    $btnSlider.removeClass('active');
    $(this).addClass('active');
    $('body').addClass('grid').removeClass('no-scroll');

    $swiper.hide();
    $preview.hide();
    $range.hide();

    $grid.fadeIn(250, async function () {
      await buildGrid();
    });
  });

  $btnSlider.on('click', function () {
      if ($(this).hasClass('active')) return; // ← STOP double-activation

      $btnGrid.removeClass('active');
      $(this).addClass('active');
      $('body').removeClass('grid');
      $('.home-wrap').addClass('show');

      $grid.hide();
      $swiper.fadeIn(250);
      $preview.fadeIn(250);
      $range.fadeIn(250);

      if (swiper) {
          setTimeout(() => {
              swiper.updateSize();
              swiper.updateSlides();
              swiper.updateProgress();
          }, 0);
      }
  });


  /* ============================
     INITIAL BOOT
  ============================ */
  (async function init() {
 
  await mergeAllSources();

  // Ensure every item has thumb & full
    ALL_IMAGES = (ALL_IMAGES || []).map(img => {
      return {
        ...img,
        // NEVER overwrite full if it exists
        full  : img.full  || img.url || '',
        // Thumb can fall back to full
        thumb : img.thumb || img.url || img.full || '',
      };
    });


  const SORTED = [...ALL_IMAGES]
    .filter(i => Number.isFinite(i.year))
    .sort((a, b) =>
      a.year - b.year ||
      (a.full || '').localeCompare(b.full || '')
    );

  const randomStartIndex = Math.floor(Math.random() * Math.max(1, SORTED.length));
  const LIMITED = SORTED.slice(0, IMAGE_LIMIT);


  if (!SLIDES.length) {
    renderSlides(LIMITED);
  }


  requestAnimationFrame(() => {
    // ✅ Only initialize swiper from init IF we just rendered slides here
    if (!SLIDES.length || SLIDES.length === LIMITED.length) {
      initSwiper({ startIndex: randomStartIndex, centerOnce: true });
    }
  });

})();



$(window).on("scroll", function () {
  if($('body').hasClass('home')) {
    const title = document.getElementById("title");
    const logo = document.querySelector("#landing .logo");

    const titleTop = title.getBoundingClientRect().top;
    const logoBottom = logo.getBoundingClientRect().bottom;

    const fadeEndOffset = 100; // px under logo
    const fadeEnd = logoBottom + fadeEndOffset;

    //  Force full opacity at top
    if (window.scrollY < 2) {
      title.style.opacity = 1;
      return;
    }

    // Distance from fade end
    const distance = titleTop - fadeEnd;

    // Fully hidden once past fade end
    if (distance <= 0) {
      title.style.opacity = 0;
      return;
    }

    //  Fade (simple + predictable)
    const fadeDistance = 300; // adjust if needed
    const opacity = Math.min(1, distance / fadeDistance);

    title.style.opacity = opacity;
  }
  
});


});
