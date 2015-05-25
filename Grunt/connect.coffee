# Setting of a grunt local server.
module.exports = (grunt) ->

  #
  # http://localhost:5004
  # We have to set "keepalive" as "true" to use a local server.
  # keepaliveをtrueにしておかないとサーバーが立ち上がった後にすぐに終了します
  #

  docs:
    options:
      port: 5004
      keepalive: true
      hostname: 'localhost'
      open: true
