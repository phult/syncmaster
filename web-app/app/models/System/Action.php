<?php

namespace System;

use Eloquent;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author THOQ LUONG
 * Mar 30, 2015 10:27:46 PM
 */
class Action extends Eloquent {

    //--------------------------------------------------------------------------
    //  Members
    const TYPE_CREATE = "create";
    const TYPE_UPDATE = "update";
    const TYPE_READ = "read";
    const TYPE_DELETE = "delete";
    const TYPE_FIND = "find";

    public $timestamps = false;
    protected $table = "sa_action";
    protected $guarded = array("id");
    protected $fillable = array("actor_type", "actor_id",
        "target_id", "target_type",
        "type", "data", "create_time");

    //--------------------------------------------------------------------------
    //  Initialization
    public static function boot() {
        parent::boot();
        static::created(function($action) {
            self::triggerAsyncRequest(\Config::get("liveupdate.url"), json_encode(self::actionToArray($action)), "post");
        });
        static::updated(function($action) {
            self::triggerAsyncRequest(\Config::get("liveupdate.url"), json_encode(self::actionToArray($action)), "post");
        });
    }

    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    /**
     * Call a async request
     * @param type $url
     * @param type $params :parameters as jsonstring
     * @param type $method
     */
    private static function triggerAsyncRequest($url, $params = "", $method = "get") {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_HEADER, false);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($channel, CURLOPT_POST, $method == "post" || $method == "POST" ? true : false );
        curl_setopt($channel, CURLOPT_POSTFIELDS, $params);
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_NOSIGNAL, 1);
        curl_setopt($channel, CURLOPT_TIMEOUT_MS, 200);
        curl_exec($channel);
        curl_close($channel);
    }

    private static function actionToArray($action) {
        $retVal = $action->toArray();
        $retVal["create_time"] = $action->create_time->format('d/m/Y H:i:s');
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Inner class
}
