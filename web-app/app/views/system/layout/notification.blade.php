<style type="text/css">
    #notification-dialog{
        position:absolute;
        top:0xp;
        right:0px;
        width:300px;
        border-top:1px solid #CCC;
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.26);
        border-bottom:1px solid #CCC
    }
</style>
<table id="notification-dialog"
       ng-show="notifications.length > 0"
       border="0" cellpadding="0" cellspacing="0" class="data-table">
    <tr class="animate-repeat" ng-repeat="notification in notifications">
        <td style="width: 20px">
            <img src="/system/image/icon_add.png" alt="add" ng-show="notification.type == 'create'"/>
            <img src="/system/image/icon_edit.png" alt="edit" ng-show="notification.type == 'update'"/>
            <img src="/system/image/icon_delete.png" alt="delete" ng-show="notification.type == 'delete'"/>
        </td>
        <td>
            {{notification.message}}
        </td>
    </tr>
    <tr class="form-footer">
        <td colspan="2">
            <a class="command-link" ng-click="notifications = []">Đã xem xong, ẩn thống báo</a>
        </td>
    </tr>
</table>