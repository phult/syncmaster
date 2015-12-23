<div class="dialog" id="messageDialog" style="width:700px;display:none">
    <div class="form-header">
        <img src="/system/image/Email-icon.png" style="max-width: 16px;"/> 
        <span>Tin nhắn mới</span>
    </div>
    <table width="100%" border="0" class="form-table" style="margin-bottom: 10px;">
        <tr>
            <td class="w-100">Tiêu đề</td>
            <td colspan="3">
                <input type="text" name="subject" id="subject" ng-model="message.subject"/>
                <span style="color: red;display: block;" ng-show="errors.subject[0]">{{ errors.subject[0]}}</span>
            </td>
        </tr>
        <tr>
            <td>Người nhận</td>
            <td colspan="3">
                <span ng-repeat="receiver in receiverData" ng-app=""style="color:#719602;cursor: text;margin-right: 5px">
                    <span ng-bind-html="receiver.full_name"></span>
                    <span ng-click="removerReceiver( $index )" class="command-button">&nbsp;x&nbsp;</span>
                </span>
                
                <input type="text" style="width:97%;margin-top: 5px"
                    ng-model="searchName"
                    ng-change="searchNameChange()"
                    ng-keydown="onSearchKeypress( $event )"/>
                <table style="position:absolute;width:400px;border: 2px solid #CCC;z-index: 10" 
                       ng-show="isShowRelatedUser && userData.length > 0"
                        class="data-table" cellpadding="0" cellspacing="0" border="0">
                    <tr class="{{$index == $parent.cursorPosition ? 'active':''}}" 
                         style="cursor: pointer"
                         ng-click="addReceiver( user )"
                         ng-repeat="user in userData">
                         <td ng-bind-html="user.full_name">{{user.full_name}}</td>
                    </tr>
                </table>
                <span style="color: red;display: block;" ng-show="errors.receiverData[0]">{{ errors.receiverData[0]}}</span>
            </td>
        </tr>
        <tr>
            <td>Nội dung</td>
            <td colspan="3">
                <textarea type="text" ng-model="message.content" style="height: 250px;width: 96.5%"></textarea>
                <span style="color: red;display: block;" ng-show="errors.content[0]">{{ errors.content[0]}}</span>
            </td>
        </tr>
    </table>

    <div class="form-footer">
        <button ng-disabled="isSaving" ng-click="save()">Gửi</button>
        <button onclick="$.modal.close()" ng-disabled="isSaving">Đóng cửa sổ</button>
        <img ng-show="isSaving" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
    </div>
</div>
