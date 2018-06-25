<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="poundscarth-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#poundscarth-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="2" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">第二次过磅时间：</label>
                <div class="row-input datawidth">
                    <input type="text" id="startDate" name="startDate" value="{{$startDate or ''}}" size="17" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm"  placeholder="开始时间" />
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="endDate" name="endDate" value="{{$endDate or ''}}" size="17" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm"  placeholder="结束时间" />
                </div>

                <label class="row-label">退货磅码单号</label>
                <div class="row-input">
                    <input type="text" id="poundsinno" name="poundsinno" placeholder="退货磅码单号" >
                </div>

                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" id="carid" name="carid" placeholder="车牌号">
                </div>

                <label class="row-label">客户：</label>
                <div class="row-input">
                    <select id="customer_sysno" name="customer_sysno" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">退货订单号</label>
                <div class="row-input">
                    <input type="text" id="stockrebackno" name="stockrebackno" placeholder="退货订单号" >
                </div>

                <label class="row-label">品名</label>
                <div class="row-input">
                    <input type="text" name="goodsname"  placeholder="品名">
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select id="status" name="status" data-toggle="selectpicker" data-width="100%">
                        <option value="" selected>全部</option>
                        <option value="2">核单完成</option>
                        <option value="3">重车过磅</option>
                        <option value="4">空车过磅</option>
                        <option value="5">作废</option>
                    </select>
                </div>

                <label class="row-label"></label>
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
    <table class="table table-bordered" id="poundscarth-table" data-toggle="datagrid" data-options="{
        tableWidth:'99%',
        height: '100%',
        toolbarCustom:'#poundscarth_list_tb',
        showToolbar: true,
        addLocation: 'last',
        dataUrl: 'thcar/poundsthlistJson',
        editMode:false,
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'poundsinno',width:150,align:'center'}">单据编号</th>
            <th data-options="{name:'customername',align:'center',width:200}">客户</th>
            <th data-options="{name:'deliverycompany',align:'center',width:200}">发货公司</th>
            <th data-options="{name:'loadometer',align:'center'}">地磅编号</th>
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'',align:'center',render:function(value){if(value == '') return 'kg';}}">计量单位</th>
            <th data-options="{name:'unloadnumber',align:'center',calc:'sum'}">预卸数量(kg)</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum', render:function(value){ return parseInt(value)}}">实际重量(kg)</th>
            <th data-options="{name:'carcheck', align:'center', render:function(value){ if(value=='0') {return '待核对'} else if(value=='1') {return '审核通过'} else if(value=='2') {return '车辆退回'}}}">车辆核对</th>
            <th data-options="{name:'quaulitycheck', align:'center', render:function(value){ if(value=='0') {return '待品检'} else if(value=='1') {return '合格'} else if(value=='2') {return '让步通过'}else if(value=='3') {return '不合格'}}}">品检结果</th>
            <th data-options="{name:'poundsinstatus',align:'center',render:function(value){if(value=='1') {return '新建'} else if(value=='2') {return '核单完成'} else if(value=='3') {return '重车过磅'}  else if(value=='4') {return '空车过磅'}else if(value =='5'){return '作废'; } }}">
                单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>
<div id="poundscarth_list_tb">
    <button type="button" id="poundscarth_look" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" id="poundscarth_edit" class="btn btn-green" data-icon="edit">编辑</button>
    <button type="button" id="poundscarth_del" class="btn btn-red"  data-icon="times">删除</button>
    <button type="button" id="poundscarth_void" class="btn btn-red"  data-icon="scissors">作废</button>
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="poundsrebacklist_signout()">EXCEL导出</button>
</div>
<script type="text/javascript">
    //查看
    $('#poundscarth_look').click(function(){
        var data = $.CurrentNavtab.find("#poundscarth-table").data('selectedDatas');
        console.log(data);
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '请先选中要查看的磅码单再查看');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条磅码单查看');
        }else{
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'navab999',
                url:'/thcar/edit/mode/eye/id/'+sysno,
                title:'查看退货磅码单',
            })
        }
    });

    //编辑
    $('#poundscarth_edit').click(function(){
        var data = $.CurrentNavtab.find("#poundscarth-table").data('selectedDatas');
        console.log(data);
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '请先选中要编辑的磅码单再编辑');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条磅码单编辑');
        }else if(data[0]['poundsinstatus']!=2){
            BJUI.alertmsg('warn', '只有核单完成的磅码单才能编辑');
        }else{
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'navab999',
                url:'/thcar/edit/mode/edit/id/'+sysno,
                title:'编辑退货磅码单',
            })
        }
    });

    //删除
    $('#poundscarth_del').click(function(){
        var data = $.CurrentNavtab.find("#poundscarth-table").data('selectedDatas');
        console.log(data);
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '请先选中要删除的磅码单再删除');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条磅码单删除');
        }else if(data[0]['poundsinstatus']!=2&&data[0]['poundsinstatus']!=5){
            BJUI.alertmsg('warn', '只有核单完成的磅码单才能删除');
        }else{
            var sysno = data[0]['sysno'];
            BJUI.ajax('doajax', {
                url: '/thcar/deljson',
                data:{id: sysno},
                okCallback: function(json, options) {
                    BJUI.navtab('refresh', 'navab584');
                }
            });
        }
    });

    //作废
    $('#poundscarth_void').click(function(){
        var data = $.CurrentNavtab.find("#poundscarth-table").data('selectedDatas');
        console.log(data);
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '请先选中要编辑的磅码单再作废');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条磅码单作废');
        }else if(data[0]['poundsinstatus']!=3&&data[0]['poundsinstatus']!=4){
            BJUI.alertmsg('warn', '只有重车过磅和空车过磅状态的才能作废');
        }else{
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'navab999',
                url:'/thcar/edit/mode/ablish/id/'+sysno,
                title:'作废退货磅码单',
            })
        }
    });

    //导出excel
    function poundsrebacklist_signout(){
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var poundsinno = $("#poundsinno").val();
        var carid = $("#carid").val();
        var customer_sysno = $("#customer_sysno option:selected").val();
        var stockrebackno = $("#stockrebackno").val();
        var status = $("#status option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/thcar/excel/',
            type:'POST',
            data:{startDate: startDate,endDate:endDate,poundsinno:poundsinno,carid:carid,customer_sysno:customer_sysno,stockrebackno:stockrebackno,status:status},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }
</script>