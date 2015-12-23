<?php

/**
 * Copyright (C) 2014 MEGAADS - All Rights Reserved -
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author DinhLV
 * Dec 4, 2014 4:43:59 PM
 */
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    //--------------------------------------------------------------------------
    //  Members

    const TYPE_CUSTOMER = "customer";
    const TYPE_STAFF = "staff";
    const TYPE_SUPPLIER = "supplier";
    const TYPE_SHIPPER = "shipper";

    public $timestamps = false;
    protected $table = "chi_user";
    protected $primaryKey = "id";
    protected $guarded = array( "id" );
    protected $fillable = array( "username", "type", "first_name", "last_name", "full_name",
        "email", "login_time", "about", "avatar", "status", "create_time", "active_time","update_time", "password",
        "feed_type",
        "birthday", "phone", "remember_token", "code", "search", "gender", "address", "location_id" );
    public static $rules = array(
        'username' => 'required|alpha_num|min:2|unique:chi_user',
        'email' => 'required|email|unique:chi_user',
        'password' => 'required|between:6,12',
    );

    /**
     * Automatically Hash the password when setting it
     * @param string $password The password
     */
    public function setPassword( $password ) {
        $this->password = Hash::make($password);
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array( 'password' );

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail() {
        return $this->email;
    }

    public function getRememberToken() {
        return $this->remember_token;
    }

    public function getRememberTokenName() {
        return 'remember_token';
    }

    public function setRememberToken( $value ) {
        $this->remember_token = $value;
    }

    public static function accessControl( $controller, $action ) {
        if ( Session::has("user") ) {
            $user = DB::table("user")->where("username", "=", Session::get("user")->username)->first();
            if ( $user->role_id == 1 ) {
                return "access";
            }
            $privileges = DB::table("privilege")->join("role_n_privilege", function($join) use ($user) {
                        $join->on("privilege.id", "=", "role_n_privilege.privilege_id")
                                ->on("role_n_privilege.role_id", "=", DB::raw($user->role_id));
                    })->get();
            foreach ( $privileges as $privilege ) {
                if ( strtolower($privilege->controller) == strtolower('backend_' . $controller) && strtolower($privilege->action_name) == strtolower($action) ) {
                    return "access";
                }
            }
            return "fail";
        }
        return "fail";
    }

    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
