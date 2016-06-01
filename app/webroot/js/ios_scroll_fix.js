$(function(){
    $(document)
    .on('focus', 'header > input', function(e){
      var pos = $(window).scrollTop();
      $('html, body').scrollTop(pos);
      $('header').css({top: pos});
    })
    .on('blur', 'header > input', function(e){
      $('header').css({top: 0});
    });
});


