@extends("system.layout.master")
@section("title","Home")
@section("content")
<script type="text/javascript" src="/system/controller/base-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<script type="text/javascript" src="/system/controller/user-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<script type="text/javascript">
    var usersResult = <?= $usersResult ?>;
    var rolesResult = <?= $rolesResult ?>;
</script>
<div ng-controller="UserController">
    <table width="100%" border='0' cellpadding="0" cellspacing="0">
        <tr>
            <td width="130px" valign="top" style="border-right: 2px solid #CCC;background-color: #F0F6F8">
                <?= View::make("/system/user/left-panel"); ?>
            </td>
            <td valign="top" style="padding-left: 5px">
                <?= View::make("/system/user/filter"); ?>
                <?= View::make("/system/user/list"); ?>
            </td>
        </tr>
    </table>
    <?= View::make("/system/user/form"); ?>
    <?= View::make("/system/user/change-form"); ?>
</div>
@stop