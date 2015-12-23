<?php

namespace Service;

use Session;
use System\Action;
use DateTime;

class ServiceController extends \Backend\BaseController {

    protected function log( $type, $targetId, $targetType, $data = "", $actorType = "user" ) {
        $actorId = 0;
        if ( $actorType == "user" ) {
            $actorId = Session::get("user")->id;
        }
        $actionData = array(
            "actor_type" => $actorType,
            "actor_id" => $actorId,
            "target_type" => $targetType,
            "target_id" => $targetId,
            "type" => $type,
            "data" => $data,
            "create_time" => new DateTime()
        );
        $action = Action::create($actionData);
        return $action->id;
    }

    protected function executeQuery( $query, $metric = null, $pageId = 0, $pageSize = 0 ) {
        $retVal = array();
        $recordsCount = $query->count();
        if ( "count" == $metric ) {
            $retVal["result"] = $recordsCount;
        } else {
            if ( $pageSize != 0 ) {
                $query->forPage($pageId + 1, $pageSize);
            }
            $result = $query->get();
            //build result
            $retVal["result"] = $result;
            $retVal["pageId"] = $pageId;
            $retVal["recordsCount"] = $recordsCount;
            if ( $pageSize != 0 ) {
                $pagesCount = $this->recordsCountToPagesCount($recordsCount, $pageSize);
                $retVal["pagesCount"] = $pagesCount;
            }
        }
        $retVal["status"] = ServiceController::STATUS_SUCCESSFUL;
        //return
        return $retVal;
    }

}
