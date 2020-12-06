function ShowHideDiv() {
  var chkYes = document.getElementById('whydonate-member')
  var fundraiserIDInputDiv = document.getElementById('fundraiser-apikey-div')
  var registrationDiv = document.getElementById('registration-div')
  if (chkYes.checked) {
    // console.log("i'm here");
    fundraiserIDInputDiv.style.display = 'block'
    registrationDiv.style.display = 'none'
  } else {
    registrationDiv.style.display = 'block'
    fundraiserIDInputDiv.style.display = 'none'
  }
}

function addNewFundraiserList() {
  // console.log("Called addNewFundraiserList()");
  var addNewFormDiv = document.getElementById('add-fundraiser')
  var fundraiserList = document.getElementById('fundraiser-list')
  fundraiserList.style.display = 'none'
  addNewFormDiv.style.display = 'block'
  if (jQuery('#set-end-date-input')[0].type != 'date') {
    jQuery('#set-end-date-input').datepicker({
      dateFormat: 'yy-mm-dd',
    })
    document.getElementById('set-end-date-input').placeholder = 'yyyy-mm-dd'
  }
  //fundraiserList = addNewFormDiv;
  jQuery("input[name=tipbox][value="+"1"+"]").attr('checked', 'checked');
}

function backToFundraiserList() {
  // console.log("Back to fundraiser List");
  var addNewFormDiv = document.getElementById('add-fundraiser')
  var fundraiserList = document.getElementById('fundraiser-list')
  var id = document.getElementById('fundraiser-id')
  id.innerHTML = ''
  document.getElementById('add-fundraiser-name-input').value = null
  document.getElementById('set-target-amount-input').value = null
  document.getElementById('set-end-date-input').value = null
  fundraiserList.style.display = 'block'
  addNewFormDiv.style.display = 'none'

  // if (
  //     document.getElementById("create-fundraiser-msg").style.display ===
  //     "block"
  // ) {
  //     window.location.reload();
  // }

  if (window.fundraiserCreatedThroughPlugin === true) {
    document.getElementById('create-fundraiser-msg').style.display === 'block'
    jQuery('#create-fundraiser-msg')
      .delay(10000)
      .fadeOut(10000)

    window.fundraiserCreatedThroughPlugin = false
  }
}

function addNewStyle() {
  // console.log("Called addNewStyle()");
  var addNewStyleFormDiv = document.getElementById('add-new-style')
  var pluginStyleList = document.getElementById('plugin-style-list')
  document.getElementById('set-id').value = 0
  document.getElementById('set-style-name-input').value = ''
  pluginStyleList.style.display = 'none'
  addNewStyleFormDiv.style.display = 'block'
  // console.log(addNewStyleFormDiv);
}

function editWidget(row) {
  // console.log("Called editWidget()");
  var editWidgetDiv = document.getElementById('edit-widget')
  var widgetList = document.getElementById('widget-list')
  var id = row.id
  document.getElementById('edit-widget-id').innerHTML = id
  document.getElementById('edit-widget-id').style.display = 'none'
  var fList = document.getElementById('fundraiser-list-dropdown')
  var sList = document.getElementById('style-list-dropdown')

  var selectedFundraiser = row.slugId + ' ' + row.slugName
  var selectedStyle = row.styleId
  //console.log("selected fundraiser ", selectedFundraiser);
  //console.log("selected style ", selectedStyle);

  for (i = 0; i < fList.length; i++) {
    if (fList.options[i].value === selectedFundraiser) {
      fList.options[i].selected = true
    }
  }

  for (i = 0; i < sList.length; i++) {
    // console.log(
    //      "style dropdown values " + i + " " + sList.options[i].value
    //  );
    if (sList.options[i].value === selectedStyle) {
      sList.options[i].selected = true
    }
  }

  // document.getElementById("fundraiser-redirect-url").value = row.redirectUrl;
  document.getElementById('fundraiser-success-url').value = row.successUrl
  document.getElementById('fundraiser-failure-url').value = row.failureUrl
  widgetList.style.display = 'none'
  editWidgetDiv.style.display = 'block'

  // console.log(editWidgetDiv);
}

function backToWidgetList() {
  // console.log("Called backToWidgetList()");
  var editWidgetDiv = document.getElementById('edit-widget')
  var widgetList = document.getElementById('widget-list')
  var editWidgetId = document.getElementById('edit-widget-id')

  editWidgetId.textContent = ''
  editWidgetId.style.display = 'none'
  widgetList.style.display = 'block'
  editWidgetDiv.style.display = 'none'
}

function backToStyleList() {
  // console.log("Back to style List");
  var addNewStyleFormDiv = document.getElementById('add-new-style')
  var pluginStyleList = document.getElementById('plugin-style-list')
  pluginStyleList.style.display = 'block'
  addNewStyleFormDiv.style.display = 'none'
}

function redirectToCreateWidget() {
  //console.log("called redirectToCreateWidget()");
  // window.location.href =
  //     "http://localhost/MySite/wp-admin/admin.php?page=whydonate-config-widget";
  var editWidgetDiv = document.getElementById('edit-widget')
  var widgetList = document.getElementById('widget-list')
  widgetList.style.display = 'none'
  editWidgetDiv.style.display = 'block'
}

function changeColor() {
  var divElement = document.getElementById('color-div')
  if (divElement.style.backgroundColor == 'blue') {
    divElement.style.backgroundColor = 'red'
  } else {
    divElement.style.backgroundColor = 'blue'
  }
}

function showHideDonateButton(obj) {
  // console.log("called showHideDonateButton()");
  var donateBtndDiv = document.getElementById('apreview-donate-btn-div')
  if (obj) {
    donateBtndDiv.style.display = 'block'
  } else {
    donateBtndDiv.style.display = 'none'
  }
}

function showHideProgressBar(obj) {
  // console.log("called showHideProgressBar()");
  var targetAmountDiv = document.getElementById('apreview-target-amount-div')
  var aFullbar = document.getElementById('apreview-full-bar')
  var achivedPer = document.getElementById('apreview-achived-percent')
  if (obj) {
    targetAmountDiv.style.display = 'block'
    aFullbar.style.display = 'block'
    achivedPer.style.display = 'block'
  } else {
    targetAmountDiv.style.display = 'none'
    aFullbar.style.display = 'none'
    achivedPer.style.display = 'none'
  }

  if (
    document.getElementById('select-progress-bar').checked &&
    document.getElementById('select-raised-amount').checked
  ) {
    showHideOfDiv(true)
  } else {
    showHideOfDiv(false)
  }
}

function showHideOfDiv(obj) {
  var ofDiv = document.getElementById('apreview-target-amount-of-div')
  if (obj) {
    ofDiv.style.display = 'block'
  } else {
    ofDiv.style.display = 'none'
  }
}

function showHideRaisedAmount(obj) {
  //console.log("called showHideRaiseAmount()");
  var totalRaised = document.getElementById('apreview-collected-amount-div')
  var ofDiv = document.getElementById('apreview-target-amount-of-div')
  if (obj) {
    totalRaised.style.display = 'flex'
    ofDiv.style.display = 'none'
  } else {
    totalRaised.style.display = 'none'
  }

  if (
    document.getElementById('select-progress-bar').checked &&
    document.getElementById('select-raised-amount').checked
  ) {
    showHideOfDiv(true)
  } else {
    showHideOfDiv(false)
  }
}

function showHideDaysLeft(obj) {
  //console.log("called showHideDaysLeft()");
  var totalDaysLeft = document.getElementById('apreview-days-left')
  if (obj) {
    totalDaysLeft.style.display = 'block'
  } else {
    totalDaysLeft.style.display = 'none'
  }
}

function showDonationFormOnly(obj) {
  // console.log('called showDonationFormOnly()', obj);
  var appearancePreview = document.getElementById('appearance-preview')
  if (obj) {
    appearancePreview.style.opacity = 0
    document.getElementById('select-donate-button').disabled = true
    document.getElementById('select-progress-bar').disabled = true
    document.getElementById('select-raised-amount').disabled = true
    document.getElementById('select-show-days-left').disabled = true
    document.getElementById('select-donot-show-box').disabled = true
  } else {
    appearancePreview.style.opacity = 1
    document.getElementById('select-donate-button').disabled = false
    document.getElementById('select-progress-bar').disabled = false
    document.getElementById('select-raised-amount').disabled = false
    document.getElementById('select-show-days-left').disabled = false
    document.getElementById('select-donot-show-box').disabled = false
  }
}

