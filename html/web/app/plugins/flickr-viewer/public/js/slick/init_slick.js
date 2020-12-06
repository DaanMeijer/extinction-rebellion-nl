(function($) {
  $('.multiple-items-sc').slick({
    arrows: true,
  	  	infinite: true,
  		slidesToShow: 4,
  		slidesToScroll: 4,
  		autoplay: true,
  		autoplaySpeed: 2000,  		
  });
  $('.multiple-items-sc').slickLightbox({
    largesrc: 'largesrc',
    itemSelector: '.item a'
  });
})(jQuery);