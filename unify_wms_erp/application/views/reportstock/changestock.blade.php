
    <table class="table table-bordered" id="change-stock-table"
           data-toggle="datagrid" data-options="{
                    {{--gridTitle : '货权转移',--}}
                    {{--showToolbar: true,--}}
                    height:'100%',
                    data:{{$list}},
                    paging: false,
                    linenumberAll: true,
                    filterThead:false,
                    showLinenumber:true,
                    showTfoot:true,
                    local: 'local',
                    fieldSortable:false,
                    fullGrid:true

                }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stocktransno',align:'center'}">货权转移单号</th>
            <th data-options="{name:'stocktransdate',align:'center'}">转移日期</th>
            <th data-options="{name:'buystartdate',align:'center'}">受让方计费起始日</th>
            <th data-options="{name:'sale_customername',align:'center'}">转让方名称</th>
            <th data-options="{name:'buy_customername',align:'center'}">受让方名称</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';}}}">货物性质</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'tobetransqty',align:'center', calc:'sum'}">数量</th>
            <th data-options="{name:'stocktransstatus',align:'center',render:function(value){switch(value){case '1': return '新建'; case '2':return '暂存'; case '3':return '待审核'; case '4':return '已审核'; case '7':return '作废'; case '6':return '退回';default: return '';}}}">单据状态</th>
        </tr>
        </thead>
    </table>

