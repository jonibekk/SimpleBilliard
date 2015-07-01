app.controller("GroupVisionController",
    function ($rootScope, $scope, $http, $translate, GroupVisionList, LoginUserGroupId, $sce, $modal, notificationService) {

        var group_vision_list = GroupVisionList;
        angular.forEach(group_vision_list, function (val, key) {
            group_vision_list[key].GroupVision.modified = $sce.trustAsHtml(val.GroupVision.modified);

            group_vision_list[key].GroupVision.showSettingBox = false;
            if (typeof LoginUserGroupId[val.GroupVision.group_id] !== "undefined"
                || $rootScope.login_user_admin_flg === true) {
                group_vision_list[key].GroupVision.showSettingBox = true;
            }
        });

        $scope.GroupVisionList = group_vision_list;
        $scope.archive_flag = false;

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

app.controller("GroupVisionArchiveController",
    function ($scope, $http, $translate, GroupVisionArchiveList, $sce) {

        var group_vision_list = GroupVisionArchiveList;
        angular.forEach(group_vision_list, function (val, key) {
            group_vision_list[key].GroupVision.modified = $sce.trustAsHtml(val.GroupVision.modified);
        });

        $scope.GroupVisionList = group_vision_list;
        $scope.archive_flag = true;
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
        $state.go('group_vision', {team_id: $scope.team_id});
    });
