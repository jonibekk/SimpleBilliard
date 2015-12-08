$ ->
  showFlag = false
  topBtn = $('#jsGoTop')
  topBtn.css 'bottom', '-100px'
  $(window).scroll ->
    if $(this).scrollTop() > 30
      if showFlag == false
        showFlag = true
        topBtn.stop().animate { 'bottom': '80px' }, 200
    else
      if showFlag
        showFlag = false
        topBtn.stop().animate { 'bottom': '-100px' }, 200
    return
  topBtn.click ->
    $('body,html').stop().animate { scrollTop: 0 }, 500, 'swing'
    false
  return

$ ->
  goT = $('#jsGoTop')
  goT.hover (->
    $('#jsGoTopText').stop().animate { 'right': '14px' }, 360
    return
  ), ->
    $('#jsGoTopText').stop().animate { 'right': '-140px' }, 800
    return
  return

# デバッグ用
selectAddParants = ->
  selectElm = document.querySelectorAll('select')
  selectElmNum = selectElm.length
  console.log 'このページには ' + selectElmNum + ' 個の<select>があります。'
selectAddParants()
