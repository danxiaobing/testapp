<div class="bjui-pageContent">
    <br><br>
    <table class="table table-bordered" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            filterThead:false,
            addLocation: 'last',
            dataUrl: '/introduce/outListJson/id/{{$id}}',
            dataType: 'json',
            paging: false,
            linenumberAll: true,
            }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center',width:280}">货主</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'buy_customername',align:'center'}">提货单位</th>
            <th data-options="{name:'introductiontype',align:'center',render:function(value){if(value == 1) {return '可撤销提单'} else {return '不可撤销提单'}}}">提单类型</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单数量(吨)</th>
            <th data-options="{name:'timerange',align:'center'}">提货区间</th>
            <th data-options="{name:'freecostdate',align:'center',render:function(value){if(!value || value==0){return '--'}}}">免仓期</th>
        </tr>
        </thead>
    </table>
    <br><br>
    <h4><strong>发货记录</strong></h4>
    <table class="table table-bordered" id="reviewIntroduceDetail-table" data-toggle="datagrid" data-options="{
            tableWidth:'99%',
            height: '100%',
            showToolbar: false,
            dataUrl: '/introduce/outListDetailJson/id/{{$id}}',
            paging: false,
            filterThead:false,
            addLocation:'first'
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'updated_at',align:'center'}">日期</th>
            <th data-options="{name:'poundsoutno',align:'center',render:function(value){if(!value){return '--'}}}">磅码单号</th>
            <th data-options="{name:'carid',align:'center',render:function(value){if(!value){return '--'}}}">船名/车号</th>
            <th data-options="{name:'beqty',align:'center',render:function(value){if(!value){return '--'}}}">发货数量(吨)</th>
            <th data-options="{name:'takegoodsnum',align:'center',render:function(value){if(!value){return '--'}}}">不可撤销提单数量(吨)</th>
            <th data-options="{name:'endingstock',align:'center'}">结存量(吨)</th>
            <th data-options="{name:'create_username',align:'center',render:function(value){if(!value){return '--'}}}">司磅员</th>
            <th data-options="{name:'memo',align:'center'}">备注</th>
        </tr>
        </thead>
    </table>
</div>
