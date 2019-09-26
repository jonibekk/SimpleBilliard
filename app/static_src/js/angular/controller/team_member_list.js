app.controller("TeamMemberMainController", function ($scope, $http, $sce) {

        var url_list = cake.url;
        var active_member_list = [];
        var all_member_list = [];
        $scope.display_inactive_users = false;

        function ActiveMemberList (member_list) {
            var active_member_list = [];
            angular.forEach(member_list, function(value){
                if (value.TeamMember.status == cake.const.USER_STATUS.ACTIVE) {
                    this.push(value);
                }
            }, active_member_list);
            return active_member_list;
        }

        function setTeamMemberList (user_info) {
            all_member_list = excludeInvitedMembers(user_info);
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
            $scope.isDisabled = true;
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
                        invite_list[key].Invite.feedback = '';
                        invite_list[key].Invite.result = '';
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

        $scope.inactivate = function (index, team_member_id) {
            var inactivate_url = url_list.inactivate_team_member + team_member_id;
            $http.get(inactivate_url).success(function (data) {
                $scope.team_list[index].TeamMember.status = cake.const.USER_STATUS.INACTIVE;
            });
        };

        $scope.activate = function (index, team_member_id) {
            // TODO: Should chage get request to post request
            window.location.href = url_list.activate_team_member + team_member_id;
        };

        // cancel invite or re-invite
        $scope.updateInvite = function (index, form, invite_email, user_id) {
            $scope.invite_loader[index] = true;
            var inviteData = {'user_id':user_id, 'email': invite_email, 'data[_Token][key]': cake.data.csrf_token.key};
            $http({
                url: url_list.am,
                method: "POST",
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: $.param(inviteData)
            }).then(function successCallback(response) {
                $scope.invite_loader[index] = false;
                $scope.invite_list[index].Invite.result = 'success';
                $scope.invite_list[index].Invite.feedback = "Invite sent to "+invite_email;
                document[form].username.setAttribute('disabled','disabled');
                document[form].username.classList.remove('focused');
            },function errorCallback(response){
                $scope.invite_loader[index] = false;
                $scope.invite_list[index].Invite.result = 'error';
                $scope.invite_list[index].Invite.feedback = response.data.message;
            });
        };

        // Clear feedback message if user updates email field
        $scope.resetFeedback = function(index){
            $scope.invite_list[index].Invite.result = '';
            $scope.invite_list[index].Invite.feedback = '';
        }

        // Enable email field so user can edit before resending invite.
        $scope.editInviteEmail = function(form, index){
            document[form].username.removeAttribute('disabled');
            document[form].username.classList.add('focused');
            document[form].username.focus();
            document.getElementsByClassName("dropdown-toggle-"+index)[0].classList.add('remove');
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
            if ($scope.display_inactive_users) {
                $scope.team_list = all_member_list;
            } else {
                $scope.team_list = active_member_list;
            }
        }

        function excludeInvitedMembers(teamMember) {
          var memberList = [];
          angular.forEach(teamMember, function(value){
            if (value.TeamMember.status != cake.const.USER_STATUS.INVITED) {
              this.push(value);
            }
          }, memberList);

          return memberList;
        }
    }
);
