import notify from "gulp-notify"
import notifier from "node-notifier"

module.exports = function(err) {
  notify.onError({
    title:    "Gulp Error",
    message:  "Error: <%= error %>",
  })(err);
  this.emit('end');
};
