window.onload = (resizeLoad) ->
  winW = window.innerWidth
  winH = window.innerHeight
  scrH = document.body.scrollTop
  # ヘッダーサブが出現しているかどうかの判定が必要。
  if winW < 992 and scrH == 0
    varHeight = winH-380 + "px"
  else
    varHeight = winH-336 + "px"
  CircleListBody = document.getElementById ("jsDashboardCircleListBody")
  if CircleListBody?
    CircleListBody.style.height = varHeight
###
 todo
 Scrollの処理。
###

window.onresize = (resizeChanged) ->
  winW = window.innerWidth
  winH = window.innerHeight
  scrH = document.body.scrollTop
  # ヘッダーサブが出現しているかどうかの判定が必要。
  if winW < 992 and scrH == 0
    varHeight = winH-380 + "px"
  else
    varHeight = winH-336 + "px"
  CircleListBody = document.getElementById ("jsDashboardCircleListBody")
  if CircleListBody?
    CircleListBody.style.height = varHeight

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
