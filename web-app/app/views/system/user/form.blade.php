<div class="dialog" id="userDialog" style="width:600px;display: none;">
    <div class="form-header">
        <img src="/system/image/user_red_female.png"/>
        <span ng-show="mode == 'create'">Create user</span>
        <span ng-show="mode == 'update'">Update user</span>
        <span ng-show="mode == 'detail'">User detail</span>
    </div>
    <table width="100%" border="0" class="form-table">
        <tr>
            <td class="w-60">Code</td>
            <td class="w-80">
                <input type="text" ng-model="user.code" ng-readonly="mode == 'detail'"/>
            </td>
            <td class="w-80">Username</td>
            <td class="w-100">
                <input type="text" ng-model="user.username" ng-readonly="mode == 'detail' || mode == 'update'"/>
            </td>
            <td class="w-60">Type</td>
            <td>
                <select ng-model="user.type" ng-options="type.name for type in types" 
                        ng-disabled="mode == 'detail'">
                </select>
            </td>
        </tr>
        
        <tr>
            <td>Full Name</td>
            <td colspan="3">
                <input type="text" 
                       ng-readonly="mode == 'detail'"
                       ng-model="user.full_name" style="width: 99.5%"/>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>Email</td>
            <td colspan="3">
                <input type="text" 
                       ng-readonly="mode == 'detail'"
                       ng-model="user.email" style="width: 99.5%"/>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>Roles</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="6">
                <table border="0" class="data-table" style="width: 100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <th class="w-30"></th>
                        <th class="w-80">Name</th>
                        <th>Description</th>
                    </tr>
                    <tr ng-repeat="role in user.roles">
                        <td>
                            <input type="checkbox" 
                                   ng-disabled="mode == 'detail'"
                                   ng-model="role.selected"/>
                        </td>
                        <td>{{role.name}}</td>
                        <td>{{role.description}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="form-footer">
        <button ng-click="save()" 
                ng-show="mode == 'create' || mode == 'update'"
                type="button" ng-disabled="isSaving">Save</button>
        <button onclick="$.modal.close()" type="button" ng-disabled="isSaving">Close</button>
        
        <a ng-show="mode == 'update' || mode == 'detail'" class="command-link" ng-click="resetPassword(user.id)">reset password</a>
        
        <img ng-show="isSaving" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
        &nbsp;
        <span style="color: red">
            <b>{{failMessage}}</b>
        </span>
    </div>
</div>
