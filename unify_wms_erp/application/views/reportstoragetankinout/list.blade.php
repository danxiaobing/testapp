<div class="bjui-pageHeader">
    <form id="searchstoragetank" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#storagetankinoutlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围</label>
                <div class="row-input required">
                    <input id="date1" type="text" name="date1" data-rule="required" value="{{date('Y-m-d',strtotime('-1 months'))}}" placeholder="开始时间"  data-toggle="datepicker" readonly>
                </div>
                <div class="row-input required">
                    <input id="date2" type="text" name="date2" data-rule="required" value="{{date('Y-m-d')}}" placeholder="结束时间"  data-toggle="datepicker" readonly>
                </div>
                <label class="row-label">储罐号</label>
                <div class="row-input">
                    <select id="tankno" name="tankno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($storagetank as $key=>$value)
                            <option value="{{$value['sysno']}}" @if($value['sysno']==$tankno) selected="selected" @endif>{{$value['storagetankname']}}</option>
                        @endforeach
                    </select>
                </div>
                {{--<label class="row-label"></label>--}}
                {{--<label class="row-label">储罐性质</label>--}}
                {{--<div class="row-input">--}}
                    {{--<select id="storagetanknature" name="storagetanknature" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">--}}
                        {{--<option value="">请选择</option>--}}
                        {{--@foreach($storagetank as $key=>$value)--}}
                            {{--<option value="{{$key}}" @if($key==$storagetanknature) selected="selected" @endif>{{$value}}</option>--}}
                        {{--@endforeach--}}
                        {{--<option value="1" @if($storagetanknature == '1') {{selected}} @endif>内贸罐</option>--}}
                        {{--<option value="2" @if($storagetanknature == '2') {{selected}} @endif>外贸罐</option>--}}
                        {{--<option value="3" @if($storagetanknature == '3') {{selected}} @endif>保税罐</option>--}}
                    {{--</select>--}}
                {{--</div>--}}
                <label class="row-label">产品名称</label>
                <div class="row-input">
                    <select id="goodsno" name="goodsno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($getgoodsinfo as $key=>$value)
                            <option value="{{$key}}" @if($key==$goodsno) selected="selected" @endif>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label"></label><label class="row-label"></label>
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
    <table class="table table-bordered" id="storagetankinoutlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'3000',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showretanklist',
            addLocation: 'last',
            dataUrl: '/report_storagetankinout/ListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:13},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            tableWidth:'100%',
            showTfoot:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',width:80,align:'center'}">储罐号</th>
            <th data-options="{name:'storagetanknature',width:80,align:'center',render:function(value){if(value==1){ return value='内贸罐';}else if(value==2){return value='外贸罐';}else if(value==3){return value='保税罐';}}}">储罐性质</th>
            <th data-options="{name:'goodsname',width:100,align:'center'}">产品名称</th>
            <th data-options="{name:'startqty',align:'center',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">上期结存量(吨)</th>
            <th data-options="{name:'totalinstockqty',align:'center',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">本期入库数量(吨)</th>
            <th data-options="{name:'totaloutstockqty',align:'center',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">本期出库数量(吨)</th>
            <th data-options="{name:'inretank',align:'center',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">本期倒入量(吨)</th>
            <th data-options="{name:'outretank',align:'center',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">本期倒出量(吨)</th>
            <th data-options="{name:'totalclearqty',align:'center',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">本期损耗量(吨)</th>
            <th data-options="{name:'totalreturnqty',align:'center',render:function(value){if(!value) return 0;}}">本期退货量(吨)</th>
            <th data-options="{name:'totalstockqty',align:'center',calc:'sum',render:function(value){if(!value){ return 0;}else if(value<0){return -value;}}}">本期结存量(吨)</th>
            <th data-options="{name:'storagetank_sysno',width:100,align:'center',render:storagetanklist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showretanklist">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="tankinout()"></i>EXCEL导出</button>
</div>
<script type="text/javascript">
    function storagetanklist_operation(val,data){ //console.log(val,data);return;
        return '<button type="button" class="btn-green" onclick="see_storagetankdetail('+val+','+data.startqty+','+data.totalstockqty+','+data.goods_sysno+','+data.sysno+','+data.storagetank_sysno+','+data.storagetanknature+')">联查明细</button>';
    }
    function see_storagetankdetail(val,starqty,totalqty,goods_sysno,sysno,storagetank_sysno,storagetanknature){
        var date1 = $("#date1").val();
        var date2 = $("#date2").val();
        BJUI.navtab({
            id:'menu175',
            url:'/report_storagetankinout/detaillist/sid/'+val+'/id/'+sysno,
            type: 'post',
            data:{startqty:starqty,totalstockqty:totalqty,date1:date1,date2:date2,goods_sysno:goods_sysno,storagetanksysno:storagetank_sysno,storagetanknature:storagetanknature},
            title:'储罐汇总明细'
        });
    }

    function tankinout(){
        var date1 = $("#date1").val();
        var date2 = $("#date2").val();
        var tankno = $("#tankno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/report_Storagetankinout/Excel/',
            type:'POST',
            data:{date1: date1,date2:date2,tankno:tankno},
            successCallback: function(json, options) {
            }
        });
    }

</script>
