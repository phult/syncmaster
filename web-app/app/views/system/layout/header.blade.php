<style>
    td.active{
        background-color: #008EBE;color: #FFF;
        text-decoration: none;
    }
    span.module-feature-item{
        padding-top: 6px;
        padding-bottom: 3px;
    }
    span.module-feature-item:hover{
        border-bottom: 2px solid #FFF;
    }
</style>
<script type="text/javascript">
    var currentStaffId = <?= Session::get("user")->id; ?>;
    var initClockValue = {
        date: '<?= date("d/m/Y") ?>',
        time: '<?= date("H:i:s") ?>'
    };
    var thresholdToAlertFullMessage = <?= Config::get("sa.thresholdToAlertFullMessage") != null ? Config::get("sa.thresholdToAlertFullMessage") : 0 ?>;
<?php
$clockConfig = Config::get("sa.clock");
?>
    initClockValue.showDate = <?= (isset($clockConfig) && array_key_exists("showDate", $clockConfig) && $clockConfig["showDate"] ) ? "true" : "false" ?>;
    initClockValue.showTime = <?= (isset($clockConfig) && array_key_exists("showTime", $clockConfig) && $clockConfig["showTime"] ) ? "true" : "false" ?>;
<?php ?>
</script>

<?php
$liveUpdate = Config::get("sa.liveUpdate", null);
if ($liveUpdate != null) {
    $liveUpdateServerUrl = "http://" . $liveUpdate["host"] . ":" . $liveUpdate["port"];
    ?>
    <script type="text/javascript" src="<?= $liveUpdateServerUrl ?>/socket.io/socket.io.js"></script>
    <script type="text/javascript">
    var liveUpdateServerUrl = "<?= $liveUpdateServerUrl ?>";
    </script>
    <?php
}
?>

