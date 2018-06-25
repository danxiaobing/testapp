<br>
<br>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="checkreport-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            addLocation: 'last',
            dataUrl: 'check/seereportJson/id/{{$id}}',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:20},
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center',width:100}">储罐编号</th>
            <th data-options="{name:'storagetanknature',align:'center',width:100,render:function(value){if(value=='1') {return '内贸罐'} else if(value=='2') {return '外贸罐'}else if(value=='3') {return '保税罐'} } }">储罐性质</th>
            <th data-options="{name:'goodsname',align:'center',width:100}">货品名称</th>
            <th data-options="{name:'storagetankaccountqty',align:'center',width:100}">储罐账面数量</th>
            <th data-options="{name:'storagetankqty',align:'center',width:100}">储罐实际数量</th>
            <th data-options="{name:'storagedcsqty',align:'center',width:100}">DCS数量</th>
            <th data-options="{name:'profitqty',align:'center',width:100}">盈亏</th>
            <th data-options="{name:'checkdate',align:'center',width:100,render:function(value){if(value=='0000-00-00 00:00:00') {return '--'}}}">上次盘点时间</th>
        </tr>
        </thead>
    </table>
</div>
