<div class="form-header">
    Users list
    &nbsp;&nbsp;&nbsp;
    <a class="command-link" ng-click="openCreateDialog()">create user</a>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
    <tr>      
        <th class="w-60" scope="col">Code</th>        
        <th class="w-100" scope="col">Username</th>
        <th class="w-180" scope="col">Full name</th>
        <th class="w-180" scope="col">Email</th>
        <th>Roles</th>        
        <th class="w-80">Type</th>
        <th class="w-120" scope="col">Last active time</th>
        <th class="w-100" scope="col">Created time</th>

        <th class="w-100" scope="col">&nbsp;</th>
    </tr>
    <tr ng-repeat="user in users">
        <td>
            <a class="command-link" ng-click="openDetailDialog(user)">{{user.code}}</a>
        </td>
        <td>{{user.username}}</td>
        <td>
            {{user.full_name}}
        </td>        
        <td>{{user.email}}</td>
        <td>
            <span ng-repeat="role in user.roles" class="border-box" style="border-color: #CCC;margin-right: 5px">{{role.name}}</span>
        </td>        
        <td>
            {{getByCode(types,user.type).name}}
        </td>
        <td>{{summarizeDateTime(user.active_time, true)}}</td>        
        <td>{{summarizeDate(user.create_time, true)}}</td>  
        <td>
            &nbsp;<a class="command-link" ng-click="openUpdateDialog(user)">edit</a>
            &nbsp;<a class="command-link" ng-click="delete(user)" type="button">delete</a>
        </td>
    </tr>
</table>



<div class="form-footer">
    <button type="button" 
            ng-click="openCreateDialog()">Create user</button>

    <?=
    View::make("/system/common/paginator", array("accessPageId" => "filter.pageId",
        "accessPagesCount" => "pagesCount",
        "accessFind" => "find()"));
    ?>

    <span style="float: right" ng-show="usersCount != null">
        Show {{usersCount}} user(s) in total of {{usersCount}} user(s)
        &nbsp;&nbsp;&nbsp;
    </span>
</div>