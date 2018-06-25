<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>云仓管家 仓储管理系统 系统登录</title>
    <!-- <meta name="Keywords" content="国烨,大宗商品,跨境,撮合,化工"/>
    <meta name="Description" content="国烨跨境大宗商品电商平台是中国大宗商品电子商务领域第一门户类服务网站,致力于发展下游贸易分销商务平台，已逐步成为贯穿整条产业链的全球性大宗商品一站式电商平台"/> -->
    <link href="favicon.ico" mce_href="favicon.ico" rel="icon">
    <!-- bootstrap - css -->
    <link href="/static/BJUI/B-JUI/themes/css/bootstrap.css" rel="stylesheet">
    <!-- core - css -->
    <link href="/static/BJUI/B-JUI/themes/css/style.css?version={{VERSION}}" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/themes/green/core.css" id="bjui-link-theme" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/themes/css/fontsize.css" id="bjui-link-theme" rel="stylesheet">
    <!-- plug - css -->
    <link href="/static/BJUI/B-JUI/plugins/kindeditor_4.1.11/themes/default/default.css" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/plugins/nice-validator-1.0.7/jquery.validator.css" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/plugins/bootstrapSelect/bootstrap-select.css" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/plugins/webuploader/webuploader.css" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/themes/css/FA/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/BJUI/B-JUI/plugins/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed" href="/static/BJUI/assets/ico/apple-touch-icon-precomposed.png">
    <!-- <link rel="shortcut icon" href="/static/BJUI/assets/ico/favicon.png">
    -->
    <!--[if lte IE 7]>
    <link href="/static/BJUI/B-JUI/themes/css/ie7.css" rel="stylesheet">
    <![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lte IE 9]>
    <script src="/static/BJUI/B-JUI/other/html5shiv.min.js"></script>
    <script src="/static/BJUI/B-JUI/other/respond.min.js"></script>
    <![endif]-->
    <!-- jquery -->
    <script src="/static/BJUI/B-JUI/js/jquery-1.11.3.min.js"></script>
    <script src="/static/BJUI/B-JUI/js/jquery.cookie.js"></script>
    <!--[if lte IE 9]>
    <script src="/static/BJUI/B-JUI/other/jquery.iframe-transport.js"></script>
    <![endif]-->
    <!--jquery-ui移动位置-->
