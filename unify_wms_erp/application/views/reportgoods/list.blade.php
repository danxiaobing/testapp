<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" id="reportgoodslist-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportgoodslist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" id="begin_time" value="{{$begin_time or ''}}" data-toggle="datepicker" placeholder="开始时间" data-rule="required"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" id="end_time" value="{{$end_time or ''}}" data-toggle="datepicker" placeholder="结束时间" data-rule="required"></div>

                <label class="row-label">产品名称</label>
                <div class="row-input">
                    <select name="id" data-toggle="selectpicker"  data-width="100%" class="show-tick" data-live-search="true" data-size='10'>
                        <option value="">请选择货品</option>
                        @foreach($goods as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select></div><br>

                <label class="row-label">货物性质</label>
                <div class="row-input">
                    <select name="goodsnature" data-toggle="selectpicker"  data-width="100%" class="show-tick" data-live-search="true" data-size='10'>
                        <option value="">全部</option>
                        <option value="1">保税</option>
                        <option value="2">外贸</option>
                        <option value="3">内贸转出口</option>
                        <option value="4">内贸内销</option>
                    </select></div>

                <label class="row-label">码头</label>
                <div class="row-input">
                    <select name="wharfname" data-toggle="selectpicker"  data-width="100%" class="show-tick" data-live-search="true" data-size='10'>
                        <option value="">请选择码头</option>
                        @foreach($wharf as $val)
                            <option value="{{$val['wharfname']}}">{{$val['wharfname']}}</option>
                        @endforeach
                    </select></div>

                <label class="row-label">槽车</label>
                <div class="row-input">
                    <select name="cartype" data-toggle="selectpicker"  data-width="100%" class="show-tick" data-live-search="true" data-size='10'>
                        <option value="">全部</option>
                        <option value="1">槽车</option>
                        <option value="2">非槽车</option>
                    </select></div>

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
    <table class="table table-bordered" id="reportgoodslist-table" data-toggle="datagrid" data-options="{
        
        height: '100%',
        showToolbar: true,
        toolbarItem: 'export',
        dataUrl: 'Reportgoods/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:7},
        exportOption: {type:'file', options:{url:'/Reportgoods/Excellist',form:$('#reportgoodslist-excel')}},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
        showNoDataTip:true,
        editMode:false,
        fullGrid:true
    }">

        <thead>
        <tr data-options="{name:'sysno'}">
           <th data-options="{name:'stockinno',align:'center',width:200}">入库单号</th>
            <th data-options="{name:'customername',align:'center'}">客户名称</th> 
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'stockindate',align:'center'}">进货日期</th>
            <th data-options="{name:'shipname',align:'center',render:function(data,value){if(value.stockintype == '3') {return '管入 '}}}">进货船名/槽车进货/管入</th>
            <th data-options="{name:'takegoodsnum',align:'center',calc:'sum'}">提单量</th>
            <th data-options="{name:'instockqty',align:'center',calc:'sum'}">商检量</th>
            <th data-options="{name:'ghoststockqty',align:'center',calc:'sum'}">期初数量</th>
            <th data-options="{name:'outstockqty',align:'center',calc:'sum'}">出库数量</th>
            <th data-options="{name:'transqty',align:'center',calc:'sum'}">货权转移数量</th>
            <th data-options="{name:'wharfname',align:'center'}">卸船码头</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}   else  {return ''}}}">内外贸</th>
            <th data-options="{name:'ullage',align:'center',calc:'sum'}">损耗数量</th>
            <th data-options="{name:'lastqty',align:'center',calc:'sum'}">期末余量</th>
            <th data-options="{name:'wagon',align:'center',calc:'sum'}">槽车车数</th>
            <th data-options="{name:'beyondqty',align:'center',calc:'sum'}">超发数量</th>
            
        </tr>
        </thead>

    </table>
</div>
<!-- <div id="reportgoods_ship_tb">
    <button type="button" class="btn btn-blue" data-icon="filter" id="reportgoodslist_btn">联查明细</button>
</div> -->
<script type="text/javascript">
        function goodslist_operation(value,data) {
        return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="look('+data.goods_sysno+','+data.ghoststockqty+','+data.lastqty+ ')">联查明细</button>';
    }
    function look(goods_sysno,ghoststockqty,lastqty){
        var begin_time = $('#begin_time').val();
        var end_time = $('#end_time').val();
        // console.log(begin_time); return;
            BJUI.navtab({
                id:'reportgoodslist',
                type:'POST',
                url:'/reportgoods/detail/id/'+goods_sysno,
                data:{goods_sysno:goods_sysno,begin_time:begin_time,end_time:end_time,ghoststockqty:ghoststockqty,lastqty:lastqty},
                title:'货品收发存明细表',
            });
        // BJUI.navtab('reloadFlag','menu173');
        
    };
</script>