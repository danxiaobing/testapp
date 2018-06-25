<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="poundscarin-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#poundscarin-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="2" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">入库磅码单号</label>
                <div class="row-input">
                    <input type="text" name="poundsinno"  placeholder="入库磅码单号">
                </div>

                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" name="pounds_carid" placeholder="车牌号">
                </div>

                <label class="row-label">客户：</label>
                <div class="row-input">
                    <input type="hidden" id="poundcustomername" name="customername" value="">
                    <select id="poundcusid" name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">入库订单号</label>
                <div class="row-input">
                    <input type="text" name="stockinno"  placeholder="入库订单号">
                </div>

                <label class="row-label">品名</label>
                <div class="row-input">
                    <input type="text" name="goodsname"  placeholder="品名">
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select name="pounds_status" data-toggle="selectpicker" data-width="100%">
                        <option value="" selected>全部</option>
                        <option value="2">核单完成</option>
                        <option value="3">重车过磅</option>
                        <option value="4">空车过磅</option>
                        <option value="5">作废</option>
                        <option value="6">退单</option>
                    </select>
                </div>
            </div>
            <div class="bjui-row col-3">
                <label class="row-label">第二次过磅时间：</label>

                <div class="row-input datawidth">
                    <input type="text" name="startDate" value="" size="17" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm" placeholder="开始时间" />
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="endDate" value="" size="17" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm" placeholder="结束时间" >
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
    <table class="table table-bordered" id="poundscarin-table" data-toggle="datagrid" data-options="{
        height: '100%',
        toolbarCustom:'#poundscarin_list_tb',
        toolbarItem: 'export',
        showToolbar: true,
        addLocation: 'last',
        dataUrl: 'pendcarin/poundslistJson',
        exportOption: {type:'file', options:{url:'/pendcarin/Excellist',form:$('#poundscarin-excel')}},
        editMode:false,
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'poundsinno',align:'center'}">单据编号</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'deliverycompany',align:'center'}">发货公司</th>
            <th data-options="{name:'loadometer',align:'center'}">地磅编号</th>
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'',align:'center',render:function(value){if(value == '') return 'kg';}}">计量单位</th>
            <th data-options="{name:'unloadnumber',align:'center',calc:'sum', render:function(value){return parseInt(value);}}">预卸数量(kg)</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum', render:function(value){return parseInt(value);}}">实际重量(kg)</th>
            <th data-options="{name:'carcheck',align:'center', render:function(value){if(value=='0') {return '待核对'}else if(value=='1') {return '审核通过'}else if(value=='2') {return '车辆退回'}}}">车辆核对</th>
            <th data-options="{name:'quaulitycheck',align:'center', render:function(value){if(value=='0') {return '待品检'}else if(value=='1') {return '合格'}else if(value=='2') {return '让步通过'}else if(value=='3') {return '不合格'}}}">品检结果</th>
            <th data-options="{name:'poundsinstatus',align:'center',render:function(value){if(value=='1') {return '新建'} else if(value=='2') {return '核单完成'} else if(value=='3') {return '重车过磅'} else if(value=='4') {return '空车过磅'} else if(value =='5'){return '作废'}else if(value =='6'){return '退单'} }}">
                单据状态
            </th>
            <th data-options="{name:'stockin_sysno',align:'center',hide:'true'}">入库单ID</th>
        </tr>
        </thead>
    </table>
</div>
<div id="poundscarin_list_tb">
    <button type="button" id="poundscarin_look" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" id="poundscarin_edit" class="btn btn-green" data-icon="edit">编辑</button>
    <button type="button" id="poundscarin_del" class="btn btn-red"  data-icon="times">删除</button>
    <button type="button" id="poundscarin_void" class="btn btn-red"  data-icon="scissors">作废</button>
    <button type="button" id="poundscarin_back" class="btn btn-red"  data-icon="scissors">退单</button>
