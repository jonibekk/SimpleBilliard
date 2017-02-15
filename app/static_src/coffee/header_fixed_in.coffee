# Workaround for buggy header/footer fixed position when virtual keyboard is on/off
$(document).on 'focus', 'input, textarea', ->
  $('.navbar').css 'position', 'fixed'
  return
$(document).on 'blur', 'input, textarea', ->
  $('.navbar').css 'position', 'fixed'
  #force page redraw to fix incorrectly positioned fixed elements
  setTimeout (->
    #noinspection JSUnresolvedVariable
    if typeof $.mobile != 'undefined'
      #noinspection JSUnresolvedVariable
      window.scrollTo $.mobile.window.scrollLeft(), $.mobile.window.scrollTop()
    return
  ), 20
  return
