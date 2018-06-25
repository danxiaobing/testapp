//手机号正则表达式
var mobileRegularExpression = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
//座机号正则表达式
var telephoneRegularExpression = /^0\d{2,3}-?\d{7,8}$/;
//邮箱正则表达式
var emailRegularExpression = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/;
//汉字正则表达式
var chineseRegularExpression = /^[\u2E80-\u9FFF]+$/;
//1-16位数字正则表达式
var numberRegularExpression = /^[\d]{1,16}$/;
//英文字母a-z, A-Z正则表达式
var wordsRegularExpression = /^[a-zA-Z]*$/;
//公司组织机构代码
var organizationCodeRegularExpression = /[a-zA-Z0-9]{8}-[a-zA-Z0-9]/;
//员工工号正则表达式
var rag = /^\d{4}$/;

//全选
function checkAllNodes(obj) {
    // body...
    var allorun= $(obj).text();

    $("#qx-checkNode ul.ztree").each(function() {

        var treeObj=$.fn.zTree.getZTreeObj($(this).attr("id"));
        if(allorun=="全选"){
            treeObj.checkAllNodes(true);
            $(obj).text("取消");
        }else{
            treeObj.checkAllNodes(false);
            $(obj).text("全选");
        }
    });
}


//反选
$("#backcheck").click(function(event) {

    $("#qx-checkNode ul.ztree").each(function() {

        var treeObj=$.fn.zTree.getZTreeObj($(this).attr("id"));

        var nodesChecked = treeObj.getCheckedNodes(true);
        var nodesunChecked = treeObj.getCheckedNodes(false);

        for (var i = nodesChecked.length - 1; i >= 0; i--) {

            treeObj.checkNode(nodesChecked[i],false,false);
        }

        for (var i = nodesunChecked.length - 1; i >= 0; i--) {
            treeObj.checkNode(nodesunChecked[i],true,true);
        }
    });

});

//提交节点dataid
$("#treesubmit").click(function(event) {

    var treestring = "";

    $("#qx-checkNode ul.ztree").each(function() {

        var treeObj=$.fn.zTree.getZTreeObj($(this).attr("id"));

        var nodesChecked = treeObj.getCheckedNodes(true);

        for (var i = nodesChecked.length - 1; i >= 0; i--) {
            // if(nodesChecked[i].isParent==false){
            //     treestring+=nodesChecked[i].id+",";
            // }
            treestring+=nodesChecked[i].id+",";
        }
    });

    $("#treedata").val(treestring.substr(0,treestring.length-1));
    $('#treeform').submit();
});


//选择事件
function S_NodeCheck(e, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj(treeId),
        nodes = zTree.getCheckedNodes(true)
    var ids = '', names = ''

    for (var i = 0; i < nodes.length; i++) {
        ids   += ','+ nodes[i].id
        names += ','+ nodes[i].name
    }
    if (ids.length > 0) {
        ids = ids.substr(1), names = names.substr(1)
    }

    var $from = $('#'+ treeId).data('fromObj')

    if ($from && $from.length){
        $from.val(names);
        $("#parentId").val(ids);
    }
}
//单击事件
function S_NodeClick(event, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj(treeId)

    zTree.checkNode(treeNode, !treeNode.checked, true, true)

    event.preventDefault()
}

//获取当前时间  yyyy-mm-dd
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }

    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
    return currentdate;
}

//金额转化中文大写

function changeNumMoneyToChinese(money, cnunit, cnz, type) {
    var cnNums = new Array("零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖"); //汉字的数字
    var cnIntRadice = new Array("", "拾", "佰", "仟"); //基本单位
    var cnIntUnits = new Array("", "万", "亿", "兆"); //对应整数部分扩展单位
    var cnDecUnits = new Array("角", "分", "毫", "厘");
    if(type) {
        cnDecUnits = new Array("", "", "", ""); //对应小数部分单位
    }
    var cnInteger = cnz; //整数金额时后面跟的字符
    var cnIntLast = cnunit; //整型完以后的单位
    var maxNum = 999999999999999.9999; //最大处理的数字
    var IntegerNum; //金额整数部分
    var DecimalNum; //金额小数部分
    var ChineseStr = ""; //输出的中文金额字符串
    var parts; //分离金额后用的数组，预定义
    if (money == "") {
        return "";
    }
    money = parseFloat(money);
    if (money >= maxNum) {
        alert('超出最大处理数字');
        return "";
    }
    if (money == 0) {
        ChineseStr = cnNums[0] + cnIntLast + cnInteger;
        return ChineseStr;
    }
    money = money.toString(); //转换为字符串
    if (money.indexOf(".") == -1) {
        IntegerNum = money;
        DecimalNum = '';
    } else {
        parts = money.split(".");
        IntegerNum = parts[0];
        DecimalNum = parts[1].substr(0, 4);
    }
    if (parseInt(IntegerNum, 10) > 0) { //获取整型部分转换
        var zeroCount = 0;
        var IntLen = IntegerNum.length;
        for (var i = 0; i < IntLen; i++) {
            var n = IntegerNum.substr(i, 1);
            var p = IntLen - i - 1;
            var q = p / 4;
            var m = p % 4;
            if (n == "0") {
                zeroCount++;
            } else {
                if (zeroCount > 0) {
                    ChineseStr += cnNums[0];
                }
                zeroCount = 0; //归零
                ChineseStr += cnNums[parseInt(n)] + cnIntRadice[m];
            }
            if (m == 0 && zeroCount < 4) {
                ChineseStr += cnIntUnits[q];
            }
        }
        if(!type){
            ChineseStr += cnIntLast;
        }

        //整型部分处理完毕
    }
    if (DecimalNum != '') { //小数部分
        if(type){
            ChineseStr += '点';
        }
        var decLen = DecimalNum.length;
        for (var i = 0; i < decLen; i++) {
            var n = DecimalNum.substr(i, 1);
            if (n != '0') {
                ChineseStr += cnNums[Number(n)] + cnDecUnits[i];
            }
        }
    }
    if (ChineseStr == '') {
        ChineseStr += cnNums[0] + cnIntLast + cnInteger;
    } else if (DecimalNum == '') {
        ChineseStr += cnInteger;
    }
    if(type){
        ChineseStr += cnIntLast;
    }
    return ChineseStr;

}