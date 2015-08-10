$ ->
  winH = window.innerHeight
  wrapH = $('#jsRightSideContainerWrap').height()
  t = 60 # height from top

  $(window).scroll ->
    if $(this).scrollTop() + winH > wrapH + t
      $('#jsRightSideContainer').addClass 'js-right-side-fixed-container'
    else
      $('#jsRightSideContainer').removeClass 'js-right-side-fixed-container'
      return
    return
