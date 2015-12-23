<?php

namespace SyncMaster;

use Auth;
use Validator;
use Input;
use Redirect;
use Session;
use User;
use View;
use Hash;
use App;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PhuLuong
 * July 8, 2015 5:56:52 AM
 */
class HomeController extends BaseController {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    public function index() {
        $respose = array();
        $respose["serverURL"] = \Config::get("syncmaster.serverURL");
        $respose["user"] = \Session::get("user");
        return View::make("/syncmaster/home/index", $respose);
    }

    public function login() {
        if (Session::has("user")) {
            return Redirect::to("/");
        }
        if (Input::isMethod("GET")) {
            return View::make("/syncmaster/home/login");
        }
        //ELSE:
        $rules = array(
            'username' => 'required',
            'password' => 'required|min:3'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return View::make("/syncmaster/home/login", array("username" => Input::get("username"), "status" => "fail"));
        } else {
            $user = User::where("username", "=", Input::get('username'))->first();
            if (Hash::check(Input::get("password"), $user->password)) {
                Session::put("user", $user);
                //load permission
                if ($user->type == "staff") {
                    $aclService = App::make("ACLService");
                    $aclService->loadPermissions();
                }
                return Redirect::to("/");
            } else {
                return View::make("/syncmaster/home/login", array("username" => Input::get("username"), "status" => "fail"));
            }
        }
    }

    public function logout() {
        Auth::logout();
        Session::forget("user");
        return Redirect::to("/login");
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
