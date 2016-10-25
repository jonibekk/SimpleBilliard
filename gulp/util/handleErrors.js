import notify from "gulp-notify"

module.exports = function(err) {
  notify.onError({
    title:    "Gulp Error",
    message:  "Error: <%= error %>",
  })(err);
  this.emit('end');
};
