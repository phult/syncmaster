<div class="dialog" id="detailDialog" style="width:700px;display: none">
    <div class="form-header">
        <img src="/system/image/Email-icon.png" style="max-width: 16px;"/> 
        Chi tiết tin nhắn "{{detailMessage.subject}}"
    </div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="form-table">
        <tr>
            <td class="w-100">Người gửi</td>
            <td style="padding-top: 5px;padding-bottom: 5px">
                {{detailMessage.sender_name}}
            </td>
        </tr>
        <tr>
            <td>Người nhận</td>
            <td style="padding-top: 5px;padding-bottom: 5px">
                <span style="display: inline-block;margin-right: 10px;" ng-repeat="receiver in detailMessage.receivers">
                    <img ng-show="receiver.is_read == 1" src="/system/image/icon-tick.png" style="max-width: 12px;" />
                    {{receiver.full_name}}{{$index < detailMessage.receivers.length-1 ? ", " : ""}}
                </span>
            </td>
        </tr>
        <tr>
            <td>Nội dung</td>
            <td style="vertical-align: top;padding-top: 5px;padding-bottom: 5px">
                <textarea style="height: 250px;overflow: auto" readonly="readonly">{{detailMessage.content}}</textarea>
            </td>
        </tr>
    </table>

    <div class="form-footer">
        <button onclick="$.modal.close()" ng-disabled="isSaving">Đóng cửa sổ</button>
    </div>
</div>
