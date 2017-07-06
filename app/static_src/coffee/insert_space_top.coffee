insertSpaceTop = (height) ->
  $header = $('#header')
  $header.css 'max-height', parseInt($header.css('max-height')) + height + 'px'
  $header.css 'padding-top', parseInt($header.css('padding-top')) + height + 'px'
  $jsLeftSideContainer = $('#jsLeftSideContainer')
  $jsLeftSideContainer.css 'top', parseInt($jsLeftSideContainer.css('top')) + height + 'px'
  $jsRightSideContainer = $('#jsRightSideContainer')
  $jsRightSideContainer.css 'top', parseInt($jsRightSideContainer.css('top')) + height + 'px'
  $body = $('body')
  $body.css 'padding-top', parseInt($body.css('padding-top')) + height + 'px'
  $spFeedAltSub = $('#SpFeedAltSub')
  $spFeedAltSub.css 'top', parseInt($spFeedAltSub.css('top')) + height + 'px'
  $sidebarSetting = $('#SidebarSetting')
  $sidebarSetting.css 'top', parseInt($sidebarSetting.css('top')) + height + 'px'
  $scrollSpyContents = $('#ScrollSpyContents > div')
  $scrollSpyContents.each (i, elem) ->
    $(elem).css 'padding-top', parseInt($(elem).css('padding-top')) + height + 'px'
    $(elem).css 'margin-top', parseInt($(elem).css('margin-top')) - height + 'px'

$ ->
  if cake.is_mb_app_ios
    insertSpaceTop(20)
