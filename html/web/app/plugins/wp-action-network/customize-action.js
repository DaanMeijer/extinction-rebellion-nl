function actionNetworkCustomizeForm( can_id, can_properties ) {
	
	var thank_you, help_us, social, email, embed;
	thank_you = can_properties.thank_you ? can_properties.thank_you : '';
	help_us = can_properties.help_us ? can_properties.help_us : '';
	hide_social = can_properties.hide_social ? true : false;
	hide_email = can_properties.hide_email ? true : false;
	hide_embed = can_properties.hide_embed ? true : false;
	
	jQuery(document).on( can_id+'_submitted', function(){
		if (thank_you.length) { jQuery('#' + can_id + ' #can_thank_you h1').text(thank_you); }
		if (help_us.length) { jQuery('#' + can_id + ' #can_thank_you h4').text(help_us); }
		if (hide_social) { jQuery('#' + can_id + ' .can_thank_you-block').eq(0).hide(); }
		if (hide_email) { jQuery('#' + can_id + ' .can_thank_you-block').eq(1).hide(); }
		if (hide_embed) { jQuery('#' + can_id + ' .can_thank_you-block').eq(2).hide(); }
	});
}

if (typeof actionNetworkCustomizations == 'object') {
	for (var actionNetworkId in actionNetworkCustomizations) {
		if (actionNetworkCustomizations.hasOwnProperty(actionNetworkId)) {
			actionNetworkCustomizeForm( actionNetworkId, actionNetworkCustomizations[actionNetworkId] );
		}
	}
}