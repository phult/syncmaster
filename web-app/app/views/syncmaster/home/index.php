<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title>Hi. I'm SyncMaster</title>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">        
        <link href="/common/css/style.css" rel="stylesheet" type="text/css">
        <link href="/common/css/layout.css" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,400italic,600italic' rel='stylesheet' type='text/css'>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="/common/audio.css" media="screen">    
        <link rel="stylesheet" media="all" href="/common/css/jScrollPane.css" type="text/css" />

        <script type="text/javascript" src="/common/js/jquery.js"></script>
        <script type="text/javascript" src="/common/js/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="/common/js/jScrollPane.js"></script>
        <script type="text/javascript" src="/common/js/countdown.js"></script> 

        <script type="text/javascript" src="/common/js/angular.min.js"></script>
        <script type="text/javascript" src="/common/js/angular-sanitize.min.js"></script>
        <script type="text/javascript" src="/common/js/socket.io.js"></script>
        <script type="text/javascript" src="/syncmaster/js/controller/base-controller.js?v=<?= Config::get("syncmaster.version") ?>"></script>
        <script type="text/javascript" src="/syncmaster/js/controller/syncmaster-controller.js?v=<?= Config::get("syncmaster.version") ?>"></script>
    </head>

    <body ng-app="syncmaster" ng-controller="SyncMasterController">
        <script type="text/javascript">
                    var serverURL = "<?= $serverURL ?>";
                    var user = <?= json_encode($user) ?>;
        </script>

        <section id="unifiedSidebar">
            <article class="userAvatar"> 
                <img src="/syncmaster/images/user.png" alt="{{user.full_name}}">
            </article>
            <article class="userName"> <span>{{user.full_name}}</span></article>
            <article class="uploadMusic">
                <a href="/logout" style="cursor: pointer">Đăng xuất</a>
            </article>
        </section>
        <section id="mainContentCover" >
            <header id="TopBar">
                <div class="logo">
                    <img src="/syncmaster/images/megasyncmaster.png" alt="Mega Play!">
                </div>
                <div class="sologan">
                    Sync Master
                </div>
                <div class="notification">
                    <img src="/syncmaster/images/make-love.png" height="30px">
                </div> 
            </header>
            <section id="mainContent">                
                <article id="leftContent" ng-show="false">
                    <div>
                        <a style="font-weight: bold"><img src="/syncmaster/images/music.png" width="16" height="16" style="vertical-align: middle">&nbsp;</a>
                    </div>
                    <div class="accInfo">
                        <b>Người đăng:</b>
                    </div>
                    <div id="lyrics">        	
                        <label><b>Lời bài hát:</b></label>  
                        <p></p>
                    </div>
                    <div class="Message">
                        <label><b>Thông điệp của người đăng:</b></label>
                        <div style="white-space: pre-line"></div>
                    </div>
                </article>
                <article id="rightContent">
                    <div class="listName">Activity</div>
                    <div id="pane2" class="scroll-pane" style="margin: 0">
                        <ul class="list" style="padding-bottom: 40px">
                            <!-- list activities -->
                            <li class="list-item pause" ng-repeat="message in messages">
                                <div class="item">
                                    <span class="item-number" ng-click="showSMSForm(message)"><img src="/syncmaster/images/{{getMessageIcon(message)}}.png" width="20"></span>
                                    <span class="item-name" ng-click="showSMSForm(message)">
                                        <a title="{{formatPhone(message.phone)}}">{{getContactDispByMessage(message)}}</a>
                                    </span>
                                    <span class="item-time">
                                        <a title="">{{summarizeDateTime(message.create_time)}}</a>
                                    </span>
                                    <br/>
                                    <span class="item-description">
                                        <a>{{message.data}}</a>
                                    </span>
                                </div>
                                <div class="tool-song">
                                </div>
                                <div class="clear"></div>
                            </li>                            
                            <!-- end list activities -->
                        </ul>
                    </div>
                    <div class="addItem">
                        <input type="button" class="button" ng-click="showSMSForm()" value="New SMS"/>

                        <input type="button" class="button" 
                               ng-click="onMessage({phone: '01662459579', 'data': 'hello baby', type: 'in_sms', create_time: '2015-12-12 23:59:59'})" 
                               value="Test Incoming SMS"/>
                        <input type="button" class="button" 
                               ng-click="onMessage({phone: '01662459579', type: 'in_call', create_time: '2015-12-12 23:59:59'})" 
                               value="Test Incoming Call"/>
                    </div>
                </article>
                <div class="clear"></div>
                <div id="AddListItem" class="popup">                    
                    <?= View::make("/syncmaster/components/sms-form"); ?>
                </div>

                <div id="processing-popup" class="popup">
                    Đang kết nối đến server...
                </div>

                <div class="clear"></div>
            </section>
            <div class="clear"></div>          
        </section>
        <!-- Footer -->
        <footer id="footer">
        </footer>
        <div id="backgroundummy" style="dissyncmaster:none"></div>
        <script language="JavaScript">
                    document.addEventListener('DOMContentLoaded', function () {
                        if (Notification.permission !== "granted")
                            Notification.requestPermission();
                    });
                    WebFontConfig = {
                        google: {families: ['Noto+Sans:400,700italic,700,400italic:latin']}
                    };
                    (function () {
                        var wf = document.createElement('script');
                        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                                '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
                        wf.type = 'text/javascript';
                        wf.async = 'true';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(wf, s);
                    })();
                    jQuery(document).ready(function () {
                        //Login				
//                        jQuery('#pane2').jScrollPane({showArrows: true});                        
                        jQuery('#backgroundummy').click(function () {
                            jQuery('.popup').fadeOut("fast");
                            jQuery('#backgroundummy').fadeOut("fast");
                        });

                        jQuery('#dummybackground').click(function () {
                            jQuery('#LoginArea').fadeOut("slow");
                            jQuery('#dummybackground').fadeOut("fast");

                        });
                    });
        </script>

    </body>

</html>
