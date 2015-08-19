message_list_app.controller(
    "MessageListCtrl",
    function(
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
    ) {
        angular.forEach(getMessageList.message_list, function(val, key) {
            // メッセージ本文の改行を削除する
            this[key].Post.body = this[key].Post.body.replace(/<br \/>/g, "");

            // 自分以外のメッセージ共有者の名前と画像
            var share_users = [];
            var auth_info = getMessageList.auth_info;
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

        }, getMessageList.message_list);

        // 画像配置用の css クラス
        var imageClasses = [
            [],
            // 画像１個の場合
            [{
                'message-list-panel-card-avator-md': true
            }],
            // 画像が２個の場合
            [{
                'message-list-panel-card-avator-vl-left': true
            }, {
                'message-list-panel-card-avator-vl-right': true
            }],
            // 画像が３個の場合
            [{
                'message-list-panel-card-avator-vl-left': true
            }, {
                'message-list-panel-card-avator-sm-tr': true
            }, {
                'message-list-panel-card-avator-sm-br': true
            }],
            // 画像が４個の場合
            [{
                'message-list-panel-card-avator-sm-tl': true
            }, {
                'message-list-panel-card-avator-sm-tr': true
            }, {
                'message-list-panel-card-avator-sm-bl': true
            }, {
                'message-list-panel-card-avator-sm-br': true
            }, ]
        ];
        $scope.imageLayout = function(index, share_users_num) {
            var photo_num = (share_users_num <= 4) ? share_users_num : 4;
            return imageClasses[photo_num][index];
        };

        $scope.message_list = getMessageList.message_list;
        $scope.auth_info = getMessageList.auth_info;
        $scope.name_list_max = 3;
    });
