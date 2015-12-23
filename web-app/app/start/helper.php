<?php

/**
 * Copyright (C) 2015, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 *
 * @author THO Q LUONG
 * Jul 9, 2015 10:21:58 AM
 */

function hasPermission($resource){
    $aclService = App::make("ACLService");
    return $aclService -> hasPermission($resource);
}