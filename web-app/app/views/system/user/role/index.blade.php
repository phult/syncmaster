@extends("system.layout.master")
@section("title","Home")
@section("content")
<script type="text/javascript" src="/system/controller/base-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<script type="text/javascript" src="/system/controller/role-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<script type="text/javascript">
    var rolesResult = <?= $rolesResult ?>;
    var resources = <?= json_encode($resources) ?>;
    var modules = <?= json_encode($modules) ?>;
</script>
<div ng-controller="RoleController">
    <table width="100%" border='0' cellpadding="0" cellspacing="0">
        <tr>
            <td width="130px" valign="top" style="border-right: 2px solid #CCC;background-color: #F0F6F8">
                <?= View::make("/system/user/left-panel"); ?>
            </td>
            <td valign="top" style="padding-left: 5px">
                <?= View::make("/system/user/role/list"); ?>
            </td>
        </tr>
    </table>
    <?= View::make("/system/user/role/form"); ?>
</div>
@stop