evGoalsMoreView = ->
  attrUndefinedCheck this, 'next-page-num'
  attrUndefinedCheck this, 'get-url'
  attrUndefinedCheck this, 'goal-type'
  $obj = $(this)
  next_page_num = $obj.attr('next-page-num')
  get_url = $obj.attr('get-url')
  type = $obj.attr('goal-type')
  #リンクを無効化
  $obj.attr 'disabled', 'disabled'
  $loader_html = $('<i class="fa fa-refresh fa-spin"></i>')
  #ローダー表示
  $obj.after $loader_html
  #url生成
  url = get_url + '/page:' + next_page_num + '/type:' + type
  listBox = undefined
  moreViewButton = $obj
  limitNumber = undefined
  if type == 'leader'
    listBox = $('#LeaderGoals')
    limitNumber = cake.data.e
  else if type == 'collabo'
    listBox = $('#CollaboGoals')
    limitNumber = cake.data.f
  else if type == 'my_prev'
    listBox = $('#PrevGoals')
    limitNumber = cake.data.k
  $.ajax
    type: 'GET'
    url: url
    async: true
    dataType: 'json'
    success: (data) ->
      if !$.isEmptyObject(data.html)
        #取得したhtmlをオブジェクト化
        $goals = $(data.html)
        #一旦非表示
        $goals.hide()
        listBox.append $goals
        #html表示
        # もっと見るボタン非表示
        moreViewButton.hide()
        $goals.show()
        #ページ番号をインクリメント
        next_page_num++
        #次のページ番号をセット
        $obj.attr 'next-page-num', next_page_num
        #ローダーを削除
        $loader_html.remove()
        #もっと見るボタン表示
        moreViewButton.show()
        #リンクを有効化
        $obj.removeAttr 'disabled'
        #画像をレイジーロード
        imageLazyOn()
        #画像リサイズ
        $goals.find('.fileinput_post_comment').fileinput().on 'change.bs.fileinput', ->
          $(this).children('.nailthumb-container').nailthumb
            width: 50
            height: 50
            fitDirection: 'center cgenter'
          return
        if data.count < limitNumber
          moreViewButton.hide()
        $('.custom-radio-check').customRadioCheck()
        goalsCardProgress()
        openResizeColumn()
      else
        # もっと見るボタンの削除
        moreViewButton.hide()
      return
    error: ->
      alert cake.message.notice.c
      return
  false

$ ->
  $(document).on 'click', '.click-my-goals-read-more', evGoalsMoreView
  $(document).on 'click', '.click-collabo-goals-read-more', evGoalsMoreView
  $(document).on 'click', '.click-follow-goals-read-more', evGoalsMoreView
