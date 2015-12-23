<?php

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PHI DINH SON
 * Dec 4, 2014 4:54:14 PM
 */
class Parameter extends Eloquent{
    //--------------------------------------------------------------------------
    //  Members
    
    const TYPE_IMAGE = 'image';
    const DATA_TYPE_JSON = 'json';
    
    public $timestamps = false;
    protected $table = "chi_parameter";
    protected $guarded = array("id");
    protected $fillable = array( "key2", "value2", "type", "data_type", "title", "description", "update_time", "create_time");
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    
    public static $rules = array(
        'key2' => 'required',
        'type' => 'required',
        'data_type' => 'required',
    );
    
    public static function validate($data) {
        return Validator::make($data, static::$rules);
    }
    
    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
