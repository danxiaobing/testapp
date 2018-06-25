<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockpipeinlist-table-data')}">
        <fieldset>
            <input type="hidden" id="pipeinlist_print_data" name="print_data" value="">
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>

                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>
                {{--
                    <label class="row-label">入库单号</label>
                    <div class="row-input">
                        <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="入库单号">
                    </div>
                --}}
                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" id="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称"></div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" id="bar_stockinstatus" data-width="100%"
                            name="bar_stockinstatus">
                        <option value="-100" selected="">不限</option>
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">已完成</option>
                        <option value="5">作废</option>
                        <option value="6">退回</option>
                    </select>
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>

            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="stockpipeinlist-table-data" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'del,|',
        toolbarCustom: '#stockpipein_button_on',
        addLocation: 'last',
        dataUrl: 'stockpipein/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
     {{--   editMode: {navtab:{title:'管入库单信息',id:'navab350'}},
        editUrl: '/stockpipein/edit/type/edit/id/{sysno}',--}}
        delUrl:'/stockpipein/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockinno',align:'center'}">入库单号</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'tobeqty',align:'center'}">通知数量(吨)</th>
            <th data-options="{name:'beqty',align:'center'}">商检数量(吨)</th>
            <th data-options="{name:'cs_employeename',align:'center',render:function(value){if(value=='请选择'){return '--'}else{return value}}}">客服</th>
            <th data-options="{name:'stockinstatus',align:'center',render:function(value){if(value=='2') {return '暂存'}
                else if(value=='3') {return '待审核'} else if(value=='4') {return '已完成'} else if(value=='5') {return '作废'}
                else if(value=='6') {return '退回'}
                else  {return '新建'}}}">单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>

<div id="stockpipein_button_on">
    <button id="edit_stockpipein_table" class="btn btn-green" data-icon='edit'>编辑</button>
    <button id="show_stockpipein_table" class="btn btn-green" data-icon='eye'>查看</button>

    <button id="audit_stockpipein_table" class="btn btn-green" data-icon='gavel'>审核</button>

    <button id="back_stockpipein_table" class="btn btn-red" data-icon="fa-scissors">作废</button>

    <button id="print_stockpipein_table2" type="button" onclick="pipeStockinPrint('2')" class="btn btn-green" data-icon="print" >打印核单</button>

    <button id="print_stockpipein_table1" type="button"  onclick="pipeStockinPrint('1')" class="btn btn-green" data-icon="print">打印入库单</button>

 {{--   <button id="attach_stockshipin_btn" class="btn btn-green" data-icon="filter">附件</button>--}}

   {{-- <button id="register_stockshipin_btn" class="btn btn-green" data-icon="fa fa-add">登记放行信息</button>--}}

    <button id="dbexcel_stockpipein_btn" onclick=" Excel_stockpipein()" class="btn btn-green" data-icon="external-link">
        Excel导出
    </button>
</div>

