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
 * @author PHI DINH SON
 * April 15, 2015 10:27:46 PM
 */
class Message extends Eloquent {

    //--------------------------------------------------------------------------
    //  Members

    public $timestamps = false;
    protected $table = "sa_message";
    protected $guarded = array( "id" );
    protected $fillable = array( "sender_id", "receivers",
        "subject", "content",
        "is_deleted_by_sender", "create_time", "sender_name" );

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
