message_app.controller("MessageDetailCtrl",
    function ($scope, $http, $translate, $sce, notificationService, getMessage, $pusher, $stateParams) {

        var message_list = getMessage.message_list;
        angular.forEach(message_list, function (val, key) {
            message_list[key].Comment.created = $sce.trustAsHtml(val.Comment.created);
        });
        $scope.message_list = message_list;

        // pusherメッセージ内容を受け取る
        var pusher = new Pusher(cake.pusher.key);
        var test_channel = pusher.subscribe('test-channel');
        test_channel.bind('new_message', function (data) {
            console.log(data)
        });

        // メッセージを送信する
        $scope.clickMessage = function () {
            var request = {
                method: 'GET',
                url: cake.url.ag +$scope.message
            };
            $http(request).then(function(response) {
                console.log(response);
            });
        }
    });
