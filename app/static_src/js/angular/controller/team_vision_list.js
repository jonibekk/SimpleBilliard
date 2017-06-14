app.controller("TeamVisionController",
    function ($scope, $http, $translate, teamVisionList, isTeamAdmin, $sce) {

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
