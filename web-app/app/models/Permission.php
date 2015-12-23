<?php

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author THO Q L
 * July 8, 2015
 */
class Permission extends Eloquent{

    //--------------------------------------------------------------------------
    //  Members
    public $timestamps = false;
    protected $table = "sa_permission";
    protected $guarded = array("id");
    protected $fillable = array("role_id", 
        "resource", "module", "parameters", 
        "access", "update_time", "create_time");

    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
}
