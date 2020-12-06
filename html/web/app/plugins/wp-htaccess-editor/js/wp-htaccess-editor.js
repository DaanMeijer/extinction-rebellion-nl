/**
 * WP Htaccess Editor
 * (c) WebFactory Ltd, 2018-2019
 */

jQuery(document).ready(function($) {
  var wphe_changed = false;

  // setup CodeMirror
  wp.CodeMirror.defineInitHook(function(wphe_cm) {
    // add resize handle for editor
    cmResize(wphe_cm);

    // detect user changes in editor
    wphe_cm.on('change', function() {
      wphe_changed = true;
    });

    // detect and save editor size change
    wphe_cm.on('update', function(e) {
      localStorage.setItem('wphe-editor-width', e.display.lastWrapWidth);
      localStorage.setItem('wphe-editor-height', e.display.lastWrapHeight);
    });

    // kill WP's beforeunload
    $(window).off('beforeunload');

    // set beforeunload save check
    $(window).bind('beforeunload', function() {
      if (wphe_changed) {
        return true;
      }
    });
    
    let wphe_width = parseInt(localStorage.getItem('wphe-editor-width'), 10);
    let wphe_height = parseInt(localStorage.getItem('wphe-editor-height'), 10);
    
    if(!(wphe_width > 0 && wphe_height > 0)){
        wphe_width = 676;
        wphe_height = 400;
    }
    
    if (wphe_width > 0 && wphe_height > 0) {
      $('.CodeMirror')
        .width(wphe_width)
        .height(wphe_height);
        
      $('#enable-editor-notice')
        .innerHeight(wphe_height)
        .innerWidth(wphe_width);
    }
  }); // CodeMirror setup

  // init code editor
  wp.themePluginEditor.init(
    $('#htaccess-editor-wrap'),
    wp_htaccess_editor.cm_settings
  );
  wp.themePluginEditor.themeOrPlugin = 'theme';

  // display a message while an action is performed
  function block_ui(message) {
    tmp = swal({
      text: message,
      type: false,
      imageUrl: wp_htaccess_editor.loading_icon_url,
      onOpen: () => {
        $(swal.getImage()).addClass('rotating');
      },
      imageWidth: 100,
      imageHeight: 100,
      imageAlt: message,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
      width: 600
    });

    return tmp;
  } // block_ui

  // display dialog to confirm action
  function confirm_action(title, question, btn_confirm, btn_cancel) {
    tmp = swal({
      title: title,
      type: 'question',
      html: question,
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText: btn_confirm,
      cancelButtonText: btn_cancel,
      confirmButtonColor: '#dd3036',
      width: 600
    });

    return tmp;
  } // confirm_action

  // dismiss notice / pointer
  $('.wphe-dismiss-notice').on('click', function(e) {
    notice_name = $(this).data('notice');
    if (!notice_name) {
      return true;
    }

    $.get(ajaxurl, {
      notice_name: notice_name,
      _ajax_nonce: wp_htaccess_editor.nonce_dismiss_notice,
      action: 'wp_htaccess_editor_dismiss_notice'
    });

    $(this)
      .parents('.notice-wrapper')
      .fadeOut();

    if (notice_name == 'editor-warning') {
      $('#wphe-buttons').fadeIn();
    }

    e.preventDefault();
    return false;
  }); // dismiss notice

  // collapse / expand card
  $('.card').on('click', '.toggle-card', function(e) {
    e.preventDefault();

    card = $(this)
      .parents('.card')
      .toggleClass('collapsed');
    $('.dashicons', this)
      .toggleClass('dashicons-arrow-up-alt2')
      .toggleClass('dashicons-arrow-down-alt2');
    $(this).blur();

    cards = localStorage.getItem('wp-htaccess-editor-cards');
    if (cards == null) {
      cards = new Object();
    } else {
      cards = JSON.parse(cards);
    }

    if (card.hasClass('collapsed')) {
      cards[card.attr('id')] = 'collapsed';
    } else {
      cards[card.attr('id')] = 'expanded';
    }
    localStorage.setItem('wp-htaccess-editor-cards', JSON.stringify(cards));

    return false;
  }); // toggle-card

  // init cards; collapse those that need collapsing
  cards = localStorage.getItem('wp-htaccess-editor-cards');
  if (cards != null) {
    cards = JSON.parse(cards);
  }
  $.each(cards, function(card_name, card_value) {
    if (card_value == 'collapsed') {
      $('a.toggle-card', '#' + card_name).trigger('click');
    }
  });

  // save htaccess file
  $('#wphe_save_htaccess').click(function(e) {
    e.preventDefault();

    block_ui(wp_htaccess_editor.saving);
    $.post({
      url: ajaxurl,
      data: {
        action: 'wp_htaccess_editor_do_action',
        _ajax_nonce: wp_htaccess_editor.nonce_do_action,
        subaction: 'save_htaccess',
        new_content: wp.themePluginEditor.instance.codemirror.getValue()
      }
    })
      .always(function(data) {
        swal.close();
      })
      .done(function(data) {
        if (typeof data.success != 'undefined' && data.success) {
          jQuery
            .get(wp_htaccess_editor.home_url)
            .always(function(data, text, xhr) {
              status = xhr.status;
              wphe_changed = false;
              if (status.substr(0, 1) != '2') {
                swal({ type: 'error', title: wp_htaccess_editor.site_error });
              } else {
                swal({
                  type: 'success',
                  title: wp_htaccess_editor.save_success,
                  showConfirmButton: false,
                  timer: 1000
                });
                $('#wphe-rating-notice').show();
              }
            });
        } else if (typeof data.success != 'undefined' && !data.success) {
          swal({ type: 'error', title: data.data });
        } else {
          swal({ type: 'error', title: wp_htaccess_editor.undocumented_error });
        }
      })
      .fail(function(data) {
        if (data.data) {
          swal({
            type: 'error',
            title: wp_htaccess_editor.documented_error + ' ' + data.data
          });
        } else {
          swal({ type: 'error', title: wp_htaccess_editor.undocumented_error });
        }
      });

    return false;
  }); // save htaccess


  // test htaccess file
  $('#wphe_test_htaccess').click(function(e) {
    e.preventDefault();

    block_ui(wp_htaccess_editor.testing);
    $.post({
      url: ajaxurl,
      data: {
        action: 'wp_htaccess_editor_do_action',
        _ajax_nonce: wp_htaccess_editor.nonce_do_action,
        subaction: 'test_htaccess',
        new_content: wp.themePluginEditor.instance.codemirror.getValue()
      }
    })
      .always(function(data) {
        swal.close();
      })
      .done(function(data) {
        if (typeof data.success != 'undefined' && data.success) {
            swal({
                type: 'success',
                title: wp_htaccess_editor.test_success,
                html: data.data
            });
        } else if (typeof data.success != 'undefined' && !data.success) {
          swal({
              type: 'error',
              title: wp_htaccess_editor.test_failed,
              html: data.data });
        } else {
          swal({
              type: 'error',
              title: wp_htaccess_editor.undocumented_error });
        }
      })
      .fail(function(data) {
        if (data.data) {
          swal({
            type: 'error',
            title: wp_htaccess_editor.test_failed
          });
        } else {
          swal({ type: 'error', title: wp_htaccess_editor.undocumented_error });
        }
      });

    return false;
  }); // test htaccess

  
  // restore htaccess backup from DB
  $('#wphe_restore_htaccess').click(function(e) {
    message = wp_htaccess_editor.restore_message;
    swal({
      title: wp_htaccess_editor.restore_title,
      type: 'question',
      html: message,
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText: wp_htaccess_editor.restore_button,
      cancelButtonText: wp_htaccess_editor.cancel_button,
      confirmButtonColor: '#dd3036',
      width: 600
    }).then(result => {
      if (result.value === true) {
        block_ui(wp_htaccess_editor.restoring);
        $.post({
          url: ajaxurl,
          data: {
            action: 'wp_htaccess_editor_do_action',
            _ajax_nonce: wp_htaccess_editor.nonce_do_action,
            subaction: 'restore_htaccess_from_db'
          }
        })
          .always(function(data) {
            swal.close();
          })
          .done(function(data) {
            if (data.success) {
              wphe_changed = false;
              swal({
                type: 'success',
                title: wp_htaccess_editor.restore_success,
                onClose: function() {
                  location.reload();
                }
              });
            } else {
              swal({
                type: 'error',
                title: wp_htaccess_editor.documented_error + ' ' + data.data
              });
            }
          })
          .fail(function(data) {
            swal({
              type: 'error',
              title: wp_htaccess_editor.undocumented_error
            });
          });
        return false;
      }
    });
  }); // restore htaccess from DB
}); // onload
