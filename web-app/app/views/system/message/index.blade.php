@extends("system.layout.master")
@section("title","Message")
@section("content")
<script type="text/javascript">
    var users = <?= $users ?>;
</script>
<script type="text/javascript" src="/system/controller/message-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<div ng-controller="MessageController">
    <table width="100%" border='0' cellpadding="0" cellspacing="0">
        <tr>
            <td width="130px" valign="top" style="border-right: 2px solid #CCC;background-color: #F0F6F8">
                <?= View::make("/system/message/left-panel"); ?>
            </td>
            <td valign="top" style="padding-left: 5px">
                <?= View::make("/system/message/list"); ?>
            </td>
        </tr>
    </table>
    <?= View::make("/system/message/form"); ?>
    <?= View::make("/system/message/detail"); ?>
</div>
@stop