function doNotShowBox(obj) {
  // console.log('called donotshowbox()', obj);
  var appearancePreview = document.getElementById('appearance-preview-card')
  if (obj) {
    appearancePreview.style.background = 'transparent'
    appearancePreview.style.boxShadow = 'none'
  } else {
    appearancePreview.style.background = 'rgb(248, 245, 245)'
    appearancePreview.style.boxShadow =
      '0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)'
    // background: rgb(248, 245, 245);
    // box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  }
}

function setFont(fontRef) {
  let font = ''
  if (typeof fontRef === 'object') {
    font = fontRef.value
  } else {
    font = fontRef
  }

  // console.log("called setFont()", font);
  if (event.key !== 'Enter') {
    //console.log("tabbed");

    // appearance preview
    var lastDonationTime = document.getElementById('apreview-donation-time')
    var collectedAmount = document.getElementById(
      'apreview-collected-amount-div',
    )
    var targetAmount = document.getElementById('apreview-target-amount-div')
    var infoDiv = document.getElementById('apreview-progress-info')
    var button = document.getElementById('apreview-donate-btn')

    lastDonationTime.style.fontFamily = font
    collectedAmount.style.fontFamily = font
    targetAmount.style.fontFamily = font
    infoDiv.style.fontFamily = font
    button.style.fontFamily = font

    // preview
    var previewHeader = document.getElementById('preview-header-label-div')
    var intervalOnetime = document.getElementById('preview-intervals-onetime')
    var intervalMonthly = document.getElementById('preview-intervals-monthly')
    var intervalYearly = document.getElementById('preview-intervals-yearly')
    var selectAmount = document.getElementById('preview-select-amount')
    var previewDonateBtn = document.getElementById('preview-donate-btn')

    previewHeader.style.fontFamily = font
    intervalOnetime.style.fontFamily = font
    intervalMonthly.style.fontFamily = font
    intervalYearly.style.fontFamily = font
    selectAmount.style.fontFamily = font
    previewDonateBtn.style.fontFamily = font
  }
}

function showHideOneTime(obj) {
  // console.log("called showHideOneTime() ", obj);
  var periodIntervalOnetime = document.getElementById(
    'preview-intervals-onetime',
  )
  if (obj) {
    periodIntervalOnetime.style.display = 'flex'
  } else {
    periodIntervalOnetime.style.display = 'none'
  }
}

function showHideMonthly(obj) {
  //console.log("called showHideMonthly()");
  var periodIntervalMonthly = document.getElementById(
    'preview-intervals-monthly',
  )
  if (obj) {
    periodIntervalMonthly.style.display = 'flex'
  } else {
    periodIntervalMonthly.style.display = 'none'
  }
}

function showHideYearly(obj) {
  //console.log("called showHideYearly()");
  var periodIntervalYearly = document.getElementById('preview-intervals-yearly')
  if (obj) {
    periodIntervalYearly.style.display = 'flex'
  } else {
    periodIntervalYearly.style.display = 'none'
  }
}

function showAmountFirst(obj) {
  //console.log("called showAmountFirst()");
  var previewDivforFirstAmount = document.getElementById(
    'amount-boundary-box-1',
  )
  if (obj) {
    previewDivforFirstAmount.style.display = 'flex'
  } else {
    previewDivforFirstAmount.style.display = 'none'
  }
}

function changeFirstAmount() {
  //console.log("called changeFirstAmount()");
  var firstAmount = document.getElementById('select-first-amount')
  var previewTextFirstAmount = document.getElementById(
    'amount-boundary-box-1-p',
  )
  previewTextFirstAmount.innerHTML = '€' + firstAmount.value
}

function showAmountSecond(obj) {
  //console.log("called showAmountSecond()");
  var previewDivforSeondAmount = document.getElementById(
    'amount-boundary-box-2',
  )
  if (obj) {
    previewDivforSeondAmount.style.display = 'flex'
  } else {
    previewDivforSeondAmount.style.display = 'none'
  }
}

function changeSecondAmount() {
  //console.log("called changeSecondAmount()");
  var secondAmount = document.getElementById('select-second-amount')
  var previewTextSecondAmount = document.getElementById(
    'amount-boundary-box-2-p',
  )
  previewTextSecondAmount.innerHTML = '€' + secondAmount.value
}

function showAmountThird(obj) {
  //console.log("called showAmountThird()");
  var previewDivforThirdAmount = document.getElementById(
    'amount-boundary-box-3',
  )
  if (obj) {
    previewDivforThirdAmount.style.display = 'flex'
  } else {
    previewDivforThirdAmount.style.display = 'none'
  }
}

function changeThirdAmount() {
  //console.log("called changeThirdAmount()");
  var thirdAmount = document.getElementById('select-third-amount')
  var previewTextThirdAmount = document.getElementById(
    'amount-boundary-box-3-p',
  )
  previewTextThirdAmount.innerHTML = '€' + thirdAmount.value
}

function showAmountForth(obj) {
  //console.log("called showAmountForth()");
  var previewDivforForthAmount = document.getElementById(
    'amount-boundary-box-4',
  )
  if (obj) {
    previewDivforForthAmount.style.display = 'flex'
  } else {
    previewDivforForthAmount.style.display = 'none'
  }
}

function changeForthAmount() {
  //console.log("called changeForthAmount()");
  var forthAmount = document.getElementById('select-forth-amount')
  var previewTextForthAmount = document.getElementById(
    'amount-boundary-box-4-p',
  )
  previewTextForthAmount.innerHTML = '€' + forthAmount.value
}

function showOtherAmount(obj) {
  //console.log("called showOtherAmount()");
  var previewDivforOtherAmount = document.getElementById(
    'amount-boundary-box-other',
  )
  if (obj) {
    //console.log("In here");
    previewDivforOtherAmount.style.display = 'flex'
    document.getElementById('select-other').value = 1
  } else {
    previewDivforOtherAmount.style.display = 'none'
    document.getElementById('select-other').value = 0
  }
}

function changeColor(color) {
  //console.log("called changeColor()", color);
  var colorInputField = document.getElementById('set-color-input-field')
  var colorInput = document.getElementById('set-color-input')
  colorInputField.value = color
  colorInput.value = color

  var appearanceDonateBtn = document.getElementById('apreview-donate-btn')
  appearanceDonateBtn.style.backgroundColor = color

  var previewHeader = document.getElementById('preview-header')
  previewHeader.style.backgroundColor = color

  var previewDonateBtn = document.getElementById('preview-donate-btn')
  previewDonateBtn.style.backgroundColor = color

  var previewDivider = document.getElementById('preview-intervals-onetime-bar')
  previewDivider.style.backgroundColor = color
}

function changeButtonRadius(obj) {
  //console.log("called changeButtonRadius()", obj);

  var appearanceDonateBtn = document.getElementById('apreview-donate-btn')
  var previewDonateBtn = document.getElementById('preview-donate-btn')
  appearanceDonateBtn.style.borderRadius = obj + 'px'
  previewDonateBtn.style.borderRadius = obj + 'px'
}

function editStyle(row) {
  // var rowArr = rowInfo.split(', ');
  // console.log(row);

  var addNewStyleFormDiv = document.getElementById('add-new-style')
  var pluginStyleList = document.getElementById('plugin-style-list')
  pluginStyleList.style.display = 'none'
  addNewStyleFormDiv.style.display = 'flex'

  var nameField = document.getElementById('set-style-name-input')
  //nameField.value = rowArr[0];
  nameField.value = row.styleName
  //console.log(parseInt(rowArr[2]));

  if (parseInt(row.oneTimeCheck) === 0) {
    document.getElementById('select-interval-onetime').checked = false
    showHideOneTime(false)
  }

  if (parseInt(row.monthlyCheck) === 0) {
    //console.log("monthly check ", row.monthlyCheck);
    document.getElementById('select-interval-monthly').checked = false
    showHideMonthly(false)
  }

  if (parseInt(row.yearlyCheck) === 0) {
    //console.log("yearly check ", row.yearlyCheck);
    document.getElementById('select-interval-yearly').checked = false
    showHideYearly(false)
  }

  if (parseInt(row.firstAmountCheck) === 0) {
    document.getElementById('select-first').checked = false
    showAmountFirst(false)
  }

  if (parseInt(row.secondAmountCheck) === 0) {
    // console.log("second amount check ", parseInt(row.secondAmountCheck));
    document.getElementById('select-second').checked = false
    showAmountSecond(false)
  }

  if (parseInt(row.thirdAmountCheck) === 0) {
    //console.log("second amount check ", parseInt(row.secondAmountCheck));
    document.getElementById('select-third').checked = false
    showAmountThird(false)
  }

  if (parseInt(row.forthAmountCheck) === 0) {
    document.getElementById('select-forth').checked = false
    showAmountForth(false)
  }

  document.getElementById('select-first-amount').value = parseInt(
    row.firstAmount,
  )
  document.getElementById('select-second-amount').value = parseInt(
    row.secondAmount,
  )
  document.getElementById('select-third-amount').value = parseInt(
    row.thirdAmount,
  )
  document.getElementById('select-forth-amount').value = parseInt(
    row.forthAmount,
  )

  if (parseInt(row.otherChecked) === 0) {
    document.getElementById('select-other').checked = false
    showOtherAmount(false)
  }

  if (parseInt(row.showDonateButton) === 0) {
    document.getElementById('select-donate-button').checked = false
    showHideDonateButton(false)
  }

  if (parseInt(row.showProgressBar) === 0) {
    document.getElementById('select-progress-bar').checked = false
    showHideProgressBar(false)
  }

  if (parseInt(row.showRaisedAmount) === 0) {
    document.getElementById('select-raised-amount').checked = false
    showHideRaisedAmount(false)
  }

  if (parseInt(row.showEndDate) === 0) {
    document.getElementById('select-show-days-left').checked = false
    showHideDaysLeft(false)
  }

  if (parseInt(row.showDonationFormOnly) === 1) {
    document.getElementById('select-show-donate-form-only').checked = true
    showDonationFormOnly(true)
  }

  if (parseInt(row.doNotShowBox) === 1) {
    document.getElementById('select-donot-show-box').checked = true
    doNotShowBox(true)
  }

  if (
    parseInt(row.showProgressBar) !== 0 &&
    parseInt(row.showRaisedAmount) !== 0
  ) {
    showHideOfDiv(true)
  }

  if (
    parseInt(row.showProgressBar) === 0 ||
    parseInt(row.showRaisedAmount) === 0
  ) {
    showHideOfDiv(false)
  }

  document.getElementById('set-color-input-field').value = row.colorCode
  document.getElementById('set-color-input').value = row.colorCode
  changeColor(row.colorCode)

  // document.getElementById("set-font-input").value = row.font;
  document.getElementById('set-font-input-list').value = row.font
  //console.log("font is ", row.font);
  setFont(row.font)
  document.getElementById('set-button-radius-input').value = row.buttonRadius
  changeButtonRadius(parseInt(row.buttonRadius))

  document.getElementById('set-id').value = row.id

  document.getElementById('set-donation-form').value = row.donationTitle
  document.getElementById('preview-header-label').textContent =
    row.donationTitle
  document.getElementById('amount-boundary-box-1-p').innerHTML = row.firstAmount
  document.getElementById('amount-boundary-box-2-p').innerHTML =
    row.secondAmount
  document.getElementById('amount-boundary-box-3-p').innerHTML = row.thirdAmount
  document.getElementById('amount-boundary-box-4-p').innerHTML = row.forthAmount
}

jQuery('.plugin-style-list-item-remove-btn').click(function() {
  // console.log("remove row clicked");

  var button = jQuery(this),
    tr = button.closest('tr')
  var idNum = tr.find('td.plugin-style-list-id').text()
  //console.log("clicked button with id", idNum);

  /// ****** More standard way of doing AJAX Call /////////

  if (confirm('Are you sure you want to delete this entry?')) {
    // jQuery("#modal").css("display", "block");

    jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
        action: 'my_action',
        id: idNum,
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function(response) {
        tr.remove()
        //jQuery("#modal").css("display", "none");
      },
      error: function(xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }

  ///// ****** This is also working. Ajax Call in a different way //////

  // if (confirm('Are you sure you want to delete this entry?')) {
  //     var data = {
  //         'action': 'my_action',
  //         'id': idNum
  //     };

  //     // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
  //     jQuery.post(ajaxurl, data, function (response) {
  //         alert('Got this from the server: ' + response);
  //         console.log(response);
  //         //if (response.status) {
  //         tr.remove();
  //         // } else {
  //         //     console.log('Error occured while deleting');
  //         //  }
  //     });
  // }
})

jQuery('.widgets-list-item-remove-btn').click(function() {
  // console.log("remove widget clicked");

  var button = jQuery(this),
    tr = button.closest('tr')
  var idNum = tr.find('td.widgets-list-style-id').text()
  // console.log("clicked button with id", idNum);

  /// ****** More standard way of doing AJAX Call /////////

  if (confirm('Are you sure you want to delete this entry?')) {
    // jQuery("#modal").css("display", "block");

    jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
        action: 'remove_widget_action',
        id: idNum,
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function(response) {
        tr.remove()
        //jQuery("#modal").css("display", "none");
      },
      error: function(xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }
})

// function submitApiKey() {
//     console.log('Api Key Submitted');
//     var apiKey = document.getElementById('fundraiser-id-input').value;
//     console.log(apiKey);
// }

jQuery('#submit-api-key-btn').click(function(e) {
  e.preventDefault()
  var apiKey = document.getElementById('fundraiser-id-input').value
  //console.log("api key btn clicked ", apiKey);

  jQuery('#blank-apikey-msg').hide()
  // jQuery("#apikey-error-msg").text("");
  jQuery('#apikey-error-msg').hide()
  // jQuery("#apikey-error-msg").css("display","none");
  // jQuery("#updating-apikey-msg").text("");
  jQuery('#updating-apikey-msg').hide()
  // jQuery("#updating-apikey-msg").css("display","none");
  // jQuery("#update-apikey-success-msg").text("");
  jQuery('#update-apikey-success-msg').hide()
  // jQuery("#update-apikey-success-msg").css("display","none");
  // jQuery('#apikey-account').text("");
  jQuery('#apikey-account').hide()
  // jQuery("#apikey-account").css("display","none");

  if (apiKey === '') {
    //console.log("api key blank");
    jQuery('#blank-apikey-msg').show()
  } else {
    payload = {
      api_key: apiKey,
    }
    jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
        action: 'check_api_key',
        payload: payload,
        api_key: apiKey,
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function (response) {
        // console.log(response)
        response =
          response.substr(response.length - 1, 1) === '0'
            ? response.substr(0, response.length - 1)
            : response
        jsonArray = JSON.parse(response)
        if (jsonArray['status'] === 200) {
          username = jsonArray['data']['username']
          email = jsonArray['data']['email']
          //console.log('email ', email);
          jQuery('#apikey-owner-email-label').text(email)
          jQuery('#connect-account-msg').css('display', 'flex')
          jQuery('#connect-account-msg')
            .delay(4000)
            .fadeOut(3000)

          jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
              action: 'api_key',
              api_key: apiKey,
              username: username,
              email: email,
            },
            beforeSend: function() {
              jQuery('#updating-apikey-msg').show()
              jQuery('#modal').css('display', 'block')
            },
            success: function(response) {
              console.log('code changed')
              console.log('response ', response)
              if (
                response == 'Insert success' ||
                response == 'Update success'
              ) {
                jQuery('#updating-apikey-msg').hide()
                jQuery('#update-apikey-success-msg').show()
                jQuery('apikey-account').show()
                jQuery('#apikey-account').css('display', 'flex')
                jQuery('#apikey-account-owner-email').text(email)
                localStorage.setItem('shouldRedirect', 'true')
                window.location.reload()
              } else if (
                response == 'Insert not success' ||
                response == 'Update not success'
              ) {
                jQuery('apikey-error-msg').show()
              } else {
                jQuery('blank-apikey-msg').show()
              }
            },
            error: function(xhr) {
            },
            complete: function() {
              // hide loader here
              jQuery('#modal').css('display', 'none')
            },
          })
        } else {
          // var text = jQuery("#apikey-account").text()
          // console.log('api key text content ', document.getElementById('apikey-account').textContent);
          jQuery('#apikey-error-msg').show()
          // jQuery("#resTest").css("color", "red");

          // jQuery("#apikey-account").css("font-weight", "Bold");
          // jQuery("#apikey-account").css("color", "red");
          // jQuery("#apikey-account").text('Failed to get information of the api key owner.')

          jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
              action: 'api_key',
              api_key: apiKey,
              usename: '',
              email: '',
            },
            beforeSend: function() {
              // show loader here
              // jQuery("#updating-apikey-msg").css("display", "block");
              jQuery('#modal').css('display', 'block')
            },
            success: function(response) {
              //jQuery("#modal").css("display", "none");
              console.log('code changed')
              console.log('success', response)

              // jQuery("#resTest").css("font-weight", "Bold");
              if (
                response == 'Insert success' ||
                response == 'Update success'
              ) {
                // jQuery("#updating-apikey-msg").hide();
                // jQuery("#update-apikey-success-msg").show();
                // jQuery("#apikey-account").hide();
                // jQuery("#resTest").text(response);
                // jQuery("#resTest").css("color", "green");
                // jQuery("#apikey-account").css("font-weight", "Bold");
                // jQuery("#apikey-account").css("color", "red");
                // jQuery("#apikey-account").text('Failed to retrive information of the api key owner.')
                // var location = window.location.href;
                // var locationArr = location.split("?");
                // location = locationArr[0] + "?page=whydonate-widget-list";
                // window.location.replace(location);
              } else if (
                response == 'Insert not success' ||
                response == 'Update not success'
              ) {
                jQuery('#apikey-error-msg').show()
                // jQuery("#apikey-error-msg").text(
                //     "It seems you have the same apikey stored already. Otherwise you can submit again."
                // );
                // jQuery("#resTest").css("color", "red");

                // } else if (response == 'The key is invalid, please check') {
                //     jQuery('#resTest').text(response);
                //     jQuery('#resTest').css("color", "red");
              } else {
                jQuery('#blank-apikey-msg').show()
                // jQuery("#resTest").text(
                //     "Please fill the form before submitting"
                // );
                // jQuery("#resTest").css("color", "blue");
              }
            },
            error: function(xhr) {
              //error handling
              //console.log("suppression echoué");
            },
            complete: function() {
              // hide loader here
              // jQuery("#update-apikey-success-msg").hide();
              jQuery('#modal').css('display', 'none')
            },
          })
        }
      },
      error: function(xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }
})

