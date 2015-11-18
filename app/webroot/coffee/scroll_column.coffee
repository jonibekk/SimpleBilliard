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
  rightColContentsHeight = $(rightColContentsId).height()
  winHeight = window.innerHeight
  winWidth = window.innerWidth
  heightFromTop = 60
  heightFromBottom = 88

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
  varErrMessage = ' is undefined'
  if !varErrMessage?
    console.log varErrMessage +' is undefined'
  if !fixedOnTop?
    console.log fixedOnTop+varErrMessage
  if !fixedOnBottom?
    console.log fixedOnBottom+varErrMessage
  if !rightColContentsId?
    console.log rightColContentsId+varErrMessage
  if !rightColContentsHeight?
    console.log rightColContentsHeight+varErrMessage
  if !winHeight?
    console.log winHeight+varErrMessage
  if !winWidth?
    console.log winWidth+varErrMessage
  if !heightFromTop?
    console.log heightFromTop+varErrMessage
  if !heightFromBottom?
    console.log heightFromBottom+varErrMessage

  if winWidth > 991
    # Scrollする前に右側が固定される領域かの判定
    if rightColContentsHeight + heightFromTop < winHeight
      $(rightColContentsId).addClass fixedOnTop
      return
    else
      $(window).scroll ->
        if $(this).scrollTop() + winHeight > rightColContentsHeight + heightFromTop + heightFromBottom
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
  rightColContentsHeight = $(rightColContentsId).height()
  winHeight = window.innerHeight
  winWidth = window.innerWidth
  heightFromTop = 60
  heightFromBottom = 88

  ###
  '?'存在演算子を使って変数が存在するかテスト
  ###

  varErrMessage = ' is undefined'
  if !varErrMessage?
    console.log varErrMessage +' is undefined'
  if !fixedOnTop?
    console.log fixedOnTop+varErrMessage
  if !fixedOnBottom?
    console.log fixedOnBottom+varErrMessage
  if !rightColContentsId?
    console.log rightColContentsId+varErrMessage
  if !rightColContentsHeight?
    console.log rightColContentsHeight+varErrMessage
  if !winHeight?
    console.log winHeight+varErrMessage
  if !winWidth?
    console.log winWidth+varErrMessage
  if !heightFromTop?
    console.log heightFromTop+varErrMessage
  if !heightFromBottom?
    console.log heightFromBottom+varErrMessage

  if winWidth > 991
    ###
    要素の高さがスクロールできる分の高さに達したときの処理
    ###
    if  $(rightSideContainer).hasClass fixedOnTop and rightColContentsHeight + heightFromTop + heightFromBottom > winHeight
      $(rightSideContainer).removeClass fixedOnTop
    else
      # 下に固定されていた場合の処理
      if  $(rightSideContainer).hasClass fixedOnBottom
        if $(@).scrollTop() + winHeight < rightColContentsHeight + heightFromTop + heightFromBottom
          $(rightSideContainer).removeClass fixedOnBottom
        else
          return
      else
        # 固定されていない場合は最初の関数を読み込み直すだけ
        scrollFixColumn()
  return
