readConfigDir = require('./readconfigdir')
_ = requre('lodash')

module.exports = (grunt, options) ->
  merge = options.mergeFunction or _.merge
  _([ options.configPath ], [ options.overridePath ])
  .flatten()
  .compact()
  .reduce ((config, configPath) ->
    overrideConfig = readConfigDir(configPath, grunt, options)
    merge config, overrideConfig
  ), {}
