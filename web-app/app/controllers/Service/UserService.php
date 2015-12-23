<?php

namespace Service;

use DateTime;
use DB;
use Exception;
use Hash;
use Input;
use Response;
use Session;
use User;

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PHI DINH SON
 * Mar 13, 2015 11:45:07 AM
 * 
 * Updated by THO Q L
 * July 7, 2015
 */
class UserService extends ServiceController {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    /**
     * Find user by:
     * 1. id OR
     * 2. search
     * 3. createTimeFrom, createTimeTo
     * 4. roleIds
     * 5. roleId
     * 6. withRole
     * 7. status
     * 8. statuses
     * 9. type
     * 10.types
     */
    public function find() {
        try {
            //Initialize query
            $query = DB::table("chi_user")
                    ->select(array(
                "id", "code", "username", "type", "full_name", "create_time", "active_time", "about", "email"
            ));
            if (Input::has("roleId") || Input::has("roleIds")) {
                $query->join("sa_user_n_role", "sa_user_n_role.reference_id", "=", "chi_user.id");
            }
            //fill where condtion
            if (Input::exists("code") && Input::get("code") != "") {
                $query->where("chi_user.code", "LIKE", "%" . Input::get("code") . "%");
            }
            if (Input::exists("type") && Input::get("type") != "") {
                $query->where("chi_user.type", "=", Input::get("type"));
            }
            //createTimeFrom
            $createTimeFrom = $this->stringToDateOrNull(Input::get("createTimeFrom"));
            if ($createTimeFrom != null) {
                $query->where("chi_user", ">=", $createTimeFrom);
            }
            //createTimeTo
            $createTimeTo = $this->stringToDateOrNull(Input::get("createTimeTo"));
            if ($createTimeTo != null) {
                $query->where("chi_user", "<=", $createTimeTo);
            }
            //roleId
            if (Input::has("roleId")) {
                $query->where("sa_user_n_role.reference_id", "=", Input::get("roleId"));
            }
            //roleIds
            if (Input::has("roleIds")) {
                $query->whereIn("sa_user_n_role.reference_id", Input::get("roleIds"));
            }
            //types
            if (Input::has("types")) {
                $types = array(-1);
                if (count(Input::get("types")) > 0) {
                    $types = Input::get("types");
                }
                $query->whereIn("chi_user.type", $types);
            }
            //type
            if (Input::has("type")) {
                $query->where("chi_user.type", "=", Input::get("type"));
            }
            //status
            if (Input::has("status")) {
                $query->where("chi_user.status", "=", Input::get("status"));
            }
            //statuses
            if (Input::has("statuses")) {
                $statuses = array(-1);
                if (count(Input::get("statuses")) > 0) {
                    $statuses = Input::get("statuses");
                }
                $query->whereIn("chi_user.status", $statuses);
            }
            //search
            if (Input::has("search")) {
                $search = Input::get("search");
                $query->where("chi_user.search", "LIKE", "%" . $this->getFriendlyString($search) . "%");
            }
            //order
            $query->orderBy("chi_user.id", "DESC");
            //response
            $pageId = Input::get("pageId", 0);
            $pageSize = Input::get("pageSize", 0);
            $metric = Input::get("metric");
            $result = $this->executeQuery($query, $metric, $pageId, $pageSize);
            //fillRoles
            if (Input::has("withRole")) {
                $this->fillRoles($result);
            }
        } catch (Exception $ex) {
            $result = array();
            $result["status"] = UserService::STATUS_FAIL;
            $result["message"] = $ex->getMessage();
        }
        return Response::json($result);
    }

    /**
     * 
     * @return type
     */
    public function create() {
        return $this->createOrUpdate();
    }

    public function update() {
        return $this->createOrUpdate();
    }

