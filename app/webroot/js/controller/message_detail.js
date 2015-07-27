message_app.controller("MessageDetailCtrl",
    function ($scope, $http, $translate, $sce, notificationService, getMessage, $pusher) {

        var message_list = getMessage.message_list;
        angular.forEach(message_list, function (val, key) {
            message_list[key].Comment.created = $sce.trustAsHtml(val.Comment.created);
        });

        $scope.message_list = message_list;
        //console.log(message_list);

        var client = new Pusher(cake.pusher.key);
        var pusher = $pusher(client);

        var my_channel = pusher.subscribe('test-channel');
        my_channel.bind('new_message', function (data) {
            console.log(data)
        })
    });
