<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use DateTime;
use System\Action;

/**
 * Description of TestLiveUpdate
 *
 * @author thoqbk
 */
class TestLiveUpdate extends TestCase {
    
    /**
     * @test
     */
    public function doTest() {
        $action = new Action();
        $action -> actor_type = 'system';
        $action -> actor_id = 0;
        $action -> target_id = rand(1, 10000);
        $action -> target_type = 'test';
        $action -> type = 'test-action';
        $action -> data = 'simple-data-' . rand(1,10000);
        $action -> create_time = new DateTime();
        
        $action -> save();
    }
}