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
class NTicket extends Eloquent{
    //--------------------------------------------------------------------------
    //  Members
    public $timestamps = false;
    protected $table = "chi_n_ticket";
    protected $guarded = array("id");
    protected $fillable = array( "ticket_id", "reference_id", "reference_type", "reference_name", "create_time");
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