<script>

    function  Excel_stockpipein() {
        var begin_time = $("#begin_time").val();
        var end_time = $("#end_time").val();
        var bar_name = $("#bar_name").val();
        var bar_stockinstatus = $('#bar_stockinstatus option:selected').val();
        BJUI.ajax('ajaxdownload', {
            url: '/stockpipein/excel/',
            type: 'POST',
            data: {begin_time: begin_time, end_time: end_time, bar_name: bar_name,bar_stockinstatus:bar_stockinstatus},
            successCallback: function (json, options) {
                console.log(Success);
            }
        });
    }
    //编辑
    $("#edit_stockpipein_table").click(function () {
        var checkdata = $('#stockpipeinlist-table-data').data('selectedDatas');
       // console.log(checkdata);
        var chks = $.CurrentNavtab.find("#stockpipeinlist-table-data");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        if (checkdata == '' || checkdata == null || checkdata=='undefined') {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata[0].stockinstatus !=2 && checkdata[0].stockinstatus !=6) {
            BJUI.alertmsg('warn', '必须选择暂存数据编辑', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'navab350',
                url: '/stockpipein/edit/type/edit/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'booking_sysno': checkdata[0].booking_in_sysno},
                title: '编辑管入库订单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能编辑多条数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });

//审核
    $("#audit_stockpipein_table").click(function () {
        var checkdata = $('#stockpipeinlist-table-data').data('selectedDatas');
        //console.log(checkdata[0].sysno);return;
        var chks = $.CurrentNavtab.find("#stockpipeinlist-table-data");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }

        if (checkdata[0].stockinstatus !=3 ) {
            BJUI.alertmsg('warn', '必须选择待审核的数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'reviewshipin757',
                url: '/stockpipein/edit/type/review/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'booking_sysno': checkdata[0].booking_in_sysno},
                title: '审核管入库订单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能审核多条数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });

    //作废
    $("#back_stockpipein_table").click(function () {
        var chks = $.CurrentNavtab.find("#stockpipeinlist-table-data");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#stockpipeinlist-table-data').data('selectedDatas');
        //console.log(checkdata[0].sysno);return;
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata[0].stockinstatus !=4 ) {
            BJUI.alertmsg('warn', '必须选择已完成的数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'delshipin759',
                url: '/stockpipein/edit/type/back/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'booking_sysno': checkdata[0].booking_in_sysno},
                title: '作废管入库订单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能作废多条记录', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });


     //查看
    $("#show_stockpipein_table").click(function () {
        var checkdata = $('#stockpipeinlist-table-data').data('selectedDatas');
        //console.log(checkdata[0].booking_in_sysno);return;
        var chks = $.CurrentNavtab.find("#stockpipeinlist-table-data");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'showshipin758',
                url: '/stockpipein/edit/type/eye/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'booking_sysno': checkdata[0].booking_in_sysno},
                title: '查看管入库订单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能查看多条数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });

   function pipeStockinPrint(type) {
        var chks=$.CurrentNavtab.find("#stockpipeinlist-table-data");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#stockpipeinlist-table-data").data('selectedDatas');
        if (data == '' || data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        if (data.length != 1) {
            BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        BJUI.ajax('doajax', {
            url: "/stockshipin/executePrint/id/"+data[0].sysno+'/type/'+type,
            loadingmask: true,
            okCallback: function(json, options) {
                if(json.code == 300){
                    BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }else{

                    $('#pipeinlist_print_data').val(JSON.stringify(json));

                    var LODOP; //声明为全局打印变量
                    //打印入库单字段布局
                    var date = new Date();
                    var now = date.getFullYear()+"-" + (date.getMonth()+1) + "-" + date.getDate();

                    var CreateStockIn = function CreateStockIn() {
                        var data = $('#pipeinlist_print_data').val();
                        data = JSON.parse(data);
                        LODOP = getLodop();
                        LODOP.PRINT_INITA(0, 0, 800, 600, "管入库单");
                        // LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");

                        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
                        LODOP.SET_PRINT_STYLEA(2, "FontName", "黑体");
                        LODOP.SET_PRINT_STYLEA(2, "FontSize", 30);
                        if(type==1){
                            LODOP.ADD_PRINT_TEXT(90, 130, 200, 24, data.goodsname);
                            LODOP.ADD_PRINT_TEXT(140, 130, 200, 24, data.stockindate);
                            LODOP.ADD_PRINT_TEXT(190, 130, 200, 24, data.customername );
                            LODOP.ADD_PRINT_TEXT(241, 130, 200, 24, '');
                            LODOP.ADD_PRINT_TEXT(291, 130, 200, 24, data.deliverycompany);
                            LODOP.ADD_PRINT_TEXT(390, 130, 200, 24, data.sby_employeename);


                            LODOP.ADD_PRINT_TEXT(90, 380, 200, 24, '管线输送');
                            LODOP.ADD_PRINT_TEXT(140, 380, 200, 24, data.storagetankname);
                            LODOP.ADD_PRINT_TEXT(190, 380, 200, 24, '\\吨');
                            LODOP.ADD_PRINT_TEXT(240, 380, 200, 24, '\\吨');
                            LODOP.ADD_PRINT_TEXT(290, 380, 200, 24, data.beqty);
                            LODOP.ADD_PRINT_TEXT(337, 380, 200, 24, data.stockindate);

                            LODOP.ADD_PRINT_TEXT(140, 680, 200, 90, data.memo);
                        }else {
                        LODOP.ADD_PRINT_TEXT(90, 130, 200, 24, now);
                        LODOP.ADD_PRINT_TEXT(140, 130, 200, 24, data.customername);
                        LODOP.ADD_PRINT_TEXT(140, 380, 200, 24, data.goodsname);
                        LODOP.ADD_PRINT_TEXT(90, 380, 200, 24, data.tobeqty);
                        LODOP.ADD_PRINT_TEXT(190, 130, 200, 24, data.storagetankname);
                        }
                    }
                    Setup(CreateStockIn)

                }
            }
        })

    };

</script>