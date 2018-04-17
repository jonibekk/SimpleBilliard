var Mention = function(target) {
  this.postId = target.attr('post-id')
  this.hasMention = target.attr('has-mention') == 1
  if (!this.hasMention) return;
  this.values = {}
  var self = this
  var bind = function(target) {
    if (!target[0]) return
    $(document).on('blur.atwho', '#' + target.attr('id'), function(e) {
      var _this = target.data('atwho')
      var c;
      if (c = _this.controller()) {
        c.expectedQueryCBId = null;
        return c.view.hide(e, c.getOpt("displayTimeout"));
      }
    })
    function normalize(str) {
      return str
        .replace(/\(/g, '\\(')
        .replace(/\)/g, '\\)')
    }
    target[0].submitValue = function() {
      var replaced = target.val()
      $.each(self.values, function(key, value) {
        var regexp = new RegExp('@'+normalize(key), 'g')
        replaced = replaced.replace(regexp, '%%%'+value+'%%%')
      })
      return replaced
    }
    // textare.width - image.width - margin
    var styleStr = 'width:'+(target.width()-42)+'px;';
    target.atwho({
      at: '@',
      limit: 20,
      displayTpl: '<li data-id="${id}" data-text="${text}"><div style="width:' + target.width() + 'px" class="mention-wrapper">\
        <div class="mention-image" style="background-image:url(${image});"></div>\
        <div style="' + styleStr + '" class="mention-text">${text}</div>\
      </div></li>',
      insertTpl: '@${text}',
      searchKey : 'text',
      suspendOnComposing: true,
      // data: [{text:'A'}]
      callbacks: {
        remoteFilter: function(query, callback) {
          if (!query) callback([])
          var params = {
            term: query,
            page_limit: '20',
            in_post_id: self.postId
          }
          var results = []
          $.ajax({
            url: cake.url.a,
            data: params
          }).then(function(res) {
            results = results.concat(res.results)
            return $.ajax({
              url: cake.url.select2_circles,
              data: params
            })
          }).then(function(res) {
            results = results.concat(res.results)
            callback(results)
          })
        }
      }
    })
    target.on('inserted.atwho', function(atwhoEvent, $li, browserEvent) {
      self.values[$li.data('text')] = $li.data('id')
    })
  }
  bind(target)
  var convert = function(text) {
    if (!self.postId) return text
    var matches = text.match(/%%%(.*?:.*?)%%%/g)
    if (!matches) return text
    for (var i=0; i<matches.length; i++) {
      var split = matches[i].replace(/%/g, '').split(':')
      self.values[split[1]] = split[0]
    }
    return text.replace(/%%%.*?:(.*?)%%%/g, '@$1')
  }
  if (target.val()) {
    target.val(convert(target.val()))
  }
}