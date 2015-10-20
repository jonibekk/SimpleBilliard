$ ->
  prog = $('.jsGoalsCardProgress')
  slightGray = '#c6c6c6'
  waterBlue = '#74d5f1'
  progNum = prog.attr 'goalProgPercent'

  # find out Browser | ブラウザ判定
  ua = navigator.userAgent.toLowerCase()
  if ua.indexOf('safari') != -1 or ua.indexOf('chrome') != -1 or ua.indexOf('ipad') != -1 or ua.indexOf('ipod') != -1 or ua.indexOf('iphone') != -1 or ua.indexOf('android') != -1
    # set Gradiation for each type of Browser | ブラウザ毎にグラデーションの書き方を指定
    prog.stop().css 'background': '-webkit-linear-gradient(bottom, '+waterBlue+' 0%, '+waterBlue+' '+progNum+'%, '+slightGray+' '+progNum+++'%,' +slightGray+ ' 100%)'
  else if ua.indexOf('firefox')
    prog.stop().css 'background': '-moz-linear-gradient(bottom, '+waterBlue+' 0%, '+waterBlue+' '+progNum+'%, '+slightGray+' '+progNum+++'%,' +slightGray+ ' 100%)'
  else
    prog.stop().css 'background': 'linear-gradient(bottom, '+waterBlue+' 0%, '+waterBlue+' '+progNum+'%, '+slightGray+' '+progNum+++'%,' +slightGray+ ' 100%)'
  return
