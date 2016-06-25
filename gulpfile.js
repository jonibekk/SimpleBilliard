const requireDir = require('require-dir');
const babelRegister = require('babel-register')

requireDir('./gulp/tasks', {recurse: true});
