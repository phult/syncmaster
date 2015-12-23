<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Hello, this's Chiaki System Admin</title>
        <link rel="shortcut icon" href="/system/image/logo.png" />

        <link rel="stylesheet" href="/system/style/main.css?v=<?= Config::get("sa.version") ?>" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/system/style/ui-lightness/jquery-ui-1.8.21.custom.css" />   

        <style type="text/css">
            #simplemodal-overlay {background-color:#000;}
        </style>

        <script type="text/javascript" src="/system/script/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="/system/script/jquery.simplemodal.1.4.2.min.js"></script>        
        <script type="text/javascript" src="/system/script/jquery.ui.core.min.js"></script>
        <script type="text/javascript" src="/system/script/jquery.ui.datepicker.min.js"></script>
        <script type="text/javascript" src="/system/script/jquery-ui-timepicker-addon.js"></script>   
        <script type="text/javascript" src="/system/script/angular.min.js"></script>
        <script type="text/javascript" src="/system/script/angular-sanitize.min.js"></script>
        <script type="text/javascript" src="/system/script/ng-upload.min.js"></script>
        <script type="text/javascript" src="/system/controller/system.js?v=<?= Config::get("sa.version") ?>"></script>
        <script type="text/javascript" src="/system/controller/base-controller.js?v=<?= Config::get("sa.version") ?>"></script>
    </head>

    <body ng-app="system" ng-cloak>
        <?php echo View::make('/system/layout/header') ?>
        @yield("content")
        <?php echo View::make('/system/layout/footer') ?>
        <!-- Ticket -->
        <script type="text/javascript" src="/system/controller/ticket-controller.js?v=<?= Config::get("sa.version") ?>"></script>
        <div ng-controller="TicketController">
            <?php echo View::make('/system/ticket/form', array( "mode" => "dialog" )) ?>
            <?php echo View::make('/system/ticket/list', array( "mode" => "dialog" )) ?>
        </div>
    </body>
</html>