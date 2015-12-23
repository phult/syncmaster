<?php

namespace Service;

use Session;
use DateTime;
use Input;
use Response;
use Exception;
use DateInterval;
use DB;
use User;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author THOQ LUONG
 * Mar 30, 2015 10:23:10 PM
 */
class ActionService extends ServiceController {
    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding

    /**
     * Request: {
     *      actor_type, actor_id, target_id, target_type, code, data
     * }
     * 
     * Response format: {
     *      result: id of the action,
     *      status,
     *      message
     * }
     * @return type
     */
    public function create() {
        $actionData = $this->buildActionData();
        $result = array();
        try {
            $action = Action::create($actionData);
            $result["status"] = ActionService::STATUS_SUCCESSFUL;
            $result["result"] = $action;
        } catch ( Exception $e ) {
            $result["status"] = ActionService::STATUS_FAIL;
            $result["message"] = $e->getMessage();
        }
        return Response::json($result);
    }

    /**
     * Find action by:
     * 1. actorType
     * 2. actorId
     * 3. targetId
     * 4. targetType
     * 5. type
     * 6. createTimeFrom, createTimeTo in long format
     * 7. pageSize, pageId
     * 8. metric: "count"
     * 9. id
     * 
     * Respone format:{
     *      status,
     *      message,
     *      result: list of actions, order by id DESC
     * }
     */
    public function find() {
        $query = DB::table("sa_action");
        if ( Input::has("actorType") ) {
            $query->where("actor_type", "=", Input::get("actorType"));
        }
        if ( Input::has("actorId") ) {
            $query->where("actor_id", "=", Input::get("actorId"));
        }
        if ( Input::has("targetId") ) {
            $query->where("target_id", "=", Input::get("targetId"));
        }
        if ( Input::has("targetType") ) {
            $query->where("target_type", "=", Input::get("targetType"));
        }
        if ( Input::has("type") ) {
            $query->where("type", "=", Input::get("type"));
        }
        //create time from
        if ( Input::has("createTimeFrom") ) {
            $createTimeFrom = new DateTime();
            $createTimeFrom->setTimestamp(Input::get("createTimeFrom"));
            $query->where("create_time", ">=", $createTimeFrom);
        }
        //create time to
        if ( Input::has("createTimeTo") ) {
            $createTimeTo = new DateTime();
            $createTimeTo->setTimestamp(Input::get("createTimeTo"));
            $query->where("create_time", "<=", $createTimeTo);
        }
        if ( Input::has("id") ) {
            $query->where("id", "=", Input::get("id"));
        }
        $result = array();
        $recordsCount = $query->count();
        if ( "count" == Input::get("metric") ) {
            $result["result"] = $recordsCount;
        } else {
            $pageId = Input::get("pageId");
            $pageSize = Input::get("pageSize");
            $query->orderBy("id", "DESC");
            if ( $pageSize != 0 ) {
                $query->forPage($pageId + 1, $pageSize);
            }
            $actions = $query->get();
            $result["result"] = $actions;
            $result["pageId"] = $pageId;
            $result["recordsCount"] = $recordsCount;
            if ( $pageSize != 0 ) {
                $pagesCount = $this->recordsCountToPagesCount($recordsCount, $pageSize);
                $result["pagesCount"] = $pagesCount;
            }
        }
        $result["status"] = ActionService::STATUS_SUCCESSFUL;
        //return
        return Response::json($result);
    }

    /**
     * return all online staffIds
     * Response format:{
     *      status,
     *      result: array of staffId
     * }
     */
    public function touch() {
        $result = array();
        try {
            $userId = Session::get("user")->id;
            DB::table("chi_user")
                    ->where("id", "=", $userId)
                    ->update(["active_time" => new DateTime() ]);
            $currentDate = new DateTime();
            $currentDate->sub(new DateInterval("PT3M"));
            $onlineStaffs = DB::table("chi_user")
                    ->select("id", "full_name", "username")
                    ->where("type", "=", User::TYPE_STAFF)
                    ->where("active_time", ">=", $currentDate)
                    ->orderByRaw("RAND()")
                    ->get();
            //select ticket
            $ticketsCount = DB::table("chi_ticket")
                    ->where("status", "=", "assigned")
                    ->count();
            //select message
            $messagesCount = DB::table("sa_message")
                    ->join("sa_message_n_user", "sa_message_n_user.message_id", "=", "sa_message.id")
                    ->where("sa_message_n_user.is_deleted", "=", 0)
                    ->where("sa_message_n_user.user_id", "=", $userId)
                    ->where("sa_message_n_user.is_read", "=", 0)
                    ->count();
            $result["status"] = ActionService::STATUS_SUCCESSFUL;
            $result["result"] = array(
                "onlineStaffs" => $onlineStaffs,
                "ticketsCount" => $ticketsCount,
                "messagesCount" => $messagesCount
            );
        } catch ( Exception $e ) {
            $result["status"] = ActionService::STATUS_FAIL;
            $result["message"] = $e->getMessage();
        }
        return Response::json($result);
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    private function buildActionData() {
        $retVal = Input::only("actor_type", "actor_id"
                        , "target_id", "target_type"
                        , "type", "data");
        if ( Input::has("id") ) {
            $retVal["id"] = Input::get("id");
        } else {
            $retVal["create_time"] = new DateTime();
        }
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Inner class
}
