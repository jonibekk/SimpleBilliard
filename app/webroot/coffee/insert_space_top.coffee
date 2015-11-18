insertSpaceTop = (height) ->
  $navbar = $('.navbar')
  $navbar.css 'max-height', parseInt($navbar.css('max-height')) + height + 'px'
  $navbar.css 'padding-top', parseInt($navbar.css('padding-top')) + height + 'px'
  $body = $('.body')
  $body.css 'padding-top', parseInt($body.css('padding-top')) + height + 'px'
  $offcanvas = $('.offcanvas')
  $offcanvas.css 'top', parseInt($offcanvas.css('top')) + height + 'px'
  return
