<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="/system/style/main.css" type="text/css" />
        <title>Hello. I'm System Admin</title>
        <link rel="shortcut icon" href="/system/image/logo.png" />
    </head>
    <body style="margin: 0px;background-image: url('/system/image/bg.png')">
        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
        <form method="post" action="/system/home/login">
            <div align="center">
                <table width="500" cellpadding="6" cellspacing="0" class="form-table" style="border:1px solid #C60">
                    <tr class="form-header">
                        <td style="border-bottom:1px solid #C60"><img src="/system/image/security_lock.png" /></td>
                        <td style="border-bottom:1px solid #C60">Đăng nhập</td>
                    </tr>

                    <?php
                    if ( isset($status) && $status == "fail" ) {
                        ?>
                        <tr>
                            <td style="color:red;border-bottom:1px solid #C60" colspan="2">
                                Đăng nhập không thành công!
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                    <tr>
                        <td>Tên đăng nhập</td>
                        <td><input type="text" name="username" id="username" /></td>
                    </tr>
                    <tr>
                        <td>Mật khẩu</td>
                        <td><input type="password" name="password" id="password" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" name="login" id="login" value="Đăng nhập" /></td>
                    </tr>

                    <tr class="form-footer">
                        <td colspan="2">
                            System Admin. &copy; <?= date("Y") ?>. CHIAKI.VN
                        </td>
                    </tr>
                </table>                
            </div>
        </form>
    </body>
</html>