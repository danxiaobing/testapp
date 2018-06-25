<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockshipindeclarelist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">业务期间:</label>

                <div class="row-input datawidth">
                    <input type="text" name="start_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="customername" value="" placeholder="客户名称"></div>

                <label class="row-label">入库单号</label>
                <div class="row-input">
                   	<input type="text" name="stockinno" value="" placeholder="入库单号">
                </div>

                <label class="row-label">货品</label>
                <div class="row-input">
                    <select name="bar_goodsname" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">类别</label>
                <div class="row-input">
                    <select data-toggle="selectpicker"  data-width="100%"
                            name="bar_stockintype">
                        <option value="" selected="">全部</option>
                        <option value="1" >船入库</option>
                        <option value="2" >车入库</option>
                        <option value="3" >管入库</option>
                    </select>
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
    <table class="table table-bordered" id="stockshipindeclarelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'false',
        toolbarCustom: '#stockshipindeclare_add_btn',
        addLocation: 'last',
        dataUrl: 'stockshipin/declareListJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: 'false',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockinno',align:'center',width:150}">入库单号</th>
            <th data-options="{name:'stockintype',align:'center',width:100,render:function(value){if(value==1){return '船入库';}else if(value==2){return '车入库';}else if(value==3){return '管入库';}}}">类别</th>
            <th data-options="{name:'customername',align:'center',width:150}">客户名称</th>
            <th data-options="{name:'goodsname',align:'center',width:100}">品名</th>
            <th data-options="{name:'',align:'center',width:100,render:function(value,data){if(data.stockintype==1){return data.shipname;}else if(data.stockintype==2){return '槽车入库';}else if(data.stockintype==3){return '管入库';}}}">船名</th>
            <th data-options="{name:'beqty',align:'center',width:100}">商检数量</th>
            <th data-options="{name:'takegoodsnum',align:'center',width:100}">提单量</th>
            <th data-options="{name:'release_num',align:'center',render:function(value){return value =='' ? '--' : value},width:100 }">已报关数量</th>
            <th data-options="{name:'unrelease_num',align:'center',render:function(value){return value =='' ? '--' : value},width:100}">未报关数量</th>
        </tr>
        </thead>
    </table>
</div>

<div id="stockshipindeclare_add_btn">
    <button id="show_stockshipindeclare_btn" class="btn btn-blue" data-icon='eye'>查看</button>

    <button id="edit_stockshipindeclare_btn" class="btn btn-green" data-icon='gavel'>编辑</button>
</div>
<script>
    $('#edit_stockshipindeclare_btn').click(function(){
        var id = $('#stockshipindeclarelist-table').data('selectedDatas')[0]['sysno'];
        
                BJUI.navtab({
                id: 'stockshipindeclareedit'+id,
                url: '/stockshipin/declareDetail/stockin_sysno/' + id,
                mask: true,
                title: '船入库报关录入'
            });

    });

    $('#show_stockshipindeclare_btn').click(function(){
        var id = $('#stockshipindeclarelist-table').data('selectedDatas')[0]['sysno'];
                BJUI.navtab({
                id: 'showstockshipindeclare'+id,
                url: '/stockshipin/showdeclaredetail/stockin_sysno/' + id,
                mask: true,
                title: '查看船入库报关信息'
            });

    });

</script>