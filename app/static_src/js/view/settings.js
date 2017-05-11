// Global Object for all Team Setting functions
var teamSettings = {
    //Some shorthands for important DOM elements
    button:document.getElementById("editTerm").getElementsByClassName("btn")[0],
    agree:document.getElementById("term_agreement"),
    editedTerm:document.getElementById("editTerm").getElementsByClassName("edited-term")[0],
    caret:document.getElementById("editTerm").getElementsByClassName("fa-caret-down")[0],
    formStart:document.getElementById("term_start"),
    formLength:document.getElementById("term_length"),
    //DOM elements that displays term dates
    view:{
        currentEnd:document.getElementsByClassName("edited-term")[0].getElementsByClassName("this-end")[0],
        nextStart:document.getElementsByClassName("edited-term")[0].getElementsByClassName("next-start")[0],
        nextEnd:document.getElementsByClassName("edited-term")[0].getElementsByClassName("next-end")[0]
    },
    //Two booleans to check when to enable submit button
    status:{
        agreement:false,
        changed:false
    },
    //Store default dates so we know when to re-disable submit button
    defaults:{
        start:document.getElementById("term_start").value,
        length:document.getElementById("term_length").value
    },
    //Check whether to enable/disable submit button
    testSubmit:function(){
        if(this.status.agreement && this.status.changed){
            this.button.removeAttribute("disabled");
        }else{
            this.button.setAttribute("disabled","disabled");
        }
    },
    //Check whether start date or term have been changed back to default value
    testDefaults:function(start,length){
        if(this.defaults.start != start || this.defaults.length != length){
            this.status.changed=true;
            this.testSubmit();
        }else{
            this.status.changed=false;
            this.testSubmit();
        }
        if(this.editedTerm.classList.contains("mod-hide")){
            this.caret.classList.remove("mod-hide");
            setTimeout(function(){
                document.getElementById("editTerm").getElementsByClassName("edited-term")[0].classList.remove("mod-hide");
            },100);
        }
    },
    //Create calendar objects 
    calendar:{
        currentStart:new Date(),
        currentEnd:new Date(),
        nextStart:new Date(),
        nextEnd:new Date()
    },
    //Define array that matches month number with month name
    months:[
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
    ],
    /* Initialize the calender object
        Grabs the starting month of current term
        Grabs the term length
        Defines all other dates based on these two variables
    */
    calendarInit:function(){
        this.calendar.currentStart.setMonth(
            parseInt(document.getElementById("currentStart").getAttribute("data-date").substring(0,2))-1
        );
        this.calendar.currentStart.setYear(
            document.getElementById("currentStart").getAttribute("data-date").substring(3,7)
        );
        this.startUpdate();
    },
    // Check that term data is updated
    termUpdate:function(){
        this.calendar.nextEnd.setMonth(
            this.calendar.nextStart.getMonth()+(parseInt(document.getElementById("term_length").value)-1)
        );
        this.viewUpdate();
    },
    // Check that start date is updated
    startUpdate:function(){
        this.calendar.nextStart.setMonth(
            parseInt(document.getElementById("term_start").value.substring(0,2))-1
        );
        this.calendar.nextStart.setYear(
            parseInt(document.getElementById("term_start").value.substring(3,7))
        );
        this.calendar.nextEnd.setYear(
            this.calendar.nextStart.getFullYear()
        );
        this.calendar.nextEnd.setMonth(
            this.calendar.nextStart.getMonth()
        );
        this.calendar.currentEnd.setMonth(
            this.calendar.nextStart.getMonth()-1
        );
        this.termUpdate();
    },
    // Update the view with the new date values
    viewUpdate:function(){
        if(this.view.currentEnd.getAttribute("data-date")!=(parseInt(this.calendar.currentEnd.getMonth())+1)+"_"+this.calendar.currentEnd.getFullYear()){
            this.view.currentEnd.classList.add("edited");
        }else{
            this.view.currentEnd.classList.remove("edited");
        }
        if(this.view.nextStart.getAttribute("data-date")!=(parseInt(this.calendar.nextStart.getMonth())+1)+"_"+this.calendar.nextStart.getFullYear()){
            this.view.nextStart.classList.add("edited");
        }else{
            this.view.nextStart.classList.remove("edited");
        }
        if(this.view.nextEnd.getAttribute("data-date")!=(parseInt(this.calendar.nextEnd.getMonth())+1)+"_"+this.calendar.nextEnd.getFullYear()){
            this.view.nextEnd.classList.add("edited");
        }else{
            this.view.nextEnd.classList.remove("edited");
        }
        if(!document.getElementsByClassName("edited")[0]){
            this.caret.classList.add("mod-hide");
            setTimeout(function(){
                document.getElementById("editTerm").getElementsByClassName("edited-term")[0].classList.add("mod-hide");
            },100);
        }
        this.view.currentEnd.innerHTML = this.months[this.calendar.currentEnd.getMonth()]+" "+this.calendar.currentEnd.getFullYear();
        this.view.nextStart.innerHTML = this.months[this.calendar.nextStart.getMonth()]+" "+this.calendar.nextStart.getFullYear(); 
        this.view.nextEnd.innerHTML = this.months[this.calendar.nextEnd.getMonth()]+" "+this.calendar.nextEnd.getFullYear(); 
    }
};

//Page load statements//

//Disable submit button
teamSettings.button.setAttribute("disabled","disabled");
//Hide Edited term box for now
teamSettings.editedTerm.classList.add("mod-hide");
teamSettings.caret.classList.add("mod-hide");

//Run calendar initializer
teamSettings.calendarInit();

//Define function when agreement checkbox is clicked
teamSettings.agree.onclick = function(){
    if(teamSettings.agree.getAttribute("checked")=="checked"){
        teamSettings.agree.removeAttribute("checked","checked");
        teamSettings.status.agreement=false;
        teamSettings.testSubmit();
    }else{
        teamSettings.agree.setAttribute("checked","checked");
        teamSettings.status.agreement=true;
        teamSettings.testSubmit();
    }
};

//Define functions when term start or term length is updated
teamSettings.formStart.onchange = function(){
    teamSettings.testDefaults(teamSettings.formStart.value, teamSettings.formLength.value);
    teamSettings.startUpdate();
};
teamSettings.formLength.onchange = function(){
    teamSettings.testDefaults(teamSettings.formStart.value,teamSettings.formLength.value);
    teamSettings.startUpdate();
};