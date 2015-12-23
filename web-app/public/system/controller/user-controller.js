/**
 * Copyright (C) 2014, MEGAADS, JSC - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author SON PHI
 * Dec 15, 2014 3:27:30 PM
 * 
 * Updated by Tho Q Luong
 * July 9, 2015
 */
system.controller("UserController", UserController);

/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @param {type} $timeout
 * @returns {UserController}
 */
function UserController($scope, $http, $rootScope, $timeout) {
    //--------------------------------------------------------------------------
    // Members
    var self = this;
    $scope.controllerName = "UserController";

    $scope.users = usersResult.result;
    $scope.roles = rolesResult.result;

    $scope.mode = "create";

    $scope.types = [
        {code: "staff", name: "Staff"},
        {code: "customer", name: "Customer"}
    ];

    $scope.user = {
        roles: []
    };

    $scope.failMessage = null;

    $scope.filter = null;
    var lastFilterHash = null;

    //--------------------------------------------------------------------------
    //  Initialization

    this.__proto__ = new BaseController($scope, $http, $rootScope);

    function initialize() {
        $scope.filter = {
            pageId: 0,
            pageSize: 40,
            type: $scope.getByCode($scope.types, "staff"),
            withRole: true
        };
        lastFilterHash = $scope.calculateHash($scope.filter, ["pageId"]);
    }

    $scope.openCreateDialog = function () {
        var roles = [];
        angular.copy($scope.roles, roles);
        $scope.user = {
            type: $scope.getByCode($scope.types, "staff"),
            roles: roles
        };
        $scope.failMessage = null;
        $scope.mode = "create";
        $('#userDialog').modal({
            modal: true,
            persist: true,
            position: [30, 0],
            autoPosition: true
        });
    };

    $scope.resetPassword = function (userId) {
        $http.post("/service/user/reset-password", {userId: userId}).success(function (data) {
            if (data.status == "fail") {
                $timeout(function () {
                    alert("Reset password fail. Message: " + data.message);
                });
            } else {
                $timeout(function () {
                    alert("New password is: " + data.result);
                });
            }
        });
    };

    $scope.openUpdateDialog = function (user) {
        load(user.id, function (user) {
            $scope.user = user;
        });
        $scope.failMessage = null;
        $scope.mode = "update";
        $('#userDialog').modal({
            modal: true,
            persist: true,
            position: [30, 0],
            autoPosition: true
        });
    };

    $scope.openDetailDialog = function (user) {
        load(user.id, function (user) {
            $scope.user = user;
        });
        $scope.failMessage = null;
        $scope.mode = "detail";
        $('#userDialog').modal({
            modal: true,
            persist: true,
            position: [30, 0],
            autoPosition: true
        });
    };

    $scope.find = function () {
        var filterData = {
            pageId: $scope.filter.pageId,
            pageSize: 40,
            withRole: true
        };

        //code
        if (!$scope.isEmptyString($scope.filter.code)) {
            filterData.code = $scope.filter.code;
        }
        //search
        if (!$scope.isEmptyString($scope.filter.search)) {
            filterData.search = $scope.toFriendlyString($scope.filter.search);
        }
        //role
        if ($scope.filter.role != null) {
            filterData.roleId = $scope.filter.role.id;
        }
        //type
        if ($scope.filter.type != null) {
            filterData.type = $scope.filter.type.code;
        }

        var filterHash = $scope.calculateHash(filterData, ["pageId"]);
        if (lastFilterHash != filterHash) {
            lastFilterHash = filterHash;
            filterData.pageId = 0;
        }

        $scope.showLoading();
        $http.post("/service/user/find", filterData)
                .success(function (data) {
                    $scope.isFinding = false;
                    $scope.users = data.result;
                    $scope.pagesCount = data.pagesCount;
                    $scope.filter.pageId = data.pageId;

                    //hide loading
                    $scope.hideLoading();
                });
    };

    $scope.delete = function (user) {
        $timeout(function () {
            var confirmMessage = "Are you sure you want to delete user \"" + (user.full_name) + "\" ?";
            var yes = confirm(confirmMessage);
            if (!yes) {
                return;
            }
            //ELSE:
            $scope.showLoading();
            $http.post("/service/user/delete", {id: user.id})
                    .success(function (data) {
                        $scope.hideLoading();
                        if (data.status == "fail") {
                            $timeout(function () {
                                alert("Delete user fail. Message: " + data.message);
                            });
                        } else {
                            $scope.find();
                        }
                    });
        });
    };

    $scope.reset = function () {
        $scope.filter = {
            pageId: 0,
            pageSize: 40,
            type: $scope.getByCode($scope.types, "staff"),
            withRole: true
        };
        $scope.find();
    };

    $scope.save = function () {
        var userData = buildUserData();
        var failMessages = validateUserData(userData);
        if (failMessages.length > 0) {
            $scope.failMessage = failMessages[0];
            return;
        }
        //ELSE:
        var url = $scope.mode == "create" ? "/service/user/create" : "/service/user/update";
        $scope.showLoading();
        $http.post(url, userData).success(function (data) {
            $scope.hideLoading();
            if (data.status == "fail") {
                $timeout(function () {
                    alert(($scope.mode == "create" ? "Create" : "Update")
                            + " user fail. Message: "
                            + data.message);
                });
            }else{
                $.modal.close();
                $scope.find();
            }
        });
    };

    //--------------------------------------------------------------------------
    //  Utils

    function load(userId, callback) {
        var rawUser = $scope.getByField($scope.users, "id", userId);
        var user = {};
        angular.copy(rawUser, user);
        //roles
        user.roles = [];
        angular.copy($scope.roles, user.roles);
        user.roles.forEach(function (role) {
            var selected = false;
            rawUser.roles.forEach(function (tempRole) {
                if (tempRole.id == role.id) {
                    selected = true;
                }
            });
            role.selected = selected;
        });
        //type
        user.type = $scope.getByCode($scope.types, rawUser.type);
        //callback
        callback(user);
    }

    function buildUserData() {
        var retVal = {
            username: $scope.user.username,
            type: $scope.user.type.code,
            full_name: $scope.user.full_name,
            email: $scope.user.email,
            roleIds: []
        };
        //id
        if ($scope.user.id != null) {
            retVal.id = $scope.user.id;
        }
        //code
        if (!$scope.isEmptyString($scope.user.code)) {
            retVal.code = $scope.user.code;
        }
        //roles
        $scope.user.roles.forEach(function (role) {
            if (role.selected) {
                retVal.roleIds.push(role.id);
            }
        });
        //return
        return retVal;
    }

    function validateUserData(userData) {
        var retVal = [];
        var isValidUsername = userData.username != null && userData.username.match(/^[a-z0-9_]{5,}$/gi) != null;
        if (!$scope.isEmptyString(userData.username) && !isValidUsername) {
            retVal.push("Username is invalid. Available characters: a-z0-9_");
        }
        if (userData.type == "staff"
                && ($scope.isEmptyString(userData.username) || !isValidUsername)) {
            retVal.push("Require username value. Available characters: a-z0-9_");
        }
        if (userData.type == "staff"
                && !$scope.isValidEmail(userData.email)) {
            retVal.push("Email is empty or invalid");
        }
        if ($scope.isEmptyString(userData.full_name)) {
            retVal.push("Require full name");
        }
        //return
        return retVal;
    }

    initialize();
}