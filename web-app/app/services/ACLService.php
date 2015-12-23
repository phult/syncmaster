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
 * Jul 8, 2015 3:52:58 PM
 */
interface ACLService {
    
    const ACCESS_DENY = "deny";
    const ACCESS_ACCEPT = "accept";
    const ACCESS_NOT_SET = "not_set";

    public function createPermission($permission);

    public function updatePermission($permission);

    /**
     * 1. userId
     * 2. roleId
     * 
     * @param type $filter
     */
    public function findPermissions($filter);

    public function deletePermission($id);

    /**
     * Load current user permissions
     */
    public function loadPermissions();

    public function hasPermission($resource);
    
    /**
     * 1. module
     * @param type $filter
     */
    public function findResources($filter);
    
    /**
     * 
     * @param type $filter
     */
    public function findModules($filter);
    
    public function getResourceByName($resource);
}
