
    <table class="table table-bordered" id="out-stock-table" height="40+50*{{count($list)}}"
           data-toggle="datagrid" data-options="{
                    height:'100%',
                    fullGrid:true,
                    {{--gridTitle : '出库',--}}
                    {{--showToolbar: true,--}}
                    data:{{$list}},
                    paging: false,
                    linenumberAll: true,
                    filterThead:false,
                    showLinenumber:true,
                    local:'local',
                    fieldSortable:false,
                    showTfoot:true
                }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockoutno',align:'center'}">出库单号</th>
            <th data-options="{name:'stockoutdate',align:'center'}">出库日期</th>
            <th data-options="{name:'stockouttype',align:'center',render:function(value){switch(value){case '1': return '船出库'; case '2':return '车出库';default: return '';}}}">出库单类型</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'customername',align:'center'}">货主</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'countnum',align:'center', calc:'sum'}">数量</th>
            <th data-options="{name:'stockoutstatus',align:'center',render:function(value,data){switch(value){case '1': return '新建'; case '2':return '暂存';case '3':if(data.stockouttype ==1){return '待审核'}else{return '出库中';};case '4':return '已完成';case '5':return '作废';default: return '';}}}">单据状态</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';}}}">货物性质</th>
        </tr>
        </thead>
    </table>
