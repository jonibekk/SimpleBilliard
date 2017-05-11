var teamSettings = {
    button:document.getElementById("editTerm").getElementsByClassName("btn")[0],
    agree:document.getElementById("term_agreement"),
    start:document.getElementById("term_start"),
    length:document.getElementById("term_length"),
    status:{
        agreement:false,
        changed:false
    },
    defaults:{
        start:document.getElementById("term_start").value,
        length:document.getElementById("term_length").value
    },
    testSubmit:function(){
        if(this.status.agreement && this.status.changed){
            this.button.removeAttribute("disabled");
        }else{
            this.button.setAttribute("disabled","disabled");
        }
    },
    testDefaults:function(start,length){
        if(this.defaults.start != start || this.defaults.length != length){
            this.status.changed=true;
            this.testSubmit();
        }else{
            this.status.changed=false;
            this.testSubmit();
        }
    }
};

teamSettings.button.setAttribute("disabled","disabled");
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
teamSettings.start.onchange = function(){
    teamSettings.testDefaults(teamSettings.start.value, teamSettings.length.value);
};
teamSettings.length.onchange = function(){
    teamSettings.testDefaults(teamSettings.start.value,teamSettings.length.value);
};