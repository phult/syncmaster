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
class Contact extends Eloquent {

    //--------------------------------------------------------------------------
    //  Members

    public $timestamps = false;
    protected $table = "sm_contact";
    protected $guarded = array("id");
    protected $fillable = array("name", "phone", "email");

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
