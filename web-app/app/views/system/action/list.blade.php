<table width="100%"
       ng-init="currentActionIdx = -1"
       border="0" class="data-table" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <th style="width: 20px" scope="col">#</th>
            <th scope="col" class="w-80">Thời gian</th>
            <th class="w-120" scope="col">Hành động</th>
            <th class="w-120" scope="col">Người thực hiện</th>
            <th scope="col">Thay đổi</th>            
        </tr>
        <tr
            ng-repeat ="action in actions">
            <td>{{$index + 1}}</td>
            <td>
                {{summarizeDateTime( action.create_time )}}
            </td>
            <td>
                {{getByCode( actionTypes, action.type ).name}}
            </td>
            <td>
                {{getByField( staffs, "id", action.actor_id ).full_name}}
            </td>
            <td>
                <a class="command-link" 
                   ng-show="action.type == 'update' || action.type == 'change-status'"
                   ng-click="parseInoutputAction( action );
                           $parent.currentActionIdx = $index;"
                   target="_blank">chi tiết</a>
                <img src="/system/image/play.png" alt="current" 
                     ng-show="$index == currentActionIdx"
                     style="float: right;margin-right: 5px"/>
            </td>
        </tr>
    </tbody>
</table>