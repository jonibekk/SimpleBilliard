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
  CircleListBody.style.height = varHeight
  console.log (scrH)

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
  CircleListBody.style.height = varHeight
  console.log (scrH)
