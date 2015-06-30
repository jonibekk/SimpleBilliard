app.controller("GroupVisionController",
    function ($scope, $http, $translate, GroupVisionList, $sce, $modal, notificationService) {

        var group_vision_list = GroupVisionList;
        angular.forEach(group_vision_list, function (val, key) {
            group_vision_list[key].GroupVision.modified = $sce.trustAsHtml(val.GroupVision.modified);
        });

        $scope.GroupVisionList = group_vision_list;
        $scope.archive_flag = false;
    });

app.controller("GroupVisionArchiveController",
    function ($scope, $http, $translate, GroupVisionArchiveList, $sce) {

        var group_vision_list = GroupVisionArchiveList;
        angular.forEach(group_vision_list, function (val, key) {
            group_vision_list[key].GroupVision.modified = $sce.trustAsHtml(val.GroupVision.modified);
        });

        $scope.GroupVisionList = group_vision_list;
        $scope.archive_flag = true;
    });
