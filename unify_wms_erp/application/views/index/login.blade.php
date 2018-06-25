<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>云仓管家 仓储管理系统 系统登录</title>
    <meta name="renderer" content="webkit">
    <script src="/static/BJUI/B-JUI/js/jquery-1.11.3.min.js"></script>
    <script src="/static/BJUI/B-JUI/js/jquery.cookie.js"></script>
    <link href="/static/BJUI/B-JUI/themes/css/bootstrap.min.css" rel="stylesheet">

    <style type="text/css">
        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: "Verdana", "Tahoma", "Lucida Grande", "Microsoft YaHei", "Hiragino Sans GB", sans-serif;
            background: url('/static/images/loginbg_09.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .form-control{height:50px;background-color: #f1f1f1;border-radius: 0; box-shadow: none;}
        .form-group {margin-bottom: 20px;}
        .input-group .form-control {width: 95%;}
        .input-group-addon {border-radius: 0;}
        .input-group-addon:last-child {border-left: 1px solid #ccc;}
        .main_box{position:absolute; top:42%; left:50%; margin:-200px 0 0 -180px; padding:15px 20px; width:360px; height:485px; min-width:320px; background:#FAFAFA; background:rgba(255,255,255,1); box-shadow: 1px 2px 8px rgba(0,0,0,0.5); border-radius:0;}
        .login_msg{line-height:50px;min-height: 20px;}
        .input-group >.input-group-addon.code{padding:0;}
        #captcha_img{cursor:pointer;height: 48px;}
        .main_box .logo img{height:35px;}
        @media (min-width: 768px) {
            .main_box {margin-left:-240px; padding:15px 55px; width:480px;}
            .main_box .logo img{height:40px;}
        }

        .btnlogin {width: 100%;border-radius: 0;background-color: #43c146;}
        .btnlogins {width: 100%;border-radius: 0;border-color: #43c146;color: #43c146;}
        .banquan {position: absolute;bottom: 5%;left: 10%;font-size: 16px;width: 90%;color:#FFF;}
        .banquan span {width: 29%;text-align: center;display: inline-block;color:#FFF;}
    </style>
    <script type="text/javascript">
        $(function() {
            choose_bg();
         //   changeCode();

            $("#captcha_img").click(function(){
                changeCode();
            });

        });
        function changeCode(){
            $("#captcha_img").attr("src", "/index/vcode?t="+ (new Date().getTime()));
        }
        function choose_bg() {
            var bg = Math.floor(Math.random() * 9 + 1);
            $('body').css('background-image', 'url(/static/images/loginbg_0'+ 4 +'s.jpg)');
        }
    </script>
</head>
<body>
<!--[if lte IE 7]>
<style type="text/css">
    #errorie {position: fixed; top: 0; z-index: 100000; height: 30px; background: #FCF8E3;}
    #errorie div {width: 900px; margin: 0 auto; line-height: 30px; color: orange; font-size: 14px; text-align: center;}
    #errorie div a {color: #459f79;font-size: 14px;}
    #errorie div a:hover {text-decoration: underline;}
</style>
<div id="errorie"><div>您还在使用老掉牙的IE，请升级您的浏览器到 IE8以上版本 <a target="_blank" href="http://windows.microsoft.com/zh-cn/internet-explorer/ie-8-worldwide-languages">点击升级</a>&nbsp;&nbsp;强烈建议您更改换浏览器：<a href="http://down.tech.sina.com.cn/content/40975.html" target="_blank">谷歌 Chrome</a></div></div>
<![endif]-->
<div class="container">
    <div class="main_box">
        <form action="/index/UserLogin" id="login_form" method="post">
            <div style="height: 20px;"></div>
            <p class="text-center logo"><img src="/static/images/logo2.png" height="45"></p>
            <div style="height: 15px;"></div>
            <div class="form-group">
                <input type="text" class="form-control"  name="username" value="" placeholder="登录账号" aria-describedby="sizing-addon-user">
                <!-- <div class="input-group">
                    <span class="input-group-addon" id="sizing-addon-user"><span class="glyphicon glyphicon-user"></span></span>
                    <input type="text" class="form-control"  name="username" value="" placeholder="登录账号" aria-describedby="sizing-addon-user">
                </div> -->
            </div>
            <div class="form-group">
            <input type="password" class="form-control"  name="passwordhash" placeholder="登录密码" aria-describedby="sizing-addon-password">
                <!-- <div class="input-group">
                    <span class="input-group-addon" id="sizing-addon-password"><span class="glyphicon glyphicon-lock"></span></span>
                    <input type="password" class="form-control"  name="passwordhash" placeholder="登录密码" aria-describedby="sizing-addon-password">
                </div> -->
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <div class="input-group">
                    <!-- <span class="input-group-addon" id="sizing-addon-password"><span class="glyphicon glyphicon-exclamation-sign"></span></span> -->
                    <input type="text" class="form-control"  name="captcha" placeholder="验证码" aria-describedby="sizing-addon-password">
                    <span class="input-group-addon code" id="basic-addon-code"><img id="captcha_img" src="/index/vcode?a=123" alt="点击更换" title="点击更换" class="m"></span>
                </div>
            </div>
            <div class="login_msg text-center"><font color="red">{{$msg}}</font></div>
            <div class="text-center">
                <button type="submit" id="login_ok" class="btn btn-success btn-lg btnlogin">&nbsp;登&nbsp;录&nbsp;</button>
                <br><br>
                <button type="reset" class="btn btn-default btn-lg btnlogins">&nbsp;重&nbsp;置&nbsp;</button>
            </div>
           <!--  <div class="text-center">
                <hr>
                 2014 - 2016 <a href="http://www.chinayie.com">上海国烨跨境电子商务有限公司</a> 
                <a href="http://www.chinayie.com/" style="color:white;">2016－2017 上海国烨跨境电子商务有限公司</a><br>
                <a href="http://www.hyplc.com/" style="color:white;">战略合作：恒阳石化物流有限公司</a>
            </div> -->
        </form>
    </div>
</div>
    <div class="banquan"><span>版权所有 copyright © 2015-2018</span>|<span>沪ICP备15040931号-1</span>|<span>增值电信业务经营许可证：沪B2-20160174</span></div>
</body>
</html>