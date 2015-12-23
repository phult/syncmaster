<div class="dialog" id="userChangeDialog" style="display: none;">
    <div class="form-header">
        <img src="/system/image/user_red_female.png"/>
        <span>Thay đổi password</span>
    </div>
    <form action="/service/user/change-password" ng-upload="changeCompleted(content)" method="post">
        <table width="100%" border="0" class="form-table">
            <tr>
                <td class="w-120">Password cũ: </td>
                <td class="w-180">
                    <input type="password" name="oldPassword" id="oldPassword" autocomplete="off" />
                </td>
            </tr>
            <tr>
                <td class="w-120">Password mới: </td>
                <td class="w-180">
                    <input type="password" name="newPassword" id="newPassword" autocomplete="off" />
                </td>
            </tr>
            <tr>
                <td class="w-120">Nhập lại password: </td>
                <td class="w-180">
                    <input type="password" name="rePassword" id="rePassword" autocomplete="off" />
                </td>
            </tr>
        </table>
        <div class="form-footer" id="change-pass-footer">
            <button ng-click="isSaving = true" type="submit">Lưu thay đổi</button>
            <button onclick="$.modal.close()" type="button">Hủy</button>
            <img ng-show="isSaving" src="/system/image/ajax-loader.gif" alt="loading" style="height: 12px"/>
            &nbsp;
            <span style="color: red">
                <b>{{errors}}</b>
            </span>
        </div>
    </form>
</div>
