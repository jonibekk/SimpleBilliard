// Global Object for all Team Setting functions
if (document.getElementById("editTerm")) {
  var teamSettings = {
    //Some shorthands for important DOM elements
    button: document.getElementById("editTerm").getElementsByClassName("btn")[0],
    agree: document.getElementById("term_agreement"),
    caret: document.getElementById("editTerm").getElementsByClassName("fa-caret-down")[0],
    attention: document.getElementsByClassName("term-attention")[0],
    //DOM elements that displays term dates
    formStart: function() {
      return document.getElementById("term_start");
    },
    formLength: function() {
      return document.getElementById("term_length");;
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
    defaults: function() {
      return {
        start: this.formStart().value,
        length: this.formLength().value
      }
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
    testDefaults: function(start, length) {
      if (this.defaults().start != start || this.defaults().length != length) {
        this.status.changed = true;
        this.updateSubmitButton();
      } else {
        this.status.changed = false;
        this.updateSubmitButton();
      }
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
      this.startUpdate();
    },
    // Check that start date is updated
    startUpdate: function() {
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
      if (!document.getElementsByClassName("edited")[0]) {
        this.caret.classList.add("mod-hide");
        setTimeout(function() {
          document.getElementsByClassName("edited-term")[0].classList.add("mod-hide");
        }, 100);
        this.attention.classList.add("mod-hide");
      }
      this.view().currentEnd.innerHTML = this.formatDate(this.calendar.currentEnd);
      this.view().nextStart.innerHTML = this.formatDate(this.calendar.nextStart);
      this.view().nextEnd.innerHTML = this.formatDate(this.calendar.nextEnd);
    },
    formatDate: function(date) {
      var month = date.getMonth() + 1;
      if (cake.lang === 'jpn') {
        return date.getFullYear() + "年" + month + "月";
      } else {
        return this.months.eng[month - 1] + " " + date.getFullYear();
      }
    }
  };

  //Page load statements//

  //Run calendar initializer
  teamSettings.calendarInit();

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
    teamSettings.testDefaults(teamSettings.formStart().value, teamSettings.formLength().value);
    teamSettings.startUpdate();
  };
  teamSettings.formLength().onchange = function() {
    teamSettings.testDefaults(teamSettings.formStart().value, teamSettings.formLength().value);
    teamSettings.startUpdate();
  };
}
