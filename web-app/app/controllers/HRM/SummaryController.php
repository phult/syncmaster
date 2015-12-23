<?php

namespace HRM;

use Auth;
use Validator;
use Input;
use Redirect;
use Session;
use User;
use View;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PhuLuong
 * Mar 13, 2015 5:56:52 PM
 */
class SummaryController extends BaseController {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    public function index() {
        $staffId = \Input::get("staffId", -1);
        
        $categories = \DB::table("chi_category")->where("type", "=", "doc")->get(array("id", "title"));
        return View::make("/hrm/doc/index", array("categories" => $categories));
    }

    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
