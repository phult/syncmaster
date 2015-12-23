<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="/system/image/logo.png" />
        <title>Hello, this is Chiaki System Admin</title>
        <script type="text/javascript">
            var actions = <?= $actions ?>;
            var shippers = <?= $shippers ?>;
            var staffs = <?= $staffs ?>;
            var locations = <?= $locations ?>;
        </script>
        <link rel="stylesheet" href="/system/style/main.css?v=<?= Config::get("sa.version") ?>" type="text/css" />
        <script type="text/javascript" src="/system/script/angular.min.js"></script>
        <script type="text/javascript" src="/system/script/angular-sanitize.min.js"></script>
        <script type="text/javascript" src="/system/script/ng-upload.min.js"></script>
        <script type="text/javascript" src="/system/controller/system.js?v=<?= Config::get("sa.version") ?>"></script>
        <script type="text/javascript" src="/system/controller/base-controller.js?v=<?= Config::get("sa.version") ?>"></script>
        <script type="text/javascript" src="/system/controller/action-controller.js?v=<?= Config::get("sa.version") ?>"></script>
    </head>
    <body style="padding:0px" ng-app="system" ng-controller="ActionController" ng-cloak>
        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%">
            <tr>
                <td style="width: 450px;vertical-align: top">
                    <div class="form-header">Danh sách thao tác</div>
                    <?= View::make("/system/action/list"); ?>
                </td>
                <td style="vertical-align: top;padding-left: 1px">
                    <div class="form-header">Chi tiết thao tác</div>
                    <?= View::make("/system/action/detail"); ?>
                </td>
            </tr>
        </table>        
    </body>
</html>