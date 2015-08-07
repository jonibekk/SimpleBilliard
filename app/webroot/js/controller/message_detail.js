message_app.controller(
    "MessageDetailCtrl",
    function ($scope,
              $http,
              $translate,
              notificationService,
              getPostDetail,
              $pusher,
              $stateParams,
              $anchorScroll,
              $location) {

        $scope.view_flag = true;

        // 最初のメッセージはPostテーブルから取得
        var post_detail = getPostDetail.room_info;

        // 投稿が存在しない時
        if (typeof post_detail.Post === 'undefined') {
            $scope.view_flag = false;
            document.location = "/";
        } else {

            // シェアされてない人は表示をしない
            var share_users = [post_detail.Post.user_id];
            angular.forEach(getPostDetail.share_users, function (suid) {
                share_users.push(suid);
            });

            // 権限がない時
            if (share_users.indexOf(getPostDetail.auth_info.user_id) < 0) {
                $scope.view_flag = false;
                document.location = "/";
            }
        }

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
        $scope.message_list = message_list;

        var current_id = 0;
        var message_scroll = function (id) {
            var message_id = id;
            if (current_id === 0) {
                message_id = $scope.message_list.length;
            }
            current_id = message_id;
            $location.hash('m_' + message_id);
            $anchorScroll();
        };

        // pusherメッセージ内容を受け取る
        var pusher = new Pusher(cake.pusher.key);
        var test_channel = pusher.subscribe('message-channel-' + $stateParams.post_id);
        test_channel.bind('new_message', function (data) {
            // 既読処理
            var read_comment_id = data.Comment.id;
            var request = {
                method: 'GET',
                url: cake.url.ak + $stateParams.post_id + '/' + read_comment_id
            };
            $http(request).then(function (response) {
            });

            // メッセージ表示
            $scope.$apply($scope.message_list.push(data));
            message_scroll(current_id);
        });

        // pusherから既読されたcomment_idを取得する
        test_channel.bind('read_message', function (comment_id) {
            var read_box = document.getElementById("mr_" + comment_id).innerText;
            document.getElementById("mr_" + comment_id).innerText = Number(read_box) + 1;
        });

        // メッセージを送信する
        $scope.clickMessage = function () {
            var request = {
                method: 'GET',
                url: cake.url.ai + $stateParams.post_id + '/' + $scope.message
            };
            $http(request).then(function (response) {
                message_scroll($scope.message_list.length);
                $scope.message = "";
            });
        };

        var limit = 10;
        var page_num = 1;
        $scope.loadMore = function () {
            if (document.getElementById("message_box").scrollTop === 0) {
                var request = {
                    method: 'GET',
                    url: cake.url.ah + $stateParams.post_id + '/' + limit + '/' + page_num
                };
                $http(request).then(function (response) {
                    angular.forEach(response.data.message_list, function (val) {
                        this.push(val);
                    }, $scope.message_list);
                    if (response.data.message_list.length > 0) {
                        message_scroll(current_id);
                    }
                    page_num = page_num + 1;
                });
            }
        }
    });
