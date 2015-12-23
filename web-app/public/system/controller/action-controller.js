/**
 * Copyright (C) 2014, MEGAADS, JSC - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 *
 * @author THOQ-LUONG
 * Apr 25, 2015 5:08:40 PM
 */
system.controller( "ActionController", ActionController );
/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function ActionController( $scope, $http, $rootScope ) {
    //--------------------------------------------------------------------------
    //  Members
    $scope.controllerName = "ActionController";
    $scope.changeItems = [ ];

    $scope.actions = [ ];
    $scope.staffs = [ ];

    $scope.actionItems = [ ];

    //--------------------------------------------------------------------------
    //  Initialize
    this.__proto__ = new BaseController( $scope, $http, $rootScope );
    this.initialize = function ( ) {
        $scope.staffs = staffs;
        processResult( actions );
    };
    //--------------------------------------------------------------------------
    // Method binding

    function processResult( response ) {
        $scope.actions = response.result;
    }
    //--------------------------------------------------------------------------
    // Utils
    this.initialize( );
}