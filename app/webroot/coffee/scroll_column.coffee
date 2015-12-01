###
if right column is smaller than window height, right culumn is fix
else scrolling, fix right column
右カラムがWindowの高さより低い場合は固定
スクロールして一番下にきたら固定
###
scrollFixColumn = ->
  fixedOnTop = 'js-right-side-fixed-ontop'
  fixedOnBottom = 'js-right-side-fixed-onbottom'
  rightColContentsId = '#jsRightSideContainer'
  marginTop = 60
  marginBottom = 88

  ###
  '?'存在演算子を使って変数が存在するかテスト
  中身がnullかundefinedの場合はfalseを返し、それ以外はtrueを返します。
  未定義変数がある場合ももちろんfalseです。
  JS'?'は三項演算子なのでその違いに注意
  下の例では、fixedOnTopがfalseだったら fixedOnTop is undefinedとconsoleに返す。

  ```coffeescript
  if !fixedOnTop?
    console.log fixedOnTop+' is undefined'
  ```
  ###
#  varErrMessage = ' is undefined'
#  if !varErrMessage?
#    console.log 'varErrMessage' +' is undefined'
#  if !fixedOnTop?
#    console.log 'fixedOnTop'+varErrMessage
#  if !fixedOnBottom?
#    console.log 'fixedOnBottom'+varErrMessage
#  if !rightColContentsId?
#    console.log 'rightColContentsId'+varErrMessage
#  if !marginTop?
#    console.log 'marginTop'+varErrMessage
#  if !marginBottom?
#    console.log 'marginBottom'+varErrMessage

  if window.innerWidth > 991
    # Scrollする前に右側が固定される領域かの判定
    if $(rightColContentsId).height() + marginTop < window.innerHeight
      $(rightColContentsId).addClass fixedOnTop
      return
    else
      $(window).scroll ->
        if $(this).scrollTop() + window.innerHeight > $(rightColContentsId).height() + marginTop + marginBottom
          $(rightColContentsId).addClass fixedOnBottom
        else
          $(rightColContentsId).removeClass fixedOnBottom
        return

$ ->
  scrollFixColumn()

###
When opening right column content, this function resize right column
###

openResizeColumn = ->
  fixedOnTop = 'js-right-side-fixed-ontop'
  fixedOnBottom = 'js-right-side-fixed-onbottom'
  rightColContentsId = '#jsRightSideContainer'
  marginTop = 60
  marginBottom = 88

  ###
  '?'存在演算子を使って変数が存在するかテスト
  ###

#  varErrMessage = ' is undefined'
#  if !varErrMessage?
#    console.log 'varErrMessage' +' is undefined'
#  if !fixedOnTop?
#    console.log 'fixedOnTop'+varErrMessage
#  if !fixedOnBottom?
#    console.log 'fixedOnBottom'+varErrMessage
#  if !rightColContentsId?
#    console.log 'rightColContentsId'+varErrMessage
#  if !marginTop?
#    console.log 'marginTop'+varErrMessage
#  if !marginBottom?
#    console.log 'marginBottom'+varErrMessage

  if window.innerWidth > 991
    ###
    要素の高さがスクロールできる分の高さに達したときの処理
    ###
    if  $(rightColContentsId).hasClass fixedOnTop
      if window.innerHeight < $(rightColContentsId).height() + marginTop + marginBottom
        $(rightColContentsId).removeClass fixedOnTop
        $(window).scroll ->
          if $(this).scrollTop() + window.innerHeight > $(rightColContentsId).height() + marginTop + marginBottom
            $(rightColContentsId).addClass fixedOnBottom
          else
            $(rightColContentsId).removeClass fixedOnBottom
          return
      else
        $(rightColContentsId).addClass 'hoge'
    else
      ###
       下に固定されていた場合の処理
      ###
      if  $(rightColContentsId).hasClass fixedOnBottom
        if $(window).scrollTop() + window.innerHeight < $(rightColContentsId).height() + marginTop + marginBottom
          $(rightColContentsId).removeClass fixedOnBottom
        else
          return
      else
        ###
         固定されていない場合はそのまま再読み込み
        ###
        $(window).scroll ->
          if $(this).scrollTop() + window.innerHeight > $(rightColContentsId).height() + marginTop + marginBottom
            $(rightColContentsId).addClass fixedOnBottom
          else
            $(rightColContentsId).removeClass fixedOnBottom
          return
  return
