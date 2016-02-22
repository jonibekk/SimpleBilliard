message_list_app.controller(
    "MessageListCtrl",
    function ($scope,
              $http,
              $translate,
              notificationService,
              $pusher,
              $stateParams,
              $anchorScroll,
              $location,
              $sce) {

        // 画像配置用の css クラス
        var imageClasses = [
            [],
            // 画像１個の場合
            [{
                'message-list-panel-card-avatar-md': true
            }],
            // 画像が２個の場合
            [{
                'message-list-panel-card-avatar-vl-left': true
            }, {
                'message-list-panel-card-avatar-vl-right': true
            }],
            // 画像が３個の場合
            [{
                'message-list-panel-card-avatar-vl-left': true
            }, {
                'message-list-panel-card-avatar-sm-tr': true
            }, {
                'message-list-panel-card-avatar-sm-br': true
            }],
            // 画像が４個の場合
            [{
                'message-list-panel-card-avatar-sm-tl': true
            }, {
                'message-list-panel-card-avatar-sm-tr': true
            }, {
                'message-list-panel-card-avatar-sm-bl': true
            }, {
                'message-list-panel-card-avatar-sm-br': true
            }]
        ];

        var page_num = 1;
        $scope.message_list = [];
        $scope.name_list_max = 3;
        $scope.disable_scroll = false;
        $scope.scroll_end = false;

        // mobile safariで2回クリックしないとクリックイベントが発生しないため
        // ここで1回目のダミークリックイベントを発生
        $("#GlobalForms").click();

        $scope.loadMore = function () {

            if ($scope.disable_scroll === true) return;
            $scope.disable_scroll = true;

            var request = {
                method: 'GET',
                url: cake.url.al + '/' + page_num
            };

            $http(request).then(function (response) {

                if (response.data.message_list.length > 0) {

                    var message_info = response.data;

                    angular.forEach(message_info.message_list, function (val, key) {
                        // メッセージ本文の改行を削除する
                        this[key].Post.body = this[key].Post.body.replace(/<br \/>/g, "");

                        // 自分以外のメッセージ共有者の名前と画像
                        var share_users = [];
                        var auth_info = message_info.auth_info;
                        if (val.PostUser.id != auth_info.user_id) {
                            share_users.push({
                                first_name: val.PostUser.display_first_name,
                                photo_path: val.PostUser.photo_path
                            });
                        }
                        for (var i = 0; i < val.PostShareUser.length; i++) {
                            if (val.PostShareUser[i].User.id != auth_info.user_id) {
                                share_users.push({
                                    first_name: val.PostShareUser[i].User.display_first_name,
                                    photo_path: val.PostShareUser[i].User.photo_path
                                });
                            }
                        }
                        this[key].share_users = share_users;

                        // 最新のメッセージがすべて読まれたか
                        this[key].read_comment_status = '';
                        var user_count = share_users.length;

                        if (typeof val.Comment[0] === "undefined") {
                            var comment_read_count = 0;
                        } else {
                            var comment_read_count = Number(val.Comment[0].comment_read_count);
                        }
                        this[key].comment_read_count = comment_read_count;

                        if (user_count === comment_read_count) {
                            this[key].read_comment_status = 'all_read';
                        } else if (comment_read_count === 0) {
                            this[key].read_comment_status = 'not_read';
                        }

                    }, message_info.message_list);

                    $scope.imageLayout = function (index, share_users_num) {
                        var photo_num = (share_users_num <= 4) ? share_users_num : 4;
                        return imageClasses[photo_num][index];
                    };

                    $scope.message_list = $scope.message_list.concat(message_info.message_list);
                    $scope.auth_info = message_info.auth_info;

                    page_num += 1;
                    $scope.disable_scroll = false;

                } else {
                    $scope.scroll_end = true;

                }

            });
        }

        $scope.postProcess = function(){
            $scope.disable_scroll = true;
        }
    });
