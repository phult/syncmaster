<?php

namespace Service;

use DateTime;
use DB;
use Exception;
use Input;
use Response;
use Session;
use System\Message;
use System\MessageNUser;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PHI DINH SON
 * April 15, 2015 11:27:30 AM
 */
class MessageService extends ServiceController {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    
    public function find() {
        if ( Session::has("user") ) {
            $user = Session::get("user");
            $type = "";
            if (Input::exists("type") && Input::get("type")) {
                $type = Input::get("type");
            }
            $query = DB::table("sa_message");
            if ($type == "inbox") {
                $query->join("sa_message_n_user", "sa_message_n_user.message_id", "=", "sa_message.id")
                    ->where("sa_message_n_user.is_deleted", "=", 0)
                    ->where("sa_message_n_user.user_id", "=", $user->id)
                    ->addSelect("sa_message_n_user.is_read");
            }
            if ($type == "send") {
                $query->where("sa_message.sender_id", "=", $user->id)
                    ->where("sa_message.is_deleted_by_sender", "=", 0);
            }
            $recordsCount = $query->count();
            $pageId = Input::get("pageId");
            $pageSize = Input::get("pageSize");
            if ( $pageSize != 0 ) {
                $query->forPage($pageId + 1, $pageSize);
            }
            $messages = $query->orderBy("id", "desc")->addSelect("sa_message.*")->get();
            if ( $pageSize != 0 ) {
                $pagesCount = $this->recordsCountToPagesCount($recordsCount, $pageSize);
            }
            $sumMessageNotRead = 0;
            foreach ($messages as $message) {
                $message->receivers = json_decode($message->receivers);
                if ($type == "inbox" && $message->is_read != 1) {
                    $sumMessageNotRead++;
                }
            }
            $result = [
                "status" => MessageService::STATUS_SUCCESSFUL,
                "messages" => $messages,
                "sumMessageNotRead" => $sumMessageNotRead,
                "pageId" => $pageId,
                "pagesCount" => $pagesCount
            ];
        } else {
            $result = [
                "status" => MessageService::STATUS_FAIL
            ];
        }
        return Response::json($result);
    }
    
    public function create() {
        $result = array();
        if ( Session::has("user") ) {
            $user = Session::get("user");
            $input = Input::all();
            DB::beginTransaction();
            if (Input::exists("receiverData") && Input::get("receiverData")) {
                $input["receivers"] = json_encode($input["receiverData"]);
            }
            $input["is_deleted_by_sender"] = 0;
            $input["create_time"] = new DateTime();
            $input["sender_id"] = $user->id;
            $input["sender_name"] = $user->full_name;
            try {
                $message = Message::create($input);
                foreach ( Input::get("receiverData") as $receiver ) {
                    $messageNUser = [
                        "user_id" => $receiver["id"],
                        "message_id" => $message->id,
                        "is_read" => 0,
                        "is_deleted" => 0
                    ];
                    MessageNUser::create($messageNUser);
                }
                DB::commit();
                $result["status"] = MessageService::STATUS_SUCCESSFUL;
                $result["result"] = $message;
            } catch ( Exception $ex ) {
                DB::rollBack();
                $result["status"] = MessageService::STATUS_FAIL;
                $result["message"] = $ex->getMessage();
            }
        } else {
            $result["status"] = MessageService::STATUS_FAIL;
        }
        //return
        return Response::json($result);
    }

    /**
     * Input: id
     * @return type
     */
    public function delete() {
        if ( Session::has("user") ) {
            DB::beginTransaction();
            $user = Session::get("user");
            $result = array();
            $id = Input::get("id");
            $message = Message::find($id);
            $messageNUser = MessageNUser::where("user_id", "=", $user->id)->where("message_id", "=", $id)->first();
            if ($message) {
                try {
                    if ($message->sender_id == $user->id) {
                        $message->is_deleted_by_sender = 1;
                        $message->save();
                    }
                    if ($messageNUser) {
                        $messageNUser->is_deleted = 1;
                        $messageNUser->save();
                    }
                    DB::commit();
                    $result["status"] = MessageService::STATUS_SUCCESSFUL;
                    $result["result"] = $id;
                } catch ( Exception $ex ) {
                    DB::rollBack();
                    $result["status"] = MessageService::STATUS_FAIL;
                    $result["message"] = $ex->getMessage();
                }
            } else {
                $result["status"] = MessageService::STATUS_FAIL;
            }
        } else {
            $result["status"] = MessageService::STATUS_FAIL;
        }
        //return
        return Response::json($result);
    }
    
    public function findById() {
        if ( Session::has("user") ) {
            $user = Session::get("user");
            $messageId = Input::get("messageId");
            $isRead = Input::get("isRead");
            if ($messageId) {
                //update is_read
                if ($isRead == 0) {
                    $messageNUser = MessageNUser::where("user_id", "=", $user->id)->where("message_id", "=", $messageId)->first();
                    if ($messageNUser) {
                        $messageNUser->is_read = 1;
                        $messageNUser->save();
                    }
                }
                //select message
                $query = DB::table("sa_message");
                $query->join("sa_message_n_user", "sa_message_n_user.message_id", "=", "sa_message.id")
                        ->where("sa_message_n_user.is_deleted", "=", 0)
                        ->where("sa_message_n_user.message_id", "=", $messageId)
                        ->select("sa_message_n_user.is_read", "sa_message_n_user.user_id as user_id", "sa_message.*");
                $messages = $query->get();
                $receivers = [];
                foreach ($messages as $message) {
                    $receiverData = [
                        "is_read" => $message->is_read,
                        "id" => $message->user_id
                    ];
                    foreach (json_decode($message->receivers) as $receiver) {
                        if ($receiver->id == $message->user_id) {
                            $receiverData["full_name"] = $receiver->full_name;
                        }
                    }
                    $receivers[] = $receiverData;
                }
                $messageData = [
                    "subject" => $messages[0]->subject,
                    "sender_name" => $messages[0]->sender_name,
                    "receivers" => $receivers,
                    "content" => $messages[0]->content
                ];
                
                $result = [
                    "status" => "successful",
                    "message" => $messageData
                ];
            } else {
                $result = [
                    "status" => "fail"
                ];
            }
            //return
            return Response::json($result);
        }
    }
    //--------------------------------------------------------------------------
    //  Inner class
}
