<?php

namespace Impl;

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */
use Permission;
use DB;
use Session;
use AbstractServiceImpl;
use ACLService;
use Config;

/**
 * Description of ACLServiceImpl
 *
 * @author THO Q LUONG
 * Jul 8, 2015 4:11:07 PM
 */
class ACLServiceImpl extends AbstractServiceImpl implements ACLService {

    public function createPermission($permission) {
        $newPermission = Permission::create($permission);
        //return
        return $newPermission->id;
    }

    public function updatePermission($permission) {
        Permission::where("id", "=", $permission->id)
                ->update($permission);
    }

    public function deletePermission($id) {
        Permission::where("id", "=", $id)
                ->delete();
    }

    /**
     * 1. userId
     * 2. roleId
     * 
     * @param type $filter
     */
    public function findPermissions($filter) {
        //build query
        $query = DB::table("sa_permission");
        //userId
        if (array_key_exists("userId", $filter)) {
            $query->join("sa_role", "sa_role.id", "=", "sa_permission.role_id")
                    ->join("sa_user_n_role", "sa_user_n_role.group_id", "=", "sa_role.id");
        }
        //add where condition
        if (array_key_exists("userId", $filter)) {
            $query->where("sa_user_n_role.reference_id", "=", $filter["userId"]);
        }
        if (array_key_exists("roleId", $filter)) {
            $query->where("sa_permission.role_id", "=", $filter["roleId"]);
        }
        //execute query
        $pageId = array_key_exists("pageId", $filter) ? $filter["pageId"] : 0;
        $pageSize = array_key_exists("pageSize", $filter) ? $filter["pageSize"] : 0;
        $metric = array_key_exists("metric", $filter) ? $filter["metric"] : null;
        //return
        return $this->executeQuery($query, $metric, $pageId, $pageSize);
    }

    public function hasPermission($resourceName) {
        $retVal = false;
        if ($this->mustReloadPermissions()) {
            $this->loadPermissions();
        }
        $user = Session::get("user");
        if ($user != null) {
            $allResources = Config::get("acl.resources");
            $existsResource = false;
            foreach ($allResources as $resource) {
                if ($resource["name"] == $resourceName) {
                    $existsResource = true;
                    break;
                }
            }
            if (!$existsResource) {
                $retVal = true;
            } else {
                $resourceNPermissionMap = $user->resourceNPermissionMap;
                if ($resourceNPermissionMap != null && array_key_exists($resourceName, $resourceNPermissionMap)) {
                    $permission = $resourceNPermissionMap[$resourceName];
                    $retVal = $permission->access == ACLService::ACCESS_ACCEPT;
                }
            }
        }
        //return
        return $retVal;
    }

    public function loadPermissions() {
        $permissions = $this->findPermissions(array(
            "pageId" => 0,
            "pageSize" => 0,
            "userId" => Session::get("user")->id
        ));
        //Join real permissions
        $resources = array();
        $resourceNPermissionMap = array();
        foreach ($permissions as $permission) {
            $resources[$permission->resource] = true;
        }
        foreach (array_keys($resources) as $resource) {
            $resourcePermissions = array();
            foreach ($permissions as $permission) {
                if ($permission->resource == $resource) {
                    $resourcePermissions[] = $permission;
                }
            }
            $joinedResourcePermission = $this->joinResourcePermissions($resourcePermissions);
            $resourceNPermissionMap[$resource] = $joinedResourcePermission;
        }

        //save permissions
        Session::get("user")->resourceNPermissionMap = $resourceNPermissionMap;
        Session::get("user")->loadPermissionsTime = time(); //in second
    }

    //--------------------------------------------------------------------------
    //  Utils
    private function joinResourcePermissions($resourcePermissions) {
        $retVal = $resourcePermissions[0];
        $idx = 1;
        while ($idx < count($resourcePermissions)) {
            $permission2 = $resourcePermissions[$idx];
            if ($retVal->access == ACLService::ACCESS_DENY || $permission2->access == ACLService::ACCESS_DENY) {
                $retVal->access = ACLService::ACCESS_DENY;
            } else if ($retVal->access == ACLService::ACCESS_ACCEPT || $permission2->access == ACLService::ACCESS_ACCEPT) {
                $retVal->access = ACLService::ACCESS_ACCEPT;
            } else {
                $retVal->access = ACLService::ACCESS_NOT_SET;
            }
            //next $idx
            $idx++;
        }
        //return
        return $retVal;
    }

    private function mustReloadPermissions() {
        $user = Session::get("user");
        $retVal = $user != null &&
                is_int($user->loadPermissionsTime) &&
                (time() - $user->loadPermissionsTime >= Config::get("acl.reloadPermissionsInterval") );
        //return
        return $retVal;
    }

    /**
     * 1. module
     * @param type $filter
     */
    public function findResources($filter) {
        $allResources = Config::get("acl.resources");
        return $allResources;
    }

    public function findModules($filter) {
        $allModules = Config::get("acl.modules");
        return $allModules;
    }

    public function getResourceByName($resource) {
        return $resource;
    }

}
