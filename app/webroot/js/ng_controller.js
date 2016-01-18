app.controller("GroupVisionController",
    function ($rootScope, $scope, $http, $translate, GroupVisionList, LoginUserGroupId, $sce, notificationService) {

        var group_vision_list = GroupVisionList;
        var my_group_vision_count = 0;
        angular.forEach(group_vision_list, function (val, key) {
            group_vision_list[key].GroupVision.modified = $sce.trustAsHtml(val.GroupVision.modified);

            group_vision_list[key].GroupVision.showSettingBox = false;
            if (typeof LoginUserGroupId[val.GroupVision.group_id] !== "undefined"
                || $rootScope.login_user_admin_flg === true) {
                group_vision_list[key].GroupVision.showSettingBox = true;
            }

            if (LoginUserGroupId[val.GroupVision.group_id]) {
                my_group_vision_count++;
            }
        });

        $scope.GroupVisionList = group_vision_list;
        $scope.GroupVisionCount = group_vision_list.length;
        $scope.archive_flag = false;
        $scope.allMyGroupVisionCreated = Object.keys(LoginUserGroupId).length == my_group_vision_count;
    });

app.controller("GroupVisionArchiveController",
    function ($rootScope, $scope, $http, $translate, GroupVisionArchiveList, LoginUserGroupId, $sce, $modal) {

        var group_vision_list = GroupVisionArchiveList;
        angular.forEach(group_vision_list, function (val, key) {
            group_vision_list[key].GroupVision.modified = $sce.trustAsHtml(val.GroupVision.modified);

            group_vision_list[key].GroupVision.showSettingBox = false;
            if (typeof LoginUserGroupId[val.GroupVision.group_id] !== "undefined"
                || $rootScope.login_user_admin_flg === true) {
                group_vision_list[key].GroupVision.showSettingBox = true;
            }
        });

        $scope.GroupVisionList = group_vision_list;
        $scope.GroupVisionCount = group_vision_list.length;
        $scope.archive_flag = true;

        $scope.viewDeleteModal = function (group_vision_id, name) {
            $modal.open({
                templateUrl: '/template/modal/vision_delete.html',
                controller: function ($scope, $state, $modalInstance) {
                    $scope.vision_title = 'グループビジョン';
                    $scope.vision_body = name;
                    $scope.ok = function () {
                        $modalInstance.close();
                        $state.go('group_vision_delete', {group_vision_id: group_vision_id});
                    };
                    $scope.cancel = function () {
                        $modalInstance.dismiss();
                    };
                }
            });
        };
    });

app.controller("GroupVisionSetArchiveController",
    function ($scope, $state, setGroupVisionArchive, notificationService, $translate) {
        if (setGroupVisionArchive === false) {
            notificationService.error($translate.instant('GROUP_VISION.ARCHIVE_FAILED_MASSAGE'));
        } else {
            notificationService.success($translate.instant('GROUP_VISION.ARCHIVE_SUCCESS_MASSAGE'));
        }
        $state.go('group_vision', {team_id: $scope.team_id});
    });

app.controller("GroupVisionDeleteController",
    function ($scope, $state, deleteVision, notificationService, $translate) {
        if (deleteVision === false) {
            notificationService.error($translate.instant('GROUP_VISION.DELETE_FAILED_MASSAGE'));
        } else {
            notificationService.success($translate.instant('GROUP_VISION.DELETE_SUCCESS_MASSAGE'));
        }
        $state.go('group_vision_archive', {team_id: $scope.team_id, active_flg: 0});
    });

app.controller("GroupVisionDetailController",
    function ($rootScope, $scope, $http, $translate, $sce, $modal, notificationService, groupVisionDetail, LoginUserGroupId, $stateParams) {

        $scope.archive_flag = false;
        if (Number($stateParams.active_flg) === 0) {
            $scope.archive_flag = true;
        }

        groupVisionDetail.GroupVision.showSettingBox = false;
        if (typeof LoginUserGroupId[groupVisionDetail.GroupVision.group_id] !== "undefined"
            || $rootScope.login_user_admin_flg === true) {
            groupVisionDetail.GroupVision.showSettingBox = true;
        }

        groupVisionDetail.GroupVision.modified = $sce.trustAsHtml(groupVisionDetail.GroupVision.modified);
        $scope.detail = groupVisionDetail.GroupVision;

        $scope.viewDeleteModal = function (group_vision_id, name) {
            $modal.open({
                templateUrl: '/template/modal/vision_delete.html',
                controller: function ($scope, $state, $modalInstance) {
                    $scope.vision_title = 'グループビジョン';
                    $scope.vision_body = name;
                    $scope.ok = function () {
                        $modalInstance.close();
                        $state.go('group_vision_delete', {group_vision_id: group_vision_id});
                    };
                    $scope.cancel = function () {
                        $modalInstance.dismiss();
                    };
                }
            });
        };
    });