<!--     <script src="/static/BJUI/B-JUI/js/highlight.min.js"></script>
    <script src="/static/BJUI/B-JUI/js/jquery-ui.js"></script>
    <script src="/static/BJUI/B-JUI/js/raindrops.js"></script> -->
    <!-- B-JUI -->
    <script src="/static/BJUI/B-JUI/js/bjui-all.js?version={{VERSION}}"></script>
    <script src="/static/BJUI/B-JUI/js/iconfont.js"></script>
    <!-- plugins -->
    <!-- swfupload for kindeditor -->
    <script src="/static/BJUI/B-JUI/plugins/swfupload/swfupload.js"></script>
    <!-- Webuploader -->
    <script src="/static/BJUI/B-JUI/plugins/webuploader/webuploader.js"></script>
    <!-- kindeditor -->
    <script src="/static/BJUI/B-JUI/plugins/kindeditor_4.1.11/kindeditor-all-min.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/kindeditor_4.1.11/lang/zh-CN.js"></script>
    <!-- colorpicker -->
    <script src="/static/BJUI/B-JUI/plugins/colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- ztree -->
    <script src="/static/BJUI/B-JUI/plugins/ztree/jquery.ztree.all-3.5.js"></script>
    <!-- nice validate -->
    <script src="/static/BJUI/B-JUI/plugins/nice-validator-1.0.7/jquery.validator.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/nice-validator-1.0.7/jquery.validator.themes.js"></script>
    <!-- bootstrap plugins -->
    <script src="/static/BJUI/B-JUI/plugins/bootstrap.min.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/bootstrapSelect/bootstrap-select.min.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/bootstrapSelect/defaults-zh_CN.min.js"></script>
    <!-- icheck -->
    <script src="/static/BJUI/B-JUI/plugins/icheck/icheck.min.js"></script>
    <!-- HighCharts -->
    <script src="/static/BJUI/B-JUI/plugins/highcharts/highcharts.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/highcharts/highcharts-3d.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/highcharts/themes/gray.js"></script>
    <!--bootstrap datepicker-->
    <script src="/static/BJUI/B-JUI/plugins/datepicker/bootstrap-datetimepicker.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/datepicker/bootstrap-datetimepicker.zh-CN.js"></script>
    <!-- other plugins -->
    <!-- other plugins -->
    <script src="/static/BJUI/B-JUI/plugins/other/jquery.autosize.js"></script>
    <link href="/static/BJUI/B-JUI/plugins/uploadify/css/uploadify.css" rel="stylesheet">
    <script src="/static/BJUI/B-JUI/plugins/uploadify/scripts/jquery.uploadify.min.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/download/jquery.fileDownload.js"></script>
    <script src="/static/BJUI/B-JUI/plugins/printarea/jquery.PrintArea.js"></script>
    <!-- init -->
    <script type="text/javascript">
        $(function() {
            BJUI.init({
                JSPATH       : '/static/BJUI/B-JUI/',         //[可选]框架路径
                PLUGINPATH   : '/static/BJUI/B-JUI/plugins/', //[可选]插件路径
                loginInfo    : {url:'/logintimeout', title:'登录', width:530, height:420}, // 会话超时后弹出登录对话框
                statusCode   : {ok:200, error:300, timeout:301}, //[可选]
                ajaxTimeout  : 300000, //[可选]全局Ajax请求超时时间(毫秒)
                alertTimeout : 3000,  //[可选]信息提示[info/correct]自动关闭延时(毫秒)
                pageInfo     : {total:'totalRow', pageCurrent:'pageCurrent', pageSize:'pageSize', orderField:'orderField', orderDirection:'orderDirection'}, //[可选]分页参数
                keys         : {statusCode:'statusCode', message:'message'}, //[可选]
                ui           : {
                    sidenavWidth     : 220,
                    showSlidebar     : true, //[可选]左侧导航栏锁定/隐藏
                    overwriteHomeTab : false //[可选]当打开一个未定义id的navtab时，是否可以覆盖主navtab(我的主页)
                },
                debug        : true,    // [可选]调试模式 [true|false，默认false]
                theme        : 'green' // 若有Cookie['bjui_theme'],优先选择Cookie['bjui_theme']。皮肤[五种皮肤:default, orange, purple, blue, red, green]
            })
            //时钟
            var strTime='{{$servicetime}}';    //字符串日期格式，获取服务器时间显示         
            var today = new Date(Date.parse(strTime.replace(/-/g,  "/"))), time = today.getTime()
            $('#bjui-date').html(today.formatDate('yyyy/MM/dd'))
            setInterval(function() {
                today = new Date(today.setSeconds(today.getSeconds() + 1))
                $('#bjui-clock').html(today.formatDate('HH:mm:ss'))
            }, 1000);          
        })

        /*window.onbeforeunload = function(){
         return "确定要关闭本系统 ?";
         }*/

        //菜单-事件-zTree
        function MainMenuClick(event, treeId, treeNode) {
            if (treeNode.target && treeNode.target == 'dialog' || treeNode.target == 'navtab')
                event.preventDefault()

            if (treeNode.isParent) {
                var zTree = $.fn.zTree.getZTreeObj(treeId)

                zTree.expandNode(treeNode)
                return
            }

            if (treeNode.target && treeNode.target == 'dialog')
                $(event.target).dialog({id:treeNode.targetid, url:treeNode.url, title:treeNode.name})
            else if (treeNode.target && treeNode.target == 'navtab')
                $(event.target).navtab({id:treeNode.targetid, url:treeNode.url, title:treeNode.name, fresh:treeNode.fresh, external:treeNode.external})
        }

        // 满屏开关
        var bjui_index_container = 'container_fluid'

        function bjui_index_exchange() {
            bjui_index_container = bjui_index_container == 'container_fluid' ? 'container' : 'container_fluid'

            $('#bjui-top').find('> div').attr('class', bjui_index_container)
            $('#bjui-navbar').find('> div').attr('class', bjui_index_container)
            $('#bjui-body-box').find('> div').attr('class', bjui_index_container)
        }
    </script>
    <!-- highlight && ZeroClipboard -->
    <link href="/static/BJUI/assets/prettify.css" rel="stylesheet">
    <script src="/static/BJUI/assets/prettify.js"></script>
    <link href="/static/BJUI/assets/ZeroClipboard.css" rel="stylesheet">
    <script src="/static/BJUI/assets/ZeroClipboard.js"></script>
