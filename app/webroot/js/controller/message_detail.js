message_app.controller(
    "MessageDetailCtrl",
    function (
        $scope,
        $http,
        $translate,
        $sce,
        notificationService,
        getMessage,
        $pusher,
        $stateParams,
        $anchorScroll,
        $location
    ){

        var message_scroll = function () {
            var length = $scope.message_list.length;
            $location.hash('m_'+length);
            $anchorScroll();
        };

        var message_list = getMessage.message_list;
        angular.forEach(message_list, function (val, key) {
            message_list[key].Comment.created = $sce.trustAsHtml(val.Comment.created);
        });
        $scope.message_list = message_list;
        message_scroll();

        // pusherメッセージ内容を受け取る
        var pusher = new Pusher(cake.pusher.key);
        // TODO: Uniqueチャンネル名に指定
        var test_channel = pusher.subscribe('test-channel');
        test_channel.bind('new_message', function (data) {
            data.Comment.created = $sce.trustAsHtml(data.Comment.created);
            $scope.$apply($scope.message_list.push(data));
            message_scroll();
        });

        // メッセージを送信する
        $scope.clickMessage = function () {
            var request = {
                method: 'GET',
                url: cake.url.ai +$scope.message
            };
            $http(request).then(function(response) {});
            $scope.message = "";
        };

    });
