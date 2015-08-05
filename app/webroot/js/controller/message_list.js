message_list_app.controller(
    "MessageListCtrl",
    function (
        $scope,
        $http,
        $translate,
        notificationService,
        $pusher,
        $stateParams,
        $anchorScroll,
        $location,
        getMessageList
    ){
        console.log(getMessageList);
        $scope.message_list = getMessageList;
    });
