<?php
$actionParts = explode("@", Route::currentRouteAction());
$action = $actionParts[1];
$b = Route::currentRouteAction() == "Service\InoutputService@findItems" || $action == "out"; //show button create inoutput from ticket
?>
<div class="dialog" id="ticketDialog" style="width:800px<?= $mode == "dialog" ? ";display:none" : "" ?>">
    <div class="form-header">
        <img src="/system/image/ticket.png"/> 
        <span ng-show="mode == 'create'">Thêm ticket</span>
        <span ng-show="mode == 'update'">Sửa ticket {{ticket.code}}</span>
        <span ng-show="mode == 'detail'">Chi tiết ticket {{ticket.code}}</span>
        <img ng-show="isFinding" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
    </div>
    <table width="100%" border="0" class="form-table" style="margin-bottom: 10px;">
        <tr>
            <td class="w-80">Tiêu đề</td>
            <td colspan="3">
                <input type="text" 
                       ng-readonly="mode == 'detail'"
                       name="title" id="title" ng-model="ticket.title"/>
            </td>
        </tr>
        <tr>
            <td>Nội dung</td>
            <td colspan="3">
                <textarea ng-readonly="mode == 'detail'" ng-model="ticket.content"></textarea>
            </td>
        </tr>
        <tr>
            <td>Xử lý</td>
            <td class="w-180">                
                <select  ng-disabled="mode == 'detail'"                    
                         ng-model="ticket.assignee" ng-options="assignee.full_name for assignee in assignees">
                </select>            
            </td>
            <td class="w-60">Thời hạn</td>
            <td>
                <input ng-disabled="mode == 'detail'" type="text" id="expectedEndTime"/>
            </td>
        </tr>
        <tr>
            <td>Trạng thái</td>
            <td>
                <select  ng-disabled="mode == 'detail'"
                         ng-model="ticket.status"
                         ng-options="status.name for status in statuses">
                </select>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="vertical-align: top">Liên kết</td>
            <td colspan="3">
                <div ng-repeat="nTicket in ticket.nTickets" style="margin-bottom: 5px">
                    <span ng-show="nTicket.editting">
                        <select
                            ng-change="onNTicketChange( nTicket )"
                            style="width: 100px"
                            ng-model="nTicket.referenceType" 
                            ng-options="referenceType.name for referenceType in referenceTypes">                
                        </select>
                        &nbsp;
                        Mã 
                        &nbsp;
                        <input ng-model="nTicket.code" 
                               ng-change="onNTicketChange( nTicket )"
                               style="width: 80px"/>
                        &nbsp;
                        <span style="color: red">{{nTicket.failMessage}}</span>
                    </span>
                    {{nTicket.reference_name}}
                    &nbsp;                    
                    <a class="command-button" 
                       ng-show="mode != 'detail'" 
                       ng-click="removeNTicket( nTicket )">&nbsp;x&nbsp;</a>
                </div>
                <a href="#" ng-show="mode != 'detail'" ng-click="addNTicket()" class="command-link">Thêm</a>
            </td>
        </tr>
    </table>

    <div class="form-footer">
        <button ng-disabled="isSaving" ng-show="mode != 'detail'" ng-click="save()">Lưu Ticket</button>
        <?php
        if ( $b ) {
            ?>
            <button onclick="$.modal.close()" ng-show="mode == 'detail' && ticket.status.code == 'assigned'" 
                    ng-click="$root.$broadcast( 'order.openCreateInoutputDialog', { ticketId: ticket.id, note: ticket.code + ' ' + ticket.title } );">Tạo đơn hàng</button>
                    <?php
                }
                ?>
        <button onclick="$.modal.close()" ng-disabled="isSaving">Đóng cửa sổ</button>
        <img ng-show="isSaving" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
        &nbsp;
        <span style="color: red" ng-show="failMessage != null">
            <b>{{failMessage}}</b>
        </span>
    </div>
</div>
