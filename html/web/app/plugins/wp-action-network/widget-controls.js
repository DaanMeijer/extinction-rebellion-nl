function widgetControlsPrepare() {
	var $ = jQuery;
	console.log('widgetControlsPrepare called');
	$('.widget-controls').each(function(){
		if ($(this).find('.widget-control-advanced').length && !$(this).find('.widget-control-toggle-advanced').length) {
			$(this).find('.widget-control-advanced').hide();
			var id = $(this).closest('.widget').attr('id') + '-toggle-advanced';
			console.log('widgetControlsPrepare prepared widget '+id);
			var speed = $(this).find('.widget-control-advanced').length * 25;
			$(this).find('.widget-control-advanced').filter(':first').before(
				'<li class="widget-control"><input class="widget-control-toggle-advanced" type="checkbox" id="'+id+'" data-speed="'+speed+'" /> <label for="'+id+'">'+widgetcontrolText.showAdvanced+'</label></li>'
			);
		}
	});
	$('.widget-control-toggle-advanced').change(function(){
		var speed = $(this).attr('data-speed');
		if ($(this).is(':checked')) {
			$(this).closest('.widget-controls').find('.widget-control-advanced').slideDown(speed);
		} else {
			$(this).closest('.widget-controls').find('.widget-control-advanced').slideUp(speed);
		}
	});
}

// prepare controls on load
jQuery(document).ready(function($){
	console.log('document.ready called');
	widgetControlsPrepare();
});

// prepare controls after an ajax call (i.e., widget was saved)
jQuery(document).ajaxComplete(function(){
    console.log('document.ajaxComplete called');
    widgetControlsPrepare();
});