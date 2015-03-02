# ## クラス名
# クラス説明
# ```
# 使い方
# ```
class Hoge
  # コンストラクタだよ
  # * @arg1: 引数です
  constructor: (@arg1)->

# サンプルコード
# コメントが左右に出るので
# 上手い感じにコード書いてください
# Shallas use only this code.

sum = (array) ->
  result = 0
  for i in array
    result += i
  result

array = new Array(1,2,3)
console.log "result=", sum(array)

# # サンプルコード2
# ## 実は
# ### なにげに
# #### Markdownに
# ##### 対応
# ただしトップの文章を見出しにすると微妙に位置がズレているように見えるのでCSSをカスタマイズしたいです。

sum = (array) ->
  result = 0
  for i in array
    result += i
  result

array = new Array(1,2,3)
console.log "result=", sum(array)