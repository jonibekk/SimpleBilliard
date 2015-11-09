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

        // TODO: 添付ファイルのプレビューを表示するために一時的に高さを少なくする
        var input_box_height = 280;

        // onloadの場合
        $scope.$on('$viewContentLoaded', function () {
            var $m_box = $("#message_box");
            $m_box.css("height", window.innerHeight - input_box_height);
            $m_box.animate({scrollTop: window.innerHeight}, 500);
        });

        // ブラウザリサイズの場合、入力フォームサイズ変更+オートスクロール
        $(window).resize(function () {
            $scope.$apply(function () {
                $("#message_box").css("height", window.innerHeight - input_box_height);
                bottom_scroll();
            });
        });
        //データが存在しない場合はエラーメッセージを出力しホームにリダイレクト
        if (Object.keys(getPostDetail).length === 0) {
            notificationService.error($translate.instant('ACCESS_MESSAGE_DETAIL_MESSAGE'));
            document.location = "/";
            return;
        }

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

            var limit = 10, page_num = 1, message_list = [], post_msg_view_flag = false, loaded_message_ids = [];

            // スレッド情報
            $scope.auth_info = getPostDetail.auth_info;
            $scope.post_detail = post_detail;
            $scope.message_list = message_list;
            $scope.first_share_user = getPostDetail.first_share_user;

            var bottom_scroll = function () {
                var $m_box = $("#message_box");
                $m_box.animate({scrollTop: $m_box[0].scrollHeight}, 300);
            };

            var pushMessage = function (message) {
                if (!loaded_message_ids[message.Comment.id]) {
                    loaded_message_ids[message.Comment.id] = true;
                    $scope.message_list.push(message);
                }
            };

            // pusherメッセージ内容を受け取る
            var pusher = new Pusher(cake.pusher.key);
            var pusher_channel = pusher.subscribe('message-channel-' + $stateParams.post_id);
            pusher_channel.bind('new_message', function (data) {
                // 既読処理
                var read_comment_id = data.Comment.id;
                var request = {
                    method: 'GET',
                    url: cake.url.ak + $stateParams.post_id + '/' + read_comment_id
                };
                $http(request).then(function (response) {
                });

                data.AttachedFileHtml = $sce.trustAsHtml(data.AttachedFileHtml);

                // メッセージ表示
                pushMessage(data);
                bottom_scroll();
            });

            // pusherから既読されたcomment_idを取得する
            //pusher_channel.bind('read_message', function (comment_id) {
            //    var read_box = document.getElementById("mr_" + comment_id).innerText;
            //    document.getElementById("mr_" + comment_id).innerText = Number(read_box) + 1;
            //    bottom_scroll();
            //});

            // メッセージを送信する
            $scope.clickMessage = function (event,val) {
                if($scope.flag)
                {
                    return;
                }
                $scope.flag = true;
                if(val==='like')
                {
                    $scope.message = '[like]';
                }
                event.target.disabled = 'disabled';
                var sendMessageLoader = document.getElementById("SendMessageLoader");
                sendMessageLoader.style.display = "inline-block";

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
                            socket_id: pusher.connection.socket_id,
                            _Token: {key: cake.data.csrf_token.key}
                        },
                        _method: "POST"
                    }),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                };

                $http(request).then(function (response) {
                    $scope.flag = false;
                    event.target.disabled = '';
                    sendMessageLoader.style.display = 'none';
                    $scope.message = "";
                    // プレビューエレメント配下の子エレメントを削除する
                    var file_preview_element = document.getElementById("messageUploadFilePreviewArea");
                    for (var i = file_preview_element.childNodes.length - 1; i >= 0; i--) {
                        file_preview_element.removeChild(file_preview_element.childNodes[i]);
                    }

                    // ファイルアップロード処理初期化
                    angular.forEach(file_redis_key, function (key) {
                        var node = document.getElementById(key);
                        node.parentNode.removeChild(node);
                    });
                    document.getElementById("message_text_input").focus();

                    // 未読メッセージ一覧を取得（送信直後の自身のメッセージを含む）
                    var urlParams = [$stateParams.post_id, 10, 1];
                    if ($scope.message_list.length >= 2) {
                        // 最新メッセージの送信時間をパラメータに追加
                        var lastMessage = $scope.message_list.reduce(function (a, b) {
                            if (!a.Comment) {
                                return b;
                            }
                            if (!b.Comment) {
                                return a;
                            }
                            return parseInt(a.Comment.created, 10) > parseInt(b.Comment.created, 10) ? a : b;
                        });
                        urlParams.push(parseInt(lastMessage.Comment.created, 10));
                    }
                    var request = {
                        method: 'GET',
                        url: cake.url.ah + urlParams.join('/'),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    };
                    $http(request).then(function (response) {
                        angular.forEach(response.data.message_list, function (val) {
                            val.AttachedFileHtml = $sce.trustAsHtml(val.AttachedFileHtml);
                            pushMessage(val);
                        }, $scope.message_list);
                        // メッセージ表示
                        bottom_scroll();
                    });
                }).error(function(){
                    $scope.flag = false;
                });
            };

            var pushPostMessage = function () {
                if (post_msg_view_flag === false) {
                    message_list.push({
                        AttachedFileHtml: $sce.trustAsHtml(post_detail.AttachedFileHtml),
                        Comment: {
                            body: post_detail.Post.body,
                            comment_read_count: post_detail.Post.post_read_count,
                            created: post_detail.Post.created
                        },
                        User: {
                            id: post_detail.User.id,
                            display_username: post_detail.User.display_username,
                            photo_path: post_detail.User.photo_path
                        },
                        'get_red_user_model_url': '/posts/ajax_get_message_red_users/post_id:' + post_detail.Post.id
                    });
                    post_msg_view_flag = true;
                }
            };

            $scope.loadMore = function () {
                if (document.getElementById("message_box").scrollTop === 0) {
                    var request = {
                        method: 'GET',
                        url: cake.url.ah + $stateParams.post_id + '/' + limit + '/' + page_num
                    };
                    $http(request).then(function (response) {
                        if (response.data.message_list.length < limit) {
                            pushPostMessage();
                        }

                        angular.forEach(response.data.message_list, function (val) {
                            val.AttachedFileHtml = $sce.trustAsHtml(val.AttachedFileHtml);
                            pushMessage(val);
                        }, $scope.message_list);

                        if (response.data.message_list.length > 0) {
                            // 新しいメッセージが view に確実に反映されるように少し遅らす
                            setTimeout(function () {
                                $location.hash('m_' + response.data.message_list.length);
                                $anchorScroll();
                            }, 1);
                        }
                        page_num = page_num + 1;
                    });
                }
            };

            $scope.add_messenger_user = function () {
                $("#post_messenger").val(post_detail.Post.id);
                $("#MessageFormShareUser").show();
                $("#message_add_list").append($("#MessageFormShareUser"));
            }
        }

        // 戻るボタンのURL
        $scope.message_list_url = cake.url.message_list;

    });
