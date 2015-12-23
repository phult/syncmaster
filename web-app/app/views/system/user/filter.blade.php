<div class="form-header">
    Find users
</div>
<table style="width: 100%" border="0" class="form-table">
    <tr>
        <td class="w-60">
            Code
        </td>
        <td class="w-100">
            <input type="text" ng-model="filter.code" />
        </td>
        <td class="w-60">
            Search
        </td>
        <td class="w-180">
            <input type="text" ng-model="filter.search" />
        </td>
        <td class="w-60">
            Role
        </td>
        <td class="w-120">
            <select ng-model = "filter.role"
                    ng-change="find()"
                    ng-options="role.name for role in roles">
                <option value="">All</option>
            </select>
        </td>
        <td class="w-60">
            Type
        </td>
        <td class="w-120">
            <select ng-model = "filter.type"
                    ng-change="find()"
                    ng-options="type.name for type in types">
                <option value="">All</option>
            </select>
        </td>
        <td></td>
    </tr>
</table>
<div class="form-footer">
    <input type="button" ng-click="find()" value="Tìm kiếm" />
    <input type="button" ng-click="reset()" value="Xóa form" />
</div>