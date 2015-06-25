app.controller("TeamVisionController",
    function ($scope, $http, $translate, teamVisionList, $sce, $modal) {

        var team_vision_list = teamVisionList;
        angular.forEach(team_vision_list, function (val, key) {
            team_vision_list[key].TeamVision.modified = $sce.trustAsHtml(val.TeamVision.modified);
        });
        $scope.teamVisionList = team_vision_list;
        $scope.archive_flag = false;

        $scope.viewDeleteModal = function (team_vision_id, name) {

            var modalInstance = $modal.open({
                templateUrl: '/template/modal/team_vision_delete.html',
                controller: function ($scope, $modalInstance) {
                    $scope.team_vision_body = name;
                    $scope.ok = function () {
                    };
                }
            });
        }

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
