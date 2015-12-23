<?php

namespace Service;

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */
use DB;
use Input;
use DateTime;
use Response;

/**
 * Description of RoleService
 *
 * @author THO Q LUONG
 * Jul 9, 2015 3:55:22 PM
 */
class RoleService extends GroupService {

    public function __construct() {
        $this->tableName = "sa_role";
        $this->nTableName = "sa_user_n_role";
    }

    public function create() {
        $this->createOrUpdate();
    }

    public function update() {
        $this->createOrUpdate();
    }

    //--------------------------------------------------------------------------
    //  Utils
    private function createOrUpdate() {
        $roleData = Input::only("name", "description");
        DB::beginTransaction();
        try {
            if (Input::has("id")) {
                $roleId = Input::get("id");
                DB::table("sa_role")
                        ->where("id", "=", Input::get("id"))
                        ->update($roleData);
            } else {
                $roleId = DB::table("sa_role")
                        ->insertGetId($roleData);
            }
            //permissions
            $permissions = Input::get("permissions");
            $validPermissionResources = array();
            foreach ($permissions as $permission) {
                $permissionData = array(
                    "resource" => $permission["resource"],
                    "access" => $permission["access"],
                    "role_id" => $roleId,
                    "update_time" => new DateTime()
                );
                if (array_key_exists("id", $permission)) {
                    DB::table("sa_permission")
                            ->where("id", "=", $permission["id"])
                            ->update($permissionData);
                } else {
                    $permissionData["create_time"] = new DateTime();
                    DB::table("sa_permission")
                            ->insert($permissionData);
                }
                $validPermissionResources[] = $permission["resource"];
            }
            //delete dirty permissions
            if (Input::has("id")) {
                $deletePermissionQuery = DB::table("sa_permission")
                        ->where("role_id", "=", $roleId);
                if (count($validPermissionResources) > 0) {
                    $deletePermissionQuery->whereNotIn("resource", $validPermissionResources);
                }
                $deletePermissionQuery->delete();
            }
            DB::commit();
            $result = array(
                "status" => RoleService::STATUS_SUCCESSFUL,
                "result" => $roleId
            );
        } catch (Exception $ex) {
            DB::rollBack();
            $result = array(
                "status" => RoleService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        //response
        return Response::json($result);
    }

}
