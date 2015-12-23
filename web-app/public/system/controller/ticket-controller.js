/**
 * Copyright (C) 2014, MEGAADS, JSC - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author SON PHI
 * Dec 15, 2014 3:27:30 PM
 */
system.controller( "TicketController", TicketController );
/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function TicketController( $scope, $http, $rootScope ) {
    $scope.controllerName = "TicketController";
    $scope.assignees = [ ];
    $scope.tickets = [ ];
    $scope.staffs = [ ];
    $scope.isFinding = false;
    $scope.isSaving = false;
    $scope.mode = "create";
    $scope.statuses = [
        { code: "assigned", name: "Đang xử lý" },
        { code: "finished", name: "Đã xử lý" },
        { code: "cancelled", name: "Hủy" }
    ];

    $scope.referenceTypes = [
        { code: "customer", name: "Khách hàng" },
        { code: "inoutput", name: "Đơn hàng" }
    ];

    $scope.failMessage = "";

    $scope.ticket = null;

    $scope.filter = {
        assignee_id: "",
        pageId: 0,
        status: $scope.statuses[0],
        pageSize: 20
    };

    this.__proto__ = new BaseController( $scope, $http, $rootScope );

    $scope.openCreateTicketDialog = function () {
        $scope.mode = "create";
        if ( $scope.assignees.length == 0 ) {
            initializeAssignees( function () {
                resetTicket();
                $( "#expectedEndTime" ).val( "" );
                showDialog();
            } );
        } else {
            resetTicket();
            $( "#expectedEndTime" ).val( "" );
            showDialog();
        }
    };

    $scope.openUpdateTicketDialog = function ( id ) {
        $scope.mode = "update";
        load( id, function () {
            showDialog();
        } );
    };

    $scope.openDetailTicketDialog = function ( id ) {
        $scope.mode = "detail";
        load( id, function () {
            showDialog();
        } );
    };

    $scope.openListTicketDialog = function () {
        if ( $scope.assignees.length == 0 ) {
            initializeAssignees( function () {
                $scope.find( function () {
                    $( '#listTicketDialog' ).modal( {
                        modal: true,
                        persist: true,
                        position: [ 30, 0 ],
                        autoPosition: true
                    } );
                } );
            } );
        } else {
            $scope.find( function () {
                $( '#listTicketDialog' ).modal( {
                    modal: true,
                    persist: true,
                    position: [ 30, 0 ],
                    autoPosition: true
                } );
            } );
        }
    };

    $scope.find = function ( callback ) {
        var filterData = {
            assignee_id: $scope.filter.assignee_id,
            status: $scope.filter.status.code,
            pageId: $scope.filter.pageId
        };
        $scope.showLoading();
        $http.post( "/service/ticket/find", filterData ).success( function ( data ) {
            $scope.isFinding = false;
            //ticket's status
            data.result.forEach( function ( ticket ) {
                ticket.status = $scope.getByCode( $scope.statuses, ticket.status );
            } );
            $scope.tickets = data.result;
            $scope.pagesCount = data.pagesCount;
            $scope.filter.pageId = data.pageId;

            $scope.hideLoading();
            if ( callback != null ) {
                callback();
            }
        } );
    };

    function load( id, callback ) {
        $scope.showLoading();
        $http.post( "/service/ticket/find", { id: id } ).success( function ( data ) {
            $scope.ticket = data.result;
            $scope.ticket.expected_end_time = $scope.toVietnameseDate( $scope.ticket.expected_end_time, true );
            $scope.ticket.assignee = $scope.getByField( $scope.assignees, "id", $scope.ticket.assignee_id );
            $scope.ticket.status = $scope.getByCode( $scope.statuses, $scope.ticket.status );
            $( "#expectedEndTime" ).val( $scope.toVietnameseDate( $scope.ticket.expected_end_time ) );
            if ( $scope.ticket.nTickets != null ) {
                $scope.ticket.nTickets.forEach( function ( nTicket ) {
                    nTicket.referenceType = $scope.getByCode( $scope.referenceTypes, nTicket.reference_type );
                } );
            }
            $scope.hideLoading();
            if ( callback != null ) {
                callback();
            }
        } );
    }

    $scope.addNTicket = function () {
        var nTicket = { referenceType: $scope.getReferenceTypeByCode( "inoutput" ), editting: true };
        $scope.ticket.nTickets.push( nTicket );
    };

    $scope.removeNTicket = function ( nTicket ) {
        var idx = -1;
        for ( var tempIdx = 0; tempIdx < $scope.ticket.nTickets.length; tempIdx++ ) {
            if ( $scope.ticket.nTickets[tempIdx] == nTicket ) {
                idx = tempIdx;
                break;
            }
        }
        if ( idx != -1 ) {
            $scope.ticket.nTickets.splice( idx, 1 );
        }
    };

    $scope.getReferenceTypeByCode = function ( code ) {
        var retVal = null;
        $scope.referenceTypes.forEach( function ( referenceType ) {
            if ( referenceType.code == code ) {
                retVal = referenceType;
            }
        } );
        return retVal;
    };

    $scope.onNTicketChange = function ( nTicket ) {
        if ( nTicket.code != null && nTicket.code.length >= 6 ) {
            $scope.isFinding = true;
            var url = nTicket.referenceType.code == "customer" ? "/service/customer/find" : "/service/inoutput/find";
            $http.post( url, { code: nTicket.code } ).success( function ( data ) {
                if ( data.result == null ) {
                    nTicket.reference_name = "";
                    nTicket.failMessage = "Không tìm thấy " + ( nTicket.referenceType.code == "customer" ? "khách hàng" : "đơn hàng" );
                } else {
                    nTicket.failMessage = "";
                    var referenceName;
                    if ( nTicket.referenceType.code == "customer" ) {
                        referenceName = data.result.code + " " + data.result.full_name + ", điện thoại: " + $scope.formatPhone( data.result.phone );
                    } else {
                        referenceName = data.result.code + ", khách hàng: " + data.result.related_user_name
                                + ", ngày tạo: " + $scope.toVietnameseDate( data.result.create_time, true );
                    }
                    nTicket.reference_name = referenceName;
                    nTicket.reference_id = data.result.id;
                }
                $scope.isFinding = false;
            } );
        }
    };

    $scope.save = function () {
        var ticketData = buildTicketData();
        validateTicketData( ticketData );
        if ( $scope.failMessage == null ) {
            $scope.isSaving = true;
            var url = $scope.mode == "create" ? "/service/ticket/create" : "/service/ticket/update";
            $http.post( url, ticketData ).success( function ( data ) {
                if ( data.status == "fail" ) {
                    $scope.failMessage = data.message;
                } else {
                    $.modal.close();
                }
                //update ticket
                $rootScope.$broadcast( "header.touch" );
                $scope.isSaving = false;
            } );
        }
    };

    $scope.delete = function ( ticket ) {
        var yes = confirm( "Bạn muốn xóa ticket " + ticket.code + "?" );
        if ( !yes ) {
            return;
        }
        //ELSE:
        $scope.isDeleting = true;
        $http.post( "/service/ticket/delete", { id: ticket.id } ).success( function ( data ) {
            if ( data.status == 'fail' ) {
                $scope.failMessage = data.message;
            } else {
                $scope.failMessage = "";
                $scope.find();
                $rootScope.$broadcast( "header.touch" );
            }
            $scope.isDeleting = false;
        } );
    };
    //--------------------------------------------------------------------------
    // Event listener
    $scope.$on( "ticket.openListTicketDialog", function ( ) {
        $scope.openListTicketDialog();
    } );

    $scope.$on( "ticket.openCreateTicketDialog", function ( ) {
        $scope.openCreateTicketDialog();
    } );

    //--------------------------------------------------------------------------
    // Util
    function resetTicket() {
        $scope.ticket = {
            status: $scope.statuses[0],
            nTickets: [ ],
            assignee: $scope.getByField( $scope.assignees, "id", currentStaffId )
        };
        $scope.addNTicket();
    }

    function showDialog() {
        $( "#expectedEndTime" ).datepicker( {
            dateFormat: "dd/mm/yy"
        } );
        $( '#ticketDialog' ).modal( {
            modal: true,
            persist: true,
            position: [ 30, 0 ],
            autoPosition: true
        } );
    }

    function buildTicketData() {
        var retVal = {
            title: $scope.ticket.title,
            content: $scope.ticket.content,
            assignee_id: $scope.ticket.assignee.id,
            assignee_name: $scope.ticket.assignee.full_name,
            status: $scope.ticket.status.code
        };
        if ( $scope.ticket.id != null ) {
            retVal.id = $scope.ticket.id;
        }
        //expectedEndTime
        var expectedEndTime = $( "#expectedEndTime" ).val( );
        if ( $scope.isValidVietnameseDate( expectedEndTime ) ) {
            retVal.expected_end_time = $( "#expectedEndTime" ).datepicker( 'getDate' ) / 1000;
        }
        //nTickets
        retVal.nTickets = $scope.ticket.nTickets;
        if ( retVal.nTickets != null ) {
            retVal.nTickets.forEach( function ( nTicket ) {
                nTicket.reference_type = nTicket.referenceType.code;
            } );
        }
        //return
        return retVal;
    }

    function validateTicketData( ticketData ) {
        var message = null;
        if ( $scope.isEmptyString( ticketData.title ) ) {
            message = "Thiếu tiêu đề";
        } else if ( ticketData.expected_end_time == null ) {
            message = "Thiếu thời hạn";
        } else {
            var nTicketHashes = { };
            ticketData.nTickets.forEach( function ( nTicket ) {
                if ( message != null ) {
                    return;
                }
                if ( nTicket.failMessage ) {
                    message = "Liên kết không hợp lệ";
                } else if ( nTicket.referenceType.code == "customer" && nTicket.reference_name == null ) {
                    message = "Thiếu mã khách hàng hoặc mã khách hàng không hợp lệ";
                } else {
                    var hash = ( nTicket.reference_type + nTicket.reference_id ).toLowerCase();
                    if ( nTicketHashes[hash] != null ) {
                        message = "Liên kết bị lặp";
                    } else {
                        nTicketHashes[hash] = true;
                    }
                }
            } );
        }
        $scope.failMessage = message;
    }

    function initializeAssignees( callback ) {
        if ( typeof staffs != 'undefined' && Array.isArray( staffs ) ) {
            $scope.assignees = staffs;
            if ( callback != null ) {
                callback();
            }
        } else {
            $scope.showLoading();
            $http.post( "/service/user/find", { type: "staff" } ).success( function ( data ) {
                $scope.assignees = data.result;
                $scope.hideLoading();
                if ( callback != null ) {
                    callback();
                }
            } );
        }
    }
}