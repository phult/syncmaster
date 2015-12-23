<?php

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author Phi Dinh Son
 * April 06, 2015 3:27:30 PM
 */
class Ticket extends Eloquent{
    //--------------------------------------------------------------------------
    //  Members
    public $timestamps = false;
    protected $table = "chi_ticket";
    protected $guarded = array("id");
    protected $fillable = array( "code", "title", "content", "status", "assignee_id", "assignee_name", "creator_id", "creator_name", "expected_end_time", "create_time");
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