</div>
<script type="text/javascript">
    $("#poundcusid").change(function (){
        $("#poundcustomername").val($("#poundcusid option:selected").text())
    });
        $('#poundscarin_look').click(function(){
        var checkdata=$('#poundscarin-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>查看时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;

            BJUI.navtab({
                id:'pendcarin_edit',
                url:'/pendcarin/poundsdetail/id/'+id+'/type/look',
                title:'车入库磅码单',
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>');
        }
    });


        $('#poundscarin_void').click(function(){
        var checkdata=$('#poundscarin-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>作废时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;
            var status = checkdata[0].poundsinstatus;
            var arr = new Array(3,4);
            var arrinStatus = false;
            for(var i = 0; i < arr.length; i++){
                if(status == arr[i]){
                    arrinStatus =  true;
                }
            }
            if(!arrinStatus){
                BJUI.alertmsg('warn','<h4>该单据不能作废!</h4>');
                return;
            }

            BJUI.navtab({
                id:'pendcarin_edit',
                url:'/pendcarin/poundsdetail/id/'+id+'/void/'+1,
                title:'车入库磅码单',
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>');
        }
    });

    $('#poundscarin_del').click(function(){
        var checkdata=$('#poundscarin-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>删除时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;
            var status = checkdata[0].poundsinstatus;
            if(status == 6){
                BJUI.alertmsg('warn','<h4>退单的单据不能删除!<h4>');
                return;
            }
//            if(checkdata[0].carcheck != 0){
//                BJUI.alertmsg('warn','<h4>该单据已车辆核对,不能删除!</h4>');
//                return;
//            }
            // console.log(id);return;
            if(status!=2){
                BJUI.alertmsg('warn','<h4>该单据不能删除!</h4>');
                return;
            }else{
                BJUI.alertmsg('confirm','你确定删除吗',{
                    okCall: function(){
                    BJUI.ajax('doajax',{
                        url:'/pendcarin/poundsdel/id/'+id,
                        loadingmask: true,
                        okCallback: function() {
                            BJUI.navtab('refresh','navab447');
                        }
                    });
                } 
            });

            }

        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>');
        }        
    });
    $('#poundscarin_back').click(function () {
        var checkdata=$('#poundscarin-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>退单时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;
            var status = checkdata[0].quaulitycheck;

//            if(checkdata[0].carcheck != 0){
//                BJUI.alertmsg('warn','<h4>该单据已车辆核对,不能删除!</h4>');
//                return;
//            }
//             console.log(status);return;
            if(status==3 || status==0){
                BJUI.alertmsg('confirm','你确定退单吗',{
                    okCall: function(){
                        BJUI.ajax('doajax',{
                            url:'/pendcarin/poundsback/id/'+id,
                            loadingmask: true,
                            okCallback: function() {
                                BJUI.navtab('refresh','navab447');
                            }
                        });
                    }
                });
            }else{
                BJUI.alertmsg('warn','<h4>只有车辆退回和品检不合格的单据才能点击退单!</h4>');
                return;
            }

        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>');
        }
    });


    $('#poundscarin_edit').click(function(){
        var checkdata=$('#poundscarin-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>编辑时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].stockin_sysno;
            var status = checkdata[0].poundsinstatus;
            var carid = checkdata[0].carid;
            var poundid = checkdata[0].sysno;
            if(status == 6){
                BJUI.alertmsg('warn','<h4>退单的单据不能编辑!<h4>');
                return;
            }
            if(checkdata[0].quaulitycheck != 0){
                BJUI.alertmsg('warn','<h4>该单据已经品检,不能编辑!</h4>');
                return;
            }
            var carcheck = checkdata[0].carcheck;
            if(carcheck != 0)
            {
                BJUI.alertmsg('warn','<h4>该单据已车辆检查，不能编辑！</h4>');
                return;
            }
            if(status!=2){
                BJUI.alertmsg('warn','<h4>该单据不能编辑!</h4>');
                return;
            }else{
                BJUI.navtab({
                    id:'pendcarin_edit'+id,
                    url:'/pendcarin/edit/type/1/'+ poundid,
                    type: 'POST',
                    data:{id:id,carid:carid,poundid:poundid},
                    title:'车入库磅码单',
                });
            }

        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>');
        }     
    });
</script>