</head>
<body>
    <!--[if lte IE 7]>
    <div id="errorie">
        <div>
            您还在使用老掉牙的IE，正常使用系统前请升级您的浏览器到 IE8以上版本
            <a target="_blank" href="http://windows.microsoft.com/zh-cn/internet-explorer/ie-8-worldwide-languages">点击升级</a>
            &nbsp;&nbsp;强烈建议您更改换浏览器：
            <a href="http://down.tech.sina.com.cn/content/40975.html" target="_blank">谷歌 Chrome</a>
        </div>
    </div>
    <![endif]-->
<div id="bjui-top" class="bjui-header">
    <div class="container_fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapsenavbar" data-target="#bjui-top-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <nav class="collapse navbar-collapse" id="bjui-top-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="javascript:;" onclick="messageDialog()">消息&nbsp;<span class="badge bred" id="messageSpan">{{$count}}</span></a>
                </li>
                <li class="datetime">
                    <a>
                        <span id="bjui-date">0000/00/00</span>
                        <span id="bjui-clock">00:00:00</span>
                    </a>
                </li>
                <li>
                    <a href="#">操作员：{{ $user['opername'] }}</a>
                </li>
                @if($position)
                <li>
                <a href="#">岗位：{{ $position }}</a>
                @endif
                </li>
                <li>
                    <a href="/index/changepassword" data-toggle="dialog" data-id="sys_user_changepass" data-mask="true" data-width="600" data-height="400">修改密码</a>
                </li>
                <li>
                    <a href="/index/logout" style="font-weight:bold;">
                        &nbsp; <i class="fa fa-power-off"></i>
                        注销登录
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<header class="navbar bjui-header" id="bjui-navbar">

    <div class="container_fluid">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" id="bjui-navbar-collapsebtn" data-toggle="collapsenavbar" data-target="#bjui-navbar-collapse" aria-expanded="false">
                <i class="fa fa-angle-double-right"></i>
            </button>
            <a class="navbar-brand" href="/">
                <img src="/static/images/logogreen.png" height="52">
            </a>
        </div>

        <nav class="collapse navbar-collapse" id="bjui-navbar-collapse">
            <ul class="nav navbar-nav navbar-right" id="bjui-hnav-navbar">
                @foreach($navtab  as $key => $tab)
                <li @if($key == 0) class="active" @endif>
                    <a oncontextmenu = "customContextMenu(event)" ondragstart="return false" href="/navtab/{{$tab['sysno']}}" data-toggle="sidenav" data-id-key="targetid">
                        @if($tab['parentsysnoicon'] != '')
                        <svg class="icon" aria-hidden="true">
                            <use xlink:href="{{$tab['parentsysnoicon']}}"></use>
                        </svg>
                        @endif
                                    {{$tab['privilegename']}}
                    </a>
                </li>
                @endforeach
             
            </ul>
        </nav>

    </div>

