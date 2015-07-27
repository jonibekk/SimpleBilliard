message_app.controller("MessageDetailCtrl",
    function ($scope, $http, $translate, $sce, notificationService, getMessage, $pusher) {

        var message_list = getMessage.message_list;
        angular.forEach(message_list, function (val, key) {
            message_list[key].Comment.created = $sce.trustAsHtml(val.Comment.created);
        });
        $scope.message_list = message_list;

        /*
        // メッセージが届いたらバインドする
        var client = new Pusher(cake.pusher.key);
        var pusher = $pusher(client);
        var socket_id = pusher.connection.socket_id;

        var test_channel = pusher.subscribe('test-channel');
        test_channel.bind('new_message', function (data) {
            console.log(data)
        });
        */

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