//function generateFundraiserList() {

//var apiKey = ''

jQuery('#generate-fundraiser-list-btn').click(function(e) {
  //console.log("Generate List");
  document.getElementById('error-msg-p').innerHTML = ''
  jQuery('#error-msg-p').css('color', 'red')
  jQuery.ajax({
    url: ajaxurl,
    data: {
      action: 'fundraiser_list',
    },
    beforeSend: function() {
      // show loader here
      jQuery('#modal').css('display', 'block')
    },
    success: function(response) {
      jQuery('#modal').css('display', 'none')
      //console.log("response ", response);

      if (response != '') {
        var jsonArray = JSON.parse(response)

        if (
          jsonArray['detail'] == 'Authentication credentials were not provided.'
        ) {
          document.getElementById('error-msg-p').innerHTML =
            'Check your api key!'
          jQuery('#error-msg-p').css('color', 'red')
        } else {
          var resultArr = jsonArray['results']
          //console.log('success', resultArr);
          //resultArr.map((item, i) =>
          //console.log("Index:", i, "title:", item.title)
          // );
          // console.log("length ", resultArr.length);

          var option = jQuery('<option></option>')
            .attr('value', 'option value')
            .text('Text')
          jQuery('#fundraiser-list-dropdown')
            .empty()
            .append(option)

          var jQueryel = jQuery('#fundraiser-list-dropdown')
          //jQuery('#fundraiser-list-dropdown option:gt(0)').remove();
          jQueryel.empty() // remove old options
          jQuery.each(resultArr, function(key, value) {
            //console.log("title ", value);
            jQueryel.append(
              jQuery('<option></option>')
                .attr('value', value.id + ' ' + value.slug)
                .text(value.title),
            )
          })
        }
      }
    },
    error: function(xhr) {
      //error handling
      //console.log("suppression echoué");
    },
    complete: function() {
      // hide loader here
      jQuery('#modal').css('display', 'none')
    },
  })
})
//}

