<?php

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author THOQ LUONG
 * Oct 8, 2014 8:46:20 AM
 */
class TestSendMail extends TestCase {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    /**
     * @test
     */
    public function doTestSendMail() {
        echo "Hello Vietnam";
        //Mail::send('/backend/user/welcome', array(), function($message) {
            //$message->to('thoqbk@gmail.com', 'Jon Doe')->subject('Welcome to the Laravel 4 Auth App!');
       // });
        
        mail("thoqbk@gmail.com","My subject","hello");
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
