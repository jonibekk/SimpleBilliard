var Mention = {
  bind: function(target) {
    function normalize(str) {
      return str
        .replace(/\(/g, '\\(')
        .replace(/\)/g, '\\)')
    }
    var values = {}
    target[0].submitValue = function() {
      var replaced = target.val()
      $.each(values, function(key, value) {
        var regexp = new RegExp('<@'+normalize(key)+'>', 'g')
        replaced = replaced.replace(regexp, '<@'+value+'>')
      })
      return replaced
    }
    target.atwho({
      at: '@',
      displayTpl: '<li data-id="${id}" data-text="${text}"><div style="display:flex;align-items: center;width:200px;">\
        <div class="mention-options-image" style="background-image:url(${image})"></div>\
        <div class="mention-options-text">${text}</div>\
      </div></li>',
      insertTpl: '<@${text}>',
      searchKey : 'text',
      // data: [{text:'A'}]
      callbacks: {
        remoteFilter: function(query, callback) {
          if (!query) callback([])
          var params = {
            term: query,
            page_limit: '20'
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
            return $.ajax({
              url: cake.url.k,
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
      values[$li.data('text')] = $li.data('id')
    })
  }
}