;message_app.controller(
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
                    if (typeof Dropzone.instances[0] !== "" && Dropzone.instances[0].files.length > 0) {
                        // ajax で submit するので、アップロード完了後に Dropzone のファイルリストを空にする
                        // （参照先の配列を空にするため空配列の代入はしない）
                        Dropzone.instances[0].files.length = 0;
                    }

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
                        var pushed_message_num = 0;
                        if (response.data.message_list.length < limit) {
                            pushPostMessage();
                            pushed_message_num++;
                        }

                        angular.forEach(response.data.message_list, function (val) {
                            val.AttachedFileHtml = $sce.trustAsHtml(val.AttachedFileHtml);
                            pushMessage(val);
                            pushed_message_num++;
                        }, $scope.message_list);

                        if (response.data.message_list.length > 0) {
                            // 新しいメッセージが view に確実に反映されるように少し遅らす
                            setTimeout(function () {
                                $location.hash('m_' + pushed_message_num);
                                $anchorScroll();
                            }, 1);
                            // １ページ目の表示時
                            // 確実に画面下に行くようにする
                            if ($scope.message_list.length === pushed_message_num) {
                                setTimeout(function () {
                                    bottom_scroll();
                                }, 200);
                            }
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
;message_list_app.controller(
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
    });
;app.controller("TeamMemberMainController", function ($scope, $http, $sce) {

        var url_list = cake.url;
        var active_member_list = [];
        var all_member_list = [];
        $scope.disp_active_flag = '1';

        function ActiveMemberList (member_list) {
            var active_member_list = [];
            angular.forEach(member_list, function(value){
                if (value.TeamMember.active_flg === true) {
                    this.push(value);
                }
            }, active_member_list);
            return active_member_list;
        }

        function setTeamMemberList (user_info) {
            all_member_list = user_info;
            active_member_list = ActiveMemberList(user_info);
            $scope.team_list = active_member_list;
            $scope.invite_member_url = cake.url.invite_member;
        }

        function getAllTeamMember () {
            $http.get(url_list.i).success(function (data) {
                setTeamMemberList(data.user_info);
            });
        };
        getAllTeamMember();

        function init () {
            $scope.invite_box_show = false;
            $scope.name_field_show = true;
            $scope.coach_name_field_show = false;
            $scope.group_field_show = false;
            $scope.name_field = '';
            $scope.group_id = null;

        };
        init();

        $scope.changeFilter = function () {

            var filter_name = $scope.filter_name;

            if (filter_name === 'group_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = false;
                $scope.group_field_show = true;
                $http.get(url_list.k).success(function (data) {
                    $scope.group_list = data;
                });
                getAllTeamMember();

            } else if (filter_name === 'coach_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = true;
                $scope.group_field_show = false;
                $http.get(url_list.i).success(function (data) {
                    setTeamMemberList(data.user_info);
                });

            } else if (filter_name === 'two_step') {
                init();
                $http.get(url_list.l).success(function (data) {
                    setTeamMemberList(data.user_info);
                });

            } else if (filter_name === 'team_admin') {
                init();
                $http.get(url_list.m).success(function (data) {
                    setTeamMemberList(data.user_info);
                });

            } else if (filter_name === 'invite') {
                $scope.invite_box_show = true;
                $http.get(url_list.t).success(function (data) {
                    var invite_list = data.user_info;
                    angular.forEach(invite_list, function(val, key){
                        invite_list[key].Invite.created = $sce.trustAsHtml(val.Invite.created);
                    });
                    $scope.invite_list = invite_list;
                });
            } else {
                init();
                $http.get(url_list.i).success(function (data) {
                    setTeamMemberList(data.user_info);
                });
            }
        };

        $scope.changeGroupFilter = function () {
            var get_group_url = url_list.n + $scope.group_id;
            if ($scope.group_id === null) {
                get_group_url = url_list.i;
            }
            $http.get(get_group_url).success(function (data) {
                setTeamMemberList(data.user_info);
            });
        };

        $scope.setActiveFlag = function (index, member_id, active_flg) {
            var change_active_flag_url = url_list.o + member_id + '/' + active_flg;
            $http.get(change_active_flag_url).success(function (data) {
                var active_show_flg = false;
                if (active_flg === 'ON') {
                    active_show_flg = true;
                }
                $scope.team_list[index].TeamMember.active_flg = active_show_flg;
            });
        };

        $scope.setAdminUserFlag = function (index, member_id, admin_flg) {
            var change_admin_user_flag_url = url_list.p + member_id + '/' + admin_flg;
            $http.get(change_admin_user_flag_url).success(function (data) {

                var admin_show_flg = false;
                if (admin_flg === 'ON') {
                    admin_show_flg = true;
                    $scope.admin_user_cnt = $scope.admin_user_cnt + 1;

                } else if (admin_flg === 'OFF') {
                    admin_show_flg = false;
                    $scope.admin_user_cnt = $scope.admin_user_cnt - 1;

                    if ($scope.login_user_id === $scope.team_list[index].User.id) {
                        $scope.login_user_admin_flg = false;
                    }
                }

                $scope.team_list[index].TeamMember.admin_flg = admin_show_flg;
            });
        };

        $scope.setEvaluationFlag = function (index, member_id, evaluation_flg) {

            var change_evaluation_flag_url = url_list.q + member_id + '/' + evaluation_flg;
            $http.get(change_evaluation_flag_url).success(function (data) {

                var show_evaluation_flg = false;
                if (evaluation_flg === 'ON') {
                    show_evaluation_flg = true;
                }

                $scope.team_list[index].TeamMember.evaluation_enable_flg = show_evaluation_flg;
            });

        };

        $scope.viewMemberlistChange = function() {
            if ($scope.disp_active_flag === 0) {
                $scope.team_list = all_member_list;
            } else if ($scope.disp_active_flag === 1) {
                $scope.team_list = active_member_list;
            }
        }
    }
);
;app.controller("TeamVisionController",
    function ($scope, $http, $translate, teamVisionList, isTeamAdmin, $sce, notificationService) {

        var team_vision_list = teamVisionList;
        angular.forEach(team_vision_list, function (val, key) {
            team_vision_list[key].TeamVision.modified = $sce.trustAsHtml(val.TeamVision.modified);
        });
        $scope.teamVisionList = team_vision_list;
        $scope.teamVisionCount = team_vision_list.length;
        $scope.archive_flag = false;
        $scope.isTeamAdmin = isTeamAdmin;
    });

app.controller("TeamVisionArchiveController",
    function ($scope, $http, $translate, teamVisionArchiveList, $sce, $modal) {

        var team_vision_list = teamVisionArchiveList;
        angular.forEach(team_vision_list, function (val, key) {
            team_vision_list[key].TeamVision.modified = $sce.trustAsHtml(val.TeamVision.modified);
        });
        $scope.teamVisionList = team_vision_list;
        $scope.teamVisionCount = team_vision_list.length;
        $scope.archive_flag = true;

        $scope.viewDeleteModal = function (team_vision_id, name) {
            $modal.open({
                templateUrl: '/template/modal/vision_delete.html',
                controller: function ($scope, $state, $modalInstance) {
                    $scope.vision_title = 'チームビジョン';
                    $scope.vision_body = name;
                    $scope.ok = function () {
                        $modalInstance.close();
                        $state.go('vision_delete', {team_vision_id: team_vision_id});
                    };
                    $scope.cancel = function () {
                        $modalInstance.dismiss();
                    };
                }
            });
        };

    });

app.controller("TeamVisionDeleteController",
    function ($scope, $state, deleteVision, notificationService, $translate) {
        if (deleteVision === false) {
            notificationService.error($translate.instant('TEAM_VISION.DELETE_FAILED_MASSAGE'));
        } else {
            notificationService.success($translate.instant('TEAM_VISION.DELETE_SUCCESS_MASSAGE'));
        }
        $state.go('vision_archive', {team_id: $scope.team_id, active_flg:0});
    });

app.controller("TeamVisionSetArchiveController",
    function ($scope, $state, setVisionArchive, notificationService, $translate) {
        if (setVisionArchive === false) {
            notificationService.error($translate.instant('TEAM_VISION.ARCHIVE_FAILED_MASSAGE'));
        } else {
            notificationService.success($translate.instant('TEAM_VISION.ARCHIVE_SUCCESS_MASSAGE'));
        }
        $state.go('vision', {team_id: $scope.team_id});
    });

app.controller("TeamVisionDetailController",
    function ($scope, $http, $translate, $sce, $modal, notificationService, teamVisionDetail, $stateParams) {

        $scope.archive_flag = false;
        if (Number($stateParams.active_flg) === 0) {
            $scope.archive_flag = true;
        }

        teamVisionDetail.TeamVision.modified = $sce.trustAsHtml(teamVisionDetail.TeamVision.modified);
        $scope.detail = teamVisionDetail.TeamVision;

        $scope.viewDeleteModal = function (team_vision_id, name) {
            $modal.open({
                templateUrl: '/template/modal/vision_delete.html',
                controller: function ($scope, $state, $modalInstance) {
                    $scope.vision_title = 'チームビジョン';
                    $scope.vision_body = name;
                    $scope.ok = function () {
                        $modalInstance.close();
                        $state.go('vision_delete', {team_vision_id: team_vision_id});
                    };
                    $scope.cancel = function () {
                        $modalInstance.dismiss();
                    };
                }
            });
        };
    });
