<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#customerintraday-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">查询区间</label>
                <div class="row-input">
                    <input type="text" name="bar_date" id="customerintraday_bar_date" data-toggle="datepicker" value="{{$bookingoutdate}}" readonly>
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select name="customer_sysno" id="customerintraday_customer_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($customerlist as $value)
                        	<option value="{{$value['sysno']}}">{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" id="customerintraday_goods_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($goodslist as $value)
                            <option value="{{$value['sysno']}}">{{$value['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>
                
                <label class="row-label">货物性质</label>
                <div class="row-input">
                    <select name="goodsnature" id="customerintraday_goodsnature" data-size="10" data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="">全部</option>
                        <option value="1" @if($goodsnature == '1') {{selected}} @endif>保税</option>
                        <option value="2" @if($goodsnature == '2') {{selected}} @endif>外贸</option>
                        <option value="3" @if($goodsnature == '3') {{selected}} @endif>内贸转出口</option>
                        <option value="4" @if($goodsnature == '4') {{selected}} @endif>内贸内销</option>
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
    <table class="table table-bordered" id="customerintraday-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#customerintraday_tb',
            addLocation: 'last',
            dataUrl: '/report_customerintraday/listJson',
            dataType: 'json',
            editMode:false,
            jsonPrefix: 'obj',
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:false,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';}}}">货物性质</th>
            <th data-options="{name:'endingstocks',align:'center'}">昨日结存量</th>
            <th data-options="{name:'inqty',align:'center'}">今日入库量</th>
            <th data-options="{name:'outqty',align:'center'}">今日出库量</th>
            <th data-options="{name:'ullage',align:'center'}">今日损耗量</th>
            <th data-options="{name:'endstock',align:'center'}">今日结存量</th>
            <th data-options="{name:'info',align:'center',render:customerintraday_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="customerintraday_tb">
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="customerintraday_export">EXCEL导出</button>
</div>
<script type="text/javascript">
    function customerintraday_operation(val,data){
        return '<button type="button" class="btn-green" data-source='+val+' onclick="see_customerIntradayDetail(this)">查看明细</button>';
    }

    function see_customerIntradayDetail(vals){
        var data = $(vals).attr('data-source');
        var bar_date = $('#customerintraday_bar_date').val();
        BJUI.navtab({
            id:'customerintraday001',
            url:'/report_customerintraday/detailList/'+Math.random(),
            type:'POST',
            data:{data:data,bar_date:bar_date},
            title:'客户日收发存明细'
        });
        
    }

    $('#customerintraday_export').click(function(event) {
        var bar_date = $('#customerintraday_bar_date').val();
        var customer_sysno = $('#customerintraday_customer_sysno').val();
        var goods_sysno = $('#customerintraday_goods_sysno').val();
        var goodsnature = $('#customerintraday_goodsnature').val();
        
        BJUI.ajax('ajaxdownload', {
            url:'/report_customerintraday/dbtoexcel/',
            type:'POST',
            data:{bar_date:bar_date, customer_sysno:customer_sysno,goods_sysno:goods_sysno,goodsnature:goodsnature},
            successCallback: function(json, options) {

            }
        });
    });
</script>