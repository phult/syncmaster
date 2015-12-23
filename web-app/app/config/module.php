<?php

return array(
    "home" => array(
        "title" => "HOME",
        "url" => "/system/home",
        "activeWhen" => '$controller == "HomeController" && $action == "index"',
        "homeIcon" => "/system/image/module_home.png",
        "showInTab" => true,
        "showInHome" => true
    ),
    "message" => array(
        "title" => "Tin nhắn",
        "url" => "/system/message",
        "activeWhen" => '$action == "message"',
        "homeIcon" => "/system/image/icon-message.png",
        "showInTab" => true,
        "showInHome" => true
    ),
    "acl" => array(
        "title" => "Acl",
        "url" => "/system/user",
        "activeWhen" => '$controller == "UserController"',
        "homeIcon" => "/system/image/acl.png",
        "showInTab" => hasPermission("controllers.System.UserController.index"),
        "showInHome" => hasPermission("controllers.System.UserController.index")
    )
);
