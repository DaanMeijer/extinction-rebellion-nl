(function($) {


	var $container = $('.js-masonry');

	// initialize Masonry after all images have loaded  
	$container.imagesLoaded( function() {
	     $container.masonry();
	});
	
	/*
	var $grid = $('.grid').masonry({
		// options
		itemSelector: 'div.grid.js-masonry div.thumbnails div.thumbnail.grid-item',
		// columnWidth: 50
	});

	// layout Masonry after each image loads
	$grid.imagesLoaded().progress( function() {
	  $grid.masonry('layout');
	});
	*/


})(jQuery);



