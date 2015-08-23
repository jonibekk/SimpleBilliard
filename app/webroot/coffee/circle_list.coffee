winH = window.innerHeight
CircleListBody = document.getElementById ("jsDashboardCircleListBody")
if CircleListBody?
  if winH < 676
    CircleListBody.style.maxHeight = "176px"
  if winH < 596
    CircleListBody.style.maxHeight = "140px"
