# Setting of a grunt local server.
module.exports = (grunt) ->

  #
  # http://localhost:5004
  # We have to set "keepalive" as "true" to use a local server.
  # keepaliveをtrueにしておかないとサーバーが立ち上がった後にすぐに終了してしまいます(なんでやろ？)
  # baseにdocsを指定しておくことで、docsディレクトリ内のindex.htmlを読み取ります。

  docs:
    options:
      port: 5004
      hostname: 'localhost'
      open: true
      base: 'docs'
      keepalive: true
