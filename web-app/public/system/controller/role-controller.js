/**
 * Copyright (C) 2015, MEGAADS, JSC - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author THO Q LUONG
 * 
 * July 10, 2015
 */


system.controller("RoleController", RoleController);


/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @param {type} $timeout
 * @returns {UserController}
 */
function RoleController($scope, $http, $rootScope, $timeout) {
    //--------------------------------------------------------------------------
    // Members
    var self = this;
    $scope.controllerName = "RoleController";

    $scope.roles = rolesResult.result;

    $scope.mode = "create";
    $scope.resources = resources;
    $scope.modules = modules;

    $scope.accesses = [
        {code: "not_set", name: "Not set"},
        {code: "accept", name: "Accept"},
        {code: "deny", name: "Deny"}
    ];

    $scope.form = {
        filter: {
            pageId: 0,
            pageSize: 10
        },
        permissionPagesCount: -1
    };

    $scope.failMessage = null;


    //--------------------------------------------------------------------------
    //  Initialization

    this.__proto__ = new BaseController($scope, $http, $rootScope);

    function initialize() {
    }

    $scope.openCreateDialog = function () {
        $scope.role = {
            allPermissions: []
        };

        $scope.resources.forEach(function (resource) {
            $scope.role.allPermissions.push({
                resource: resource.name,
                access: $scope.getByCode($scope.accesses, "not_set")
            });
        });
        sortPermissionsByResource($scope.role.allPermissions);

        $scope.failMessage = null;

        resetFormFilter();

        $scope.findPermissions();

        $scope.mode = "create";
        $('#roleDialog').modal({
            modal: true,
            persist: true,
            position: [30, 0],
            autoPosition: true
        });
    };

    $scope.openUpdateDialog = function (role) {
        $scope.showLoading();
        load(role.id, function (role) {

            $scope.role = role;

            $scope.hideLoading();

            $scope.failMessage = null;

            resetFormFilter();

            $scope.role.allPermissions = $scope.role.permissions;

            $scope.mode = "update";
            $('#roleDialog').modal({
                modal: true,
                persist: true,
                position: [30, 0],
                autoPosition: true
            });
        });
    };

    $scope.openDetailDialog = function (role) {

        $scope.showLoading();
        load(role.id, function (role) {

            $scope.role = role;

            $scope.hideLoading();

            $scope.failMessage = null;

            resetFormFilter();
            $scope.role.allPermissions = $scope.role.permissions;
            $scope.findPermissions();

            $scope.mode = "detail";
            $('#roleDialog').modal({
                modal: true,
                persist: true,
                position: [30, 0],
                autoPosition: true
            });
        });
    };

    $scope.delete = function (role) {
        $timeout(function () {
            var confirmMessage = "Are you sure you want to delete role \"" + (role.name) + "\" ?";
            var yes = confirm(confirmMessage);
            if (!yes) {
                return;
            }
            //ELSE:
            $scope.showLoading();
            $http.post("/service/role/delete", {id: role.id})
                    .success(function (data) {
                        $scope.hideLoading();
                        if (data.status == "fail") {
                            $timeout(function () {
                                alert("Delete role fail. Message: " + data.message);
                            });
                        } else {
                            $scope.find();
                        }
                    });
        });
    };

    $scope.find = function () {
        $http.post("/service/role/find", {pageId: 0, pageSize: 0})
                .success(function (data) {
                    $scope.roles = data.result;
                });
    };

    $scope.save = function () {
        var roleData = buildRoleData();
        var failMessages = validateRoleData(roleData);
        if (failMessages.length > 0) {
            $scope.failMessage = failMessages[0];
            return;
        }
        //ELSE:
        var url = $scope.mode == "create" ? "/service/role/create" : "/service/role/update";
        $scope.showLoading();
        $http.post(url, roleData).success(function (data) {
            $scope.hideLoading();
            if (data.status == "fail") {
                $timeout(function () {
                    alert(($scope.mode == "create" ? "Create" : "Update")
                            + " role fail. Message: "
                            + data.message);
                });
            } else {
                $.modal.close();
                $scope.find();
            }
        });
    };

    $scope.findPermissions = function () {
        var permissions = [];
        $scope.role.allPermissions.forEach(function (permission) {
            var resourceName = permission.resource;
            var module = $scope.getByField($scope.resources, "name", resourceName).module;

            var b = $scope.form.filter.module == null ||
                    ($scope.form.filter.module != null &&
                            module == $scope.form.filter.module.code);
            var b1 = $scope.form.filter.access == null ||
                    ($scope.form.filter.access != null &&
                            permission.access.code == $scope.form.filter.access.code);
            if (b && b1) {
                permissions.push(permission);
            }
        });
        sortPermissionsByResource(permissions);
        $scope.form.permissionPagesCount = calculatePagesCount(permissions.length,
                $scope.form.filter.pageSize);
        $scope.role.permissions = extractPage(permissions,
                $scope.form.filter.pageId,
                $scope.form.filter.pageSize);
    };
    //--------------------------------------------------------------------------
    //  Utils

    function extractPage(list, pageId, pageSize) {
        var retVal = [];
        for (var idx = pageId * pageSize; idx < (pageId + 1) * pageSize && idx < list.length; idx++) {
            retVal.push(list[idx]);
        }
        //return
        return retVal;
    }

    function calculatePagesCount(recordsCount, pageSize) {
        var retVal = Math.floor(recordsCount / pageSize);
        if (recordsCount % pageSize != 0) {
            retVal++;
        }
        //return
        return retVal;
    }

    function resetFormFilter() {
        $scope.form.filter = {
            pageId: 0,
            pageSize: $scope.form.filter.pageSize
        };
    }

    function sortPermissionsByResource(permissions) {
        permissions.sort(function (p1, p2) {
            if (p1.resource > p2.resource) {
                return 1;
            } else if (p1.resource < p2.resource) {
                return -1;
            } else {
                return 0;
            }
        });
    }

    function buildRoleData() {
        var retVal = {
            name: $scope.role.name,
            description: $scope.role.description,
            permissions: []
        };
        if ($scope.role.id != null) {
            retVal.id = $scope.role.id;
        }
        $scope.role.allPermissions.forEach(function (permission) {
            if (permission.access.code != "not_set") {
                var permissionData = {
                    resource: permission.resource,
                    access: permission.access.code
                };
                if (permission.id != null) {
                    permissionData.id = permission.id;
                }
                retVal.permissions.push(permissionData);
            }
        });
        //return
        return retVal;
    }

    function validateRoleData(roleData) {
        var retVal = [];
        if ($scope.isEmptyString(roleData.name)) {
            retVal.push("Name is required field");
        }
        if ($scope.isEmptyString(roleData.description)) {
            retVal.push("Description is required field");
        }
        return retVal;
    }

    function load(id, callback) {
        $http.post("/system/user/get-role", {id: id}).success(function (roleWithPermissions) {
            var role = roleWithPermissions;

            $scope.resources.forEach(function (resource) {
                var existed = false;
                role.permissions.forEach(function (permission) {
                    if (resource.name == permission.resource) {
                        existed = true;
                    }
                });
                if (!existed) {
                    role.permissions.push({
                        resource: resource.name,
                        access: "not_set"
                    });
                }
            });
            role.permissions.forEach(function (permission) {
                permission.access = $scope.getByCode($scope.accesses, permission.access);
            });
            sortPermissionsByResource(role.permissions);
            role.allPermissions = role.permissions;
            //callback
            callback(role);
        });
    }

    initialize();
}