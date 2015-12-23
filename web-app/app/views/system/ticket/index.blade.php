@extends("system.layout.master")
@section("title","Ticket")
@section("content")
<script type="text/javascript" src="/system/controller/ticket-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<div ng-controller="TicketController">
    <table width="100%" border='0' cellpadding="0" cellspacing="0">
        <tr>
            <td width="130px" valign="top" style="border-right: 2px solid #CCC;background-color: #F0F6F8">
                <?= View::make("/system/ticket/left-panel"); ?>
            </td>
            <td valign="top" style="padding-left: 5px">
                <?= View::make("/system/ticket/filter"); ?>
                <?= View::make("/system/ticket/list", array( "index" => true )); ?>
            </td>
        </tr>
    </table>
    <?= View::make("/system/ticket/form"); ?>
</div>
@stop