jQuery('#generate-fundraiser-list-table-btn').click(function(e) {
  //console.log("Generate fundraiser list table");

  jQuery.ajax({
    url: ajaxurl,
    data: {
      action: 'fundraiser_list',
    },
    beforeSend: function() {
      // show loader here
      jQuery('#modal').css('display', 'block')
    },
    success: function(response) {
      jQuery('#modal').css('display', 'none')
      //console.log("response ", response);

      if (response != '') {
        var jsonArray = JSON.parse(response)
        //console.log("list array ", jsonArray);

        if (
          jsonArray['detail'] == 'Authentication credentials were not provided.'
        ) {
          document.getElementById('error-msg-p').innerHTML =
            'Check your api key!'
          jQuery('#error-msg-p').css('color', 'red')
        } else {
          var resultArr = jsonArray['results']
          // console.log("success", resultArr);

          jQuery(resultArr).each(function(index, item) {
            // console.log(item);
            //console.log(receipts[index]);
            var time = item.created_at.split('T')

            jQuery('#fundraiser-list-table tbody').append(
              "<tr><td style='text-align: center'>" +
                item.id +
                "</td><td style='text-align: center'>" +
                item.title +
                "</td><td style='text-align: center'>" +
                time[0] +
                // "</td><td style='text-align: center'>" +
                // item.slug +
                "</td><td style='text-align: center'>" +
                "<button class='fundraiser-list-item-edit-btn' onclick='editFundraiser(" +
                JSON.stringify(item) +
                ")'>Edit</button>" +
                "<button class='fundraiser-list-item-remove-btn' style='margin: 5px' onclick='removeFundraiser()'>Remove</button>" +
                '</td></tr>',
            )
          })
        }
      }
    },
    error: function(xhr) {
      //error handling
      //console.log("suppression echoué");
    },
    complete: function() {
      // hide loader here
      jQuery('#modal').css('display', 'none')
    },
  })
})

jQuery('#save-new-fundraiser').click(function(e) {
  e.preventDefault()
  //console.log("save new fundraiser");

  let id = document.getElementById('fundraiser-id').textContent
  let title = document.getElementById('add-fundraiser-name-input').value
  let amount = document.getElementById('set-target-amount-input').value
  let endDate = document.getElementById('set-end-date-input').value
  
  let tipEnabled=jQuery('input[name=tipbox]:checked').val()
  // console.log('tip_enabled ', tipEnabled)
  // console.log("end date ", endDate);
  // console.log("id ", id);
  // console.log("title ", title);
  // console.log('amount ', amount);
  // console.log('end date', endDate);
  // console.log("amount ", amount);
  document.getElementById('title-error-message').style.display = 'none'
  document.getElementById('amount-error-message').style.display = 'none'
  document.getElementById('negetive-amount-error-message').style.display =
    'none'
  val = Number(document.getElementById('set-target-amount-input').value)

  if (amount === '') {
    amount = 0
    // console.log('amount value', amount);
  } else {
    amount = parseInt(amount)
    // console.log('amount value', amount);
  }

  if (endDate === '') {
    endDate = '2099-01-01'
    // console.log('end date ', endDate)
  } else {
    // console.log('end date ', endDate)
  }

  if (title === '') {
    document.getElementById('title-error-message').style.display = 'block'

    // if (
    //     Number(document.getElementById("set-target-amount-input").value) %
    //     1 !=
    //     0
    // ) {
    //     document.getElementById("amount-error-message").style.display =
    //         "block";
    // }
  } else if (
    Number(document.getElementById('set-target-amount-input').value) % 1 !=
    0
  ) {
    document.getElementById('amount-error-message').style.display = 'block'
  } else if (amount < 0) {
    document.getElementById('negetive-amount-error-message').style.display =
      'block'
  } else {
    if (id === '') {
      // console.log("when id is null");
      let payload = {
        amount_target: amount,
        // category_id: 5,
        // created_on: "http://localhost:4200",
        // currency_code: "eur",
        // description:
        //     "Test widget fundraiser Test widget fundraiser Test widget fundraiser Test widget fundraiser Test widget fundraiser Test widget fundraiser ",
        end_date: endDate,
        // language_code: "en",
        // location_local: "ChIJLxVHy3GQ_zkRUolxkCIhS_A",

        show_donation_details: true,
        title: title,
        unlimited: false,
        tip_enabled: tipEnabled === "1" ? true : false
      }

      /// ****** More standard way of doing AJAX Call /////////

      jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
          action: 'create_fundraiser',
          payload: payload,
        },
        beforeSend: function() {
          // show loader here
          jQuery('#modal').css('display', 'block')
        },
        success: function(response) {
          //console.log("response ", response);
          // document.getElementById("create-fundraiser-msg").style.display =
          //     "block";
          // window.fundraiserCreatedThroughPlugin = true;
          localStorage.setItem('fundraiserCreated', 'true')
          window.location.reload()
          window.scrollTo(0, 0)

          //jQuery("#modal").css("display", "none");
        },
        error: function(xhr) {
          //error handling
          // console.log("Failed to create fundraiser");
        },
        complete: function() {
          // hide loader here
          jQuery('#modal').css('display', 'none')
        },
      })
    } else {
      let payload = {
        amount_target: amount,
        // category_id: 5,
        // created_on: "http://localhost:4200",
        // currency_code: "eur",
        // description:
        //     "Test widget fundraiser Test widget fundraiser Test widget fundraiser Test widget fundraiser Test widget fundraiser Test widget fundraiser ",
        end_date: endDate,
        id: parseInt(id),
        // language_code: "en",
        // location_local: "ChIJLxVHy3GQ_zkRUolxkCIhS_A",
        show_donation_details: true,
        title: title,
        unlimited: false,
        tip_enabled: tipEnabled === "1" ? true : false
      }

      /// ****** More standard way of doing AJAX Call /////////

      jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
          action: 'edit_fundraiser',
          payload: payload,
        },
        beforeSend: function() {
          // show loader here
          jQuery('#modal').css('display', 'block')
        },
        success: function(response) {
          // console.log("response ", response);
          window.location.reload()
          window.scrollTo(0, 0)
          //jQuery("#modal").css("display", "none");
        },
        error: function(xhr) {
          //error handling
          //console.log("Failed to create fundraiser");
        },
        complete: function() {
          // hide loader here
          jQuery('#modal').css('display', 'none')
        },
      })
    }
  }
})

