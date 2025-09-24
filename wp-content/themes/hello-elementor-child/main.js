jQuery(function($) {

    $('body').addClass('no-scroll');
    $('.view-toggle').addClass('hide');
    $('html, body').scrollTop(0);
    // console.log($('html, body').scrollTop());

        const userAgent = navigator.userAgent.toLowerCase();
        const isTablet = /(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/.test(userAgent);
        const isMobile = /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent); 

           $.fn.isOnScreen = function(){

            var win = $(window);

            var viewport = {
                top : win.scrollTop(),
                left : win.scrollLeft()
            };
            viewport.right = viewport.left + win.width();
            viewport.bottom = viewport.top + win.height();

            var bounds = this.offset();
            bounds.right = bounds.left + this.outerWidth();
            bounds.bottom = bounds.top + this.outerHeight();

            return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

        };

    let scrolls = [];
    let total= 0;
    let leftVal;
    let bottomVal;

    $(window).bind('mousewheel', function(event, delta) {
      if($("body").hasClass('no-scroll')) {

      
        if($(window).width() > 1024 && isTablet == false) {
            if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
                console.log("intra 1");
                if(scrolls.length > 0) {
                    
                    if($('html, body').scrollTop() < 500) {
                        scrolls.pop();
                        // newTotal= 0;
                        total= 0;
                        for (let i = 0; i < scrolls.length; i++) {
                            total= total + scrolls[i];
                            // $('.scroll-bg .scroll').css({
                            //     width: total + "%"
                            // });
                        }
                      
                    } 
                
                    if(total == 0) {
                        leftVal= -100 + "%";
                        $('.home-wrap').removeClass('inView').animate({
                            left: leftVal
                        }, 1000);
                        $('.scroll-wrap').removeClass('offView');
                        $('.view-toggle').removeClass('show');
                        // $('.home-wrap').removeClass('inView show');
                        // $('.skill-progress').removeClass('show');
                        scrolls=[];
                        // $('.scroll-bg .scroll').removeClass('show').css({
                        //     width: 0
                        // });
                        // setTimeout(function() {
                        //     $('.home-wrap .description .title').removeClass('show');
                        // }, 500);    
                    } 
                } 
            
            }
            else {
                let oneScroll = 50;
              
            
                if( total < 100 ) {
                    scrolls.push(oneScroll);
                    total= 0;
                    for (let i = 0; i < scrolls.length; i++) {
                        total= total + scrolls[i];
                    }
                   
                    // console.log(total);
                    if( total == 100) {
                        $('.home-wrap').animate({
                            left: 0
                        }, 1000);
                       
                      $('.scroll-wrap').addClass('offView');
                      $('.view-toggle').removeClass('hide').addClass('show');
                    } 
                  
                }
            }
        } else if($('body').hasClass('desktop') || isTablet == true){
          
            let val = $(this).scrollTop();
          
            if (val > 340) {
                if($('.skill-progress').hasClass('show')) {

                } else {
                    $('.skill-progress').addClass('show');
                    $('.home-wrap .description .title').addClass('show');
                }
              
            } 
            
            
        }
      }
    });


    // Initial state
    $('#range-year').show();

    // ===== Masonry (Isotope) – single instance, stable ordering =====
    var $grid = $('.grid-container');
    var $pagination = $('#grid-pagination');
    var iso = null;
    var isoReady = false;
    var gridBootstrapped = false;
    var lastGridWidth = null;
    var gridSignature = null; // "min-max" signature of last loaded range

    function initIsotope() {
      if (isoReady) return iso;

      $grid.isotope({
        itemSelector: '.gallery-item',
        layoutMode: 'masonry',
        masonry: { columnWidth: '.grid-sizer', gutter: '.gutter-sizer', horizontalOrder: true },
        sortBy: 'original-order',
        sortAscending: true,
        percentPosition: true,
        transitionDuration: 0
      });

      iso = $grid.data('isotope');
      $grid.imagesLoaded(function () {
        $grid.isotope('layout');
        lastGridWidth = $grid.width();
      });
      if (iso && iso.on) {
        iso.on('layoutComplete', function(){ lastGridWidth = $grid.width(); });
      }
      isoReady = true;
      return iso;
    }

    function debounce(fn, wait){ var t; return function(){ clearTimeout(t); var a=arguments, c=this; t=setTimeout(function(){ fn.apply(c,a); }, wait); }; }
    // Debounced grid refresh to avoid spamming AJAX while dragging
    var refreshGridDebounced = debounce(function () {
      refreshGridForCurrentRange({ page: 1 });
    }, 250);

    // Expose preview setter
    window.setPreview = function (src, loc, id, yr) {
      var $img  = $('#preview-image');
      var $loc  = $('.info-box .info-location');
      var $year = $('.info-box .info-year');
      var $meta = $('#preview-meta');
      if ($img.length)  $img.attr('src', src || '');
      if ($loc.length)  $loc.text(loc || '');
      if (!yr && id) {
        var slideEl = document.querySelector('.swiper-slide.gallery-item[data-id="'+ String(id) +'"]');
        if (slideEl) yr = slideEl.getAttribute('data-year') || '';
      }
      if ($year.length) $year.text(yr || '');
      var html = '';
      var key  = String(id || '');
      if (window.META_BY_ID && key && (key in window.META_BY_ID)) { html = window.META_BY_ID[key] || ''; }
      if ($meta.length) { $meta.html(html); $meta.toggle(!!html); }
    };

    // View toggle
    $('.slider-view').on('click', function() {
      $('#masthead').removeClass('relative');
      $('.view-button').removeClass('active');
      $(this).addClass('active');
      $('.bottom-wrap, #image-preview').show();
      $('.grid-container').hide();
      $pagination.hide();
      $('#range-year').show();
      $('body').removeClass('grid').addClass('no-scroll');
    });

    $('.grid-view').on('click', function () {
      $('body').addClass('grid').removeClass('no-scroll');
      $('.view-button').removeClass('active'); $(this).addClass('active');

      $('.bottom-wrap, #image-preview').hide();
      $('.grid-container').show();
      $('#range-year').hide();

      initIsotope();

      // Check current year range signature
      var r   = getRangeValues();
      var sig = r.min + '-' + r.max;

      var needsReload = !gridBootstrapped || gridSignature !== sig;

      if (needsReload) {
        gridBootstrapped = true;
        gridState.current_page = 1;
        gridSignature = sig;
        refreshGridForCurrentRange({ page: 1 });
      } else {
        // No content change -> do not reshuffle. Just show pagination if needed.
        $pagination.toggle(gridState.total_pages > 1);
        requestAnimationFrame(function(){
          var w = $grid.width();
          if (lastGridWidth !== w) {
            $grid.isotope('arrange');
            lastGridWidth = w;
          }
        });
      }
    });

    // Swiper
    var swiper = new Swiper('.swiper', {
      slidesPerView: 'auto',
      spaceBetween: 20,
      freeMode: true,
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
    });

    // Year range slider
    var range = document.getElementById('range-year');
    noUiSlider.create(range, { start: [1850, 1989], connect: true, range: { min: 1850, max: 1989 }, step: 1, tooltips: true, format: { to: v => Math.round(v), from: v => Number(v) } });
    function getRangeValues() {
      var min = 1850, max = 1989;
      if (range && range.noUiSlider) {
        var vals = range.noUiSlider.get();
        min = parseInt(vals[0], 10); max = parseInt(vals[1], 10);
      }
      return { min, max };
    }

    // Keep slider filtered (thumbnails) and refresh grid when in grid view
    var pendingPreview = null;
    function updateFirstVisiblePreview() {
      var src = '', loc = '', id = '', yr = '';
      for (var i=0; i<swiper.slides.length; i++) {
        var slide = swiper.slides[i];
        if (slide.style.display !== 'none') {
          var img = slide.querySelector('img');
          if (img) { src = img.getAttribute('data-full')||''; id = img.getAttribute('data-id') || slide.getAttribute('data-id') || ''; loc = img.getAttribute('data-location') || slide.getAttribute('data-location') || ''; yr = slide.getAttribute('data-year') || ''; break; }
        }
      }
      setPreview(src, loc, id, yr);
    }

    function focusSlideById(id, src, loc, yr) {
      id = String(id);
      var slidesArr = Array.prototype.slice.call(swiper.slides);
      var idx = slidesArr.findIndex(function(s){ return String(s.getAttribute('data-id')) === id; });
      if (idx === -1) {
        var html = '<div class="swiper-slide gallery-item" data-id="'+id+'" data-year="'+(yr||'')+'" data-location="'+(loc||'')+'">'+
                    '<img src="'+(src||'')+'" data-full="'+(src||'')+'" data-id="'+id+'" data-location="'+(loc||'')+'">'+
                    '</div>';
        var insertAt = slidesArr.findIndex(function(s){ var y = parseInt(s.getAttribute('data-year'),10); return !isNaN(y) && !isNaN(yr) && y > yr; });
        if (insertAt === -1) { swiper.appendSlide(html); idx = swiper.slides.length; }
        else { swiper.addSlide(insertAt, html); idx = insertAt; }
        swiper.update();
      }
      swiper.slideTo(idx, 0);
      setPreview(src, loc, id, yr);
    }

    range.noUiSlider.on('update', function(values){
      var min = parseInt(values[0],10), max = parseInt(values[1],10);

      // Update slider thumbs only when not in grid
      if (!$('body').hasClass('grid')) {
        for (var i=0;i<swiper.slides.length;i++){
          var s = swiper.slides[i]; var yr = parseInt(s.getAttribute('data-year'),10);
          s.style.display = (isNaN(yr) || (yr >= min && yr <= max)) ? '' : 'none';
        }
        swiper.update();
        if (pendingPreview){ focusSlideById(pendingPreview.id, pendingPreview.src, pendingPreview.loc); pendingPreview=null; }
        else { updateFirstVisiblePreview(); }
      }

      // In grid view: fetch grid for the new range (debounced)
      if ($('body').hasClass('grid')) {
        gridState.current_page = 1;
        refreshGridDebounced();
      }
    });

    // Slider click -> set preview
    $('.swiper-wrapper').on('click', '.gallery-item img', function () {
      var $img = $(this); var id = $img.data('id'); var src = $img.data('full') || ''; var loc = $img.data('location') || ''; var yr = $img.closest('.gallery-item').attr('data-year') || '';
      setPreview(src, loc, id, yr);
    });

    // Grid click -> switch to slider + show that exact image
    $('.grid-container').on('click', '.gallery-item img', function () {
      var $img = $(this);
      var id   = String($img.data('id') || $img.closest('.gallery-item').data('id'));
      var src  = $img.data('full') || $img.attr('src') || '';
      var loc  = $img.data('location') || $img.closest('.gallery-item').data('location') || '';
      var yr   = parseInt($img.closest('.gallery-item').data('year') || $img.data('year'), 10);
      pendingPreview = { id: id, src: src, loc: loc };
      $('.slider-view').trigger('click');
      var cur = range && range.noUiSlider ? range.noUiSlider.get() : null;
      if (cur && !isNaN(yr)) {
        var min = parseInt(cur[0],10), max = parseInt(cur[1],10);
        if (yr < min)       range.noUiSlider.set([yr, max]);
        else if (yr > max)  range.noUiSlider.set([min, yr]);
        else { focusSlideById(id, src, loc, yr); pendingPreview = null; }
      } else { focusSlideById(id, src, loc, yr); pendingPreview = null; }
      setPreview(src, loc, id, yr);
    });

    // ---------- PAGINATION (AJAX) ----------
    var gridState = { current_page: 1, per_page: 24, min: 1850, max: 1989, total: 0, total_pages: 1 };

    function renderPagination(){
      var total = gridState.total_pages;
      if (!total || total <= 1){ $pagination.empty().hide(); return; }
      var cur = gridState.current_page;
      var html = '';
      function btn(label, page, disabled, extraClass){
        var dis = disabled ? ' disabled' : '';
        var cls = 'page ' + (extraClass||'');
        var isActive = (!extraClass || extraClass.indexOf('num')!==-1) && page === cur;
        if (isActive) cls += ' active';
        var aria = disabled ? ' aria-disabled="true"' : '';
        var data = disabled ? '' : ' data-page="'+page+'"';
        return '<button type="button" class="'+cls+'"'+data+aria+dis+'>'+label+'</button>';
      }
      var start = Math.max(1, cur - 2);
      var end   = Math.min(total, start + 4);
      if (end - start < 4) start = Math.max(1, end - 4);
      html += btn('«', 1, cur===1, 'nav first');
      html += btn('‹', cur-1, cur===1, 'nav prev');
      for (var p=start; p<=end; p++){ html += btn(String(p), p, false, 'num'); }
      html += btn('›', cur+1, cur===total, 'nav next');
      html += btn('»', total, cur===total, 'nav last');
      $pagination.html(html).show();
    }

    $pagination.on('click', 'button[data-page]', function(){
      var p = parseInt($(this).data('page'), 10);
      if (!isNaN(p) && p>=1 && p<=gridState.total_pages && p!==gridState.current_page) { gotoPage(p); }
    });

    function refreshGridForCurrentRange(opts){
      opts = opts || {};
      var page = parseInt(opts.page || gridState.current_page || 1, 10) || 1;

      var r = getRangeValues();
      gridState.min = r.min;
      gridState.max = r.max;

      $.post(acfSliderAjax.ajax_url, {
        action:   'load_more_acf_slider',
        page:     page,
        per_page: gridState.per_page,
        min_year: gridState.min,
        max_year: gridState.max
      }, function(res){
        if (!res || typeof res !== 'object') return;

        var $newItems = $(res.html || '');

        initIsotope();

        // Remove current items (Isotope-managed, not sizers)
        var currentEls = iso.getItemElements ? iso.getItemElements() : $grid.find('.gallery-item').toArray();
        if (currentEls && currentEls.length) {
          $grid.isotope('remove', $(currentEls));
        }

        // Append and register new items, then layout after images load
        $grid.append($newItems);

        $grid.imagesLoaded(function () {
          $grid.isotope('appended', $newItems);
          $grid.isotope('layout');
          lastGridWidth = $grid.width();
        });

        // Update paging state & UI
        gridState.total        = res.total || 0;
        gridState.total_pages  = res.total_pages || (gridState.total ? Math.ceil(gridState.total / gridState.per_page) : 1);
        gridState.current_page = res.current_page || page;

        // Remember the range actually loaded to avoid unnecessary reloads on toggle
        gridSignature = gridState.min + '-' + gridState.max;

        renderPagination();
        $pagination.toggle(gridState.total_pages > 1);
      }, 'json');
    }

    function gotoPage(p){ gridState.current_page = p; refreshGridForCurrentRange({ page: p }); }

    // Hover & meta toggle
    $('#image-preview').hover(function(){ $('.more-info').addClass('show'); }, function(){ $('.more-info').removeClass('show'); });
    $('.more-info').on('click', function() { $(this).siblings('.info-box').toggleClass('transform'); });

    // Initial preview
    updateFirstVisiblePreview();
  });
  