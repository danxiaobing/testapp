<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="goodsinoutdetail-table" data-toggle="datagrid" data-options="{
        fullGrid:true,
        height: '100%',
        showToolbar: true,
        dataUrl: 'Report_Countgoods/inoutdetailJson/customer_sysno/{{$customer_sysno}}/storagetank_sysno/{{$storagetank_sysno}}/goods_sysno/{{$goods_sysno}}/time/{{$time}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:10},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
        showNoDataTip:true,
        editMode:false,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'doc_type',align:'center',render:function(value){ if(value==1){return '船入库'}else if(value==2){return '车入库'}else if(value==3){return '船出库'}else if(value==4){return '车出库'}else if(value==5){return '货转入'}else if(value==6){return '货转出'}else if(value==7){return '倒罐入'}else if(value==8){return '倒罐出'} }}">单据</th>
            <th data-options="{name:'docno',align:'center'}">单据编号</th>
            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'beqty',align:'center'}">实际数量</th>
        </tr>
        </thead>
    </table>
</div>