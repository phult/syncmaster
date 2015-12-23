<?php

namespace System;

use Input;
use Request;
use Route;
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
 * @author THOQ LUONG
 * Apr 25, 2015 4:59:17 PM
 */
class ActionController extends BaseController {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding

    public function find() {
        $staffs = User::select("id", "username", "full_name")
                ->where("type", "=", "staff")
                ->get();
        $shippers = User::select("id", "username", "full_name")
                ->where("type", "=", "shipper")
                ->get();
        $locations = Location::all();
        $request = Request::create("/service/action/find", "POST", array( "targetType" => Input::get("targetType"),
                    "targetId" => Input::get("targetId") ));
        Request::replace($request->input());
        $response = Route::dispatch($request);
        $actionsResult = $response->getContent();

        return View::make("/system/action/index", array( "actions" => $actionsResult,
                    "staffs" => $staffs,
                    "shippers" => $shippers,
                    "locations" => $locations ));
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
