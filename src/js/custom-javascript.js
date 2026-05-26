// Add your custom JS here.
(function(){
  // hide header on scroll

  var doc = document.documentElement;
  var w = window;

  var prevScroll = w.scrollY || doc.scrollTop;
  var curScroll;
  var direction = 0;
  var prevDirection = 0;

  var header = document.getElementById('navbar');

  var checkScroll = function() {

    /*
    ** Find the direction of scroll
    ** 0 - initial, 1 - up, 2 - down
    */

    curScroll = w.scrollY || doc.scrollTop;
    if (curScroll > prevScroll) { 
      //scrolled up
      direction = 2;
    }
    else if (curScroll < prevScroll) { 
      //scrolled down
      direction = 1;
    }

    if (direction !== prevDirection) {
      toggleHeader(direction, curScroll);
    }

    prevScroll = curScroll;
  };

  var toggleHeader = function(direction, curScroll) {
    if (direction === 2 && curScroll > 52) { 

      //replace 52 with the height of your header in px
      if (!document.getElementById('navbarCollapse').classList.contains("show")) {
          header.classList.add('hide');
          prevDirection = direction;
      }
    }
    else if (direction === 1) {
      header.classList.remove('hide');
      prevDirection = direction;
    }
  };

  window.addEventListener('scroll', checkScroll);

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


})();
