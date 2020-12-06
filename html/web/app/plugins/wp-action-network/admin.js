jQuery(document).ready(function($){

	/**
	 * Admin tabs
	 */
	 
	 if (jQuery(window.location.hash).hasClass('actionnetwork-admin-tab')) {
		$('.wrap .nav-tab-wrapper .nav-tab-active').removeClass('nav-tab-active');
		$('.wrap .nav-tab-wrapper .nav-tab[href="'+window.location.hash+'"]').addClass('nav-tab-active');
	}
	
	if (!$('.wrap .nav-tab-wrapper .nav-tab-active').length) {
		$('.wrap .nav-tab-wrapper .nav-tab:eq(0)').addClass('nav-tab-active');
	}

	var currentTab = $('.wrap .nav-tab-wrapper .nav-tab-active').attr('href');
	$(currentTab).show();

	$('.wrap .nav-tab-wrapper .nav-tab, .wrap h1 .page-title-action').click(function(event){
		var showTab = $(this).attr('href');
		$('.wrap .nav-tab-wrapper .nav-tab-active').removeClass('nav-tab-active');
		if ($(this).hasClass('nav-tab')) {
			$(this).addClass('nav-tab-active');
		} else {
			$('.wrap .nav-tab-wrapper .nav-tab[href="'+showTab+'"]').addClass('nav-tab-active');
		}
		if ($(showTab).length && $(showTab).hasClass('actionnetwork-admin-tab')) {
			event.preventDefault();
			$('.actionnetwork-admin-tab').hide();
			$(showTab).show();
			window.location.hash = '';
		}
	});
	
	/**
	 * Form validation
	 */
	
	$('form .error').focus(function(){
		$(this).removeClass('error');
	});

	/**
	 * Copy to clipboard
	 */
	$('button.copy').click(function(event){
		event.preventDefault();
		var $self = $(this);
		var $input = $($self.attr('data-copytarget'));
		if ($input.length && ( $input.is('input') || $input.is('textarea') ) ) {
			$input.select();
			try {
				// copy text
				document.execCommand('copy');
				var copyText = $self.text();
				$self.addClass('copied').text(actionnetworkText.copied);
				setTimeout(function() { $self.removeClass('copied').text(copyText); }, 400);
				$input.blur();
			}
			catch (err) {
				alert(actionnetworkText.pressCtrlCToCopy);
			}
		}
	});

	/**
	 * Append clear search results link to search results subtitle
	 */
	$('.search-results-title').append(' (<a class="search-results-clear" href="#">'+actionnetworkText.clearResults+'</a>)');
	$('a.search-results-clear').click(function(event){
		event.preventDefault();
		$('#action-search-input').val('');
		$('#actionnetwork-actions-filter').submit();
	});

	/**
	 * javascript alert for shortcode options
	 */
	var shortCodeOptionsText = $('#shortcode-options').text();
	$('#shortcode-options').hide();
	$('.shortcode-options-link a').click(function(event){
		event.preventDefault();
		alert(shortCodeOptionsText);
	});

	/**
	 * If there is an existing API Key, make sure user is sure they want to change it
	 */
	if ($('#actionnetwork_api_key').length && $('#actionnetwork_api_key').val().length) {
		$('#actionnetwork_api_key').attr('readonly','readonly');
		$('#actionnetwork_api_key').after('<button id="actionnetwork_api_key_change">'+actionnetworkText.changeAPIKey+'</button>');
		$('#actionnetwork_api_key_change').click(function(event){
			event.preventDefault();
			if ( confirm( actionnetworkText.confirmChangeAPIKey ) ) {
				$('#actionnetwork_api_key_change').remove();
				$('#actionnetwork_api_key').removeAttr('readonly').focus();
			}
		});
	}
	
	/**
	 * If there is a "sync-started" notice, update it via ajax
	 */
	if ($('#actionnetwork-update-notice-sync-started').length) {
		$('#actionnetwork-update-notice-sync-started p').append('<img src="images/loading.gif" class="loading" />');
		$('#actionnetwork-actions-filter').hide();
		updateSyncStatus = setInterval(
			function(){
				data = {
					action : 'actionnetwork_get_queue_status',
					actionnetwork_ajax_nonce : $('#actionnetwork_ajax_nonce').val()
				}
				jQuery.get(ajaxurl,data,function(response){
					$('#actionnetwork-update-notice-sync-started p').text(response.text);
					$('#actionnetwork-error-notice-sync-started p').append('<img src="images/loading.gif" class="loading" />');
					if (response.status == 'complete') {
						clearInterval( updateSyncStatus );
						$('#actionnetwork-actions-filter').submit();
					}
				})
			}, 3000
		);
	}

});
