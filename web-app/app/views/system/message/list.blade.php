<div class="form-header">
    Danh sách tin nhắn {{findType == 'inbox' ? "đến" : "đã gửi"}}    
    <img ng-show="isFinding" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
    <tr>
        <th scope="col" class="w-100" ng-show="findType == 'inbox'">Người gửi</th>
        <th scope="col" class="w-240">Người nhận</th>   
        <th scope="col">Tiêu đề</th>     
        <th scope="col" class="w-80">Thời gian</th>
        <th scope="col" class="w-60">&nbsp;</th>
    </tr>
    <tr ng-repeat="message in messages">
        <td scope="col" ng-show="findType == 'inbox'">
            <span ng-show="findType == 'inbox'">{{message.sender_name}}</span>
        </td>
        <td scope="col">
            <span ng-repeat="receiver in message.receivers">{{receiver.full_name}}{{$index < message.receivers.length-1 ? ", " : ""}}</span>
        </td>
        <td scope="col">
            <a class="command-link" ng-click="findById(message)" ng-if="message.is_read == 0">
                <strong>{{message.subject}}</strong>
            </a>
            <a class="command-link" ng-click="findById(message)" ng-if="message.is_read != 0">
                {{message.subject}}
            </a>
        </td>
        <td scope="col">{{showDateTime(message.create_time)}}</td>
        <td scope="col" style="text-align: center;">
            <a href="#" class="command-link" ng-click="delete( message )">xóa</a>
        </td>
    </tr>
</table>

<div class="form-footer">
    <button type="button" ng-click="openDialog()" ng-disabled="isFinding || isSaving">Tạo tin nhắn</button>
    <?=
    View::make("/system/common/paginator", array( "accessPageId" => "pageId",
        "accessPagesCount" => "pagesCount",
        "accessFind" => "find()" ));
    ?>
</div>