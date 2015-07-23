message_app.controller("MessageDetailCtrl",
    function ($scope, $http, $translate, $sce, notificationService, getMessage) {

        var message_list = getMessage.message_list;
        angular.forEach(message_list, function (val, key) {
            message_list[key].Comment.created = $sce.trustAsHtml(val.Comment.created);
        });

        $scope.message_list = message_list;
        console.log(message_list);
    });
