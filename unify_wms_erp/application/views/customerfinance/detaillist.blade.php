<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#customerfinancedetaillist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">时间范围:</label>
                <div class="row-input datawidth">
                    <input type="text" id="customerfinanceliststartdate" name="startdate" value="{{$begin_time or date('Y-m-d',strtotime('-1 months'))}}" data-toggle="datepicker" >
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="customerfinancedetaillistenddate" name="enddate" value="{{$end_time or date('Y-m-d',time())}}" data-toggle="datepicker" >
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="customer_name" id="customerfinancedetaillist_customer_name" value="{{$customer_name or ''}}" placeholder="客户名称"></div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <input type="text" name="goods_name" id="customerfinancedetaillist_goods_name" value="{{$goods_name or ''}}" placeholder="货品名称"></div>

                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text" name="shipname" id="customerfinancedetaillist_shipname" value="{{$shipname or ''}}" placeholder="船名"></div>

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
    <table class="table table-bordered" id="customerfinancedetaillist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            hScrollbar:true,
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#customerfinancedetaillist',
            addLocation: 'last',
            dataUrl: '/Report_Customerfinance/detaillistJson/customer_name/{{$customer_name}}/goods_name/{{$goods_name}}/begin_time/{{$begin_time}}/end_time/{{$end_time}}/',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
            fieldSortable: false,
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'instockdate',align:'center',render:function(value){if(value==''){return '--'}else{return value}} }">入库日期</th>
            <th data-options="{name:'shipname',align:'center',render:function(value){if(value==''){return '--'}else{return value}} }">船名</th>
            <th data-options="{name:'instockqty',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">进货数量（吨）</th>
            <th data-options="{name:'outstockqty',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">已提数量（吨）</th>
            <th data-options="{name:'costullage',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">损耗（吨）</th>
            <!-- <th data-options="{name:'costqty',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">结存量（吨）</th> -->
            <th data-options="{name:'costname',align:'center'}">费用明细</th>
            <th data-options="{name:'costqty',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">数量（吨）</th>
            <th data-options="{name:'unitprice',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">单价</th>
            <th data-options="{name:'datenum',align:'center',render:function(value){if(value<=0){return '--'}else{return value}},calc:'sum'}">天数（天）</th>
            <th data-options="{name:'totalprice',align:'center',calc:'sum'}">总金额（元）</th>
            <th data-options="{name:'coststatus',align:'center',render:function(value){if(value=='2') {return '待开票'}
                    else if(value=='3') {return '开票待审核'} else if(value=='4') {return '已开票'} else if(value=='5') {return '已关闭'}
                    else  {return '未生效'}}}">开票状态
                </th>
        </tr>
        </thead>
    </table>
</div>
<div id="customerfinancedetaillist">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="customerfinancedetailexcelout()">EXCEL导出</button>
</div>
<script type="text/javascript">

    function customerfinancedetailexcelout(){
        var customer_name = $("#customerfinancedetaillist_customer_name").val();
        var goods_name = $("#customerfinancedetaillist_goods_name").val();
        var shipname = $("#customerfinancedetaillist_shipname").val();
        var begin_time = $("#customerfinancedetailliststartdate").val();
        var end_time = $("#customerfinancedetaillistenddate").val();

        var data=$('#customerfinancedetaillist-table').data('allData');
        if(data=='' || data==null)
        {
            BJUI.alertmsg('warn','空数据无法导出',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxdownload', {
            url:'/Report_Customerfinance/detaillistexcel/',
            type:'POST',
            data:{customer_name:customer_name,goods_name:goods_name,begin_time:begin_time,end_time:end_time,shipname:shipname},
            successCallback: function(json, options) {
                console.log('ok');
            }
        });
    }
</script>
