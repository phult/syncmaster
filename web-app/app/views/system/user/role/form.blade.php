<div class="dialog" id="roleDialog" style="width:600px;display: none;">
    <div class="form-header">
        <img src="/system/image/user_red_female.png"/>
        <span ng-show="mode == 'create'">Create role</span>
        <span ng-show="mode == 'update'">Update role</span>
        <span ng-show="mode == 'detail'">Role detail</span>
    </div>
    <table width="100%" border="0" class="form-table">
        <tr>
            <td class="w-80">Name</td>
            <td>
                <input type="text" ng-model="role.name" ng-readonly="mode == 'detail'"/>
            </td>
        </tr>
        <tr>
            <td>Description</td>
            <td>
                <textarea ng-model="role.description" rows="2" ng-readonly="mode == 'detail'"></textarea>
            </td>
        </tr>
        <tr>
            <td>
                Permission
            </td>
            <td>
                Module
                &nbsp;
                <select 
                    style="width: 120px"
                    ng-model="form.filter.module" ng-options ="module.name for module in modules"
                    ng-change="findPermissions()">
                    <option value="">All</option>
                </select>
                &nbsp;
                Access
                &nbsp;
                <select 
                    style="width: 120px"
                    ng-model="form.filter.access" ng-options ="access.name for access in accesses"
                    ng-change="findPermissions()">
                    <option value="">All</option>
                </select>
            </td>

        </tr>
        <tr>
            <td colspan="2">
                <table class="data-table" border="0" cellpadding="0" cellspacing="0" style="width: 100%">
                    <tr>
                        <th style="width: 20px">#</th>
                        <th>Resource</th>
                        <th class="w-100">Access</th>
                    </tr>
                    <tr ng-repeat="permission in role.permissions">
                        <td>{{$index + 1}}</td>
                        <td>
                            {{permission.resource}}
                        </td>
                        <td>
                            <select ng-model="permission.access" ng-options="access.name for access in accesses" 
                                    ng-disabled="mode == 'detail'">
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="form-footer">
        <button ng-click="save()" 
                ng-show="mode == 'create' || mode == 'update'"
                type="button" ng-disabled="isSaving">Save</button>
        <button ng-show="mode == 'detail'" 
                ng-click="mode = 'update'"
                type="button">Edit</button>
        <button onclick="$.modal.close()" type="button" ng-disabled="isSaving">Close</button>

        <img ng-show="isSaving" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
        &nbsp;
        <?=
        View::make("/system/common/paginator", array("accessPageId" => "form.filter.pageId",
            "accessPagesCount" => "form.permissionPagesCount",
            "accessFind" => "findPermissions()"));
        ?>
        &nbsp;
        <span style="color: red">
            <b>{{failMessage}}</b>
        </span>
    </div>
</div>
