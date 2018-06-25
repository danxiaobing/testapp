<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="Reportcustomfrom" data-options="{searchDatagrid:$.CurrentNavtab.find('#customerlist-table')}">
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
    <table class="table table-bordered" id="customerlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        tableWidth : '100%',
        showToolbar: true,
        toolbarItem: 'export',
        addLocation: 'last',
        dataUrl: '/Reportcustom/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        exportOption: {type:'file', options:{url:'/Reportcustom/export1',form:$('#Reportcustomfrom')}},
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
            <th data-options="{name:'customername',align:'center'}">客户名称</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'unitname',align:'center',render:function(value) {if(value=='') {return '吨' }  }}">计量单位</th>
            <th data-options="{name:'ghoststockqty',align:'center',calc:'sum',render:function(value) {if(value=='') {return '0' }  }}">期初数量</th>
            <th data-options="{name:'instockqty',align:'center',calc:'sum',render:function(value){if(value=='') {return '0' } }}">入库数量</th>
            <th data-options="{name:'intransqty',align:'center',calc:'sum',render:function(value){if(value=='') {return '0' } }}">货权转移入库数量</th>
            <th data-options="{name:'outstockqty',align:'center',calc:'sum',render:function(value){if(value=='') {return '0' } }}">出库数量</th>
            <th data-options="{name:'outtransqty',align:'center',calc:'sum',render:function(value){if(value=='') {return '0' } }}">货权转移出库数量</th>
            <th data-options="{name:'lossqty',align:'center',calc:'sum',render:function(value){if(value=='') {return '0' } }}">损耗量</th>
            <th data-options="{name:'endmath',align:'center',calc:'sum',width:100}">期末余量</th>
            <th data-options="{name:'beyondqty',align:'center',calc:'sum',width:100}">超发数量</th>
            <th data-options="{name:'',align:'center',render:customlist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<script>
    function customlist_operation(value,data) {
        return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="lookdetail('+data.goods_sysno+','+data.customer_sysno+','+data.ghoststockqty+','+data.endmath+')">联查明细</button>';
    }
    function lookdetail(goods_sysno,customer_sysno,ghoststockqty,endmath){
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        BJUI.navtab({
            id:'reportcustomlist',
            type:'POST',
            url:'/Reportcustom/detail/goods_sysno/'+goods_sysno+'/customer_sysno/'+customer_sysno,
            data:{goods_sysno:goods_sysno,customer_sysno:customer_sysno,startTime:startTime,endTime:endTime,ghoststockqty:ghoststockqty,endmath:endmath,},
            title:'客户收发存明细表',
        });
    };

</script>