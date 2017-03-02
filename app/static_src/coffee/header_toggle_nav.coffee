headerToggleNav = ->
  headerDropdowns = '.header-dropdown-add, mb-app-header-dropdown-add, .header-dropdown-message, .header-dropdown-notify, .header-dropdown-functions, .mb-app-header-dropdown-functions'
  # varErrMessage = ' is undefined'
  # if !varErrMessage?
  #   console.log 'varErrMessage' +' is undefined'
  # if !headerDropdowns?
  #   console.log 'fixedOnTop'+varErrMessage
  $('.navbar-offcanvas').on 'show.bs.offcanvas', ->
    if $(headerDropdowns).hasClass 'open'
      $(headerDropdowns).removeClass 'open'
    $('#layer-black').css 'display', 'block'
    $('.toggle-icon').addClass('rotate').removeClass('rotate-reverse').addClass('fa-arrow-right').removeClass 'fa-navicon'
    $('.unread-point-on-hamburger').addClass('is-open')
    return
  $('.navbar-offcanvas').on 'hide.bs.offcanvas', ->
    $('#layer-black').css 'display', 'none'
    $('.toggle-icon').removeClass('rotate').addClass('rotate-reverse').removeClass('fa-arrow-right').addClass 'fa-navicon'
    $('.unread-point-on-hamburger').removeClass('is-open')
    return
  $(headerDropdowns).on 'click', ->
    if $('#NavbarOffcanvas').hasClass 'canvas-slid'
      $('#NavbarOffcanvas').removeClass 'in canvas-slid'
    $('#layer-black').css 'display', 'none'
    $('.toggle-icon').removeClass('rotate').addClass('rotate-reverse').removeClass('fa-arrow-right').addClass 'fa-navicon'
    $('.unread-point-on-hamburger').removeClass('is-open')
    return

$ ->
  headerToggleNav()
