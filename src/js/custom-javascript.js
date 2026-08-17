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


})();
