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
use Response;
use Exception;

/**
 * @author THOQ LUONG
 * July 6, 2015
 */
class GroupService extends ServiceController {

    //--------------------------------------------------------------------------
    //  Members
    protected $tableName = "sa_group";
    protected $nTableName = "sa_n_group";

    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    public function create() {
        $groupData = $this->buildGroupData();
        $result = array();
        try {
            $group = DB::table($this->tableName)
                    ->insert($groupData);
            $result["status"] = GroupService::STATUS_SUCCESSFUL;
            $result["result"] = $group;
        } catch (Exception $e) {
            $result["status"] = GroupService::STATUS_FAIL;
            $result["message"] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function update() {
        $groupData = $this->buildGroupData();
        $result = array();
        try {
            $group = DB::table($this->tableName)
                    ->where("id", "=", $groupData["id"])
                    ->update($groupData);
            $result["status"] = GroupService::STATUS_SUCCESSFUL;
            $result["result"] = $group;
        } catch (Exception $e) {
            $result["status"] = GroupService::STATUS_FAIL;
            $result["message"] = $e->getMessage();
        }
        return Response::json($result);
    }

    /**
     * 1. referenceId
     * 2. id
     * 3. type
     * 
     * @return type
     */
    public function find() {
        if (Input::has("id")) {
            return $this->get();
        }
        //ELSE:
        try {
            $query = DB::table($this->tableName);
            if (Input::has("referenceId")) {
                $query->join($this->nTableName, $this->nTableName . ".group_id", "=", $this->tableName . ".id")
                        ->where($this->nTableName . ".reference_id", "=", Input::get("referenceId"));
            }
            if (Input::has("type")) {
                $query->where($this->tableName . ".type", "=", Input::get("type"));
            }
            $metric = Input::get("metric");
            $pageId = Input::get("pageId", 0);
            $pageSize = Input::get("pageSize", 0);
            $result = $this->executeQuery($query, $metric, $pageId, $pageSize);
        } catch (Exception $ex) {
            $result = array(
                "status" => GroupService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        //response
        return Response::json($result);
    }

    /**
     * Input: id
     * 
     * @return type: {status, message, result:id}
     */
    public function delete() {
        try {
            DB::beginTransaction();
            DB::table($this->nTableName)
                    ->where($this->nTableName . ".group_id", "=", Input::get("id"))
                    ->delete();
            DB::table($this->tableName)
                    ->where($this->tableName . ".id", "=", Input::get("id"))
                    ->delete();
            DB::commit();
            $result = array(
                "status" => GroupService::STATUS_SUCCESSFUL,
                "result" => Input::get("id")
            );
        } catch (Exception $ex) {
            DB::rollBack();
            $result = array(
                "status" => GroupService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        //response
        return Response::json($result);
    }

    public function assign() {
        $groupId = Input::get("groupId");
        $referenceId = Input::get("referenceId");
        try {
            DB::insert(array(
                "group_id" => $groupId,
                "reference_id" => $referenceId
            ));
            $result = array(
                "status" => GroupService::STATUS_SUCCESSFUL,
                "result" => array(
                    "groupId" => $groupId,
                    "referenceId" => $referenceId
                )
            );
        } catch (Exception $ex) {
            $result = array(
                "status" => GroupService::STATUS_FAIL,
                "message" => $ex->getMessage(),
                "result" => array(
                    "groupId" => $groupId,
                    "referenceId" => $referenceId
                )
            );
        }
        //response
        return Response::json($result);
    }

    public function unassign() {
        $groupId = Input::get("groupId");
        $referenceId = Input::get("referenceId");
        try {
            DB::table($this->nTableName)
                    ->where("group_id", "=", $groupId)
                    ->where("reference_id", "=", $referenceId)
                    ->delete();
            $result = array(
                "status" => GroupService::STATUS_SUCCESSFUL,
                "result" => array(
                    "groupId" => $groupId,
                    "referenceId" => $referenceId
                )
            );
        } catch (Exception $ex) {
            $result = array(
                "status" => GroupService::STATUS_FAIL,
                "result" => array(
                    "groupId" => $groupId,
                    "referenceId" => $referenceId
                ),
                "message" => $ex->getMessage()
            );
        }
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils    
    private function get() {
        try {
            $group = DB::table($this->tableName)
                    ->find(Input::get("id"));
            $result = array(
                "status" => GroupService::STATUS_SUCCESSFUL,
                "result" => $group
            );
        } catch (Exception $ex) {
            $result = array(
                "status" => GroupService::STATUS_FAIL,
                "message" => $ex->getMessage()
            );
        }
        return Response::json($result);
    }

    private function buildGroupData() {
        $retVal = Input::only("name", "description");
        if (Input::has("id")) {
            $retVal["id"] = Input::get("id");
        } else {
            $retVal["create_time"] = new DateTime();
        }
        //type
        if (Input::has("type")) {
            $retVal["type"] = Input::get("type");
        }
        return $retVal;
    }

}
