<div <?= $mode == "dialog" ? "class='dialog' id='listTicketDialog'" : "" ?> style="<?= $mode == "dialog" ? "width:1000px;display:none" : "" ?>">
    <div class="form-header">
        Danh sách ticket
    </div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
        <tr>
            <th scope="col"style="width:20px">#</th>
            <th scope="col" class="w-80">T.g tạo</th>   
            <th scope="col" class="w-60">Mã</th>        
            <th scope="col">Tiêu đề</th>
            <th scope="col" class="w-80">Thời hạn</th>
            <th scope="col" class="w-100">Tiếp nhận</th>
            <th scope="col" class="w-100">Xử lý</th>
            <th scope="col" class="w-80">Trạng thái</th>
            <th scope="col" class="w-80"></th>
        </tr>
        <tr ng-repeat="ticket in tickets">
            <td scope="col">{{$index + 1}}</td>
            <td scope="col">{{summarizeDateTime( ticket.create_time )}}</td>   
            <td scope="col">
                <a class="command-link" onclick="$.modal.close()" ng-click="openDetailTicketDialog( ticket.id )">{{ticket.code}}</a>
            </td>
            <td scope="col">{{ticket.title}}</td>
            <td scope="col">{{toVietnameseDate( ticket.expected_end_time )}}</td>
            <td scope="col">{{ticket.creator_name}}</td>
            <td scope="col">{{ticket.assignee_name}}</td>
            <td scope="col">{{ ticket.status.name}}</td>
            <td scope="col" style="text-align: center;">
                &nbsp;<a href="#" onclick="$.modal.close()" class="command-link" ng-click="openUpdateTicketDialog( ticket.id )">sửa</a>
                &nbsp;<a href="#" class="command-link" ng-click="delete( ticket )">xóa</a>
            </td>
        </tr>

    </table>

    <div class="form-footer">
        <button type="button" onclick="$.modal.close()" ng-click="openCreateTicketDialog()">Thêm ticket</button>
        <button onclick="$.modal.close()">Đóng cửa sổ</button>
        <img ng-show="isFinding || isDeleting" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
        &nbsp;
        <span style="color: red" ng-show="failMessage != null">
            <b>{{failMessage}}</b>
        </span>
    </div>
</div>