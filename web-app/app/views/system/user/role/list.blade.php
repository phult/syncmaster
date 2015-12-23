<div class="form-header">
    Roles list
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
    <tr>      
        <th class="w-100">Name</th>
        <th>Description</th>
        <th class="w-100" scope="col">&nbsp;</th>
    </tr>
    <tr ng-repeat="role in roles">
        <td>
            <a class="command-link" ng-click="openDetailDialog(role)">
                {{role.name}}
            </a>
        </td>
        <td>
            {{role.description}}
        </td>       
        <td>
            &nbsp;<a class="command-link" ng-click="openUpdateDialog(role)">edit</a>
            &nbsp;<a class="command-link" ng-click="delete(role)" type="button">delete</a>
        </td>
    </tr>
</table>



<div class="form-footer">
    <button type="button" 
            ng-click="openCreateDialog()">Create role</button>

    <?=
    View::make("/system/common/paginator", array("accessPageId" => "filter.pageId",
        "accessPagesCount" => "pagesCount",
        "accessFind" => "find()"));
    ?>

    <span style="float: right" ng-show="roles != null">
        Show {{roles.length}} role(s) in total of {{roles.length}} role(s)
        &nbsp;&nbsp;&nbsp;
    </span>
</div>