<div class="form-header">
    Tìm kiếm ticket
</div>
<table style="width: 100%" border="0" cellpadding="0" cellspacing="0" class="form-table">
    <tr>
        <td class="w-60">
            Tên ticket
        </td>
        <td class="w-120">
            <input type="text" ng-model="filter.title" />
        </td>
        <td class="w-60">
            Người xử lý
        </td>
        <td class="w-120">
            <select name="assignee_id" ng-model="filter.assignee_id">
                <option value="">Tất cả</option>
                <option value="{{assignee.id}}" ng-repeat="assignee in assignes">{{assignee.full_name}}</option>
            </select>
        </td>
        <td class="w-60">
            Trạng thái:
        </td>
        <td class="w-120">
            <select name="status" ng-model="filter.status">
                <option value="">Tất cả</option>
                <option value="{{statusTicket.code}}" ng-repeat="statusTicket in statuses">{{statusTicket.name}}</option>
            </select>
        </td>
        <td class="w-360"></td>
    </tr>
</table>
<div class="form-footer">
    <input type="button" ng-click="find()" value="Tìm kiếm" />
    <input type="button" ng-click="reset()" value="Xóa form" />
</div>