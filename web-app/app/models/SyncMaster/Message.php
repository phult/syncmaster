<?php

namespace SyncMaster;

use Eloquent;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author Phuluong
 * Dec 10, 2015 11:54:14 PM
 */
class Message extends Eloquent {

    //--------------------------------------------------------------------------
    //  Members

    const TYPE_IN_SMS = 'in_sms';
    const TYPE_OUT_SMS = 'out_sms';
    const TYPE_IN_CALL = 'in_call';
    const TYPE_OUT_CALL = 'out_call';
    const STATUS_TODO = 'todo';
    const STATUS_DONE = 'done';

    public $timestamps = false;
    protected $table = "sm_message";
    protected $guarded = array("id");
    protected $fillable = array("contact_id", "phone", "data", "type", "status", "create_time");

    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding    
    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
