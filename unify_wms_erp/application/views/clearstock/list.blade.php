<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="clearstock-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#clearstocklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="cleardate_start" value="{{$cleardate_end or ''}}" data-toggle="datepicker" placeholder="清库开始"></div>
                <div class="row-input datawidth">
                    <input type="text" name="cleardate_end" value="{{$cleardate_end or ''}}" data-toggle="datepicker" placeholder="清库结束"></div>

                <label class="row-label">客户:</label>
                <div class="row-input">
                    <select name="customername" data-size="5"  data-toggle="selectpicker" data-live-search="true"  data-width="100%">
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">单据状态:</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="stockclearstatus">
                        <option value="" selected="">不限</option>
                        <option value="2" >暂存</option>
                        <option value="3" >待审核</option>
                        <option value="4" >已审核</option>
                        <option value="6" >退回</option>
                        <option value="7" >作废</option>
                    </select>
                </div>
                <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
            </div>
        </fieldset>
    </form>

</div>
<div class="bjui-pageContent clearfix">

    <table class="table table-bordered" id="clearstocklist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        tableWidth : '2200',
        showToolbar: true,
        toolbarItem: 'del,|,export',
        toolbarCustom : '#clearstock-button',
        addLocation: 'last',
        dataUrl: 'clearstock/listJson',
        editMode: false,
        editUrl: '/clearstock/add/id/{sysno}/type/edit',
        delUrl:'/clearstock/delJson',
        dataType: 'json',
        delPK:'sysno',
        exportOption: {type:'file', options:{url:'/clearstock/Excel',form:$('clearstock-excel')}},
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        hScrollbar:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockclearno',align:'center'}">清库单号</th>
            <th data-options="{name:'stockinno',align:'center'}">单号</th>
            <th data-options="{name:'stockindate',align:'center'}">进货日期</th>
            <th data-options="{name:'stockcleardate',align:'center'}">清库日期</th>
             <th data-options="{name:'dateperiod',align:'center',width:'160'}">损耗计提期间</th>
            <th data-options="{name:'shipname',align:'center',render:function(value,data){if(!data.shipname || data.shipname=='') {return '--';} else {return data.shipname;} } }">进货船名</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){return value;}}">计量单位</th>
            <th data-options="{name:'instockqty',align:'center'}">商检量</th>
            <th data-options="{name:'outstockqty',align:'center'}">发货量</th>
            <th data-options="{name:'okqty',align:'center'}">盈亏量</th>
            <th data-options="{name:'loss',align:'center'}" >盈亏率‰</th>
            <th data-options="{name:'stockclearstatus',align:'center',render:function(value){if(value==1){return '新建';}else if(value==2){return '暂存';}else if(value==3){return '待审核';}else if(value==4){return '已审核';}else if(value==5){return '已完成';}else if(value==6){return '退回';} else if(value==7){return '作废';}  }}">单据状态</th>
            <th data-options="{name:'stockinno',align:'center',hide:'true',render:function(value,data){if(value){return value;}else{return data.stocktransno;}}}">单号</th>
        </tr>
        </thead>
    </table>
</div>
<div id="clearstock-button">
    <button type="button" id="clearstock_edit" class="btn btn-green" data-icon="gavel" >编辑</button>
    <button type="button" id="clearstock_provide" class="btn btn-green" data-icon="gavel" >审核</button>
    <button type="button" id="look_clearstock_data" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" id="clearstock_attachment" class="btn btn-green" data-icon="filter">附件</button>
    <button type="button" id="clearstock_Print" class="btn btn-green" data-icon="print">打印</button>
    <button type="button" id="clearstock_void" class="btn btn-red" data-icon="scissors" >作废</button>
</div>
<script type="text/javascript">
    //编辑
    $('#clearstock_edit').click(function(){
        var checkdata=$('#clearstocklist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockclearstatus = checkdata[0].stockclearstatus;
        if(checkdata.length==1){
            if(stockclearstatus==2 || stockclearstatus==6){
                BJUI.navtab({
                    id : 'clearstock_edit'+checkdata[0].sysno,
                    url : '/clearstock/add/id/'+checkdata[0].sysno+'/type/edit',
                    type : 'post',
                    title : '编辑清库单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择暂存或退回的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

    })



    //审核
    $('#clearstock_provide').click(function(){
        var checkdata=$('#clearstocklist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockclearstatus = checkdata[0].stockclearstatus;
        if(checkdata.length==1){
            if(stockclearstatus==3){
                BJUI.navtab({
                    id : 'clearstock_provides'+checkdata[0].sysno,
                    url : '/clearstock/add/id/'+checkdata[0].sysno+'/type/audit',
                    type : 'post',
                    title : '审核清库单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择待审核的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

    })

    //作废
    $('#clearstock_void').click(function(){
        var checkdata=$('#clearstocklist-table').data('selectedDatas');
//        console.log(checkdata);return;
        if(typeof(checkdata)=='undefined' || checkdata==''|| checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockclearstatus = checkdata[0].stockclearstatus;
        if(checkdata.length==1){
            if(stockclearstatus==4){
                BJUI.navtab({
                    id : 'clearstock_void'+checkdata[0].sysno,
                    url : '/clearstock/add/id/'+checkdata[0].sysno,
                    type : 'post',
                    title : '作废清库单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择已审核的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }

    })


    //打印
    $('#clearstock_Print').click(function(){
        var checkdata=$('#clearstocklist-table').data('selectedDatas');
        console.log(checkdata);
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockclearstatus = checkdata[0].stockclearstatus;

        if(checkdata.length==1){
            if(stockclearstatus==4){
                BJUI.navtab({
                    id : 'clearstock_print'+checkdata[0].sysno,
                    url : '/clearstock/add/id/'+checkdata[0].sysno+'/print/1',
                    type : 'post',
                    title : '打印清库单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择已审核的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }

    })


    //查看
    $('#look_clearstock_data').click(function(){
        var checkdata = $('#clearstocklist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(checkdata.length==1){
            BJUI.navtab({
                id: 'lookclearstockdata'+checkdata[0].sysno,
                url: '/clearstock/lookclearstock/id/' + checkdata[0].sysno + '/val/1',
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看清库单'
            });
        }else {
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //查看附件
    $("#clearstock_attachment").click(function() {
        var data  = $("#clearstocklist-table").data('selectedDatas');
        if (data == undefined || data=='' || data==null) {
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            var obj = data[0];
            if (obj.sysno != '') {
                BJUI.dialog({
                    url:'/attachment/view/clearstock/clear-edit/'+obj.sysno,
                    title:'查看'+obj.stockclearno+"附件",
                    width:900,
                    height:600,
                    mask:true
                });
            }
        }
    });
</script>
