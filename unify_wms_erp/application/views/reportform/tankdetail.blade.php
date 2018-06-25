<div class="bjui-pageHeader">
    <form id="tankdetail" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportformtankdetail-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">时间范围</label>
                <div class="row-input datawidth required">
                    <input id="tankdetail_date1" type="text" name="date1" data-rule="required" value="@if($date1){{$date1}}@else{{date('Y-m-d',strtotime("-1 month"))}}@endif" placeholder="开始时间"  data-toggle="datepicker" >
                </div>
                <div class="row-input datawidth required">
                	<input id="tankdetail_date2" type="text" name="date2" data-rule="required" value="@if($date2){{$date2}}@else{{date('Y-m-d',time())}}@endif" placeholder="结束时间"  data-toggle="datepicker">
                </div>

                <label class="row-label">储罐号</label>
                <div class="row-input required">
                    <select id="tankdetail_tankno" name="tankno" data-rule="required" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size='10' >
                        <option value="">请选择</option>
                        @foreach($storagetank as $key=>$value)
                        	<option value="{{$key}}" @if($key==$tankno) selected="selected" @endif>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <br>

                <label class="row-label">期初数量</label>
                <div class="row-input">
                    <input id="startqty" type="text" name="startqty" value="{{$startqty}}" readonly>
                </div>
                <label class="row-label">期末数量</label>
                <div class="row-input">
                    <input id="totalstockqty" type="text" name="totalstockqty" value="{{$totalstockqty}}" readonly>
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search" onclick="getStartAndEnd()">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reportformtankdetail-table" data-toggle="datagrid" data-options="{
            fullGrid:true,
            height: '100%',
            showToolbar: true,
            toolbarCustom:$('#tanklist_tb'),
            addLocation: 'last',
            dataUrl: '/reportform/tankDetailJson/sid/{{$tankno}}/date1/{{$date1}}/date2/{{$date2}}/',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:10},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'created_at',align:'center'}">单据日期</th>
            <th data-options="{name:'doctype',align:'center',render:function(value){return value == 1 ? '车入库' :value==2?'车入库作废':value==3?'车出库':value==4?'车出库作废':value==5?'船入库':value==6?'船入库作废'
            :value==7?'船出库':value==8?'船出库作废':value==9?'盘点':value==11?'盘点作废':value==10?'倒罐':'倒罐作废'}}">
                单据类型
            </th>
            <th data-options="{name:'docno',align:'center'}">单据编号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'} else {return '未限制'}}}">
                货物性质
            </th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum'}">数量</th>
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
        var id = $("#tankdetail_tankno option:selected").val();
        var date1 = $("#tankdetail_date1").val();
        BJUI.ajax('doajax',{
            url: '/reportform/getStartAndEnd/sid/'+id,
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
        return '<button type="button" class="btn-green" onclick="see_doc('+data.doctype+','+data.doc_sysno+')">查看单据</button>';
    }

    function see_doc(val1,val2){
        if(val1==1||val1==2){
            BJUI.navtab({
                id:'pendcarin_edit',
                url:'/pendcarin/poundsdetail/id/'+val2,
                title:'车入库磅码单',
            });
        }else if(val1==3||val1==4){
            BJUI.navtab({
                id:'pendcarout_detail',
                url:'/bookoutcars/poundsoutDetail/id/'+val2,
                title:'查看车出库磅码单',
            });
        }else if(val1==5||val1==6){
            BJUI.navtab({
                id: 'showshipin758',
                url: '/stockshipin/show/id/'+val2,
                title: '查看船入库订单'
            });
        }else if(val1==7||val1==8){
            BJUI.navtab({
                id:'custom_stockout_view'+val2,
                url:'/stockout/shipview/id/'+val2,
                title:'查看船出库订单'
            });
        }else if(val1==9){
            BJUI.navtab({
                id: 'navab290',
                url: '/check/checksee/id/'+val2,
                type: 'post',
                data: {'id': val2},
                title: '查看盘点单'
            });
        }else if(val1==11){
            BJUI.navtab({
                id: 'navab290',
                url: '/check/checksee/id/'+val2,
                type: 'post',
                data: {'id': val2},
                title: '查看盘点单'
            });
        }else if(val1==10){
            BJUI.navtab({
                id: 'navab302',
                url: '/retank/lookretank/mode/eye/id/'+val2,
                title: '查看倒罐单'
            });
        }else if(val1==12){
            BJUI.navtab({
                id: 'navab302',
                url: '/retank/lookretank/mode/eye/id/'+val2,
                title: '查看倒罐单'
            });
        }
    }

    function tankdetailsignout(){
        var date1 = $("#tankdetail_date1").val();
        var date2 = $("#tankdetail_date2").val();
        var tankno = $("#tankdetail_tankno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/reportform/detailexcel/',
            type:'POST',
            data:{date1: date1,date2:date2,tankno:tankno},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>