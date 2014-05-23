/**
 * Created by bigplants on 5/23/14.
 */
function getLocalDate() {
    var getTime = jQuery.now();
    var date = new Date(getTime);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    //noinspection UnnecessaryLocalVariableJS
    var fullDate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
    return fullDate;
}
