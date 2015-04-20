module.exports = (dir, grunt, options) ->
  getKey = (file) ->
    ext = path.extname(file)
    base = path.basename(file, ext)
    base
