// Global Object for all Team Setting functions
if (document.getElementById("addOtherTeam")) {
  var addOtherTeam = {
    months: {
      'eng': [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec"
      ]
    },
    init: function() {
      this.generateCurrentTermList()
    },
    current_term_form: function() {
      return document.getElementsByClassName("addOtherTeam-current-term-form")[0];
    },
    next_term_form: function() {
      return document.getElementsByClassName("addOtherTeam-next-term-form")[0];
    },
    generateCurrentTermList: function() {
      var current_start_date = new Date();
      for (var i = 0; i < 12; i++) {
        var current_end_date = this.dateMonthAfter(current_start_date, i);
        var formatted = this.generateTermRangeFormat(current_start_date, current_end_date);
        var value = this.getNextTermYmValue(current_end_date);
      }
    },
    updateNextTermList: function() {
      // for (term_length of [3, 6, 12]) {
      //
      // }
    },
    getNextTermYmValue: function(current_end_date) {
      var next_start_date = this.dateMonthAfter(current_end_date, 1);
      return this.year(next_start_date) + '-' + this.toDigit(this.month(next_start_date));
    },
    generateTermRangeFormat: function(start_date, end_date) {
      var formatted_start_date = this.dateFormatYm(start_date);
      var formatted_end_date = this.dateFormatYm(end_date);
      var formatted = formatted_start_date + ' - ' + formatted_end_date;
      return formatted;
    },
    dateFormatYm: function(date) {
      var month_index = date.getMonth();
      var month = this.month(date);
      if (cake.lang === 'jpn') {
        return this.year(date) + "年" + this.toDigit(month) + "月";
      } else {
        return this.months.eng[month_index] + " " + this.year(date);
      }
    },
    month: function(date) {
      return parseInt(date.getMonth()) + 1;
    },
    year: function(date) {
      return parseInt(date.getFullYear());
    },
    dateMonthAfter: function(date, addNumber) {
      return new Date(this.year(date), this.month(date) - 1 + addNumber)
    },
    toDigit: function(number) {
      return ('00' + number).slice(-2);
    }
  }
  addOtherTeam.init();
}
