<?php

namespace System;

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */
use Request;
use Route;
use View;
use ACLService;
use Input;
use Response;

/**
 * Description of UserController
 *
 * @author THO Q LUONG
 * Jul 9, 2015 10:37:52 AM
 */
class UserController extends BaseController {

    //--------------------------------------------------------------------------
    //  Members
    private $aclService;

    //--------------------------------------------------------------------------
    //  Initialization
    public function __construct(ACLService $aclService) {
        $this->aclService = $aclService;
    }

    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    public function index() {
        //get staffs
        $usersRequest = Request::create("/service/user/find", "GET", array(
                    "pageId" => 0,
                    "pageSize" => 40,
                    "type" => "staff",
                    "withRole" => true
        ));
        Request::replace($usersRequest->input());
        $usersResponse = Route::dispatch($usersRequest);
        $usersResult = $usersResponse->getContent();

        //get roles
        $rolesRequest = Request::create("/service/role/find", "GET", array(
                    "pageId" => 0,
                    "pageSize" => 0,
        ));
        Request::replace($rolesRequest->input());
        $rolesResponse = Route::dispatch($rolesRequest);
        $rolesResult = $rolesResponse->getContent();


        //return
        return View::make("/system/user/index", array("usersResult" => $usersResult,
                    "rolesResult" => $rolesResult));
    }

    public function role() {
        //get roles
        $rolesRequest = Request::create("/service/role/find", "GET", array(
                    "pageId" => 0,
                    "pageSize" => 0,
        ));
        Request::replace($rolesRequest->input());
        $rolesResponse = Route::dispatch($rolesRequest);
        $rolesResult = $rolesResponse->getContent();

        //resources
        $resources = $this->aclService->findResources(array(
            "pageId" => 0,
            "pageSize" => 0
        ));
        //modules
        $modules = $this->aclService->findModules(array(
            "pageId" => 0,
            "pageSize" => 0
        ));
        //return
        return View::make("/system/user/role/index", array("rolesResult" => $rolesResult,
                    "modules" => $modules,
                    "resources" => $resources));
    }

    public function getRole() {
        $roleId = Input::get("id");
        $permissions = $this->aclService->findPermissions(array("pageId" => 0,
            "pageSize" => 0,
            "roleId" => $roleId));

        $roleRequest = Request::create("/service/role/find", "GET", array(
                    "id" => $roleId
        ));
        Request::replace($roleRequest->input());
        $roleResponse = Route::dispatch($roleRequest);
        $roleResultInString = $roleResponse->getContent();

        $roleResult = json_decode($roleResultInString);

        $role = $roleResult->result;
        $role->permissions = $permissions;
        //render
        return Response::json($role);
    }

}
