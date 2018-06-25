<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="Reportgoodstrace" data-options="{searchDatagrid:$.CurrentNavtab.find('#goodstrace-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围</label>
                <div class="row-input required datawidth">
                    <input type="text" name="startTime" id="startTime" value="{{$startTime or date('Y-m-d') }}" placeholder="开始时间"  data-toggle="datepicker" data-rule="required" ></div>
                <div class="row-input required datawidth">
                    <input type="text" name="endTime" id="endTime" value="{{$endTime or date('Y-m-d')}}" placeholder="结束时间"  data-toggle="datepicker" data-rule="required"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="6" name="customername" id="customer_sysno">z
                        <option value="" selected="">不限</option>
                       @foreach($customerlist as $value)
                            <option value="{{$value['sysno']}}" >{{$value['customername']}}</option>
                       @endforeach
                    </select>
                </div>
                <label class="row-label">品名</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="6" name="goodsname" id="goods_sysno" >
                        <option value="" selected="">不限</option>
                       @foreach($goods as $val )
                            <option value="{{$val['sysno']}}">{{$val['goodsname']}}</option>
                       @endforeach
                    </select>
                </div>
                <br/>
                <label class="row-label">进货单号</label>
                <div class="row-input">
                    <input type="text" name="stockinno" value="{{$stockinno}}" placeholder="进货单号">
                </div>
                <label class="row-label">进出货船名</label>
                <div class="row-input">
                    <input type="text" name="shipname" value="{{$shipname}}" placeholder="进出货船名">
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
    <table class="table table-bordered" id="goodstrace-table" data-toggle="datagrid" data-options="{
        height: '100%',
        tableWidth : '100%',
        showToolbar: true,
        toolbarItem: 'export',
        addLocation: 'last',
        dataUrl: '/Report_Goodstrace/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        exportOption: {type:'file', options:{url:'/Report_Goodstrace/export',form:$('#Reportgoodstrace')}},
        paging: {pageSize:12},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        showTfoot:true,
        hScrollbar:true
    }">

        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockinno',align:'center'}">进货单号</th>
            <th data-options="{name:'stockintype',align:'center', render: function(value){ if(value == 1){return '船入库';}else if(value == 2){return '车入库';}else if(value == 3){return '管线入';}else if(value == 4){return '靠泊装卸入';}else{return '未知类型';}}}">类型</th>
            <th data-options="{name:'stockindate',align:'center'}">入库日期</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'shipname',align:'center', render:function(value, data){if(data.stockintype == 1){return value;}else if(data.stockintype == 2){return '槽车进货';}else if(data.stockintype == 3){return '管输';}else if(data.stockintype == 4) {return '管输';}}}">进货车船名</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单量（吨）</th>
            <th data-options="{name:'beqty',align:'center'}">商检量（吨）</th>
            <th data-options="{name:'',align:'center',render:goodstrace_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<script>

    function goodstrace_operation(value,data) {
        return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="goodstracedetail('+data.sysno+')">查看明细</button>';
    }

    function goodstracedetail(sysno){
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        BJUI.navtab({
            id:'ReportGoodstraceDetail',
            type:'POST',
            url:'/Report_Goodstrace/detail/sysno/'+sysno,
//            data:{goods_sysno:goods_sysno,customer_sysno:customer_sysno,startTime:startTime,endTime:endTime,ghoststockqty:ghoststockqty,endmath:endmath,},
            title:'货物进出货报表明细',
        });
    };

</script>