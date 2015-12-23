/**
 * Copyright (C) 2014, MEGAADS, JSC - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PhuLuong
 * April 15, 2015 2:27:30 PM
 */
system.controller("SystemNotificationController", NotificationController);
/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function NotificationController($scope, $http, $rootScope,$timeout) {
    $scope.controllerName = "NotificationController";
    this.__proto__ = new BaseController($scope, $http, $rootScope);
    $scope.modules = modules;
    $scope.count = [];
    $scope.update = function () {
        $timeout(function () {
            for (var key in $scope.modules) {
                var module = $scope.modules[key];
                if (module.notificationUrl != null) {
                    $http.post(module.notificationUrl, {}).success(function (data) {
                        $scope.count[data.module] = data.count;
                    });
                }
            }
        });
    };
    $scope.update();
}