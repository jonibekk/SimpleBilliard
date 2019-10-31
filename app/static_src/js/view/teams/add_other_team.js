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
        this.setOptionToSelect(this.current_term_form(), value, formatted);
      }
    },
    updateNextTermList: function(next_start_date) {
      var next_term_form = this.next_term_form();
      this.emptySelect(next_term_form);
      // if set other than "" instance of null to option, don't fire bootstrap validation.
      this.setOptionToSelect(next_term_form, "", __("Please select"));
      var length_list = [3, 6, 12];
      for (i in length_list) {
        var term_length = length_list[i];
        var next_end_date = this.dateMonthAfter(next_start_date, term_length - 1);
        var formatted = this.generateTermRangeFormat(next_start_date, next_end_date);
        formatted = formatted + ' (' + __(term_length + ' months') + ')'
        this.setOptionToSelect(this.next_term_form(), term_length, formatted);
      }
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
    setOptionToSelect: function(select, value, display_value) {
      var option = document.createElement('option');

      option.setAttribute('value', value);
      option.innerHTML = display_value;

      select.appendChild(option);
    },
    emptySelect: function(select) {
      // select.optionsの値を全てnullにしてもうまくemptyされなかったので、
      // jQueryの力を借りる
      $('.addOtherTeam-next-term-form').empty();
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
    dateMonthAfter: function(date, addNumber) {
      return new Date(this.year(date), this.month(date) - 1 + addNumber)
    },
    month: function(date) {
      return parseInt(date.getMonth()) + 1;
    },
    year: function(date) {
      return parseInt(date.getFullYear());
    },
    toDigit: function(number) {
      return ('00' + number).slice(-2);
    },
    submitButton: function() {
      return document.getElementsByClassName("team-button")[0];
    }
  }
  addOtherTeam.init();

  addOtherTeam.current_term_form().onchange = function() {
    var year = this.value.substr(0, 4);
    var month = this.value.substr(-2, 2);
    var next_start_date = new Date(year, parseInt(month) - 1);

    addOtherTeam.updateNextTermList(next_start_date);
  };
  addOtherTeam.submitButton().onclick = function () {
    localStorage.removeItem('token');
  };
}
