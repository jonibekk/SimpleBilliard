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

        // 最初のメッセージはPostテーブルから取得
        var post_detail = getPostDetail.room_info;
        var message_list = [];
        var first_data = {
            Comment: {
                body: post_detail.Post.body,
                comment_read_count: post_detail.Post.post_read_count,
                created: post_detail.Post.created
            },
            User: {
                display_username: post_detail.User.display_username,
                photo_path: post_detail.User.photo_path
            }
        };
        message_list.push(first_data);

        // スレッド情報
        $scope.auth_info = getPostDetail.auth_info;
        $scope.post_detail = post_detail;

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
