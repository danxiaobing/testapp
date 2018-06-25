<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockrebacklist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">

                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" id='reback_begin_time' name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" id='reback_end_time' name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" id='carid' name="carid" value="" placeholder="车牌号"></div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                        <select id='stockinstatus' name="stockinstatus" data-toggle="selectpicker" data-width="100%" >
                            <option value="" selected="">不限</option>
                            <option value="2">暂存</option>
                            <option value="3">待审核</option>
                            <option value="4">已审核</option>
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
    <table class="table table-bordered" id="stockrebacklist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'del_stocklist',
        toolbarCustom:'#custom_reback_tb',
        addLocation: 'last',
        dataUrl: '/reback/listJson',
        dataType: 'json',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
        hScrollbar:true
    }">

        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockrebackno',align:'center'}">退货单号</th>
            <th data-options="{name:'poundsoutno',align:'center'}">出库磅码单号</th>
            <th  data-options="{name:'stockrebackdate',align:'center'}">退货日期</th>
            <th  data-options="{name:'carid',align:'center'}">车牌号</th>
            <th  data-options="{name:'goodsname',align:'center'}">品名</th>
            <th  data-options="{name:'takegoodsnumber',align:'center'}">实提数量（KG）</th>
            <th data-options="{name:'rebacknumber',align:'center'}">退回数量（KG）</th>
            <th data-options="{name:'cs_employeename',align:'center'}">客服专员</th>
            <th data-options="{name:'stockinstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'}  else if(value=='4') {return '退货中'} else if(value=='6') {return '退回'} else if(value=='7') {return '作废'} else if(value=='8') {return '已完成'} else  {return '新建'}}}">单据状态</th>
        </tr>
        </thead>
    </table>

</div>
<div id="custom_reback_tb">
    <button type="button" id="reback_add_btn" data-icon="plus" class="btn btn-blue">新建退货</button>
    <button type="button" id="reback_edit_btn" data-icon="edit" class="btn btn-green">编辑</button>
    <button type="button" id="reback_view_btn" data-icon="eye" class="btn btn-green">查看</button>
    <button type="button" id="reback_del_btn" data-icon="times" class="btn btn-red">删除</button>
    <button type="button" id="reback_audit_btn" data-icon="gavel" class="btn btn-green">审核</button>
    <button type="button" id="reback_cancellation_btn" data-icon="scissors" class="btn btn-red">作废</button>
    <button type="button" id="reback_done_btn" data-icon="chain-broken" class="btn btn-green">完成退货</button>
</div>


<script type="text/javascript">
    //新建退货
    $("#reback_add_btn").click(function(){
        BJUI.navtab({
            id:'navtab-reback-add',
            url:"/reback/edit",
            title:'新建退货单'
        });
    });
    //编辑
    $("#reback_edit_btn").click(function(){

        var data  = $("#stockrebacklist-table").data('selectedDatas');
        if (data == '' || data == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if (data.length != 1) {
            BJUI.alertmsg('warn', "请选择一条退货单",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(data[0].stockinstatus==3){
            BJUI.alertmsg('warn', "只能编辑暂存状态的数据",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        console.log(data);
        BJUI.navtab({
            id:'navtab-reback-001',
            url:"/reback/edit/mode/edit/id/"+data[0].sysno,
            title:'编辑退货单'
        });


    });

    //审核
    $("#reback_audit_btn").click(function () {
        var checkdata = $('#stockrebacklist-table').data('selectedDatas');
        console.log(checkdata);
        var chks = $.CurrentNavtab.find("#stockrebacklist-table");
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
                id: 'auditreback',
                url: '/reback/edit/mode/audit/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'stockinstatus': checkdata[0].stockinstatus},
                title: '审核管入库订单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能审核多条数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });
    //删除
    $("#reback_del_btn").click(function () {

        var selectdata  =  $.CurrentNavtab.find('#stockrebacklist-table').data('selectedDatas');
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        if (selectdata.length !=1) {
            BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(selectdata[0].stockinstatus ==4){
            BJUI.alertmsg('warn','<h4>已审核的单据不能删除!</h4>');
            return;
        }
        BJUI.ajax('doajax', {
            url: 'reback/delete',
            type:'POST',
            data: {selectdata:selectdata},
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('refresh','navab550');
                console.log('返回内容：\n'+ JSON.stringify(json))
            }
        })

    });
    //查看
    $('#reback_view_btn').click(function() {

        var data  = $("#stockrebacklist-table").data('selectedDatas');
        if (data == '' || data == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        BJUI.navtab({
            id:'navtab-reback-001',
            url:"/reback/edit/mode/view/id/"+data[0].sysno,
            title:'查看退货单'
        });
    });

    $('#custom_stockout_excel_btn').click(function(event) {

        var bar_no = $('#stockoutlist_bar_no').val();
        var begin_time = $('#stockoutlist_begin_time').val();
        var end_time = $('#stockoutlist_end_time').val();
        var bar_name = $('#stockoutlist_bar_name').val();
        var bar_stockoutstatus = $('#stockoutlist_bar_stockoutstatus').val();
        var bar_receivenumber = $('#stockoutlist_bar_receivenumber').val();
        var bar_goodsname = $('#stockoutlist_bar_goodsname option:selected').val();

        BJUI.ajax('ajaxdownload', {
            url:'/stockout/cardbtoexcel/',
            type:'POST',
            data:{bar_no:bar_no, begin_time:begin_time,end_time:end_time,bar_name:bar_name,bar_stockoutstatus:bar_stockoutstatus,bar_receivenumber:bar_receivenumber,bar_goodsname:bar_goodsname},
            successCallback: function(json, options) {
            }
        });
    });

    //作废
    $("#reback_cancellation_btn").click(function () {

        var selectdata  =  $.CurrentNavtab.find('#stockrebacklist-table').data('selectedDatas'); //console.log(selectdata);return false;
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        if (selectdata.length !=1) {
            BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(selectdata[0].stockinstatus !=4){
            BJUI.alertmsg('warn','<h4>非退货中单据不能作废!</h4>');
            return;
        }
        BJUI.ajax('doajax', {
            url: 'reback/cancellation1',
            type:'POST',
            data: {selectdata:selectdata},
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('refresh','navab550');
                console.log('返回内容：\n'+ JSON.stringify(json))
            }
        })

    });
    //完成退货
    $("#reback_done_btn").click(function () {

        var selectdata  =  $.CurrentNavtab.find('#stockrebacklist-table').data('selectedDatas'); //console.log(selectdata);return false;
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        if (selectdata.length !=1) {
            BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(selectdata[0].stockinstatus !=4){
            BJUI.alertmsg('warn','<h4>非退货中单据不能进行完成退货操作!</h4>');
            return;
        }
        BJUI.ajax('doajax', {
            url: 'reback/rebackdone',
            type:'POST',
            data: {selectdata:selectdata},
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('refresh','navab550');
                console.log('返回内容：\n'+ JSON.stringify(json))
            }
        })

    });
</script>