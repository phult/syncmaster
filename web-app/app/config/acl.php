<?php

return array(
    "reloadPermissionsInterval" => 10 * 60,//in second
    "modules" => array(
        array("code" => "acl","name" =>"Access control list"),
    ),
    "resources" => array(
        array("name" => "controllers.System.UserController.index",
            "module" => "acl"
        ),
        array("name" => "controllers.System.UserController.role",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.UserService.find",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.UserService.create",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.UserService.update",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.UserService.delete",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.UserService.resetPassword",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.RoleService.find",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.RoleService.create",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.RoleService.update",
            "module" => "acl"
        ),
        array("name" => "controllers.Service.RoleService.delete",
            "module" => "acl"
        )
    )
);