    /**
     * Input: newPassword
     * @return type
     */
    public function changePassword() {
        $input = Input::all();
        try {
            $user = User::where("username", "=", Session::get("user")->username)->first();
            $user->password = Hash::make($input["newPassword"]);
            $user->save();
            $result = array(
                "status" => UserService::STATUS_SUCCESSFUL
            );
        } catch (Exception $ex) {
            $result = array(
                "status" => UserService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        //response
        return Response::json($result);
    }

    public function resetPassword() {
        $userId = Input::get("userId");
        try {
            $newPassword = rand(1000000, 9999999);
            $encryptedPassword = Hash::make($newPassword);
            User::where("id", "=", $userId)
                    ->update(array(
                        "password" => $encryptedPassword
            ));
            $result = array(
                "status" => UserService::STATUS_SUCCESSFUL,
                "result" => $newPassword
            );
        } catch (Exception $ex) {
            $result = array(
                "status" => UserService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        //response
        return Response::json($result);
    }

    public function delete() {
        $id = array(Input::get("id"));
        try {
            User::destroy($id);
            $result = array(
                "status" => UserService::STATUS_SUCCESSFUL,
                "id" => $id
            );
        } catch (Exception $ex) {
            $result = array(
                "status" => UserService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        //return
        return Response::json($result);
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    private function createOrUpdate() {
        $userData = Input::only("code", "username", "type", "full_name", "email");
        if (!Input::has("id")) {
            $userData["create_time"] = new DateTime();
        }
        $userData["update_time"] = new DateTime();
        DB::beginTransaction();
        try {
            //create or update
            $userId = Input::get("id");
            if (!Input::has("id")) {
                $user = User::create($userData);
                $userId = $user->id;
            } else {
                User::where("id", "=", Input::get("id"))
                        ->update($userData);
            }
            //render code
            if (!Input::has("code")) {
                $code = "X";
                switch ($userData["type"]) {
                    case User::TYPE_CUSTOMER: {
                            $code = "C";
                            break;
                        }
                    case User::TYPE_SHIPPER: {
                            $code = "S";
                            break;
                        }
                    case User::TYPE_STAFF: {
                            $code = "A";
                            break;
                        }
                    case User::TYPE_SUPPLIER: {
                            $code = "P";
                            break;
                        }
                }
                $code .= str_pad($userId, 6 - strlen($code), "0", STR_PAD_LEFT);
                User::where("id", "=", $userId)
                        ->update(array(
                            "code" => $code
                ));
            }
            //roles
            $newRoleIds = Input::get("roleIds");
            $oldRoleIds = array();
            if (Input::has("id")) {
                $roleIdResult = DB::table("sa_user_n_role")
                        ->select("group_id AS role_id")
                        ->where("reference_id", "=", $userId)
                        ->get();
                foreach ($roleIdResult as $roleIdItem) {
                    $oldRoleIds[] = $roleIdItem->role_id;
                }
            }
            foreach ($newRoleIds as $newRoleId) {
                $existed = false;
                foreach ($oldRoleIds as $oldRoleId) {
                    if ($newRoleId == $oldRoleId) {
                        $existed = true;
                    }
                }
                if (!$existed) {
                    DB::table("sa_user_n_role")
                            ->insert(array(
                                "group_id" => $newRoleId,
                                "reference_id" => $userId
                    ));
                }
            }
            //delete invalid roleIds
            $invalidRoleIds = array_diff($oldRoleIds, $newRoleIds);
            if (count($invalidRoleIds) > 0) {
                DB::table("sa_user_n_role")
                        ->where("reference_id", "=", $userId)
                        ->whereIn("group_id", $invalidRoleIds)
                        ->delete();
            }
            //search
            $user = User::find($userId);
            $search = $this->calculateSearch($user);
            User::where("id", "=", $userId)
                    ->update(array("search" => $search));
            //commit
            DB::commit();
            $result = array(
                "status" => UserService::STATUS_SUCCESSFUL,
                "result" => $userId
            );
        } catch (Exception $e) {
            DB::rollBack();
            $result = array(
                "status" => UserService::STATUS_FAIL,
                "message" => $e->getMessage()
            );
        }
        //render
        return Response::json($result);
    }

    private function fillRoles($userResult) {
        $userIds = array();
        if ($userResult["status"] == UserService::STATUS_FAIL) {
            return;
        }
        //ELSE:
        $users = $userResult["result"];
        foreach ($users as $user) {
            $user->roles = array();
            $userIds[] = $user->id;
        }
        //get roles
        $roles = DB::table("sa_role")
                ->join("sa_user_n_role", "sa_user_n_role.group_id", "=", "sa_role.id")
                ->select(array(
                    "sa_role.id AS id",
                    "sa_role.name AS name",
                    "sa_user_n_role.reference_id AS user_id"
                ))
                ->whereIn("sa_user_n_role.reference_id", $userIds)
                ->get();
        foreach ($roles as $role) {
            foreach ($users as $user) {
                if ($user->id == $role->user_id) {
                    $user->roles[] = array(
                        "id" => $role->id,
                        "name" => $role->name
                    );
                }
            }
        }
    }

    private function stringToDateOrNull($dateString) {
        if ($dateString == null || strlen($dateString) == 0) {
            return null;
        }
        $retVal = DateTime::createFromFormat("d/m/Y", $dateString);
        $retVal->setTime(0, 0, 0);
        return $retVal;
    }

    private function calculateSearch($user) {
        $retVal = $user->full_name . " " . $user->code;
        if ($user->username != null && $user->username != "") {
            $retVal .= " " . $user->username;
        }
        if ($user->phone != null && $user->phone != "") {
            $retVal .= " " . $user->phone;
        }
        //return
        return $this->getFriendlyString($retVal);
    }

    //--------------------------------------------------------------------------
    //  Inner class
}
