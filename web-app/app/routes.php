<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::group(array("namespace" => "System"), function() {
    Route::any("/system/home/login", "HomeController@login");
});

Route::group(array("before" => "auth.acl", "namespace" => "System"), function() {
    Route::any("/system/home", "HomeController@index");
    Route::any("/system/user", "UserController@index");
    Route::any("/system/role", "UserController@role");
    Route::any("/system/ticket", "HomeController@ticket");
    Route::any("/system/message", "HomeController@message");
    Route::any("/system/home/logout", "HomeController@logout");
    Route::any("/system/action/find", "ActionController@find");

    Route::any("/system/user/get-role", "UserController@getRole");
});

Route::group(array("before" => "auth.service", "namespace" => "Service"), function() {

    //User
    Route::any("/service/user/find", "UserService@find");
    Route::any("/service/user/create", "UserService@create");
    Route::any("/service/user/update", "UserService@update");
    Route::any("/service/user/delete", "UserService@delete");
    Route::any("/service/user/reset-password", "UserService@resetPassword");

    Route::any("/service/role/find", "RoleService@find");
    Route::any("/service/role/create", "RoleService@create");
    Route::any("/service/role/update", "RoleService@update");
    Route::any("/service/role/delete", "RoleService@delete");


    Route::any("/service/action/touch", "ActionService@touch");

    Route::any("/service/action/find", "ActionService@find");

    // Ticket
    Route::any("/service/ticket/find", "TicketService@find");
    Route::any("/service/ticket/find-code", "TicketService@findCode");
    Route::any("/service/ticket/create", "TicketService@create");
    Route::any("/service/ticket/update", "TicketService@update");
    Route::any("/service/ticket/delete", "TicketService@delete");
    Route::any("/service/ticket/warning", "TicketService@warning");
    // Message
    Route::any("/service/message/find", "MessageService@find");
    Route::any("/service/message/find-by-id", "MessageService@findById");
    Route::any("/service/message/create", "MessageService@create");
    Route::any("/service/message/delete", "MessageService@delete");
    
    // SyncMaster
    Route::any("/service/syncmaster/message/create", "SyncMasterService@createMessage");
});
// Sync master
Route::group(array("namespace" => "SyncMaster"), function() {
    Route::any("/login", array("as" => "syncmaster::home:login", "uses" => "HomeController@login"));
});
Route::group(array("before" => "auth.session", "namespace" => "SyncMaster"), function() {
    Route::any("/", array("as" => "syncmaster::home", "uses" => "HomeController@index"));
    Route::any("/logout", array("as" => "syncmaster::home:logout", "uses" => "HomeController@logout"));
    Route::any("/find-messages", array("as" => "syncmaster::find-messages", "uses" => "BaseController@findMessages"));
    Route::any("/find-contacts/{keyword}", array("as" => "syncmaster::find-contacts", "uses" => "BaseController@findContacts"));
});
