
    <table class="table table-bordered" id="clear-stock-table" height="40+50*{{count($list)}}"
           data-toggle="datagrid" data-options="{
                    height:'100%',
                    fullGrid:true,
                    {{--gridTitle : '清库',--}}
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
            <th data-options="{name:'stockclearno',align:'center'}">清库单号</th>
            <th data-options="{name:'stockindate',align:'center'}">入库日期</th>
            <th data-options="{name:'stockcleardate',align:'center'}">清库日期</th>
            <th data-options="{name:'inclearstockdate',align:'center',render:function(value,data) {return data.stockindate+'/'+data.stockoutdate}}">损耗计提期间</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'instockqty',align:'center', calc:'sum'}">入库数量</th>
            <th data-options="{name:'outstockqty',align:'center', calc:'sum'}">实发数量</th>
            <th data-options="{name:'okqty',align:'center', calc:'sum'}">清库余量</th>
            <th data-options="{name:'num',align:'center',render:function(value){return Math.round(value*100)/100;}}">损益率‰</th>
            <th data-options="{name:'stockclearstatus',align:'center',render:function(value){switch(value){case '1': return '新建'; case '2':return '暂存'; case '3':return '待审核'; case '4':return '已审核'; case '7':return '作废'; case '6':return '退回';default: return '';}}}">单据状态</th>
        </tr>
        </thead>
    </table>
