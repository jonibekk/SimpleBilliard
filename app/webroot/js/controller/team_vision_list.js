app.controller("TeamVisionController",
    function ($scope, $http, $translate, teamVisionList, $sce, $modal, notificationService) {

        var team_vision_list = teamVisionList;
        angular.forEach(team_vision_list, function (val, key) {
            team_vision_list[key].TeamVision.modified = $sce.trustAsHtml(val.TeamVision.modified);
        });
        $scope.teamVisionList = team_vision_list;
        $scope.archive_flag = false;

        $scope.viewDeleteModal = function (team_vision_id, name) {
            $modal.open({
                templateUrl: '/template/modal/team_vision_delete.html',
                controller: function ($scope, $state, $modalInstance) {
                    $scope.team_vision_body = name;
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

app.controller("TeamVisionArchiveController",
    function ($scope, $http, $translate, teamVisionArchiveList, $sce) {

        var team_vision_list = teamVisionArchiveList;
        angular.forEach(team_vision_list, function (val, key) {
            team_vision_list[key].TeamVision.modified = $sce.trustAsHtml(val.TeamVision.modified);
        });
        $scope.teamVisionList = team_vision_list;
        $scope.archive_flag = true;
    });

app.controller("TeamVisionDeleteController",
    function ($scope, $state, deleteVision, notificationService) {
        if (deleteVision === false) {
            notificationService.error('管理者以外の方には削除できません');
        }
        $state.go('vision', {team_id: $scope.team_id});
    });

app.controller("TeamVisionArchiveController",
    function ($scope, $state, setVisionArchive, notificationService) {
        if (setVisionArchive === false) {
            notificationService.error('管理者以外の方にはアーカイブできません');
        }
        $state.go('vision', {team_id: $scope.team_id});
    });
