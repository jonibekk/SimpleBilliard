$ ->
  rSideHeight = $('#jsRightSideContainer').height()
  console.log rSideHeight
  $(window).scroll ->
    if $(this).scrollTop() - rSideHeight > 1000
      $('#jsRightSideContainer').addClass 'js-right-side-fixed-container'
    else
      $('#jsRightSideContainer').removeClass 'js-right-side-fixed-container'
    return
  return
