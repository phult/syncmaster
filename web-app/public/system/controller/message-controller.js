/**
 * Copyright (C) 2014, MEGAADS, JSC - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author SON PHI
 * April 15, 2015 2:27:30 PM
 */
system.controller( "MessageController", MessageController );
/**
 * 
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function MessageController( $scope, $http, $rootScope ) {
    $scope.controllerName = "MessageController";
    $scope.messages = [];
    $scope.message = [];
    $scope.detailMessage = [];
    $scope.users = users;
    $scope.userData = [];
    $scope.receiverData = [];
    $scope.searchName = "";
    $scope.isFinding = false;
    $scope.isSaving = false;
    $scope.isShowRelatedUser = false;
    $scope.errors = "";
    $scope.findType = "inbox";
    $scope.cursorPosition = 0;
    $scope.sumMessageNotRead = 0;
    $scope.pageId = 0;
    $scope.pageSize = 20;

    this.__proto__ = new BaseController( $scope, $http, $rootScope );

    $scope.openDialog = function () {
        $scope.reset();
        $( '#messageDialog' ).modal( {
            modal: true,
            persist: true,
            position: [ 30, 0 ],
            autoPosition: true
        } );
    };
    $scope.openDetailDialog = function () {
        $( '#detailDialog' ).modal( {
            modal: true,
            persist: true,
            position: [ 30, 0 ],
            autoPosition: true
        } );
    };

    $scope.find = function ( type ) {
        if (!type) {
            var type = $scope.findType;
        }
        $scope.isFinding = true;
        $http.post( "/service/message/find", {type: type, pageId: $scope.pageId, pageSize: $scope.pageSize} ).success( function ( data ) {
            if (type == "inbox") {
                $scope.sumMessageNotRead = data.sumMessageNotRead;
            }
            $scope.isFinding = false;
            $scope.messages = data.messages;
            $scope.pageId = data.pageId;
            $scope.pagesCount = data.pagesCount;
        } );
    };
    $scope.findById = function ( message ) {
        $scope.isFinding = true;
        $http.post( "/service/message/find-by-id", {messageId: message.id, isRead: message.is_read} ).success( function ( data ) {
            $scope.isFinding = false;
            if (data.status == "successful") {
                $scope.detailMessage = {
                    subject: data.message.subject,
                    sender_name: data.message.sender_name,
                    receivers: data.message.receivers,
                    content: data.message.content
                };
                if (message.is_read != 1 && $scope.findType == "inbox") {
                    $scope.sumMessageNotRead--;
                    $rootScope.$broadcast("header.touch");
                    message.is_read = 1;
                }
                $scope.openDetailDialog();
            }
        } );
    };
    
    $scope.showDateTime = function ( dateTime ) {
        if ( dateTime != null ) {
            var date = new Date();
            var timeDay = dateTime.replace( /(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):.*/, "$3" );
            if (timeDay != date.getDate()) {
                return dateTime.replace( /(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):.*/, "$3/$2" );
            } else {
                return dateTime.replace( /(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):.*/, "$4:$5" );
            }
        }
    };

    $scope.load = function ( message ) {
        $scope.message = {
            sender_id: message.sender_id,
            subject: message.subject,
            content: message.content,
            sender_name: message.sender_name
        };
        $scope.receiverData = message.receivers;
        $scope.searchName = "";
    };
    
    $scope.searchNameChange = function ( ) {
        $scope.userData = [];
        if ($scope.searchName.length < 3) {
            return;
        }
        $scope.isShowRelatedUser = true;
        var i = 0;
        var searchName = $scope.toFriendlyString( $scope.searchName );
        $scope.users.forEach(function ( user ) {
            var text = $scope.toFriendlyString( user.username + "-" + user.full_name );
            if (i >= 5) {
                return;
            }
            if (text && text.indexOf(searchName) != -1) {
                $scope.userData.push(user);
                i++;
            }
        });
        $scope.cursorPosition = 0;
    };
    
    $scope.addReceiver = function ( user ) {
        $scope.isShowRelatedUser = false;
        $scope.receiverData.push({id:user.id, full_name: user.full_name});
        $scope.searchName = "";
    };
    
    $scope.removerReceiver = function ( index ) {
        $scope.receiverData.splice( index, 1 );
    };
    
    $scope.onSearchKeypress = function ( event ) {
        if ( event.which == 38 && $scope.cursorPosition > 0 ) {//UP
            $scope.cursorPosition--;
        } else if ( event.which == 40 && $scope.cursorPosition < $scope.userData.length - 1 ) {//DOWN
            $scope.cursorPosition++;
        } else if ( event.which == 13 ) {//ENTER
            $scope.addReceiver($scope.userData[$scope.cursorPosition]);
        }
    };

    $scope.save = function () {
        var messageData = buildMessageData();
        validateMessageData( messageData );
        if ( $scope.errors.error ) {
            $scope.isSaving = false;
            return;
        }
        $scope.isSaving = true;
        var url = "/service/message/create";
        $http.post( url, messageData ).success( function ( data ) {
            if ( data.status == "fail" ) {
                $scope.errors = data.message;
            } else {
                $.modal.close();
            }
            $scope.isSaving = false;
        } );
    };

    $scope.delete = function ( message ) {
        var yes = confirm( "Bạn muốn xóa message " + message.subject + "?" );
        if ( !yes ) {
            return;
        }
        //ELSE:
        $scope.isDeleting = true;
        $http.post( "/service/message/delete", { id: message.id } ).success( function ( data ) {
            if ( data.status == 'fail' ) {
                $scope.errors = data.message;
            } else {
                $scope.errors = "";
                $scope.find($scope.findType);
            }
            $scope.isDeleting = false;
        } );
    };

    function buildMessageData() {
        var retVal = {
            receiverData: $scope.receiverData,
            subject: $scope.message.subject,
            content: $scope.message.content
        };
        //return
        return retVal;
    }

    function validateMessageData( messageData ) {
        var error = {error: false};
        if ( $scope.isEmptyString( messageData.subject ) ) {
            error.subject = ["Thiếu tiêu đề"];
            error.error = true;
        } 
        if ( $scope.isEmptyString( messageData.content ) ) {
            error.content = ["Thiếu nội dung"];
            error.error = true;
        } 
        if ( $scope.receiverData.length < 1 ) {
            error.receiverData = ["Thiếu người nhận"];
            error.error = true;
        }
        $scope.errors = error;
    }
    
    $scope.reset = function ( ) {
        $scope.isShowRelatedUser = false;
        $scope.receiverData = [];
        $scope.message = [];
        $scope.searchName = "";
    };
    
    $scope.find($scope.findType);
}