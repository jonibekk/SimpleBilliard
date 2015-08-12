winH = window.innerHeight
CircleListBody = document.getElementById ("jsDashboardCircleListBody")
if CircleListBody?
  if winH < 672
    CircleListBody.style.maxHeight = "200px"
  if winH < 592
    CircleListBody.style.maxHeight = "140px"
