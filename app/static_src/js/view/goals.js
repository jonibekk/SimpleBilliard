$(window).resize(function(){
    goalDetail.setDetailsHeight();
    goalDetail.setDetailLinkPosition();
});
$(document).ready(function(){
    goalDetail.setHeadlineSize();
    goalDetail.setDetailsHeight();
    goalDetail.setDetailLinkPosition();
    $(".js-open-goal-details-info").click(function(e){goalDetail.toggleDetails(e)});
});
var goalDetail = {
    setHeadlineSize: function(){
        var headChar=$(".goal-detail-goal-name-top-section").html().length;
        if(headChar < 50){
            $(".goal-detail-goal-name-top-section").addClass("mod-largest-text");
        }else if(headChar > 200){
            $(".goal-detail-goal-name-top-section").addClass("mod-smallest-text");
        }else{
            $(".goal-detail-goal-name-top-section").css("font-size",function(){return (((40/headChar))+1.25)+"em"});
        }
    },
    setDetailLinkPosition: function(){
        var heightDifference = $(".goal-detail-avatar-wrap").outerHeight()-$(".goal-detail-avatar").outerHeight();
        if(heightDifference>24){
            $(".goal-detail-more-details-wrap").addClass("mod-raised");
        }else{
            $(".goal-detail-more-details-wrap").removeClass("mod-raised").css("margin-top",(-heightDifference+10));
        }
    },
    setDetailsHeight: function(){
        $(".goal-detail-more-details-info").css("height","inherit").attr("data-height",function(){return $(this).outerHeight()}).css("height","0").removeClass("active");
    },
    toggleDetails: function(e){
        e.preventDefault();
        $(".goal-detail-more-details-link .fa").toggleClass("active");
        if($(".goal-detail-more-details-info").outerHeight()>1){
            $(".goal-detail-more-details-info").css("height","0").removeClass("active");
        }else{
            $(".goal-detail-more-details-info").css("height",function(){return $(this).attr("data-height")}).addClass("active");
        }
    }
}