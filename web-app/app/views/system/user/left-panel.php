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

<table class="abc">
    <tr>
        <td>
            <img src="/system/image/module_account.png" style="width: 16px" />
        </td>
        <td>
            <a href="/system/user" class="command-link <?= ($controller == "UserController" && $action == "index") ? "active" : "" ?>">Users</a>
        </td>
    </tr>
    
    <tr ng-show="controllerName == 'UserController'">
        <td></td>
        <td>
            <a ng-click="openCreateDialog()" class="command-link">Create user</a>
        </td>
    </tr>
    
    <tr>
        <td>
            <img src="/system/image/module_param.png" style="width: 16px;" />
        </td>
        <td>
            <a href="/system/role" class="command-link <?= ($controller == "UserController" && $action == "role") ? "active" : "" ?>">Roles</a>
        </td>
    </tr>
    
</table> 