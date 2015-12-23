<style>    
    table.abc {
        font-family:Arial,Helvetica,sans-serif;
        font-size: 14px;
        font-weight: 400;
        color: #000;
        margin-left:10px;
    }
    .abc tr{
        height: 30px;
    }
</style>

<?php
$actionParts = explode("@", Route::currentRouteAction());
$action = $actionParts[1];
?>

<table class="abc">
    <tr>
        <td>
            <img src="/system/image/Email-icon.png" style="max-width: 16px;" />
        </td>
        <td>
            <a href="/system/message" class="command-link active">Tin nhắn</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a class="command-link" ng-click="findType= 'inbox';pageId = 0;find('inbox')">Tin nhắn đến ({{sumMessageNotRead}})</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a class="command-link" ng-click="findType= 'send';pageId = 0;find('send')">Tin nhắn đã gửi</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a class="command-link" ng-click="openDialog()">Tạo tin nhắn</a>
        </td>
    </tr>
</table> 