// Global Object for all Team Setting functions
if (document.getElementById("editTerm")) {
  var teamSettings = {
    //Some shorthands for important DOM elements
    button: document.getElementById("editTerm").getElementsByClassName("btn")[0],
    agree: document.getElementById("term_agreement"),
    caret: document.getElementById("editTerm").getElementsByClassName("fa-caret-down")[0],
    attention: document.getElementsByClassName("term-attention")[0],
    defaultDataInit: function() {
      this.defaults.start = this.formStart().value;
      this.defaults.length = this.formLength().value;
    },
    //DOM elements that displays term dates
    formStart: function() {
      return document.getElementById("term_start");
    },
    formLength: function() {
      return document.getElementById("term_length");
    },
    edited_term: function() {
      return document.getElementsByClassName("edited-term")[0];
    },
    view: function() {
      return {
        currentEnd: this.edited_term().getElementsByClassName("this-end")[0],
        nextStart: this.edited_term().getElementsByClassName("next-start")[0],
        nextEnd: this.edited_term().getElementsByClassName("next-end")[0]
      }
    },
    //Two booleans to check when to enable submit button
    status: {
      agreement: false,
      changed: false
    },
    //Store default dates so we know when to re-disable submit button
    defaults: {
      start: undefined,
      length: undefined
    },
    //Check whether to enable/disable submit button
    updateSubmitButton: function() {
      if (this.status.agreement && this.status.changed) {
        this.button.removeAttribute("disabled");
      } else {
        this.button.setAttribute("disabled", "disabled");
      }
    },
    //Check whether start date or term have been changed back to default value
    updateChangedStatus: function(start, length) {
      if (this.defaults.start != start || this.defaults.length != length) {
        this.status.changed = true;
      } else {
        this.status.changed = false;
      }
      this.updateSubmitButton();
      //If any settings have been updated, show the edit and attention panels
      if (this.edited_term().classList.contains("mod-hide")) {
        this.caret.classList.remove("mod-hide");
        setTimeout(function() {
          document.getElementsByClassName("edited-term")[0].classList.remove("mod-hide");
        }, 100); // Staggered animation effect
        this.attention.classList.remove("mod-hide");
      }
    },
    //Create calendar objects
    calendar: {
      currentStart: new Date(),
      currentEnd: new Date(),
      nextStart: new Date(),
      nextEnd: new Date()
    },
    //Define array that matches month number with month name
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
    /* Initialize the calender object
        Grabs the starting month of current term
        Grabs the term length
        Defines all other dates based on these two variables
    */
    calendarInit: function() {
      this.calendar.currentStart.setMonth(
        parseInt(document.getElementById("currentStart").getAttribute("data-date").substring(5, 7))
      );
      this.calendar.currentStart.setFullYear(
        document.getElementById("currentStart").getAttribute("data-date").substring(0, 4)
      );
      this.updateStartMonth();
    },
    // Check that start date is updated
    updateStartMonth: function() {
      var next_start_year = this.formStart().value.substring(0, 4);
      var next_start_month = parseInt(this.formStart().value.substring(5, 7)) - 1;
      // update next term data
      this.calendar.nextStart.setFullYear(next_start_year);
      this.calendar.nextStart.setMonth(next_start_month);
      this.calendar.nextEnd.setFullYear(next_start_year);
      this.calendar.nextEnd.setMonth(
        parseInt(next_start_month) + parseInt(this.formLength().value) - 1
      );
      // update current term data
      this.calendar.currentEnd.setFullYear(next_start_year);
      this.calendar.currentEnd.setMonth(next_start_month - 1);
      this.viewUpdate();
    },
    // Update the view with the new date values
    viewUpdate: function() {
      if (this.view().currentEnd.getAttribute("data-date") != (parseInt(this.calendar.currentEnd.getMonth()) + 1) + "_" + this.calendar.currentEnd.getFullYear()) {
        this.view().currentEnd.classList.add("edited");
      } else {
        this.view().currentEnd.classList.remove("edited");
      }
      if (this.view().nextStart.getAttribute("data-date") != (parseInt(this.calendar.nextStart.getMonth()) + 1) + "_" + this.calendar.nextStart.getFullYear()) {
        this.view().nextStart.classList.add("edited");
      } else {
        this.view().nextStart.classList.remove("edited");
      }
      if (this.view().nextEnd.getAttribute("data-date") != (parseInt(this.calendar.nextEnd.getMonth()) + 1) + "_" + this.calendar.nextEnd.getFullYear()) {
        this.view().nextEnd.classList.add("edited");
      } else {
        this.view().nextEnd.classList.remove("edited");
      }
      //If all settings have been changed back to default, re-hide edit and attention panel
      if (!this.status.changed) {
        setTimeout(function() {
          document.getElementsByClassName("edited-term")[0].classList.add("mod-hide");
          document.getElementsByClassName("current-next-arrow")[0].classList.add("mod-hide");
        }, 100);
        // this.attention.classList.add("mod-hide");
      }
      this.view().currentEnd.innerHTML = this.formatDate(this.calendar.currentEnd);
      this.view().nextStart.innerHTML = this.formatDate(this.calendar.nextStart);
      this.view().nextEnd.innerHTML = this.formatDate(this.calendar.nextEnd);
    },
    formatDate: function(date) {
      var month_index = date.getMonth();
      var month = month_index + 1;
      if (cake.lang === 'jpn') {
        var display_month = month < 10 ? "0" + month : month;
        return date.getFullYear() + "年" + display_month + "月";
      } else {
        return this.months.eng[month_index] + " " + date.getFullYear();
      }
    },
    onSubmitConfirm: function(e, message) {
      if (confirm(message)) {
        return true;
      } else {
        this.updateSubmitButton();
        // Already set onsubmit event on gl_basic.js for prevent deuble submit.
        // For keep to enable submit button when confirm cancel, stop event propagation.
        e.stopPropagation();
        return false;
      }
    }
  };

  //Page load statements//

  //Run calendar initializer
  teamSettings.calendarInit();

  teamSettings.defaultDataInit();

  //Define function when agreement checkbox is clicked
  teamSettings.agree.onclick = function() {
    if (teamSettings.agree.getAttribute("checked") == "checked") {
      teamSettings.agree.removeAttribute("checked", "checked");
      teamSettings.status.agreement = false;
      teamSettings.updateSubmitButton();
    } else {
      teamSettings.agree.setAttribute("checked", "checked");
      teamSettings.status.agreement = true;
      teamSettings.updateSubmitButton();
    }
  };

  //Define functions when term start or term length is updated
  teamSettings.formStart().onchange = function() {
    teamSettings.updateChangedStatus(teamSettings.formStart().value, teamSettings.formLength().value);
    teamSettings.updateStartMonth();
  };
  teamSettings.formLength().onchange = function() {
    teamSettings.updateChangedStatus(teamSettings.formStart().value, teamSettings.formLength().value);
    teamSettings.updateStartMonth();
  };
}

