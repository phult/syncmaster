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
            <img src="/system/image/ticket.png" style="max-width: 16px;" />
        </td>
        <td>
            <a href="/system/ticket" class="command-link <?= $action == "ticket" ? "active" : "" ?>">Ticket</a>
        </td>
    </tr>
    <tr ng-show="controllerName == 'TicketController'">
        <td></td>
        <td>
            <a class="command-link" ng-click="reser();openTicketDialog()">Thêm Ticket</a>
        </td>
    </tr>
</table> 