var Mention = {
  bind: function(target) {
    if (!target[0]) return
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
        replaced = replaced.replace(regexp, '%%%'+value+'%%%')
      })
      return replaced
    }
    target.atwho({
      at: '@',
      displayTpl: '<li data-id="${id}" data-text="${text}"><div class="mention-wrapper">\
        <div class="mention-image" style="background-image:url(${image});"></div>\
        <div class="mention-text">${text}</div>\
      </div></li>',
      insertTpl: '<@${text}>',
      searchKey : 'text',
      // data: [{text:'A'}]
      callbacks: {
        remoteFilter: function(query, callback) {
          if (!query) callback([])
          var params = {
            term: query,
            page_limit: '20',
            with_group: 1
          }
          var results = []
          $.ajax({
            url: cake.url.a,
            data: params
          }).then(function(res) {
            for (var i=0; i<res.results.length; i++) {
              if (!res.results[i].image) {
                res.results[i].image = 'img/no-image-team.jpg'
              }
            }
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
      values[$li.data('text')] = $li.data('id')
    })
  }
}