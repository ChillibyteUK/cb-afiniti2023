// Add your custom JS here.
(function(){
  // The navbar used to hide itself on scroll down and reappear on scroll up.
  // That was removed: it fought with sticky elements below it, which then had to
  // choose between sitting under the navbar when it returned or leaving a gap
  // while it was away. The navbar is now permanently fixed at the top.

    // header background

  $(document).on('scroll', function () {
    var $nav = $("#navbar");
    // $nav.toggleClass('scrolled', $(this).scrollTop() > $nav.height() );
    if (!$('#primaryNav').hasClass('show')) {
      $nav.toggleClass('scrolled', $(this).scrollTop() > $nav.height() );
    }
  });

  $('#navToggle').on('click', function(){
    var $nav = $("#navbar");
    $nav.toggleClass('navdark');
  });

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper === 'undefined') {
      return;
    }

    document.querySelectorAll('.text_image__carousel.swiper').forEach(function(element) {
      if (element.classList.contains('swiper-initialized')) {
        return;
      }

      new Swiper(element, {
        loop: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        speed: 600,
        spaceBetween: 24,
      });
    });

    document.querySelectorAll('.people_cta__slider.swiper').forEach(function(element) {
      if (element.classList.contains('swiper-initialized')) {
        return;
      }

      new Swiper(element, {
        loop: true,
        autoplay: {
          delay: 4000,
        },
        speed: 600,
        spaceBetween: 100,
      });
    });
  });

    // $('#searchTrigger').on('click',function(e) {
    //     e.stopPropagation();
    //     console.log('clunk');
    //     $('#searchBox').toggleClass('d-none');
    // });
 
    // $('#searchTriggerM').on('click',function(e) {
    //     e.stopPropagation();
    //     console.log('click');
    //     $('#searchBox').toggleClass('d-none');
    // });

    // $('#closeTrigger').on('click',function(e) {
    //     e.stopPropagation();
    //     $('#searchBox').addClass('d-none');
    // });

  // CB Hero Illustration 2026: straddle each scene across the hero's bottom
  // edge, so the middle of the scene sits on the edge and it hangs half into
  // the hero and half below it.
  //
  // The gap between the top of the stage and the hero's bottom edge is the
  // hero's margin-bottom, measured here rather than hard-coded so that
  // restyling the hero cannot quietly drag every illustration out of place.
  //
  // The stage's height is then set to whatever the scene needs plus 1rem, so
  // the half that hangs below the hero still clears the content underneath.
  //
  // The scene is moved as a whole: one shift for every item in it, so the
  // authored --y values keep setting the composition and only its position on
  // the page changes.
  //
  // Geometry is read from the --h/--y custom properties rather than from
  // getBoundingClientRect(), because the entrance animations translate and
  // scale the items while they run and would be measured mid-flight.
  // 1rem, matching --illus-space's fallback in the stylesheet.
  var CB_HERO_ILLUS_GAP = 16;

  function cb_hero_illus_centre() {
    document.querySelectorAll('.hero_illus').forEach(function (stage) {
      var hero = stage.previousElementSibling;

      while (hero && !hero.classList.contains('hero')) {
        hero = hero.previousElementSibling;
      }

      var items = stage.querySelectorAll('.hero_illus__item');

      if (!items.length) {
        return;
      }

      var top = Infinity;
      var bottom = -Infinity;

      items.forEach(function (item) {
        var cs = getComputedStyle(item);
        var y = parseFloat(cs.getPropertyValue('--y'));
        // Bootcamp's SVGs are sized by the document, not by --h.
        var h = parseFloat(cs.getPropertyValue('--h'));

        if (isNaN(h)) {
          h = item.getBoundingClientRect().height;
        }

        if (isNaN(y) || isNaN(h)) {
          return;
        }

        top = Math.min(top, y - h);
        bottom = Math.max(bottom, y);
      });

      if (!isFinite(top) || !isFinite(bottom)) {
        return;
      }

      var shift = 0;

      if (hero) {
        // Both offsets are measured from the top of the stage, which is what
        // --y is measured from. The hero's bottom edge is above that point, by
        // the hero's margin-bottom, so heroEdge is negative.
        var stageTop = stage.getBoundingClientRect().top;
        var sceneCentre = (top + bottom) / 2;
        var heroEdge = hero.getBoundingClientRect().bottom - stageTop;

        shift = Math.round(heroEdge - sceneCentre);
        stage.style.setProperty('--hero-illus-shift', shift + 'px');
      } else {
        // Nothing to straddle: the scene stays at its authored offsets.
        stage.style.removeProperty('--hero-illus-shift');
      }

      // Reserve however far the scene ends up hanging below the top of the
      // stage, plus CB_HERO_ILLUS_GAP of clear space, so the content below
      // never runs under the illustration. Setting the stage's height does not
      // move the scene: --y is measured from the top of the stage, and that
      // edge is fixed by the hero above it, so there is no feedback loop here.
      stage.style.setProperty(
        '--illus-space',
        Math.max(
          CB_HERO_ILLUS_GAP,
          Math.round(bottom + shift) + CB_HERO_ILLUS_GAP
        ) + 'px'
      );
    });
  }

  if (document.querySelector('.hero_illus')) {
    var cb_hero_illus_timer = null;

    var cb_hero_illus_queue = function () {
      clearTimeout(cb_hero_illus_timer);
      cb_hero_illus_timer = setTimeout(cb_hero_illus_centre, 100);
    };

    document.addEventListener('DOMContentLoaded', cb_hero_illus_centre);
    window.addEventListener('load', cb_hero_illus_centre);
    window.addEventListener('resize', cb_hero_illus_queue);

    // The h1's wrap point - and so the hero's height - changes when the web
    // font swaps in.
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(cb_hero_illus_centre);
    }

    cb_hero_illus_centre();
  }


})();
