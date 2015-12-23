<?php


use Illuminate\Support\ServiceProvider;
use Impl\ACLServiceImpl;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChiakiServiceProvider
 *
 * @author thoqbk
 */
class ChiakiServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->singleton('ACLService', function () {
            return new ACLServiceImpl();
        });
    }

}
