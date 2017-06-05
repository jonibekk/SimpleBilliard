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
    $moreInfo: $(".goal-detail-more-details-info"),
    $goalHeader: $(".goal-detail-goal-name-top-section"),
    setHeadlineSize: function(){
        if(this.$goalHeader.html()){
            var headChar=this.$goalHeader.html().length;
            if(headChar < 50){
                this.$goalHeader.addClass("mod-largest-text mod-counted");
            }else if(headChar > 200){
                this.$goalHeader.addClass("mod-smallest-text mod-counted");
            }else{
                this.$goalHeader.css("font-size",function(){return (((40/headChar))+1.25)+"em"}).addClass("mod-counted");
            }
        }
    },
    setDetailLinkPosition: function(){
        var heightDifference = $(".goal-detail-avatar-wrap").outerHeight()-$(".goal-detail-avatar").outerHeight();
        if(heightDifference>24){
            $(".goal-detail-more-details-wrap").addClass("mod-raised");
        }else{
            $(".goal-detail-more-details-wrap").removeClass("mod-raised").css("margin-top",(-heightDifference+10));
        }
        $('.goal-detail-upper-panel-main-flex').addClass("mod-ready");
    },
    setDetailsHeight: function(){
        this.$moreInfo.css("height","inherit").attr("data-height",function(){return $(this).outerHeight()}).css("height","0").removeClass("active").addClass("mod-height-read");
    },
    toggleDetails: function(e){
        e.preventDefault();
        $(".goal-detail-more-details-link .fa").toggleClass("active");
        if(this.$moreInfo.outerHeight()>1){
            this.$moreInfo.css("height","0").removeClass("active");
        }else{
            this.$moreInfo.css("height",function(){return $(this).attr("data-height")}).addClass("active");
        }
    }
}