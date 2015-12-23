<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <th scope="col" class="w-180">Tên trường</th>
            <th scope="col" class="w-240">Trước khi thay đổi</th>
            <th scope="col">Sau khi thay đổi</th>
        </tr>        
        <tr ng-repeat="actionItem in actionItems">
            <td>{{actionItem.fieldName}}</td>
            <td>
                <span ng-show="actionItem.oldValue != null && actionItem.oldValue != ''">{{actionItem.oldValue}}</span>
                <img src="/system/image/na.png" alt="na" ng-show="actionItem.oldValue == null || actionItem.oldValue == ''"/>
            </td>
            <td>
                <span ng-show="actionItem.newValue != null && actionItem.newValue != ''">{{actionItem.newValue}}</span>
                <img src="/system/image/na.png" alt="na" ng-show="actionItem.newValue == null || actionItem.newValue == ''"/>
            </td>
        </tr>
    </tbody>
</table>