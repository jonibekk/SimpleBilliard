$ ->
  winH = window.innerHeight
  wrapH = $('#jsRightSideContainerWrap').height()
  t = 60 # height from top
  b = 88 # height from bottom

  $(window).scroll ->
    if wrapH + t > winH
      if $(this).scrollTop() + winH > wrapH + t + b
        $('#jsRightSideContainer').addClass 'js-right-side-fixed-container'
      else
        $('#jsRightSideContainer').removeClass 'js-right-side-fixed-container'
        return
    else
      $('#jsRightSideContainer').addClass 'js-right-side-fixed-ontop'
    return
