###
if right column is smaller than window height, right culumn is fix
else scrolling, fix right column
右カラムがWindowの高さより低い場合は固定
スクロールして一番下にきたら固定
###
scrollFixColumn = ->
  fixOnTop = 'js-right-side-fixed-ontop'
  winH = window.innerHeight
  winW = window.innerWidth
  wrapH = $('#jsRightSideContainerWrap').height()
  t = 60 # height from top
  b = 88 # height from bottom
  if winW > 991
    # Scrollする前に右側が固定される領域かの判定
    if wrapH + t < winH
      $('#jsRightSideContainer').addClass(fixOnTop)
      return
    else
      $(window).scroll ->
        if $(this).scrollTop() + winH > wrapH + t + b
          $('#jsRightSideContainer').addClass 'js-right-side-fixed-container'
        else
          $('#jsRightSideContainer').removeClass 'js-right-side-fixed-container'
        return

$ ->
  scrollFixColumn()

###
When opening right column content, this function resize right column
###

openResizeColumn = ->
  rightColumnWrap = '#jsRightSideContainerWrap'
  rightSideContainer = '#jsRightSideContainer'
  fixOnTop = 'js-right-side-fixed-ontop'
  winH = window.innerHeight
  winW = window.innerWidth
  wrapH = $(rightColumnWrap).height()
  t = 60 # height from top
  b = 88 # height from bottom
  if winW > 991
    # 要素の高さがスクロールできる分の高さに達したときの処理
    if  $(rightSideContainer).hasClass('js-right-side-fixed-ontop')
      if wrapH + t > winH
        $('#jsRightSideContainer').removeClass(fixOnTop)
      else
        return
    else
      # 次
      console.log 'aaa'
  return
  # if winW > w
  #   $('#jsRightSideContainer').removeClass 'js-right-side-fixed-container js-right-side-fixed-ontop'
  #   $() ->
  #     if wrapH + t > winH
  #       if $(this).scrollTop() + winH > wrapH + t + b
  #         $('#jsRightSideContainer').addClass 'js-right-side-fixed-container'
  #       else
  #         $('#jsRightSideContainer').removeClass 'js-right-side-fixed-container'
  #         return
  #     else
  #       $('#jsRightSideContainer').addClass 'js-right-side-fixed-ontop'
  #     return