function editFundraiser(id) {
  // var value = JSON.parse(item);
  // console.log("clicked editFundraiser() ", id, end_date);
  var item = {}
  window.resultArr.forEach(element => {
    if (element.id === id) {
      item = element
    }
  })
  //console.log("item value ", item);
  document.getElementById('fundraiser-id').innerHTML = item.id

  // console.log("text content ", id);
  var slug = document.getElementById('fundraiser-slug')
  var addNewFormDiv = document.getElementById('add-fundraiser')
  var fundraiserList = document.getElementById('fundraiser-list')
  fundraiserList.style.display = 'none'
  addNewFormDiv.style.display = 'block'
  // id.textContent = id;

  // if (title.includes("<>")) {
  //     title = title.replace(/<>/g, "'");
  // }
  // console.log("title in edit fundraiser 1", title);
  // if (item.title.includes("*")) {
  //     titleArr = item.title.split("*");
  //     item.title = "";
  //     title = "";
  //     console.log("length ", titleArr.length);
  //     for (i = 0; i < titleArr.length; i++) {
  //         if (i !== titleArr.length - 1) {
  //             title += titleArr[i] + "'";
  //         } else {
  //             title += titleArr[i];
  //         }
  //         console.log(titleArr[i]);
  //     }
  //     item.title += title;
  // }
  //console.log("title in edit fundraiser2 ", item.title);
  document.getElementById('add-fundraiser-name-input').value = item.title
  document.getElementById('set-target-amount-input').value = item.amount_target

  if (jQuery('#set-end-date-input')[0].type != 'date') {
    jQuery('#set-end-date-input').datepicker({
      dateFormat: 'yy-mm-dd',
    })
    //document.getElementById("set-end-date-input").placeholder = "yyyy-mm-dd";
  }
  document.getElementById('set-end-date-input').value=item.end_date
  
  if (item.tip_enabled) {
    jQuery("input[name=tipbox][value="+"1"+"]").attr('checked', 'checked');
  } else {
    jQuery("input[name=tipbox][value="+"0"+"]").attr('checked', 'checked');
  }
  
}


function removeFundraiser() {
  //console.log("clicked removeFundraiser()");
}

