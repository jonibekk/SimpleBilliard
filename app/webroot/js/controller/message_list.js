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
        angular.forEach(getMessageList.message_list, function (val, key) {
            this[key].Post.created = $sce.trustAsHtml(val.Post.created);

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

        $scope.message_list = getMessageList.message_list;
        $scope.auth_info = getMessageList.auth_info;
        $scope.name_list_max = 3;
    });
