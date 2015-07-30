message_app.controller(
    "MessageDetailCtrl",
    function (
        $scope,
        $http,
        $translate,
        notificationService,
        getPostDetail,
        getMessage,
        $pusher,
        $stateParams,
        $anchorScroll,
        $location
    ){

        $scope.post_detail = getPostDetail;

        // 最初のメッセージはPostテーブルから取得
        var message_list = [];
        var first_data = {
            Comment: {
                body: getPostDetail.Post.body,
                comment_read_count: getPostDetail.Post.post_read_count,
                created: getPostDetail.Post.created
            },
            User: {
                display_username: getPostDetail.User.display_username,
                photo_path: getPostDetail.User.photo_path
            }
        };
        message_list.push(first_data);

        angular.forEach(getMessage.message_list, function (val) {
            this.push(val);
        }, message_list);

        $scope.message_list = message_list;
        var message_scroll = function () {
            var length = $scope.message_list.length;
            $location.hash('m_'+length);
            $anchorScroll();
        };

        message_scroll();

        // pusherメッセージ内容を受け取る
        var pusher = new Pusher(cake.pusher.key);
        // TODO: Uniqueチャンネル名に指定
        var test_channel = pusher.subscribe('test-channel');
        test_channel.bind('new_message', function (data) {
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
