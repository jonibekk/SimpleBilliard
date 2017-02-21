app.controller("TeamMemberMainController", function ($scope, $http, $sce) {

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
            $scope.invite_msg = [];
            $scope.invite_loader = [];

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

        // cancel invite or re-invite
        $scope.settingInvite = function (index, invite_id, action_flg) {
            $scope.invite_loader[index] = true;
            var change_active_flag_url = url_list.am + invite_id + '/' + action_flg;
            $http.get(change_active_flag_url).success(function (data) {
                $scope.invite_loader[index] = false;
                if (data.error != true) {
                    $scope.invite_msg[index] = action_flg;
                    $scope.invite_list[index].Invite.del_flg = true;
                } else {
                    location.reload();
                }
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