if (document.getElementById("buttonStartEvaluation")) {
    var $buttonStartEvaluation = $('#buttonStartEvaluation')
    $('input[name=term_id][type=radio]').on('change', function () {
        $buttonStartEvaluation.prop("disabled", false);
    })
    $buttonStartEvaluation.on('click', function(event) {
        event.preventDefault()

        if (window.confirm(cake.message.notice.confirm_evaluation_start)) {
            this.disabled = true
            this.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';

            var termId = $('input[name=term_id][type=radio]').filter(":checked").val();
            var req = $.post({
                url: "/api/v1/terms/" + termId + "/start_evaluation",
            }).done(function() {
                location.reload(true)
            }).fail(function(error) {
              var errorData = error.responseJSON['data'];

              if (errorData && errorData['modalContent']) {
                var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
                $modal_elm.append(errorData['modalContent']);
                $modal_elm.modal();
              } else {
                location.reload(true);
              }
            });
        }
        return false;
    });
}

// bind to dynamically added contain in unapproved goals modal
$(document).on('change', '#ignore-unapproved-goals-checkbox', function() {
  var startButton = document.querySelector('#ignore-unapproved-start-eval-btn');

  if (this.checked) {
    startButton.disabled = false;
  }
})

$(document).on('click', '#ignore-unapproved-start-eval-btn', function() {
  this.disabled = true
  this.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';
  var termId = this.dataset.termid;
  var url = "/api/v1/terms/" + termId + "/start_evaluation?ignore_unapproved=true";

  $.post({
      url: url,
  }).always(function() {
    location.reload()
  })
})