jQuery('#apply-changes-btn').click(function(e) {
  var selectedFundraiser = jQuery(
    '#fundraiser-list-dropdown option:selected',
  ).text()
  var selectedFundraiserVal = undefined
  var selectedFundraiserLocal = undefined
  var showFundraiserErrorLabel = document.getElementById(
    'show-select-fundraiser-error-msg',
  )
  var showStyleErrorLabel = document.getElementById(
    'show-select-style-error-msg',
  )
  var showErrorLabel = document.getElementById('show-redirect-url-error-msg')

  showFundraiserErrorLabel.style.display = 'none'
  showStyleErrorLabel.style.display = 'none'
  showErrorLabel.style.display = 'none'

  //console.log("selected fundraiser : ", selectedFundraiser);
  if (selectedFundraiser !== '') {
    selectedFundraiserVal = jQuery(
      '#fundraiser-list-dropdown option:selected',
    ).val()
    selectedFundraiserLocal = selectedFundraiserVal.split(' ')
  } else {
    showFundraiserErrorLabel.style.display = 'block'
    showFundraiserErrorLabel.textContent = 'Please select your fundraiser!'
  }
  var selectedStyle = jQuery('#style-list-dropdown option:selected').text()
  //console.log("selected style :", selectedStyle.length);

  if (selectedStyle === ' Choose your option ') {
    showStyleErrorLabel.style.display = 'block'
    showStyleErrorLabel.textContent = 'Please select a style!'
  }

  var checkSuccessUrlValidity = false
  var successUrl = document.getElementById('fundraiser-success-url').value
  if (successUrl !== '') {
    successUrl = trimUrl(successUrl)
    // console.log("redirect url : ", redirectUrl);
    checkSuccessUrlValidity = isURL(successUrl)
    // console.log("check valid email : ", checkUrlValidity);
    if (!checkSuccessUrlValidity) {
      showErrorLabel.style.display = 'block'
      showErrorLabel.textContent =
        'Please enter a valid url else you can leave it blank!'
    }
  } else {
    checkSuccessUrlValidity = true
  }

  var checkFailureUrlValidity = false
  var failureUrl = document.getElementById('fundraiser-failure-url').value
  if (failureUrl !== '') {
    failureUrl = trimUrl(failureUrl)
    // console.log("redirect url : ", redirectUrl);
    checkFailureUrlValidity = isURL(failureUrl)
    // console.log("check valid email : ", checkUrlValidity);
    if (!checkFailureUrlValidity) {
      showErrorLabel.style.display = 'block'
      showErrorLabel.textContent =
        'Please enter a valid url else you can leave it blank!'
    }
  } else {
    checkFailureUrlValidity = true
  }

  if (
    selectedFundraiserLocal !== undefined &&
    selectedStyle !== ' Choose your option ' &&
    checkSuccessUrlValidity &&
    checkFailureUrlValidity
  ) {
    jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
        action: 'update_config_widget',
        fundraiser_name: selectedFundraiser,
        slug_id: selectedFundraiserLocal[0],
        slug_name: selectedFundraiserLocal[1],
        selected_style: selectedStyle,
        success_url: successUrl,
        failure_url: failureUrl,
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
        showFundraiserErrorLabel.style.display = 'none'
        showStyleErrorLabel.style.display = 'none'
        showErrorLabel.style.display = 'none'
      },
      success: function(response) {
        jQuery('#modal').css('display', 'none')
        // console.log("response ", response);
        var locationArr = window.location.href.split('?')
        var location = locationArr[0] + '?page=whydonate-widget-list'
        window.location.replace(location)
      },
      error: function(xhr) {
        //error handling
        // console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }
})

function isURL(str) {
  // if (!str.includes("www")) {
  //     if (!str.includes("https://") && !str.includes("http://")) {
  //         str = "https://" + "www." + str;
  //     } else if (str.includes("https://") || str.includes("http://")) {
  //         if (str.includes("https://")) {
  //             strArr = str.split("https://");
  //             str = strArr[0] + "www." + strArr[1];
  //         } else {
  //             strArr = str.split("http://");
  //             str = strArr[0] + "www." + strArr[1];
  //         }
  //     } else {
  //         str = "https://" + "www." + str;
  //     }
  // }

  // if (!str.includes("https") && !str.includes("http")) {
  //     str = "https://" + str;
  // }

  // console.log("url to redirect: ", str);
  var regex = /(https|http):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/
  var pattern = new RegExp(regex)
  return pattern.test(str)
}

function trimUrl(str) {
  if (!str.includes('https') && !str.includes('http')) {
    str = 'https://' + str
  }

  if (!str.includes('www')) {
    if (!str.includes('https://') && !str.includes('http://')) {
      str = 'https://' + 'www.' + str
    } else if (str.includes('https://') || str.includes('http://')) {
      if (str.includes('https://')) {
        strArr = str.split('https://')
        str = 'https://' + 'www.' + strArr[1]
      } else {
        strArr = str.split('http://')
        str = 'http://' + 'www.' + strArr[1]
      }
    } else {
    }
  }

  return str
}

// function validURL(str) {
//     var pattern = new RegExp(
//         "^(https?:\\/\\/)?" + // protocol
//         "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
//         "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
//         "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
//         "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
//             "(\\#[-a-z\\d_]*)?jQuery",
//         "i"
//     ); // fragment locator
//     return !!pattern.test(str);
// }

jQuery('#update-widget-btn').click(function(e) {
  //console.log("clicked update-widget-btn");
  // var selectedFundraiser = jQuery(
  //     "#fundraiser-list-dropdown option:selected"
  // ).text();
  var selectedSlug = jQuery('#fundraiser-list-dropdown option:selected').val()
  var selectedStyle = jQuery('#style-list-dropdown option:selected').text()
  var id = parseInt(document.getElementById('edit-widget-id').textContent)
  var editWidgetId = document.getElementById('edit-widget-id')
  // console.log("F -> ", selectedFundraiser);
  // console.log("Slug -> ", selectedSlug);
  // console.log("S ->", selectedStyle);
  // console.log("id ->", id);

  // if (selectedSlug != undefined && selectedStyle != "Choose your option") {
  //     jQuery.ajax({
  //         url: ajaxurl,
  //         type: "post",
  //         data: {
  //             action: "edit_config_widget",
  //             fundraiser_name: selectedFundraiser,
  //             slug_name: selectedSlug,
  //             selected_style: selectedStyle,
  //             id: id
  //         },
  //         beforeSend: function() {
  //             // show loader here
  //             jQuery("#modal").css("display", "block");
  //         },
  //         success: function(response) {
  //             jQuery("#modal").css("display", "none");
  //             console.log("response ", response);
  //         },
  //         error: function(xhr) {
  //             //error handling
  //             console.log("suppression echoué");
  //         },
  //         complete: function() {
  //             // hide loader here
  //             jQuery("#modal").css("display", "none");
  //         }
  //     });
  // }

  var selectedFundraiser = jQuery(
    '#fundraiser-list-dropdown option:selected',
  ).text()
  var selectedFundraiserVal = undefined
  var selectedFundraiserLocal = undefined
  var showFundraiserErrorLabel = document.getElementById(
    'show-select-fundraiser-error-msg',
  )
  var showStyleErrorLabel = document.getElementById(
    'show-select-style-error-msg',
  )
  var showErrorLabel = document.getElementById('show-redirect-url-error-msg')

  showFundraiserErrorLabel.style.display = 'none'
  showStyleErrorLabel.style.display = 'none'
  showErrorLabel.style.display = 'none'

  // console.log("selected fundraiser : ", selectedFundraiser);
  if (selectedFundraiser !== '') {
    selectedFundraiserVal = jQuery(
      '#fundraiser-list-dropdown option:selected',
    ).val()
    selectedFundraiserLocal = selectedFundraiserVal.split(' ')
  } else {
    showFundraiserErrorLabel.style.display = 'block'
    showFundraiserErrorLabel.textContent = 'Please select your fundraiser!'
  }
  var selectedStyle = jQuery('#style-list-dropdown option:selected').text()
  // console.log("selected style :", selectedStyle.length);

  if (
    selectedStyle === ' Choose your option ' ||
    selectedStyle === ' Selecteer een stijl '
  ) {
    e.preventDefault()
    showStyleErrorLabel.style.display = 'block'
    showStyleErrorLabel.textContent = 'Select a style'
  }

  if (selectedStyle === ' Selecteer een stijl ') {
    e.preventDefault()
    showStyleErrorLabel.style.display = 'block'
    showStyleErrorLabel.textContent = 'Selecteer een stijl'
  }

  // var checkUrlValidity = false;
  // var redirectUrl = document.getElementById("fundraiser-redirect-url").value;
  // if (redirectUrl !== "") {
  //     redirectUrl = trimUrl(redirectUrl);
  //     checkUrlValidity = isURL(redirectUrl);
  //    // console.log("check valid url : ", checkUrlValidity);
  //     if (!checkUrlValidity) {
  //         showErrorLabel.style.display = "block";
  //         showErrorLabel.textContent =
  //             "Please enter a valid url else you can leave it blank!";
  //     }
  // } else {
  //     checkUrlValidity = true;
  // }

  var checkSuccessUrlValidity = false
  var successUrl = document.getElementById('fundraiser-success-url').value
  if (successUrl !== '') {
    successUrl = trimUrl(successUrl)
    // console.log("redirect url : ", redirectUrl);
    checkSuccessUrlValidity = isURL(successUrl)
    // console.log("check valid email : ", checkUrlValidity);
    if (!checkSuccessUrlValidity) {
      showErrorLabel.style.display = 'block'
      showErrorLabel.textContent =
        'Please enter a valid url else you can leave it blank!'
    }
  } else {
    checkSuccessUrlValidity = true
  }

  var checkFailureUrlValidity = false
  var failureUrl = document.getElementById('fundraiser-failure-url').value
  if (failureUrl !== '') {
    failureUrl = trimUrl(failureUrl)
    // console.log("redirect url : ", redirectUrl);
    checkFailureUrlValidity = isURL(failureUrl)
    // console.log("check valid email : ", checkUrlValidity);
    if (!checkFailureUrlValidity) {
      showErrorLabel.style.display = 'block'
      showErrorLabel.textContent =
        'Please enter a valid url else you can leave it blank!'
    }
  } else {
    checkFailureUrlValidity = true
  }

  // console.log(
  //     "id:",
  //     id,
  //     "fundraiser_name:",
  //     selectedFundraiser,
  //     "slug_id: ",
  //     selectedFundraiserLocal[0],
  //     "slug_name: ",
  //     selectedFundraiserLocal[1],
  //     "selected_style: ",
  //     selectedStyle,
  //     "redirect_url: ",
  //     redirectUrl
  // );

  if (isNaN(id)) {
    if (
      selectedFundraiserLocal !== undefined &&
      selectedStyle !== ' Choose your option ' &&
      selectedStyle !== ' Selecteer een stijl ' &&
      checkSuccessUrlValidity &&
      checkFailureUrlValidity
    ) {
      //console.log("In update config widget");
      editWidgetId.textContent = ''
      editWidgetId.style.display = 'none'
      jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
          action: 'update_config_widget',
          fundraiser_name: selectedFundraiser,
          slug_id: selectedFundraiserLocal[0],
          slug_name: selectedFundraiserLocal[1],
          selected_style: selectedStyle,
          success_url: successUrl,
          failure_url: failureUrl,
        },
        beforeSend: function() {
          // show loader here
          jQuery('#modal').css('display', 'block')
          showFundraiserErrorLabel.style.display = 'none'
          showStyleErrorLabel.style.display = 'none'
          showErrorLabel.style.display = 'none'
        },
        success: function(response) {
          jQuery('#modal').css('display', 'none')
          //console.log("response ", response);
          window.location.reload()
          window.scrollTo(0, 0)
        },
        error: function(xhr) {
          //error handling
          //console.log("suppression echoué");
        },
        complete: function() {
          // hide loader here
          jQuery('#modal').css('display', 'none')
        },
      })
    }
  } else {
    if (
      selectedFundraiserLocal !== undefined &&
      selectedStyle !== ' Choose your option ' &&
      selectedStyle !== ' Selecteer een stijl ' &&
      checkSuccessUrlValidity &&
      checkFailureUrlValidity
    ) {
      // console.log("In edit config widget");
      editWidgetId.textContent = ''
      editWidgetId.style.display = 'none'
      jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
          action: 'edit_config_widget',
          fundraiser_name: selectedFundraiser,
          selected_style: selectedStyle,
          slug_id: selectedFundraiserLocal[0],
          slug_name: selectedFundraiserLocal[1],
          success_url: successUrl,
          failure_url: failureUrl,
          id: id,
        },
        beforeSend: function() {
          // show loader here
          jQuery('#modal').css('display', 'block')
        },
        success: function(response) {
          jQuery('#modal').css('display', 'none')
          //console.log("response ", response);
          window.location.reload()
          window.scrollTo(0, 0)
        },
        error: function(xhr) {
          //error handling
          // console.log("suppression echoué");
        },
        complete: function() {
          // hide loader here
          jQuery('#modal').css('display', 'none')
        },
      })
    }
  }
})