</header>


<div id="bjui-body-box">

    <div class="container_fluid" id="bjui-body">

        <div id="bjui-sidenav-col">
            <div id="bjui-sidenav">
                <div id="bjui-sidenav-arrow" data-toggle="tooltip" data-placement="left" data-title="隐藏左侧菜单">
                    <i class="fa fa-caret-left" aria-hidden="true"></i>
                </div>
                <div id="bjui-sidenav-box"></div>
            </div>
        </div>

        <div id="bjui-navtab" class="tabsPage">

            <div id="bjui-sidenav-btn" data-toggle="tooltip" data-title="显示左侧菜单" data-placement="right">
                <i class="fa fa-caret-right" aria-hidden="true"></i>
            </div>
            <div class="tabsPageHeader">
                <div class="tabsPageHeaderContent">
                    <ul class="navtab-tab nav nav-tabs">
                        <li>
                            <a href="javascript:;">
                                <span>
                                    <!-- <i class="fa fa-home"></i>-->
                                    #maintab#
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tabsLeft">
                    <i class="fa fa-angle-double-left"></i>
                </div>
                <div class="tabsRight">
                    <i class="fa fa-angle-double-right"></i>
                </div>
                <div class="tabsMore">
                    <i class="fa fa-angle-double-down"></i>
                </div>
            </div>

            <ul class="tabsMoreList">
                <li>
                    <a href="javascript:;">#maintab#</a>
                </li>
            </ul>

            <div class="navtab-panel tabsPageContent">

                <div class="navtabPage unitBox">
                    <div class="bjui-pageContent  clearfix">
                    <div class="clear"></div>
                    <div class="toggleBtnare text-right">
                    <p>&nbsp;</p>
                    
                    <label>储罐单位：</label>
                        <button class="btn btn-success Btnare" data-type='1'>重量</button>
                        <button class="btn btn-success Btnare btnactive" data-type='2'>体积</button>
                    </div>
                    <!--area start-->
                    <div class="areatotalbox">

                    </div>

                    <!--area end-->
                    <div style="height: 200px;clear: both;"></div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
<style type="text/css">

</style>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/static/BJUI/B-JUI/other/ie10-viewport-bug-workaround.js"></script>

<script type="text/javascript">

function customContextMenu(event){
    event.preventDefault ? event.preventDefault():(event.returnValue = false);
};

function messageDialog(){
    BJUI.dialog({
        id:'system-messages', 
        url:'/index/message',

        loadingmask:false,
         title:'消息列表',
        data:{},
        width:800,
        height:500,
        mask:true,
        maxable:false,
        resizable:true,
        onClose:function() {
            BJUI.ajax('doajax', {
                url: 'index/getMessageCount',
                loadingmask: false,
                okCallback: function(json, options) {
                    if(json.code == 200){
                        $('#messageSpan').html(json.count);
                    }else{
                        BJUI.alertmsg('warn', '获取消息条数失败',{displayPosition:'middlecenter',displayMode:'fade'});
                    }
                }
                
            })
        }
    })
}

//储罐的动态效果

var setTime;
$('body').on('mouseenter','.waterbox',function(event) {

    var _this = $(this);

    _this.find('.pwt').css('display','block');

    var a = _this.find('.pwt').offset().left+350;
    var d = _this.find('.pwt').offset().top+310;
    var b = $(document.body)[0].clientWidth;
    var c = $(document.body)[0].clientHeight;

    _this.find('.pwt label').css('display','block');


    setTime = setTimeout(function(){

        $.get('/index/getPressure', function(data) {

            data = JSON.parse(data);
          
            var ddSpan = _this.find('.pwt dl');

           ddSpan.eq(0).find('dd span').html(data.pressure);
           ddSpan.eq(1).find('dd span').html(data.infusion);
           ddSpan.eq(2).find('dd span').html(data.temperature);

           _this.find('.pwt label').hide('fast');

        });

    },1000);

    // if (a > b) {

    //    // console.log("div不在可视范围");
    //     $(this).find('.pwt').css('left','-300px');
    // }
    // if(d > c){

    //    // console.log("div不在可视范围");
    // }

}).on('mouseleave','.waterbox',function(event) {
    $(this).find('.pwt').css('display','none');
    clearTimeout(setTime);
});

