window.onload = (resizeLoad) ->
  winWidth = window.innerWidth
  winHeight = window.innerHeight
  scrollHeight = document.body.scrollTop
  # ヘッダーサブが出現しているかどうかの判定が必要。
  if winWidth < 992 and scrollHeight == 0
    circleListHeight = winHeight-380 + "px"
  else
    circleListHeight = winHeight-336 + "px"
  circleListBodys = document.getElementsByClassName ("js-dashboard-circle-list-body")
  if circleListBodys?
    for body, index in circleListBodys
      circleListBodys[index].style.height = circleListHeight
###
 todo
 Scrollの処理。
 -> 何のことだか忘れた。11月18日
###

window.onresize = (resizeChanged) ->
  winWidth = window.innerWidth
  winHeight = window.innerHeight
  scrollHeight = document.body.scrollTop
  # ヘッダーサブが出現しているかどうかの判定が必要。
  if winWidth < 992 and scrollHeight == 0
    circleListHeight = winHeight-380 + "px"
  else
    circleListHeight = winHeight-336 + "px"
  circleListBodys = document.getElementsByClassName ("js-dashboard-circle-list-body")
  if circleListBodys?
    for body, index in circleListBodys
      circleListBodys[index].style.height = circleListHeight


reloadScrollBars = ->
  document.documentElement.style.overflow = 'auto'
  # firefox, chrome
  document.body.scroll = 'yes'
  # ie only
  return

unloadScrollBars = ->
  document.documentElement.style.overflow = 'hidden'
  # firefox, chrome
  document.body.scroll = 'no'
  # ie only
  return
