<?php

/*
  |--------------------------------------------------------------------------
  | Application & Route Filters
  |--------------------------------------------------------------------------
  |
  | Below you will find the "before" and "after" events for the application
  | which may be used to do any work before or after a request into your
  | application. Here you may also register your custom route filters.
  |
 */

App::before(function($request) {
    
});


App::after(function($request, $response) {
    
});

/*
  |--------------------------------------------------------------------------
  | Authentication Filters
  |--------------------------------------------------------------------------
  |
  | The following filters are used to verify that the user of the current
  | session is logged into this application. The "basic" filter easily
  | integrates HTTP Basic authentication for quick, simple checking.
  |
 */
Route::matched(function($route, $request) {
    $allSharedData = View::getShared();
    if (is_array($allSharedData) && (!array_key_exists("controller", $allSharedData) || !array_key_exists("action", $allSharedData))) {
        $matches = null;
        preg_match("/([A-Za-z0-9]+(?:Controller|Service))@([A-Za-z0-9]+)/", Route::currentRouteAction(), $matches);
        $controller = $matches[1];
        $action = $matches[2];
        View::share("controller", $controller);
        View::share("action", $action);
    }
});


Route::filter('auth', function() {
    
});

Route::filter("auth.service", function() {
    
});


Route::filter("auth.acl", function() {
    $routeAction = Route::currentRouteAction();
    $resource = "controllers." . preg_replace("/[\\\@]/", ".", $routeAction);
    $ok = Session::has("user") && Session::get("user")->type == 'staff' && hasPermission($resource);
    if (!$ok) {
        return Redirect::to("/system/home/login");
    }
});

Route::filter("auth.session", function() {
    $ok = Session::has("user");
    if (!$ok) {
        return Redirect::to("/login");
    }
});


Route:: filter('auth.basic', function() {
    return Auth::basic();
});

/*
  |--------------------------------------------------------------------------
  | Guest Filter
  |--------------------------------------------------------------------------
  |
  | The "guest" filter is the counterpart of the authentication filters as
  | it simply checks that the current user is not logged in. A redirect
  | response will be issued if they are, which you may freely change.
  |
 */
Route::filter('guest', function() {
    
});

/*
  |--------------------------------------------------------------------------
  | CSRF Protection Filter
  |--------------------------------------------------------------------------
  |
  | The CSRF filter is responsible for protecting your application against
  | cross-site request forgery attacks. If this special token in a user
  | session does not match the one given in this request, we'll bail.
  |
 */
Route::filter('csrf', function() {
    if (Session::token() !== Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});
