$(document).on("click", ".js-open-goal-details-info", toggleAccordion);
$(document).ready(function(){
    $(".goal-detail-more-details-info").attr("data-height",function(){return $(this).outerHeight()}).css("height","0");
});

function toggleAccordion(e){
    e.preventDefault();
    $(".goal-detail-more-details-link .fa").toggleClass("active");
    if($(".goal-detail-more-details-info").outerHeight()>1){
        $(".goal-detail-more-details-info").css("height","0");
    }else{
        $(".goal-detail-more-details-info").css("height",function(){return $(this).attr("data-height")});
    }
}