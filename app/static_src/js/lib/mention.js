var Mention = {
  bind: function(target) {
    target.atwho({
      at: '@',
      displayTpl: '<li><div style="display:flex;align-items: center;width:200px;">\
        <div class="mention-options-image" style="background-image:url(${image})"></div>\
        <div class="mention-options-text">${text}</div>\
      </div></li>',
      insertTpl: '@${text}',
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
  }
}