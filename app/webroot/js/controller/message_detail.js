message_app.controller(
    "MessageDetailCtrl",
    function ($scope,
              $http,
              $sce,
              $translate,
              notificationService,
              getPostDetail,
              $pusher,
              $stateParams,
              $anchorScroll,
              $location) {


        $scope.view_flag = true;
        if (getPostDetail.auth_info.language === 'eng') {
            $translate.use('en');
        }

        // 最初のメッセージはPostテーブルから取得
        var post_detail = getPostDetail.room_info;

        // 投稿が存在しない時
        if (typeof post_detail.Post === 'undefined') {
            $scope.view_flag = false;

        } else {
            // シェアされてない人は表示をしない
            var share_users = [post_detail.Post.user_id];
            angular.forEach(getPostDetail.share_users, function (suid) {
                share_users.push(suid);
            });

            // 権限がない時
            if (share_users.indexOf(getPostDetail.auth_info.user_id) < 0) {
                $scope.view_flag = false;
            }
        }

        // 表示権限あり
        if ($scope.view_flag === false) {

            notificationService.error($translate.instant('ACCESS_MESSAGE_DETAIL_MESSAGE'));
            document.location = "/";

        } else {

            //メッセージ通知件数をカウント
            updateMessageNotifyCnt();

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
                },
                'get_red_user_model_url': '/posts/ajax_get_message_red_users/post_id:' + post_detail.Post.id
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

                data.AttachedFileHtml = $sce.trustAsHtml(data.AttachedFileHtml)

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
            $scope.clickMessage = function (event) {

                event.target.disabled = 'disabled';

                var file_redis_key = [];
                var file_ids = document.getElementsByName("data[file_id][]");
                if (file_ids.length > 0) {
                    angular.forEach(file_ids, function (fid) {
                        this.push(fid.value);
                    }, file_redis_key);
                }

                var request = {
                    method: 'POST',
                    url: cake.url.ai + $stateParams.post_id,
                    data: $.param({
                        data: {
                            body: $scope.message,
                            file_redis_key: file_redis_key,
                            _Token: {key: cake.data.csrf_token.key}
                        },
                        _method: "POST"
                    }),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                };

                $http(request).then(function (response) {
                    event.target.disabled = '';
                    message_scroll($scope.message_list.length);
                    $scope.message = "";

                    // プレビューエレメント配下の子エレメントを削除する
                    var file_preview_element = document.getElementById("messageUploadFilePreviewArea");
                    for (var i =file_preview_element.childNodes.length-1; i>=0; i--) {
                        file_preview_element.removeChild(file_preview_element.childNodes[i]);
                    }

                    // ファイルアップロード処理初期化
                    angular.forEach(file_redis_key, function(key){
                        var node = document.getElementById(key);
                        node.parentNode.removeChild(node);
                    });
                    Dropzone.instances[0].files[0] = {};
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
                            val.AttachedFileHtml = $sce.trustAsHtml(val.AttachedFileHtml)
                            this.push(val);
                        }, $scope.message_list);

                        if (response.data.message_list.length > 0) {
                            message_scroll(current_id);
                        }
                        page_num = page_num + 1;
                    });
                }
            }
        }

    });
