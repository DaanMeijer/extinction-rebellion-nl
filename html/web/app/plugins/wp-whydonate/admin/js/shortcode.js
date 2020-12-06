var whydonateSlugs={}
var lang=''
function showDonateWindow(id, tip_enabled, colorCode, languageCode) {
  // console.log("clicked");

  // console.log('tip enabled ', tip_enabled)
  lang=languageCode.split('_')[0]
  // console.log('language ', lang)
  // Get the modal
  var modal=document.getElementById('donate-window-modal-'+id)

  // Get the button that opens the modal
  var btn=document.getElementById('apreview-donate-btn-'+id)

  // Get the <span> element that closes the modal
  var span=document.getElementById(''+id)

  // When the user clicks the button, open the modal
  // btn.onclick = function() {
  //     console.log("Im here");
  //     modal.style.display = "flex";
  // };

  if (modal.style.display=='flex') {
    modal.style.display='none'
  } else {
    modal.style.display='flex'
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick=function () {
    // console.log("on click span modal id: " + "donate-window-modal-" + id);
    modal.style.display='none'
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick=function (event) {
    // console.log("on click window modal id: " + "donate-window-modal-" + id);
    if (event.target==modal) {
      // console.log("modal should be closed");
      modal.style.display='none'
    }
  }

  // if (tip_enabled && document.getElementById('tip-box-'+id).style.display === 'none') {
  //   createTipbox(id, colorCode)
  // }
}

//  *****************  Modal Functionalitites ************************************

function createTipbox(id, color) {
  // if (modalContent) {
  //   modalContent.style.height='900px'
  // }
  var cardDiv=document.getElementById('preview-'+id)
  cardDiv.style.height='910px'
  document.getElementById('preview-card-'+id).style.height='845px'
  var tipBox=document.getElementById('tip-box-'+id)
  tipBox.className='tip-box'
  tipBox.style.display='block'
  tipBox.style.width='100%'
  tipBox.style.height='auto'
  tipBox.style.marginTop='15px'
  tipBox.style.padding='5px'
  tipBox.style.paddingLeft='10px'
  tipBox.style.backgroundColor=color+'10'
  // console.log('lighten color ', lightenColor(color, 40))

  var para1=document.createElement('p')
  para1.style.fontSize='12px'
  para1.style.fontWeight='400'
  para1.style.color='black'
  if (lang==='nl') {
    para1.textContent=
      'Whydonate heeft 0% platformkosten voor organisatoren en we rekenen op de vrijgevigheid van donateurs zoals jij om onze service te garanderen.'
  } else if (lang==='de') {
    para1.textContent=
      'Whydonate erhebt eine Plattformgebühr von 0% für Organisatoren und ist auf die Großzügigkeit von Spendern wie Ihnen angewiesen, um unseren Dienst zu betreiben.'
  } else if (lang==='es') {
    para1.textContent=
      'Whydonate tiene una tarifa de plataforma del 0% para los organizadores y depende de la generosidad de donantes como usted para operar nuestro servicio.'
  } else {
    para1.textContent=
      'Whydonate has a 0% platform fee for organizers and relies on the generosity of donors like you to operate our service.'
  }

  tipBox.appendChild(para1)

  var selectPercentileDiv=document.createElement('div')
  selectPercentileDiv.style.display='flex'
  selectPercentileDiv.style.justifyContent='space-around'

  var para2=document.createElement('p')
  para2.style.fontSize='12px'
  para2.style.fontWeight='400'
  para2.style.color='black'
  para2.style.marginTop='6px'
  if (lang==='nl') {
    para2.textContent='Bedankt voor het toevoegen van een fooi van : '
  } else if (lang==='de') {
    para2.textContent='Vielen Dank, dass Sie einen Tipp mit aufgenommen haben : '
  } else if (lang==='es') {
    para2.textContent='Gracias por incluir un consejo de : '
  } else {
    para2.textContent='Thank you for including a tip of : '
  }

  selectPercentileDiv.appendChild(para2)

  var dropdown=document.createElement('div')
  dropdown.id='custom-select'+id
  dropdown.className='custom-select'
  dropdown.style.width='150px'
  dropdown.onclick=() => this.handleTipDropdown(id)

  selectPercentileDiv.appendChild(dropdown)

  var inputTipDiv=document.createElement('div')
  inputTipDiv.id='input-tip-div'+id
  inputTipDiv.style.display='none'
  inputTipDiv.style.justifyContent='flex-end'
  inputTipDiv.style.marginTop='10px'

  var dollarSignLabel=document.createElement('label')
  dollarSignLabel.id='dollar-sign-'+id
  dollarSignLabel.style.textAlign='right'
  dollarSignLabel.style.marginRight='5px'
  dollarSignLabel.textContent='€ '
  inputTipDiv.appendChild(dollarSignLabel)
  //  <span class="currencyinput">$<input type="text" name="currency"></span>
  var inputTipSpan=document.createElement('span')

  var inputTipTextBox=document.createElement('input')
  inputTipSpan.innerHTML=inputTipTextBox.outerHTML
  inputTipSpan.innerHTML=
    '<input type="text"'+
    'id="input-tip'+
    id+
    '"'+
    `onkeyup = "calculateTotalAmount('${id}')"`+
    // id +
    // ')"' +
    ' name="currency" value="1.00" style="width: 130px; height: 25px; border-radius: 3px !important; border-color: transparent !important; font-family: arial; font-size: 15px; text-align: right; background: white !important; min-height: auto !important"></span>'
  // inputTipSpan.style.width='165px'
  inputTipDiv.appendChild(inputTipSpan)

  tipBox.appendChild(selectPercentileDiv)
  tipBox.appendChild(inputTipDiv)

  var totalChargeDiv=document.createElement('div')
  totalChargeDiv.style.height='20px'
  totalChargeDiv.style.marginTop='10px'
  totalChargeDiv.style.textAlign='right'

  var totalChargeLabel=document.createElement('label')
  totalChargeLabel.id='total-charge-label'+id
  totalChargeLabel.style.fontSize='15px'

  if (lang==='nl') {
    totalChargeLabel.innerHTML='Totaalbedrag: € '
  } else if (lang==='de') {
    totalChargeLabel.innerHTML='Gesamtsumme: € '
  } else if (lang==='es') {
    totalChargeLabel.innerHTML='Cargo total: € '
  } else {
    totalChargeLabel.innerHTML='Total Charge: € '
  }

  totalChargeLabel.style.color='black'
  // totalChargeLabel.style.marginRight='15px'
  totalChargeLabel.style.fontWeight='600'
  totalChargeLabel.style.width='100%'

  totalChargeDiv.appendChild(totalChargeLabel)
  tipBox.appendChild(totalChargeDiv)

  // Reder different options based on selection

  // Check wheather the initial selection is greater than 9
  renderOptionsForPercentile(id)

  setDropdownFunc(tipBox, id)

  calculateTotalAmount(id)
}

function lightenColor(color, percent) {
  var num=parseInt(color.replace('#', ''), 16),
    amt=Math.round(2.55*percent),
    R=(num>>16)+amt,
    B=((num>>8)&0x00ff)+amt,
    G=(num&0x0000ff)+amt
  return (
    '#'+
    (
      0x1000000+
      (R<255? (R<1? 0:R):255)*0x10000+
      (B<255? (B<1? 0:B):255)*0x100+
      (G<255? (G<1? 0:G):255)
    )
      .toString(16)
      .slice(1)
  )
}

function renderOptionsForPercentile(slug) {
  var customSelectDiv=document.getElementById('custom-select'+slug)
  while (customSelectDiv.firstChild) {
    customSelectDiv.removeChild(customSelectDiv.lastChild)
  }

  var selectList=document.createElement('select')
  selectList.id='select-dropdown'+slug

  // check which amount is actually selected
  var selectedValue=getSelectedValue(slug)

  var option0=document.createElement('option')
  option0.text='15%'+' ('+(selectedValue*0.15).toFixed(2)+') '
  option0.value=(selectedValue*0.15).toFixed(2)
  selectList.appendChild(option0)

  var option1=document.createElement('option')
  option1.text='10%'+' ('+(selectedValue*0.1).toFixed(2)+') '
  option1.value=(selectedValue*0.1).toFixed(2)
  selectList.appendChild(option1)

  var option2=document.createElement('option')
  option2.text='15%'+' ('+(selectedValue*0.15).toFixed(2)+') '
  option2.value=(selectedValue*0.15).toFixed(2)
  selectList.appendChild(option2)

  var option3=document.createElement('option')
  option3.text='20%'+' ('+(selectedValue*0.2).toFixed(2)+') '
  option3.value=(selectedValue*0.2).toFixed(2)
  selectList.appendChild(option3)

  var option4=document.createElement('option')
  if (lang==='nl') {
    option4.text='Anders'
  } else if (lang==='de') {
    option4.text='Sonstiges'
  } else if (lang==='es') {
    option4.text='Otra'
  } else {
    option4.text='Other'
  }
  option4.value='Other'
  selectList.appendChild(option4)

  customSelectDiv.appendChild(selectList)
}

function renderOptionsForAmount(slug) {
  var customSelectDiv=document.getElementById('custom-select'+slug)
  while (customSelectDiv.firstChild) {
    customSelectDiv.removeChild(customSelectDiv.lastChild)
  }

  var selectList=document.createElement('select')
  selectList.id='select-dropdown'+slug

  var option0=document.createElement('option')
  option0.text='€1'
  option0.value='1'
  selectList.appendChild(option0)

  var option1=document.createElement('option')
  option1.text='€1'
  option1.value='1'
  selectList.appendChild(option1)

  var option2=document.createElement('option')
  option2.text='€2'
  option2.value='2'
  selectList.appendChild(option2)

  var option3=document.createElement('option')
  if (lang==='nl') {
    option3.text='Bedrag'
  } else if (lang==='de') {
    option3.text='Menge'
  } else if (lang==='es') {
    option3.text='Cantidad'
  } else {
    option3.text='Amount'
  }

  option3.value='Amount'
  selectList.appendChild(option3)

  customSelectDiv.appendChild(selectList)
}

function setDropdownFunc(tipBox, slug) {
  var x, i, j, selElmnt, a, b, c
  /* Look for any elements with the class "custom-select": */
  x=tipBox.getElementsByClassName('custom-select')
  for (i=0; i<x.length; i++) {
    selElmnt=x[i].getElementsByTagName('select')[0]
    /* For each element, create a new DIV that will act as the selected item: */
    a=document.createElement('DIV')
    a.setAttribute('class', 'select-selected')
    a.innerHTML=selElmnt.options[selElmnt.selectedIndex].innerHTML
    x[i].appendChild(a)
    // this.handleSelect(tipBox, slug, a)
    /* For each element, create a new DIV that will contain the option list: */
    b=document.createElement('DIV')
    b.setAttribute('class', 'select-items select-hide')
    for (j=1; j<selElmnt.length; j++) {
      /* For each option in the original select element,
          create a new DIV that will act as an option item: */
      c=document.createElement('DIV')
      c.innerHTML=selElmnt.options[j].innerHTML
      c.addEventListener('click', function (e) {
        /* When an item is clicked, update the original select box,
            and the selected item: */
        var y, i, k, s, h
        s=this.parentNode.parentNode.getElementsByTagName('select')[0]
        h=this.parentNode.previousSibling
        for (i=0; i<s.length; i++) {
          if (s.options[i].innerHTML===this.innerHTML) {
            s.selectedIndex=i
            h.innerHTML=this.innerHTML
            y=this.parentNode.getElementsByClassName('same-as-selected')
            for (k=0; k<y.length; k++) {
              y[k].removeAttribute('class')
            }
            this.setAttribute('class', 'same-as-selected')
            break
          }
        }
        h.click()
      })
      b.appendChild(c)
    }
    x[i].appendChild(b)
    a.addEventListener('click', function (e) {
      /* When the select box is clicked, close any other select boxes,
          and open/close the current select box: */
      e.stopPropagation()
      closeAllSelect(this)
      this.nextSibling.classList.toggle('select-hide')
      this.classList.toggle('select-arrow-active')
    })
  }
}

function getSelectedValue(id) {
  // check which amount is actually selected
  var firstRadio=document.getElementById('select-amount-first-'+id)
  var secondRadio=document.getElementById('select-amount-second-'+id)
  var thirdRadio=document.getElementById('select-amount-third-'+id)
  var forthRadio=document.getElementById('select-amount-forth-'+id)
  var otherRadio=document.getElementById('select-amount-other-'+id)

  var selectedValue=0

  if (firstRadio.checked) {
    selectedValue=firstRadio.value
  }
  if (secondRadio.checked) {
    selectedValue=secondRadio.value
  }
  if (thirdRadio.checked) {
    selectedValue=thirdRadio.value
  }
  if (forthRadio.checked) {
    selectedValue=forthRadio.value
  }

  if (forthRadio.checked) {
    selectedValue=forthRadio.value
  }

  if (otherRadio.checked) {
    var otherAmountInputBox=document.getElementById(
      'other-amount-number-'+id
    )
    if (
      otherAmountInputBox.value!==''&&
      typeof parseFloat(otherAmountInputBox.value)==='number'
    ) {
      var amount=otherAmountInputBox.value
      selectedValue=parseFloat(amount.replace(',', '.'))
    } else {
      selectedValue=0.0
    }
  }

  return selectedValue
}

function handleTipDropdown(slug) {
  // var listElmenst=document.getElementById('custom-select'+slug)
  var selectedValue=document.getElementById('custom-select'+slug)
    .children[1].innerHTML
  // console.log('Selected value ', selectedValue)
  var tipBox=document.getElementById('tip-box-'+slug)
  if (selectedValue==='Other'||selectedValue==='Anders'||selectedValue==='Sonstiges'||selectedValue==='Otra') {
    renderOptionsForAmount(slug)
    setDropdownFunc(tipBox, slug)
  }

  if (selectedValue==='Amount'||selectedValue==='Bedrag'||selectedValue==='Menge'||selectedValue==='Cantidat') {
    var inputTipDiv=document.getElementById('input-tip-div'+slug)
    inputTipDiv.style.display='flex'
    var inputTipBox=document.getElementById('input-tip'+slug)

    // check which radio button actually selected
    var firstRadio=document.getElementById('select-amount-first-'+slug)
    var secondRadio=document.getElementById('select-amount-second-'+slug)
    var thirdRadio=document.getElementById('select-amount-third-'+slug)
    var forthRadio=document.getElementById('select-amount-forth-'+slug)
    var otherRadio=document.getElementById('select-amount-other-'+slug)

    if (firstRadio.checked) {
      inputTipBox.value=(firstRadio.value*0.1).toFixed(2)
    }
    if (secondRadio.checked) {
      inputTipBox.value=(secondRadio.value*0.1).toFixed(2)
    }
    if (thirdRadio.checked) {
      inputTipBox.value=(thirdRadio.value*0.1).toFixed(2)
    }
    if (forthRadio.checked) {
      inputTipBox.value=(forthRadio.value*0.1).toFixed(2)
    }
    if (otherRadio.checked) {
      var otherAmountInputBox=document.getElementById(
        'other-amount-number-'+slug
      )
      if (parseInt(otherAmountInputBox.value)>9) {
        inputTipBox.value=(otherAmountInputBox.value*0.1).toFixed(2)
        inputTipBox.value=(Math.round(inputTipBox.value*100)/100).toFixed(
          2
        )
      } else {
        inputTipBox.value=(Math.round(1*100)/100).toFixed(2)
      }
    }
  }

  if (
    (selectedValue!=='Other'&&
      selectedValue!=='Amount'&&
      !selectedValue.includes('€'))&&
    (selectedValue!=='Anders'&&
      selectedValue!=='Bedrag'&&
      !selectedValue.includes('€'))&&
    (selectedValue!=='Sonstiges'&&
      selectedValue!=='Menge'&&
      !selectedValue.includes('€'))&&
    (selectedValue!=='Otra'&&
      selectedValue!=='Cantidat'&&
      !selectedValue.includes('€'))
  ) {
    var selectedAmount=getSelectedValue(slug)

    if (!whydonateSlugs.hasOwnProperty(slug)) {
      whydonateSlugs[`${slug}`]={}
      whydonateSlugs[`${slug}`].current=selectedAmount
      if (document.getElementById('select-amount-first-'+slug).checked) {
        // Do nothing
      } else {
        renderOptionsForPercentile(slug)
        setDropdownFunc(tipBox, slug)
      }

    } else {
      if (
        whydonateSlugs[`${slug}`].current!==selectedAmount
      ) {
        whydonateSlugs[`${slug}`].current=selectedAmount
        renderOptionsForPercentile(slug)
        setDropdownFunc(tipBox, slug)
      }
    }
  }

  calculateTotalAmount(slug)
}

function calculateTotalAmount(slug) {
  var selectedAmount=getSelectedValue(slug)
  var customSelect=document.getElementById('custom-select'+slug)
  var selectItem=customSelect.children[1].innerHTML
  var tipAmount=''
  var inputTipboxDiv=document.getElementById('input-tip-div'+slug)

  if (inputTipboxDiv.style.display==='flex') {
    tipAmount=document.getElementById('input-tip'+slug).value
  } else {
    if (selectItem.includes('€')) {
      tipAmount=selectItem.substring(1)
    } else {
      tipAmount=selectItem.split('(')[1].split(')')[0]
    }
  }

  selectedAmount=Number(parseFloat(selectedAmount).toFixed(2))

  if (isNaN(tipAmount)) {
    const editedValue=tipAmount.slice(0, -1)
    document.getElementById('input-tip'+slug).value=editedValue
    return
  }

  if (tipAmount!=='') {
    tipAmount=Number(parseFloat(tipAmount.replace(',', '.')).toFixed(2))
  } else {
    tipAmount=0.0
    document.getElementById('input-tip'+slug).value=0.0
  }

  if (selectedAmount===0) {
    tipAmount=0.0
  }
  var totalAmount=selectedAmount+tipAmount
  var tipLabel=document.getElementById('total-charge-label'+slug)
  totalAmount=(Math.round(totalAmount*100)/100).toFixed(2)
  tipLabel.innerHTML=''
  // tipLabel.innerHTML='Total Charge: € '+totalAmount

  if (lang==='nl') {
    tipLabel.innerHTML='Totaalbedrag: € '+totalAmount
  } else if (lang==='de') {
    tipLabel.innerHTML='Gesamtsumme: € '+totalAmount
  } else if (lang==='es') {
    tipLabel.innerHTML='Cargo total: € '+totalAmount
  } else {
    tipLabel.innerHTML='Total Charge: € '+totalAmount
  }

  // console.log('Actual donation ', selectedAmount)
  // console.log('Tip amount ', tipAmount)
  // console.log('Total amount ', totalAmount)
  var tipBox=document.getElementById('tip-box-'+slug)
  if (tipBox.style.display==='none') {
    tipAmount=0.0
  }
  return tipAmount
}

function closeAllSelect(elmnt) {
  /* A function that will close all select boxes in the document,
  except the current select box: */
  var x,
    y,
    i,
    arrNo=[]
  x=document.getElementsByClassName('select-items')
  y=document.getElementsByClassName('select-selected')
  for (i=0; i<y.length; i++) {
    if (elmnt==y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove('select-arrow-active')
    }
  }
  for (i=0; i<x.length; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add('select-hide')
    }
  }
}

function handleOtherAmountInput(value, slug) {
  // var slug=idValue.split('other-amount-number-')[1]
  // console.log('Other amount input on change value ', value, slug)
  document.getElementById('show-other-amount-error-msg-'+slug).style.display='block'
  document.getElementById('show-other-amount-error-msg-'+slug).style.visibility='hidden'
  if (value===""||parseInt(value)<5) {
    document.getElementById('show-other-amount-error-msg-'+slug).style.visibility='visible'
  }
  else {
    if (value.includes('€')) {
      value=value.split('€ ')[1]
    }
    if (value.includes(',')) {
      value=parseFloat(value.replace(',', '.'))
    }
    if (!isNaN(value)) {
      var tipBox=document.getElementById('tip-box-'+slug)
      var inputVal=document.getElementById('other-amount-number-'+slug).value
      var intval=parseInt(inputVal)
      if (tipBox&&intval>=5) {
        var tipInputDiv=document.getElementById('input-tip-div'+slug)
        tipInputDiv.style.display='none'
        if (value<=9) {
          renderOptionsForAmount(slug)
          setDropdownFunc(tipBox, slug)
        } else {
          renderOptionsForPercentile(slug)
          setDropdownFunc(tipBox, slug)
        }
        calculateTotalAmount(slug)
      }
    } else {
      document.getElementById('other-amount-number-'+slug).value='€'
    }
  }
}

//  *****************  Modal Functionalitites ************************************

function changeMonthlyBar(colorCode, id) {
  // console.log("Checked monthly: ", colorCode);
  var monthlyDiv=document.getElementById(
    'preview-intervals-monthly-bar-'+id,
  )
  if (monthlyDiv!=null) {
    monthlyDiv.style.backgroundColor=colorCode
  }

  var onetimeDiv=document.getElementById(
    'preview-intervals-onetime-bar-'+id,
  )
  if (onetimeDiv!=null) {
    onetimeDiv.style.backgroundColor='#E8E8E8'
  }

  var yearlyDiv=document.getElementById('preview-intervals-yearly-bar-'+id)
  if (yearlyDiv!=null) {
    yearlyDiv.style.backgroundColor='#E8E8E8'
  }
}

function changeYearlyBar(colorCode, id) {
  // console.log("Checked yearly: ", colorCode);
  var monthlyDiv=document.getElementById(
    'preview-intervals-monthly-bar-'+id,
  )
  if (monthlyDiv!=null) {
    monthlyDiv.style.backgroundColor='#E8E8E8'
  }

  var onetimeDiv=document.getElementById(
    'preview-intervals-onetime-bar-'+id,
  )
  if (onetimeDiv!=null) {
    onetimeDiv.style.backgroundColor='#E8E8E8'
  }

  var yearlyDiv=document.getElementById('preview-intervals-yearly-bar-'+id)
  if (yearlyDiv!=null) {
    yearlyDiv.style.backgroundColor=colorCode
  }
}

function changeOnetimeBar(colorCode, id) {
  // console.log("Checked onetime: ", colorCode);
  var monthlyDiv=document.getElementById(
    'preview-intervals-monthly-bar-'+id,
  )
  if (monthlyDiv!=null) {
    monthlyDiv.style.backgroundColor='#E8E8E8'
  }

  var onetimeDiv=document.getElementById(
    'preview-intervals-onetime-bar-'+id,
  )
  if (onetimeDiv!=null) {
    onetimeDiv.style.backgroundColor=colorCode
  }

  var yearlyDiv=document.getElementById('preview-intervals-yearly-bar-'+id)
  if (yearlyDiv!=null) {
    yearlyDiv.style.backgroundColor='#E8E8E8'
  }
}

function selectFirstAmount(colorCode, id, tip_enabled) {
  // console.log("select amount first");
  document.getElementById('show-other-amount-error-msg-'+id).style.display='none'
  var selectFirst=document.getElementById('amount-boundary-box-1-s-'+id)
  if (selectFirst!=null) {
    selectFirst.style.backgroundColor=colorCode
  }

  var selectSecond=document.getElementById('amount-boundary-box-2-s-'+id)
  if (selectSecond!=null) {
    selectSecond.style.backgroundColor='white'
  }

  var selectThird=document.getElementById('amount-boundary-box-3-s-'+id)
  if (selectThird!=null) {
    selectThird.style.backgroundColor='white'
  }

  var selectForth=document.getElementById('amount-boundary-box-4-s-'+id)
  if (selectForth!=null) {
    selectForth.style.backgroundColor='white'
  }

  var selectOther=document.getElementById('amount-boundary-box-other-s-'+id)
  if (selectOther!=null) {
    selectOther.style.backgroundColor='white'
  }

  var otherAmountDiv=document.getElementById('other-amount-div-'+id)
  if (otherAmountDiv!=null) {
    otherAmountDiv.style.display='none'
  }
  // resetSize(id)
  if (tip_enabled) {
    handleTipDropdown(id)
  }
}

function selectSecondAmount(colorCode, id, tip_enabled) {
  // console.log("select amount second");
  document.getElementById('show-other-amount-error-msg-'+id).style.display='none'
  var selectFirst=document.getElementById('amount-boundary-box-1-s-'+id)
  if (selectFirst!=null) {
    selectFirst.style.backgroundColor='white'
  }

  var selectSecond=document.getElementById('amount-boundary-box-2-s-'+id)
  if (selectSecond!=null) {
    selectSecond.style.backgroundColor=colorCode
  }

  var selectThird=document.getElementById('amount-boundary-box-3-s-'+id)
  if (selectThird!=null) {
    selectThird.style.backgroundColor='white'
  }

  var selectForth=document.getElementById('amount-boundary-box-4-s-'+id)
  if (selectForth!=null) {
    selectForth.style.backgroundColor='white'
  }

  var selectOther=document.getElementById('amount-boundary-box-other-s-'+id)
  if (selectOther!=null) {
    selectOther.style.backgroundColor='white'
  }

  var otherAmountDiv=document.getElementById('other-amount-div-'+id)
  if (otherAmountDiv!=null) {
    otherAmountDiv.style.display='none'
  }

  if (tip_enabled) {
    handleTipDropdown(id)
  }
}

function selectThirdAmount(colorCode, id, tip_enabled) {
  // console.log("select amount third");
  document.getElementById('show-other-amount-error-msg-'+id).style.display='none'
  var selectFirst=document.getElementById('amount-boundary-box-1-s-'+id)
  if (selectFirst!=null) {
    selectFirst.style.backgroundColor='white'
  }

  var selectSecond=document.getElementById('amount-boundary-box-2-s-'+id)
  if (selectSecond!=null) {
    selectSecond.style.backgroundColor='white'
  }

  var selectThird=document.getElementById('amount-boundary-box-3-s-'+id)
  if (selectThird!=null) {
    selectThird.style.backgroundColor=colorCode
  }

  var selectForth=document.getElementById('amount-boundary-box-4-s-'+id)
  if (selectForth!=null) {
    selectForth.style.backgroundColor='white'
  }

  var selectOther=document.getElementById('amount-boundary-box-other-s-'+id)
  if (selectOther!=null) {
    selectOther.style.backgroundColor='white'
  }

  var otherAmountDiv=document.getElementById('other-amount-div-'+id)
  if (otherAmountDiv!=null) {
    otherAmountDiv.style.display='none'
  }

  if (tip_enabled) {
    handleTipDropdown(id)
  }
}

function selectForthAmount(colorCode, id, tip_enabled) {
  // console.log("select amount forth");
  document.getElementById('show-other-amount-error-msg-'+id).style.display='none'
  var selectFirst=document.getElementById('amount-boundary-box-1-s-'+id)
  if (selectFirst!=null) {
    selectFirst.style.backgroundColor='white'
  }

  var selectSecond=document.getElementById('amount-boundary-box-2-s-'+id)
  if (selectSecond!=null) {
    selectSecond.style.backgroundColor='white'
  }

  var selectThird=document.getElementById('amount-boundary-box-3-s-'+id)
  if (selectThird!=null) {
    selectThird.style.backgroundColor='white'
  }

  var selectForth=document.getElementById('amount-boundary-box-4-s-'+id)
  if (selectForth!=null) {
    selectForth.style.backgroundColor=colorCode
  }

  var selectOther=document.getElementById('amount-boundary-box-other-s-'+id)
  if (selectOther!=null) {
    selectOther.style.backgroundColor='white'
  }

  var otherAmountDiv=document.getElementById('other-amount-div-'+id)
  if (otherAmountDiv!=null) {
    otherAmountDiv.style.display='none'
  }

  if (tip_enabled) {
    handleTipDropdown(id)
  }
}

function selectOtherAmount(colorCode, id, tip_enabled) {
  // console.log("select amount other");
  document.getElementById('show-other-amount-error-msg-'+id).style.display='block'
  var selectFirst=document.getElementById('amount-boundary-box-1-s-'+id)
  if (selectFirst!=null) {
    selectFirst.style.backgroundColor='white'
  }

  var selectSecond=document.getElementById('amount-boundary-box-2-s-'+id)
  if (selectSecond!=null) {
    selectSecond.style.backgroundColor='white'
  }

  var selectThird=document.getElementById('amount-boundary-box-3-s-'+id)
  if (selectThird!=null) {
    selectThird.style.backgroundColor='white'
  }

  var selectForth=document.getElementById('amount-boundary-box-4-s-'+id)
  if (selectForth!=null) {
    selectForth.style.backgroundColor='white'
  }

  var selectOther=document.getElementById('amount-boundary-box-other-s-'+id)
  if (selectOther!=null) {
    selectOther.style.backgroundColor=colorCode
  }

  var otherAmountDiv=document.getElementById('other-amount-div-'+id)
  otherAmountDiv.style.display='flex'

  if (tip_enabled) {
    handleTipDropdown(id)
  }

}

function submitDonation(event, title, id, successUrl, failureUrl) {
  event.stopPropagation()

  var redirectLink=window.location.href
  var periods=document.getElementsByName('period-intervals-'+id)
  var periodsVal=''
  for (var i=0, length=periods.length; i<length; i++) {
    if (periods[i].checked) {
      periodsVal=periods[i].value
      break
    }
  }
  // console.log("periods ", periodsVal);
  var amount=document.getElementsByName('select-amount-'+id)
  var amountVal=''
  for (var i=0, length=amount.length; i<length; i++) {
    if (amount[i].checked) {
      amountVal=amount[i].value
      if (amountVal=='other') {
        amountVal=document.getElementById('other-amount-number-'+id).value
      }
      break
    }
  }
  // console.log("amounts ", amountVal);

  var flocalId=document.getElementById('preview-header-flocaldId-'+id)
    .textContent
  // console.log("flocal id ", flocalId);
  var firstName=document.getElementById('firstname-'+id).value
  // console.log("first name ", firstName);
  var lastName=document.getElementById('lastname-'+id).value
  // console.log("last name ", lastName);
  var email=document.getElementById('email-'+id).value
  // console.log("email ", email);
  var isAnonymous=document.getElementById('is-anonymous-'+id)

  var tipBoxDiv=document.getElementById('tip-box-'+id)
  var tipBoxEnabled=tipBoxDiv? true:false


  let flocalData={
    amount: amountVal,
    bank_account: '',
    currency_code: 'eur',
    description: title,
    email: email,
    first_name: firstName,
    fundraising_local_id: flocalId,
    is_anonymous: isAnonymous.checked? 1:0,
    lang: 'en_US',
    last_name: lastName,
    newsletter: 0,
    pay_period: periodsVal,
    return_url: redirectLink,
    tip_amount: tipBoxEnabled? calculateTotalAmount(id):0,
    source: 'plugin'
  }

  var previewCard=document.getElementById('preview-card-'+id)
  previewCard.style.height='600px'
  var firstnameError=document.getElementById('show-firstname-error-msg-'+id)
  var lastnameError=document.getElementById('show-lastname-error-msg-'+id)
  var emailError=document.getElementById('show-email-error-msg-'+id)
  var otherAmountError=document.getElementById('show-other-amount-error-msg-'+id)
  firstnameError.style.display='none'
  lastnameError.style.display='none'
  emailError.style.display='none'
  otherAmountError.style.display='none'
  let check=true

  var selectOther=document.getElementById('select-amount-other-'+id).checked

  if (!/\S/.test(firstName)) {
    // string is not empty and not just whitespace
    firstnameError.style.display='block'
    check=false
  }

  if (!/\S/.test(lastName)) {
    // string is not empty and not just whitespace
    lastnameError.style.display='block'
    check=false
  }

  if (!validateEmail(email)) {
    emailError.style.display='block'
    // emailError.textContent = "Please enter a valid email!";
    check=false
  }


  if (amountVal===""||amountVal.includes('-')||amountVal.includes('.')||amountVal.includes(',')) {
    check=false
    otherAmountError.style.display='block'
  } else {
    if (parseInt(amountVal)<5) {
      check=false
      otherAmountError.style.display='block'
    }
  }

  //*************************** NEW APPROACH FOR DONATION *******************************
  if (check) {
    var donorInfo={
      email: email,
      firstname: firstName,
      lastname: lastName,
      is_anonymous: isAnonymous.checked? true:false,
    }
    makeDonation(flocalData, successUrl, failureUrl, donorInfo)
  }

  //*************************** PREVIOUS APPROACH FOR DONATION *******************************

  // if (check) {
  //   jQuery.ajax({
  //     url: my_ajax_object.ajax_url,
  //     type: 'post',
  //     data: {
  //       action: 'make_donation',
  //       info: flocalData,
  //     },
  //     beforeSend: function() {
  //       // show loader here
  //       var modal = document.getElementById('modal')
  //       modal.style.display = 'block'

  //     },
  //     success: function(response) {
  //       console.log("response ", response);

  //       //*************************Normal Approach***************************/
  //       var propres =
  //         response.substr(response.length - 1, 1) === '0'
  //           ? response.substr(0, response.length - 1)
  //           : response
  //       var jsonArray = JSON.parse(propres)
  //       // console.log('propres ', jsonArray)
  //       if (jsonArray['status'] === 200) {
  //         localStorage.setItem('success_url', successUrl)
  //         localStorage.setItem('fail_url', failureUrl)
  //         window.location.replace(jsonArray['data']['url'])
  //       }

  //     },
  //     error: function(xhr) {
  //       // console.log("suppression echoué", xhr);
  //     },
  //     complete: function() {
  //       // hide loader here
  //       var modal = document.getElementById('modal')
  //       modal.style.display = 'none'
  //       // console.log("complete");
  //     },
  //   })
  // }
}

async function makeDonation(data, successUrl, failureUrl, donorInfo) {
  console.log('In make Donation')
  const proxyurl='https://intense-temple-29395.herokuapp.com/'
  // const donationApi='https://whydonate-development.appspot.com/api/v1/donation/order/?client=whydonate-staging'
  const donationApi='https://whydonate-production-api.appspot.com/api/v1/donation/order/?client=whydonte-production'
  const url=proxyurl+donationApi
  var modal=document.getElementById('modal')
  modal.style.display='block'

  await fetch(url, {
    method: 'post',
    headers: {
      'Content-Type': 'application/json',
      // 'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: JSON.stringify(data),
  })
    .then(function (response) {
      return response.json()
    })
    .then(function (result) {
      // console.log('In replace ', result)
      localStorage.setItem('donor_info', JSON.stringify(donorInfo))
      var modal=document.getElementById('modal')
      modal.style.display='none'
      if (result['data']['url']!=='undefined') {
        localStorage.setItem('success_url', successUrl)
        localStorage.setItem('fail_url', failureUrl)
        window.location.replace(result['data']['url'])
      }
    })
}

async function updateDonorInformation(donorInfo, urlToRedirect) {
  var proxyurl='https://intense-temple-29395.herokuapp.com/'

  // var api=
  //   'https://whydonate-development.appspot.com/api/v1/donation/donor/update/?client=whydonate-staging'

  var api=
    'https://whydonate-production-api.appspot.com/api/v1/donation/donor/update/?client=whydonate-production'

  var url=proxyurl+api

  await fetch(url, {
    method: 'put',
    headers: {
      'Content-Type': 'application/json',
      // 'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: JSON.stringify(donorInfo),
  })
    .then(function (response) {
      return response.json()
    })
    .then(function (result) {
      // console.log('donor information ', donorInfo)
      localStorage.setItem('donor_info', {})
      setTimeout(function () {
        if (urlToRedirect&&urlToRedirect!=='') {
          window.location.assign(urlToRedirect)
        }
      }, 3000);
    })
}

jQuery(document).ready(function () {

  // Add Tipbox
  var tipBoxArray=document.getElementsByClassName('tip-box')
  if (tipBoxArray.length>0) {
    for (var i=0; i<tipBoxArray.length; i++) {
      let tipBox=tipBoxArray[i]
      let id=tipBox.dataset.id
      let languageCode=tipBox.dataset.lang
      lang=languageCode.split('_')[0]
      let color=tipBox.dataset.color
      createTipbox(id, color)
    }
  }

  // Check for success and failure url
  let urlAddress=window.location.href
  let success_url=localStorage.getItem('success_url')
  let fail_url=localStorage.getItem('fail_url')
  localStorage.setItem('success_url', '')
  localStorage.setItem('fail_url', '')
  if (urlAddress.includes('&o_id=')) {
    let urlAddressArr=urlAddress.split('&o_id=')
    // console.log('order id ', urlAddressArr[1])
    let actualUrlArr=urlAddress.split('?d_id=')

    // Get donor information  
    var donorInfo=localStorage.getItem('donor_info')
    donorInfo=JSON.parse(donorInfo)
    donorInfo['o_id']=urlAddressArr[1].split('&client=')[0]

    window.history.replaceState({}, document.title, actualUrlArr[0])
    jQuery.ajax({
      url: my_ajax_object.ajax_url,
      type: 'post',
      data: {
        action: 'check_order_status',
        order_id: urlAddressArr[1],
      },
      beforeSend: function () {},
      success: function (response) {
        // console.log("main response ", response);
        //$("#modal").css("display", "none");
        var propres=
          response.substr(response.length-1, 1)==='0'
            ? response.substr(0, response.length-1)
            :response
        var jsonArray=JSON.parse(propres)
        // console.log('response arr ', jsonArray)
        if (jsonArray['data']['status']==='canceled') {
          window.location.replace(fail_url)
        } else if (jsonArray['data']['status']==='paid') {
          updateDonorInformation(donorInfo, success_url)
          // window.location.replace(success_url)
        } else {
          // Do nothing
        }
      },
      error: function (xhr) {
        //error handling
        // console.log("suppression echoué");
      },
      complete: function () {
        // hide loader here
      },
    })
  }

  // Send request to our server for installations count
  else {
    // http://localhost/plugindev/wordpress/shortcodes/
    // https://whydonate-staging-ui.appspot.com/
    // https://www.whydonate.nl/

    let domainPart=urlAddress.split('//')
    let domain=domainPart[1].split('/')[0]
    let payload={
      'url': domain,
      'product': 'plugin'
    }
    checkInstallations(payload)
  }
})

async function checkInstallations(payload) {
  // Testing Api
  //let apiUrl='http://127.0.0.1:8080/http://127.0.0.1:8000/api/v1/account/installations/?client=whydonate_staging'

  let proxyurl='https://intense-temple-29395.herokuapp.com/'
  //  let stagingApi='https://whydonate-development.appspot.com/api/v1/account/installations/?client=whydonate_staging'
  let productionApi='https://whydonate-production-api.appspot.com/api/v1/account/installations/?client=whydonate_production'
  let apiUrl=proxyurl+productionApi

  const settings={
    method: 'post',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload)
  }
  const res=await fetch(apiUrl, settings)
  if (res.ok) {
    const json=await res.json()
    // console.log(json)
  } else {
    console.log("Track installations error: "+response.status);
  }
}


// jQuery(document).ready(function () {

// })

// function checkPageUrl(link) {
//   console.log('page url ', link);
// }

function validateEmail(email) {
  var re=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(String(email).toLowerCase())
}

function amountInputSpinner(value, id) {
  // console.log('Check value for spin ', value)
  value=value.replace(/[^0-9]*/g, '');
  if (value<0) {
    value*=-1
  }
  handleOtherAmountInput(value, id)
  return value
}
