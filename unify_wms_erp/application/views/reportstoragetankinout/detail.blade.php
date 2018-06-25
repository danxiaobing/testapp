<div class="bjui-pageHeader">
    <form id="tankdetail" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#storagetankinoutdetail-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">时间范围</label>
                <div class="row-input datawidth required">
                    <input id="tankdetail_date1" type="text" name="date1" data-rule="required" value="@if($date1){{$date1}}@else{{date('Y-m-d',strtotime("-1 month"))}}@endif" placeholder="开始时间"  data-toggle="datepicker" readonly>
                </div>
                <div class="row-input datawidth required">
                    <input id="tankdetail_date2" type="text" name="date2" data-rule="required" value="@if($date2){{$date2}}@else{{date('Y-m-d',time())}}@endif" placeholder="结束时间"  data-toggle="datepicker" readonly>
                </div>
                <label class="row-label">本期结存量</label>
                <div class="row-input">
                    <input id="currenttotalstockqty" type="text" name="currenttotalstockqty" value="{{$totalstockqty}}" placeholder="本期结存量" readonly>
                </div>
                <label class="row-label">储罐号</label>
                <div class="row-input">
                    <input type="text" name="storagetankname" value="{{$storagetankname}}" readonly>
                    <input type="hidden" name="storagetank_sysno" value="{{$storagetank_sysno}}">
                </div>
                <label class="row-label">储罐性质</label>
                <div class="row-input">
                    <input type="text" name="storagetanknature" value="@if($storagetanknature == 1) 内贸罐 @elseif($storagetanknature == 2) 外贸罐 @else 保税罐 @endif" readonly>
                </div>
                <label class="row-label">产品名称</label>
                <div class="row-input">
                    <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                    <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="storagetankinoutdetail-table" data-toggle="datagrid" data-options="{
            fullGrid:true,
            height: '100%',
            showToolbar: true,
            toolbarCustom:$('#tanklist_tb'),
            addLocation: 'last',
            dataUrl: '/report_storagetankinout/tankDetailJson/sid/{{$tankno}}/goods_sysno/{{$goods_sysno}}/startqty/{{$startqty}}/date1/{{$date1}}/date2/{{$date2}}/date2/{{$date2}}/',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:10},
            {{--postData: {startqty:{{$startqty}}},--}}
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center'}">客户名称</th>
            <th data-options="{name:'created_at',align:'center'}">日期</th>
            <th data-options="{name:'doc_sysno_type',align:'center'}">方式</th>
            {{--<th data-options="{name:'doc_type',align:'center',render:function(value){if(value==1){return value='船入库';}else if(value==2){return value='车入库';}else if(value==3){return value='船出库';}else if(value==4){return value='车出库';}else if(value==5){return value='货转入';}else if(value==6){return value='货转出';}else if(value==7){return value='倒罐入';}else if(value==8){return value='倒罐出';}else if(value==9){return value='盘点(储罐)';}else if(value==10){return value='盘点(客户)';}else if(value==11){return value='管线入库';}else if(value==12){return value='管线出库';}else if(value==13){return value='提单入';}else if(value==14){return value='提单出';}else if(value==15){return value='超期损耗';}else if(value==16){return value='提单撤销入';}else if(value==17){return value='提单撤销出';}else if(value==18){return value='清库损耗';}else if(value==19){return value='补单入';}else if(value==20){return value='补单扣';}else if(value==21){return value='提单倒罐入';}else if(value==22){return value='提单倒罐出';}else if(value==23){return value='库存调整出';}else if(value==24){return value='库存调整入';}else if(value==25){return value='退货';}else{return '--';}}}">方式</th>--}}
            <th data-options="{name:'transportationtype',align:'center'}">船名/车</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum'}">数量(吨)</th>
            <th data-options="{name:'clearingstock',align:'center',render:function(value){if(value=='-0.000') return 0}}">结存量(吨)</th>
            <th data-options="{name:'doc_sysno',align:'center',render:tankdetail_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="tanklist_tb">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="tankdetailsignout()">EXCEL导出</button>
</div>