<script type="text/javascript" src="/system/controller/header-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<script type="text/javascript" src="/system/controller/notification-controller.js?v=<?= Config::get("sa.version") ?>"></script>
<!-- HEADER -->
<div ng-controller="HeaderController" 
     style="background-image:url('/system/image/bkg_top.gif');height:30px;border-bottom: 5px solid #008EBE;position: fixed;width: 100%;z-index: 999;">    
    <table width="100%" border="0px" cellspacing="0" cellpadding="0">
        <tr>
            <td ng-mouseenter="isShowShortcutMenu = true"
                width="40px" style="background-color: {{isShowShortcutMenu ? '#FFF':'#EBEEDD'}};cursor: pointer" align="center">
                <img src="<?= Config::get("sa.application.icon") ?>" style="height: 24px"/>
                <div ng-show="isShowShortcutMenu">
                    <?= View::make("/system/layout/shortcut-menu") ?>
                </div>
            </td>
            <td ng-mouseenter="isShowShortcutMenu = true"
                onclick="window.location.href = '/system/home'"
                width="90px"                 
                style="line-height: 15px;background-color: {{isShowShortcutMenu ? '#FFF':'#EBEEDD'}};font-family: Arial,Helvetica,sans-serif; font-size: 14px;font-weight: 700;color: #749B2A;cursor: pointer">
                    <?= Config::get("sa.application.name") ?>
            </td>
            <td valign="bottom">
                <table ng-controller="SystemNotificationController"
                       height="30px" cellspacing="0" cellpadding="5" style="font-family: Arial,Helvetica,sans-serif; font-size: 13px;font-weight: 700;color: #6E6E6E" >
                    <tr style="cursor:pointer">   
                        <?php
                        //var_dump(Config::get("module"));
                        foreach (Config::get("module") as $moduleName => $module) {
                            if ($module["showInTab"] && array_key_exists("activeWhen", $module)) {
                                eval('$activeWhenValue = ' . $module["activeWhen"] . ';');
                                ?>
                                <td onclick="window.location.href = '<?= $module["url"] ?>'" 
                                    class="<?= $activeWhenValue == 1 ? "active" : "" ?>"><?= $module["title"] ?>
                                    <span ng-show="count.<?= $moduleName ?> != null && count.<?= $moduleName ?> > 0"
                                          style="padding: 2px 6px;background: #cc0000;color: #ffffff;font-weight: bold;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;font-size: 11px;" id="notification_count">
                                        {{count.<?= $moduleName ?>}}
                                        
                                    </span>
                                </td>
                                <?php
                            }
                        }
                        ?>                        
                        <td>
                            <img src="/system/image/ajax-loader.gif" 
                                 ng-show="isShowLoading"
                                 alt="loading" style="height: 12px"/>
                        </td>
                    </tr>
                </table>
                <table ng-controller="SystemNotificationController"
                       height="30px" cellspacing="0" cellpadding="5" style="font-family: Arial,Helvetica,sans-serif; font-size: 13px;font-weight: 700;color: #6E6E6E" >
                    <tr style="cursor:pointer">   
                        <?php
                        //var_dump(Config::get("module"));
                        foreach (Config::get("module") as $moduleName => $module) {
                            if ($module["showInTab"] && array_key_exists("activeWhen", $module)) {
                                eval('$activeWhenValue = ' . $module["activeWhen"] . ';');
                                ?>
                                <td onclick="window.location.href = '<?= $module["url"] ?>'" 
                                    class="<?= $activeWhenValue == 1 ? "active" : "" ?>"><?= $module["title"] ?>
                                    <span ng-show="count.<?= $moduleName ?> != null && count.<?= $moduleName ?> > 0"
                                          style="padding: 2px 6px;background: #cc0000;color: #ffffff;font-weight: bold;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;font-size: 11px;" id="notification_count">
                                        {{count.<?= $moduleName ?>}}
                                    </span>
                                        <?php
                                        if ($activeWhenValue == 1 && key_exists("features", $module)) {
                                            $features = $module["features"];
                                            $defaultFeature = $module["defaultFeature"];
                                            ?>
                                        <span ng-init="currentFeature = '<?= $defaultFeature ?>'">:
                                            <?php
                                            foreach ($features as $featureCode => $feature) {
                                                ?>
                                                <span 
                                                    onclick="event.stopPropagation()"
                                                    class="module-feature-item"
                                                    style="margin-left: 5px{{currentFeature == '<?= $featureCode ?>' ? ';border-bottom: 2px solid #FFF':''}}"
                                                      ng-click="currentFeature = '<?= $featureCode ?>';<?= (array_key_exists("ngClick", $feature) ? $feature["ngClick"] : "") . ';' ?>$event.stopPropagation();"
                                                      >
                                                          <?= $feature["title"] ?>
                                                </span>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </span>
                                </td>
                                <?php
                            }
                        }
                        ?>                        
                        <td>
                            <img src="/system/image/ajax-loader.gif" 
                                 ng-show="isShowLoading"
                                 alt="loading" style="height: 12px"/>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <span style="float: right;font-size: 13px;">
                    <span ng-if="serverDate != null || serverTime != null"
                          style="border:1px solid #CCC;cursor: default;padding-left: 5px;padding-right: 5px">
                        <span style="border:1px solid #">{{serverDate}} {{serverTime}}</span>
                    </span>
                    &nbsp;
                    <img src="/system/image/Email-icon.png" alt="message" style="cursor: pointer;max-width: 16px;" ng-show="messagesCount > 0" />
                    <a ng-show="messagesCount > 0" href="/system/message" style="text-decoration: none;padding-left: 5px;padding-right: 5px{{messagesCount > 0 ? ';color: #FFF;background-color:#FF0000' : ';color: #000'}}">
                        {{messagesCount}} tin nhắn
                    </a>
                    &nbsp;
                    <img src="/system/image/ticket.png" alt="ticket" style="cursor: pointer"
                         ng-show="ticketsCount > 0"
                         ng-click="$root.$broadcast('ticket.openListTicketDialog');"/>
                    <span style="cursor: pointer;padding-left: 5px;padding-right: 5px{{ticketsCount > 0 ? ';color: #FFF;background-color:#FF0000' : ';color: #000'}}" 
                          ng-show="ticketsCount > 0"
                          ng-click="$root.$broadcast('ticket.openListTicketDialog');">
                        {{ticketsCount}} {{ticketsCount < 2 ? 'ticket' : 'tickets'}}
                    </span>
                    &nbsp;
                    <img src="/system/image/online_bullet.png" 
                         title="Online mode"
                         alt="online bullet" ng-show="!liveUpdate.enable || liveUpdate.status == 'online'"/>
                    <img src="/system/image/offline_bullet.png" 
                         title="Offline mode"
                         alt="offline bullet" ng-show="liveUpdate.enable && liveUpdate.status == 'offline'"/>
                         <?php
                         if (Session::has("user")) {
                             echo Session::get("user")->username;
                         }
                         ?>
                    &nbsp;
                    <span 
                        title="{{ otherOnlineStaffsDetail}}"
                        ng-show="otherOnlineStaffs.length > 0"
                        ng-click="isShowOtherOnlineStaffs = true;"
                        style="border:1px solid #CCC;cursor: default;padding-left: 5px;padding-right: 5px">
                        {{otherOnlineStaffsSummary}}
                    </span>
                    &nbsp;
                    <a href="/system/home/logout" class="command-link">logout</a>
                    &nbsp;&nbsp;
                </span>
            </td>
        </tr>
    </table>
    <?= View::make("/system/layout/notification") ?>
</div>
<div style="width: 100%;padding-top: 35px;">
</div>
