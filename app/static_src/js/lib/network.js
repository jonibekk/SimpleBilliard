/**
 * This file contains script to network reachability
 */
"use strict";

var network_reachable = true;

$(function () {
    console.log("LOADING: network.js");

    //Monitoring of the communication state of App Server | Appサーバーの通信状態の監視
    window.addEventListener("online", function () {
        updateNotifyCnt();
        updateMessageNotifyCnt();
        network_reachable = true;
    }, false);

    window.addEventListener("offline", function () {
        network_reachable = false;
    }, false);
});

function isOnline() {
    console.log("network.js: isOnline");
    return Boolean(network_reachable);
}