// $('body').on('mouseout','.waterbox ul',function(event) {
//     $(this).find('.pwt').css('display','none');
// });


// function hasScrolled(el, direction = "vertical") {
//     if(direction === "vertical") {
//         return el.scrollHeight > el.clientHeight;
//     }else if(direction === "horizontal") {
//         return el.scrollWidth < el.clientWidth;
//     }
//  }


//--------------获取储罐内容

var allWare = [];
var _areatotalbox = $('.areatotalbox');

$(function(){

    //请求储罐所有数据
    $.get('/index/getStorageList', function(data) {
       allWare = JSON.parse(data);
        addChuGuan(allWare,1);
    });

    //切换查看储罐的方式
    $('.Btnare').click(function(event) {

        $('.Btnare').addClass('btnactive');
        var dataType = $(this).attr('data-type');
        $(this).removeClass('btnactive');

        if(dataType == 1){

            addChuGuan(allWare,1);
        }
        else {
            addChuGuan(allWare,2);
        }
    });

});

function addChuGuan(argument,num) {

    _areatotalbox.html('');

    var htm = '';

       for (var i = 0; i < argument.length; i++) {
           
           htm += '<div class="areabox"><h4>'+argument[i].areaname+'</h4>';

            for (var j = 0; j < argument[i].storageData.length; j++) {

                htm += chuguanTpl(argument[i].storageData[j],num);
                    
            }

           htm += '</div>';

       }

   _areatotalbox.append(htm);

}

function chuguanTpl(datac,num){

    // console.log(num);

    var node ='';

    if(num == 1)
    {
        node +='<li style="color: #333;font-weight: bold;">' + datac.storagetanknature + '</li>'
             + '<li>总容量<br/><em>' + datac.actualcapacity + 'T</em></li>'
             + '<li>已用容量<br/><em>' + datac.stockqty + 'T</em></li>'
             + '<li>可用容量<br/><em>' + datac.leftqty + 'T</em></li>';
    }
    else {

        node += '<li style="color: #333;font-weight: bold;">' + datac.storagetanknature + '</li>'
             + '<li>总容量<br/><em>' + datac.theoreticalcapacity + 'm³</em></li>'
             + '<li>已用容量<br/><em>' + datac.havecapacity + 'm³</em></li>'
             + '<li>可用容量<br/><em>' + datac.leftcapacity + 'm³</em></li>';
    }


    var htm = '<div class="waterbox">'
            +'<ul>'
            + node
            + '</ul>'
            + '<div class="wt">'
            + '<div style="height:'+ parseInt(datac.stockqty/datac.actualcapacity*250) + 'px"></div>'
            + '</div>'
            + '<div class="pwt">'
            + '<label><i class="fa fa-cog fa-spin fa-fw"></i><em>稍等数据更新中...</em></label>'
            + '<dl>'
            + '<dt>罐顶压力</dt>'
            + '<dd><span>--</span> Kpa</dd>'
            + '</dl>'
            + '<dl>'
            + '<dt>罐內液位</dt>'
            + '<dd><span>--</span> M</dd>'
            + '</dl>'
            + '<dl>'
            + '<dt>罐內温度</dt>'
            + '<dd><span>--</span> ℃</dd>'
            + '</dl>'
            + '</div>'
            + '<h5>储罐编号：<span>' + datac.storagetankname + '</span></h5>'
            + '<p>' + datac.goodsname + '</p>'
            + '</div>';

            return htm;
}



</script>
</body>

</html>