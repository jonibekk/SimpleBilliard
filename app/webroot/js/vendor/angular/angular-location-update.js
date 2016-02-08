!function (angular, undefined) {
    'use strict';

    angular.module('ngLocationUpdate', [])
        .run(['$route', '$rootScope', '$location', function ($route, $rootScope, $location) {
            // todo: would be proper to change this to decorators of $location and $route
            $location.update_path = function (path, keep_previous_path_in_history) {
                if ($location.path() == path) return;


                var routeToKeep = $route.current;
                console.log(routeToKeep);
                $rootScope.$on('$locationChangeSuccess', function () {
                    console.log("locationChangeSuccess called.");
                    console.log($route.current);
                    $route.current = "/hoge";
                    if (routeToKeep) {
                        console.log("routeToKeep called.");
                        //$route.current = routeToKeep;
                        $route.current = "/hoge";
                        routeToKeep = null;
                    }
                });

                $location.path(path);
                if (!keep_previous_path_in_history) $location.replace();
                alert("FURU:ngLocationUpdate:"+path);
            };
        }]);

}(window.angular);
