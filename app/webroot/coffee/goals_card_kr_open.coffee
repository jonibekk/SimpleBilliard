evReplaceKRListAjaxGet = ->
  $(document).on 'click', '.replace-ajax-get-kr-list', ->
    # attrUndefinedCheck @, 'target-id'
    # attrUndefinedCheck @, 'ajax-url'
    # attrUndefinedCheck @, 'kr-line-id'
    target_id = $(@).attr('target-id')
    ajax_url = $(@).attr('ajax-url')
    kr_line_id = $(@).attr('kr-line-id')
    $kr_line = $('#' + kr_line_id)
    if !$('#' + target_id).hasClass('data-exists')
      $.get ajax_url, (data) ->
        $('#' + target_id).after data.html
        line_height = $kr_line.height()
        line_height -= 64
        line_height += 64 * data.count
        $kr_line.height line_height
        $('#' + target_id).remove()

        # 高さ更新
        scrollFixColumn()

      return false

$ ->
  evReplaceKRListAjaxGet()

  # evReplaceAjaxGet = ->
  #   # attrUndefinedCheck @, 'target-id'
  #   # attrUndefinedCheck @, 'ajax-url'
  #   $obj = $(@)
  #   target_id = $obj.attr('target-id')
  #   ajax_url = $obj.attr('ajax-url')
  #   #noinspection JSJQueryEfficiency
  #   if !$('#' + target_id).hasClass('data-exists')
  #     $.get ajax_url, (data) ->
  #       $('#' + target_id).append data.html
  #     return false
