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
        getMessageList,
        $sce
    ){
        var message_list = getMessageList;
        angular.forEach(message_list, function (val, key) {
            this[key].Post.created = $sce.trustAsHtml(val.Post.created);
        }, message_list);

        $scope.message_list = message_list;
    });
