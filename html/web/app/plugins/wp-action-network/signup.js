function actionNetworkAjaxSignup( $form ) {
	
	$form.addClass('submitting');
	jQuery(document).trigger('actionnetwork_signup_submitted', $form);
	var data = {
		'action' : 'actionnetwork_signup',
		'data' : $form.serialize()
	}
	jQuery.post(ajax_object.ajax_url, data, function(response) {
		jQuery(document).trigger('actionnetwork_signup_complete', $form, response);
		$form.before('<div class="actionnetwork-signup-message">' + response.message + '</div>');
		$form.removeClass('submitting').addClass('submitted');
	});
}

jQuery(document).ready(function($){
	
	$('.actionnetwork-signup-form.use-ajax').each(function(){
		
		if (typeof $(this).validate == 'function') {
			
			$(this).data("validator").settings.submitHandler = function(form) {
				actionNetworkAjaxSignup( $(form) );
				return false;
			}
			
		} else {
			$(this).submit(function(event){
				actionNetworkAjaxSignup( $(this) );
				event.preventDefault();
			});
		}
		
	});
});