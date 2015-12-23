<?php

namespace System;

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
 * @author THOQ LUONG
 * Mar 13, 2015 5:56:52 PM
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
        return View::make("/system/home/index");
    }

    public function user() {
        return View::make("/system/user/index");
    }

    public function ticket() {
        if (Session::has("user")) {
            return View::make("/system/ticket/index");
        } else {
            return Redirect::to("/system/home/login");
        }
    }

    public function message() {
        if (Session::has("user")) {
            $users = User::select("id", "username", "full_name")->where("type", "=", User::TYPE_STAFF)->get();
            return View::make("/system/message/index", array("users" => $users));
        } else {
            return Redirect::to("/system/home/login");
        }
    }

    public function login() {
        if (Session::has("user")) {
            return Redirect::to("/system/home");
        }
        if (Input::isMethod("GET")) {
            return View::make("/system/home/login");
        }
        //ELSE:
        $rules = array(
            'username' => 'required',
            'password' => 'required|min:3'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return View::make("/system/home/login", array("username" => Input::get("username"), "status" => "fail"));
        } else {
            $user = User::where("username", "=", Input::get('username'))->first();
            if (Hash::check(Input::get("password"), $user->password)) {
                Session::put("user", $user);
                //load permission
                if ($user->type == "staff") {
                    $aclService = App::make("ACLService");
                    $aclService->loadPermissions();
                }
                return Redirect::to("/system/home");
            } else {
                return View::make("/system/home/login", array("username" => Input::get("username"), "status" => "fail"));
            }
        }
    }

    public function logout() {
        Auth::logout();
        Session::forget("user");
        return Redirect::to("/system/home/login");
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
