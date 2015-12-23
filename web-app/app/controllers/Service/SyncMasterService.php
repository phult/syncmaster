<?php

namespace Service;

use Session;
use DateTime;
use Input;
use Response;
use Exception;
use DateInterval;
use DB;
use SyncMaster\Message;

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
class SyncMasterService extends ServiceController {
    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding

    public function createMessage() {
        $actionData = $this->buildActionData();
        $result = array();
        try {
            $action = Message::create($actionData);
            $result["status"] = ActionService::STATUS_SUCCESSFUL;
            $result["result"] = $action;
        } catch (Exception $e) {
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
        $retVal = Input::only("contact_id", "phone", "data", "type", "status");
        if (Input::has("id")) {
            $retVal["id"] = Input::get("id");
        } else {
            $retVal["create_time"] = new DateTime();
        }
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Inner class
}