jQuery('#set-plugin-style').click(function(event) {
  //console.log("set-plugin-style");

  var showtitleError = document.getElementById('show-title-error')
  var showIntervalError = document.getElementById('show-interval-error')
  var showAmountError = document.getElementById('show-amount-error')
  var showAppearanceError = document.getElementById('show-appearance-error')

  showtitleError.style.display = 'none'
  showIntervalError.style.display = 'none'
  showAmountError.style.display = 'none'
  showAppearanceError.style.display = 'none'

  var styleName = document.getElementById('set-style-name-input').value
  var styleId = document.getElementById('set-id').value
  var oneCheck = document.getElementById('select-interval-onetime').checked
  var monthCheck = document.getElementById('select-interval-monthly').checked
  var yearCheck = document.getElementById('select-interval-yearly').checked
  var oneTimeCheck = oneCheck ? 1 : 0
  var monthlyCheck = monthCheck ? 2 : 0
  var yearlyCheck = yearCheck ? 3 : 0

  var selectFirst = document.getElementById('select-first').checked
  var selectSecond = document.getElementById('select-second').checked
  var selectThird = document.getElementById('select-third').checked
  var selectForth = document.getElementById('select-forth').checked
  var selectOther = document.getElementById('select-other').checked
  var firstCheck = selectFirst ? 1 : 0
  var secondCheck = selectSecond ? 2 : 0
  var thirdCheck = selectThird ? 3 : 0
  var forthCheck = selectForth ? 4 : 0
  var otherCheck = selectOther ? 1 : 0

  var firstAmount = document.getElementById('select-first-amount').value
  var secondAmount = document.getElementById('select-second-amount').value
  var thirdAmount = document.getElementById('select-third-amount').value
  var forthAmount = document.getElementById('select-forth-amount').value

  var showDonate = document.getElementById('select-donate-button').checked
  var showProgress = document.getElementById('select-progress-bar').checked
  var showRaised = document.getElementById('select-raised-amount').checked
  var showDate = document.getElementById('select-show-days-left').checked
  var showDonateFormOnly = document.getElementById(
    'select-show-donate-form-only',
  ).checked
  var doNotShowBox = document.getElementById('select-donot-show-box').checked

  var showDonateButton = showDonate ? 1 : 0
  var showProgressBar = showProgress ? 2 : 0
  var showRaisedAmount = showRaised ? 3 : 0
  var showEndDate = showDate ? 4 : 0
  var showDonateForm = showDonateFormOnly ? 1 : 0
  var boxNotShow = doNotShowBox ? 1 : 0

  var colorCode = document.getElementById('set-color-input-field').value
  var e = document.getElementById('set-font-input-list')
  var fontName = e.options[e.selectedIndex].value
  var buttonRadius = document.getElementById('set-button-radius-input').value
  var label = document.getElementById('preview-header-label')

  var flag = true
  var donationTitle = ''

  if (label.textContent === '') {
    donationTitle = 'My Safe Donation'
  } else {
    donationTitle = label.textContent
  }

  if (styleName === '') {
    event.preventDefault()
    showtitleError.style.display = 'block'
    showtitleError.style.color = 'red'
    showtitleError.textContent = 'Please enter a name'
    flag = false
  }
  if (oneTimeCheck === 0 && monthlyCheck === 0 && yearlyCheck === 0) {
    event.preventDefault()
    showIntervalError.style.display = 'block'
    showIntervalError.style.color = 'red'
    showIntervalError.textContent = 'Please select an interval'
    flag = false
  }
  if (
    firstCheck === 0 &&
    secondCheck === 0 &&
    thirdCheck === 0 &&
    forthCheck === 0 &&
    otherCheck === 0
  ) {
    event.preventDefault()
    showAmountError.style.display = 'block'
    showAmountError.style.color = 'red'
    showAmountError.textContent = 'Please select an amount'
    flag = false
  }

  if (parseInt(firstAmount)<5||
    parseInt(secondAmount)<5||
    parseInt(thirdAmount)<5||
    parseInt(forthAmount)<5) {
    event.preventDefault()
    showAmountError.style.display='block'
    showAmountError.style.color='red'
    showAmountError.textContent='Minimaal €5'
    flag=false
  }

  if (showDonateForm !== 1) {
    if (
      showDonateButton === 0 &&
      showProgressBar === 0 &&
      showRaisedAmount === 0 &&
      showEndDate === 0
    ) {
      event.preventDefault()
      showAppearanceError.style.display = 'block'
      showAppearanceError.style.color = 'red'
      showAppearanceError.textContent = 'Please select an option'
      flag = false
    }
  }

  if (flag) {
    jQuery.ajax({
      url: ajaxurl,
      type: 'post',
      data: {
        action: 'set_new_style',
        styleId: styleId,
        styleName: styleName,
        oneTimeCheck: oneTimeCheck,
        monthlyCheck: monthlyCheck,
        yearlyCheck: yearlyCheck,
        firstCheck: firstCheck,
        secondCheck: secondCheck,
        thirdCheck: thirdCheck,
        forthCheck: forthCheck,
        otherCheck: otherCheck,
        firstAmount: firstAmount,
        secondAmount: secondAmount,
        thirdAmount: thirdAmount,
        forthAmount: forthAmount,
        showDonateButton: showDonateButton,
        showProgressBar: showProgressBar,
        showRaisedAmount: showRaisedAmount,
        showEndDate: showEndDate,
        showDonationFormOnly: showDonateForm,
        doNotShowBox: boxNotShow,
        colorCode: colorCode,
        fontName: fontName,
        buttonRadius: buttonRadius,
        donationTitle: donationTitle,
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function(response) {
        //console.log("response ", response);
        window.location.reload()
        window.scrollTo(0, 0)
      },
      error: function(xhr) {
        //error handling
        //console.log("suppression echoué");
        window.location.reload()
        window.scrollTo(0, 0)
      },
      complete: function() {
        // hide loader here
        // console.log("On complete");
        window.location.reload()
        window.scrollTo(0, 0)
        jQuery('#modal').css('display', 'none')
      },
    })
  }

  // console.log("style id: ", styleId);
  // console.log("style name: ", styleName);
  // console.log("one time check: ", oneTimeCheck);
  // console.log("monthly check: ", monthlyCheck);
  // console.log("yearly check: ", yearlyCheck);
  // console.log("first check: ", firstCheck);
  // console.log("second check: ", secondCheck);
  // console.log("third check: ", thirdCheck);
  // console.log("forth check: ", forthCheck);
  // console.log("other check: ", otherCheck);
  // console.log("first amount: ", firstAmount);
  // console.log("second amount: ", secondAmount);
  // console.log("third amount: ", thirdAmount);
  // console.log("forth amount: ", forthAmount);
  // console.log("show donate: ", showDonateButton);
  // console.log("show progress: ", showProgressBar);
  // console.log("show raised: ", showRaisedAmount);
  // console.log("show end: ", showEndDate);
  // console.log("color code: ", colorCode);
  // console.log("font name: ", fontName);
  // console.log("button radius: ", buttonRadius);
})

function changeStyleName(e) {
  // console.log(e.target.value);
}

function changeDonationLabel(obj) {
  //console.log("called changeDonationLabel() ", obj);
  var label = document.getElementById('preview-header-label')
  label.textContent = obj
}

jQuery(document).ready(function() {
  //console.log("called in here config widget");
  let locationString = window.location.href
  let locationArray = locationString.split('?')
  //console.log('location ', locationString);
  //console.log("location last part ", locationArray[1]);

  if (locationArray[1] === 'page=myplugin') {
    let shouldRedirect = localStorage.getItem('shouldRedirect')
    console.log('should redirect ', shouldRedirect)
    if (shouldRedirect === 'true') {
      localStorage.setItem('shouldRedirect', 'false')
      setTimeout(function() {
        //your code to be executed after 1 second
        var location = window.location.href
        var locationArr = location.split('?')
        location = locationArr[0] + '?page=whydonate-widget-list'
        window.location.replace(location)
        window.scrollTo(0, 0)
      }, 2000)
    }
  }

  if (locationArray[1] === 'page=whydonate-config-widget') {
    // console.log("Generate List");
    document.getElementById('error-msg-p').innerHTML = ''
    const newFundraiserLink =
      locationArray[0] + '?page=whydonate-fundraiser-list'
    const customStylelink =
      locationArray[0] + '?page=whydonate-plugin-style-list'
    document.getElementById(
      'create-new-fundraiser-link',
    ).href = newFundraiserLink

    document.getElementById('custom-plugin-style-link').href = customStylelink
    jQuery('#error-msg-p').css('color', 'red')
    jQuery.ajax({
      url: ajaxurl,
      data: {
        action: 'fundraiser_list',
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function(response) {
        jQuery('#modal').css('display', 'none')
        // console.log("response ", response);

        if (response != '') {
          try {
            var jsonArray = JSON.parse(response)
            if (
              jsonArray['detail'] ==
              'Authentication credentials were not provided.'
            ) {
              document.getElementById('error-msg-p').innerHTML =
                'Check your api key!'
              jQuery('#error-msg-p').css('color', 'red')
            } else {
              var resultArr = jsonArray['results']

              if (resultArr === undefined || resultArr.length == 0) {
                // array empty or does not exist
                document.getElementById('error-msg-p').innerHTML =
                  'No fundraiser created yet!'
                jQuery('#error-msg-p').css('color', 'red')
              } else {
                // resultArr.map((item, i) =>
                //     console.log(
                //         "Index:",
                //         i,
                //         "title:",
                //         item.title
                //     )
                // );
                // console.log("length ", resultArr.length);

                var option = jQuery('<option></option>')
                  .attr('value', 'option value')
                  .text('Text')
                jQuery('#fundraiser-list-dropdown')
                  .empty()
                  .append(option)

                var jQueryel = jQuery('#fundraiser-list-dropdown')
                //jQuery('#fundraiser-list-dropdown option:gt(0)').remove();
                jQueryel.empty() // remove old options
                jQuery.each(resultArr, function(key, value) {
                  // console.log("title ", value);
                  jQueryel.append(
                    jQuery('<option></option>')
                      .attr('value', value.id + ' ' + value.slug)
                      .text(value.title),
                  )
                })
              }
              //console.log('success', resultArr);
            }
          } catch (error) {
            //  console.log("On catch block");
            document.getElementById('error-msg-p').innerHTML =
              'Something went wrong please check your api key!'
            jQuery('#error-msg-p').css('color', 'red')
          }
        }
      },
      error: function(xhr) {
        //error handling
        // console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }

  if (locationArray[1] === 'page=whydonate-fundraiser-list') {
    document.getElementById('api-key-error-view').style.display = 'none'
    document.getElementById('not-have-fundraisers-view').style.display = 'none'
    document.getElementById('add-new-fundraiser-btn').style.display = 'block'

    jQuery.ajax({
      url: ajaxurl,
      data: {
        action: 'fundraiser_list',
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function(response) {
        jQuery('#modal').css('display', 'none')
        //console.log("response ", response);
        if (response.includes('TypeError')) {
          //console.log('Annonymous user');
          document.getElementById('api-key-error-view').style.display = 'block'
          document.getElementById('add-new-fundraiser-btn').style.display =
            'none'
        } else {
          if (response != '') {
            var jsonArray = JSON.parse(response)
            // console.log("list array ", jsonArray);

            if (
              jsonArray['detail'] ==
              'Authentication credentials were not provided.'
            ) {
              document.getElementById('api-key-error-view').style.display =
                'block'
            } else {
              window.resultArr = jsonArray['results']

              if (resultArr.length === 0) {
                document.getElementById(
                  'not-have-fundraisers-view',
                ).style.display = 'block'
              } else {
                jQuery(resultArr).each(function(index, item) {
                  // console.log(item);
                  //console.log(receipts[index]);
                  var time = item.created_at.split('T')
                  var str = item.can_receive_donation
                    ? "<input class='switch-input' type='checkbox' checked onchange='changeOpenClose(" +
                      item.id +
                      ',' +
                      item.is_draft +
                      ',' +
                      item.can_receive_donation +
                      ',' +
                      item.visible +
                      ")'/>"
                    : "<input class='switch-input' type='checkbox' onchange='changeOpenClose(" +
                      item.id +
                      ',' +
                      item.is_draft +
                      ',' +
                      item.can_receive_donation +
                      ',' +
                      item.visible +
                      ")'/>"

                  var title = item.title

                  var test =
                    "<button class='fundraiser-list-item-edit-btn' style='margin-top: 5px' onclick='editFundraiser(" +
                    item.id +
                    ")'>Edit</button>"

                  jQuery('#fundraiser-list-table tbody').append(
                    "<tr><td style='text-align: center'>" +
                      item.id +
                      "</td><td style='text-align: center'>" +
                      item.title +
                      "</td><td style='text-align: center'>" +
                      time[0] +
                      // "</td><td style='text-align: center'>" +
                      // item.slug +
                      "</td><td style='text-align: center; display: flex; justify-content: center'>" +
                      test +
                      "<label class='switch' style='margin: 5px'>" +
                      str +
                      "<span class='switch-label' data-on='Open' data-off='Closed'></span>" +
                      "<span class='switch-handle'></span>" +
                      '</label>' +
                      '</td></tr>',
                  )

                  var isCreated = localStorage.getItem('fundraiserCreated')
                  if (isCreated === 'true') {
                    document.getElementById(
                      'create-fundraiser-msg',
                    ).style.display = 'block'
                    localStorage.setItem('fundraiserCreated', 'false')
                  }
                })
              }
            }
          }
        }
      },
      error: function(xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }

  if (locationArray[1] === 'page=whydonate-widget-list') {
    const customStylelink =
      locationArray[0] + '?page=whydonate-plugin-style-list'

    document.getElementById(
      'custom-widget-plugin-style-link',
    ).href = customStylelink
    // document.getElementById("error-msg-p").innerHTML = "";

    jQuery.ajax({
      url: ajaxurl,
      data: {
        action: 'fundraiser_list',
      },
      beforeSend: function() {
        // show loader here
        jQuery('#modal').css('display', 'block')
      },
      success: function(response) {
        jQuery('#modal').css('display', 'none')
        // console.log("response ", response);

        if (response != '') {
          try {
            var jsonArray = JSON.parse(response)
            if (
              jsonArray['detail'] ==
              'Authentication credentials were not provided.'
            ) {
              // document.getElementById("error-msg-p").innerHTML =
              //     "Something went wrong please check your api key!";
              jQuery('#error-msg-p').css('display', 'block')
              // jQuery("#error-msg-p").css("color", "red");
            } else {
              var resultArr = jsonArray['results']
              // var editWidgetDiv = document.getElementById("edit-widget");
              // var widgetList = document.getElementById("widget-list");
              // widgetList.style.display = "none";
              // editWidgetDiv.style.display = "block";
              if (resultArr === undefined || resultArr.length == 0) {
                // array empty or does not exist

                jQuery('#no-fundraiser-yet').css('display', 'block')
                // document.getElementById(
                //     "error-msg-p"
                // ).innerHTML = "No fundraiser created yet!";
                // jQuery("#error-msg-p").css("color", "red");
              } else {
                // resultArr.map((item, i) =>
                //     console.log(
                //         "Index:",
                //         i,
                //         "title:",
                //         item.title
                //     )
                // );
                // console.log("length ", resultArr.length);

                var option = jQuery('<option></option>')
                  .attr('value', 'option value')
                  .text('Text')
                jQuery('#fundraiser-list-dropdown')
                  .empty()
                  .append(option)

                var jQueryel = jQuery('#fundraiser-list-dropdown')
                //jQuery('#fundraiser-list-dropdown option:gt(0)').remove();
                jQueryel.empty() // remove old options
                jQuery.each(resultArr, function(key, value) {
                  //console.log("title ", value);
                  jQueryel.append(
                    jQuery('<option></option>')
                      .attr('value', value.id + ' ' + value.slug)
                      .text(value.title),
                  )
                })
              }
              //console.log('success', resultArr);
            }
          } catch (error) {
            // console.log("On catch block");
            jQuery('#error-msg-p').css('display', 'block')
            // document.getElementById("error-msg-p").innerHTML =
            //     "Something went wrong please check your api key!";
            // jQuery("#error-msg-p").css("color", "red");
          }
        }
      },
      error: function(xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function() {
        // hide loader here
        jQuery('#modal').css('display', 'none')
      },
    })
  }
})

function changeOpenClose(id, is_draft, can_receive_donation, visible) {
  //console.log("id: ", id);
  //console.log("is_draft: ", is_draft);
  //console.log("can_receive_donation: ", can_receive_donation);
  //console.log("visible: ", visible);
  var obj = {
    fundraising_local_id: id,
    visible: visible,
    is_draft: is_draft,
    can_receive_donation: !can_receive_donation,
  }

  var payload = JSON.stringify(obj)
  jQuery.ajax({
    url: ajaxurl,
    type: 'post',
    data: {
      action: 'open_close_fundraiser',
      payload: obj,
    },
    beforeSend: function() {
      // show loader here
      jQuery('#modal').css('display', 'block')
    },
    success: function(response) {
      jQuery('#modal').css('display', 'none')
      // console.log("response ", response);
      window.location.reload()
    },
    error: function(xhr) {
      //error handling
      //console.log("suppression echoué");
    },
    complete: function() {
      // hide loader here
      jQuery('#modal').css('display', 'none')
    },
  })
}

function copyCode(value) {
  var shortcode = '[whydonate id="' + value + '"]'
  // console.log("shortcode ", shortcode);

  // Create new element
  var el = document.createElement('textarea')
  // Set value (string to be copied)
  el.value = shortcode
  // Set non-editable to avoid focus and move outside of view
  el.setAttribute('readonly', '')
  el.style = {
    position: 'absolute',
    left: '-9999px',
  }
  document.body.appendChild(el)
  // Select text inside element
  el.select()
  // Copy text to clipboard
  document.execCommand('copy')
  // Remove temporary element
  document.body.removeChild(el)
  alert('Copied the shortcode: ' + shortcode)
}

function checkDatabase() {
  console.log('in Chekc database')

  jQuery.ajax({
    url: ajaxurl,
    type: 'post',
    data: {
      action: 'check_database',
    },
    beforeSend: function() {
      // show loader here
      jQuery('#modal').css('display', 'block')
    },
    success: function(response) {
      console.log('check response ', response)
      jQuery('#modal').css('display', 'none')
      // console.log("response ", response);
      //window.location.reload();
    },
    error: function(xhr) {
      //error handling
      //console.log("suppression echoué");
    },
    complete: function() {
      // hide loader here
      jQuery('#modal').css('display', 'none')
    },
  })
}