<script>
    function getStartAndEnd(){
        //var id = $("#tankdetail_tankno option:selected").val();
        var date1 = $("#tankdetail_date1").val();
        BJUI.ajax('doajax',{
            url: '/report_storagetankinout/getStartAndEnd/sid/'+id,
            type:'POST',
            data:{date1:date1},
            loadingmask: true,
            okCallback: function(json, options) {
                $("#startqty").val(json.startqty);
                $("#totalstockqty").val(json.totalstockqty);
            }
        })
    }

    function tankdetail_operation(value,data) {  
        return '<button type="button" class="btn-green" onclick="see_doc('+data.doc_type+','+data.doc_sysno+','+data.booking_in_sysno+')">查看单据</button>';
    }

    function see_doc(val1,val2,val3){
        if(val1==1){
            BJUI.navtab({
                id: 'showshipin758',
                url: '/stockshipin/show/id/'+val2,
                type: 'post',
                data: {'booking_sysno':val3},
                title: '查看船入库订单'
            });
        }else if(val1==2){
            BJUI.navtab({
                id:'navab0123',
                url:'/stockcarin/see/mode/eye/id/'+val2,
                title:'查看车入库订单'
            });
        }else if(val1==3){
            BJUI.navtab({
                id:'navab0123',
                url:'/stockout/shipedit/type/view/id/'+val2,
                title:'查看船出库订单'
            });
        }else if(val1==4){ 
            BJUI.navtab({
                id:'navab0123',
                url:'/bookoutcars/poundsoutDetail/id/'+val2,
                title:'查看车出库磅码单'
            });
        }else if(val1==5 || val1==6){
            BJUI.navtab({
                id:'navab0123',
                url:'/stocktrans/edit/val/1/id/'+val2,
                title:'查看货权转移订单'
            });
        }else if(val1==7 || val1==8 || val1==21 || val1==22){
            BJUI.navtab({
                id: 'navab302',
                url: '/retank/lookretank/mode/eye/id/'+val2,
                title: '查看倒罐单'
            });
        }else if(val1==9){
            BJUI.navtab({
                id: 'navab290',
                url: '/check/storagetanksee/id/'+val2,
                type: 'post',
                data: {'id': val2},
                title: '查看盘点储罐单'
            });
        }else if(val1==10){
            BJUI.navtab({
                id: 'navab290',
                url: '/check/checksee/id/'+val2,
                type: 'post',
                data: {'id': val2},
                title: '查看盘点客户单'
            });
        }else if(val1==11 || val1==12){
            BJUI.navtab({
                id: 'navab290',
                url: '/stockout/pipelineEdit/type/view/id/'+val2,
                type: 'post',
                data: {'id': val2},
                title: '查看管出库单'
            });
        }else if(val1==13 || val1==14 || val1==16 || val1==17 || val1==23 || val1==24){
            BJUI.navtab({
                id: 'navab290',
                url: '/introduce/edit/type/view/id/'+val2,
                type: 'post',
                data: {'id': val2},
                title: '查看提单'
            });
        }else if(val1==18){
            BJUI.navtab({
                id: 'navab302',
                url: '/clearstock/lookclearstock/id/'+val2 +'/val/1',
                title: '查看清库单'
            });
        }else if(val1==19 || val1==20){
            BJUI.navtab({
                id: 'navab302',
                url: '/supplement/edit/mode/view/id/'+val2,
                title: '查看补充入库单'
            });
        }else if(val1==230 || val1==240){
            BJUI.navtab({
                id: 'navab302',
                url: '/stockullage/see/mode/eye/id/'+val2,
                title: '库存调整'
            });
        }else if(val1==26){
            BJUI.navtab({
                id: 'navab302',
                url: '/reback/edit/mode/view/id/'+val2, 
                title: '查看退货单'
            });
        }
    }


    function tankdetailsignout(){
        var date1 = $("#tankdetail_date1").val();
        var date2 = $("#tankdetail_date2").val();
        //var tankno = $("#tankdetail_tankno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/report_Storagetankinout/detailexcel/',
            type:'POST',
            data:{date1: date1,date2:date2,tankno:{{$tankno}}},
            successCallback: function(json, options) {
            }
        });
    }

</script>