<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="poundscarout-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#poundscarout-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="2" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">出库磅码单号</label>
                <div class="row-input">
                    <input type="text" name="poundsoutno" placeholder="出库磅码单号" >
                </div>

                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" name="carid" placeholder="车牌号">
                </div>

                <label class="row-label">客户：</label>
                <div class="row-input">
                    <input type="hidden" id="poundoutcustomername" name="customername" value="">
                    <select id="poundoutcusid" name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">出库订单号</label>
                <div class="row-input">
                    <input type="text" name="stockoutno" placeholder="出库订单号" >
                </div>
                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select name="status" data-toggle="selectpicker" data-width="100%">
                        <option value="" selected>全部</option>
                        <option value="2">核单完成</option>
                        <option value="3">空车过磅</option>
                        <option value="4">重车过磅</option>
                        <option value="5">作废</option>
                        <option value="6">退单</option>
                    </select>
                </div>
            </div>
            <div class="bjui-row col-3">
                <label class="row-label">第二次过磅时间：</label>
                <div class="row-input datawidth">
                    <input type="text" name="startDate" value="{{$startDate or ''}}" size="17" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm"  placeholder="开始时间" />
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="endDate" value="{{$endDate or ''}}" size="17" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm"  placeholder="结束时间" />
                </div>
                <label class="row-label">查询罐号:</label>
                <div class="row-input">
                    <select name="storagetank_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($tanklist as $item)
                            <option value="{{$item['sysno']}}">{{$item['storagetankname']}}</option>
                        @endforeach
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
    <table class="table table-bordered" id="poundscarout-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        toolbarCustom:'#poundscarout_list_tb',
        toolbarItem: 'export',
        showToolbar: true,
        addLocation: 'last',
        dataUrl: 'bookoutcars/poundsoutlistJson',
        exportOption: {type:'file', options:{url:'/bookoutcars/Excellist',form:$('#poundscarout-excel')}},
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
            <th data-options="{name:'poundsoutno',width:150,align:'center'}">单据编号</th>
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'customername',align:'center',width:200}">客户</th>
            {{--<th data-options="{name:'loadometer',align:'center'}">地磅编号</th>--}}
            <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
            <th data-options="{name:'cartype',align:'center',render:function(value){ if(value=='1'){return '槽车';}else if(value=='3'){ return '桶车';}else if(value=='2'){return '隔舱车';} } }">槽车类型</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'',align:'center',render:function(value){if(value == '') return 'kg';}}">计量单位</th>
            <th data-options="{name:'takeqty',align:'center',calc:'sum', render:function(value){ return parseInt(value)}}">预提数量(kg)</th>
            <th data-options="{name:'noticenumber',align:'center',calc:'sum', render:function(value){ return parseInt(value)}}">通知装货数(kg)</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum', render:function(value){ return parseInt(value)}}">实际重量(kg)</th>
            <th data-options="{name:'carcheck', align:'center', render:function(value){ if(value=='0') {return '待核对'} else if(value=='1') {return '审核通过'} else if(value=='2') {return '车辆退回'}}}">车辆核对</th>
            <th data-options="{name:'poundsoutstatus',align:'center',render:function(value){if(value=='1') {return '新建'} else if(value=='2') {return '核单完成'} else if(value=='4') {return '重车过磅'} else if(value=='3') {return '空车过磅'} else if(value =='5'){return '作废'; } else if(value =='6'){return '退单'; } }}">
                单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>
<div id="poundscarout_list_tb">
    <button type="button" id="poundscarout_look" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" id="poundscarout_edit" class="btn btn-green" data-icon="edit">编辑</button>
    <button type="button" id="poundscarout_del" class="btn btn-red"  data-icon="times">删除</button>
    <button type="button" id="poundscarout_void" class="btn btn-red"  data-icon="scissors">作废</button>
    <button type="button" id="poundscarout_back" class="btn btn-red"  data-icon="scissors">退单</button>
</div>
<script type="text/javascript">
    $("#poundoutcusid").change(function (){
        $("#poundoutcustomername").val($("#poundoutcusid option:selected").text())
    });

        $('#poundscarout_look').click(function(){
        var checkdata=$('#poundscarout-table').data('selectedDatas');
        var chks=$.CurrentNavtab.find("#poundscarout-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
            return;
        }
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>查看时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;

            BJUI.navtab({
                id:'pendcarout_detail',
                url:'/bookoutcars/poundsoutDetail/id/'+id+'/type/look',
                title:'查看车出库磅码单',
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>');
        }
    });


        $('#poundscarout_void').click(function(){
        var checkdata=$('#poundscarout-table').data('selectedDatas');
        var chks=$.CurrentNavtab.find("#poundscarout-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
            return;
        }

        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>作废时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;
            var status = checkdata[0].poundsoutstatus;
            // console.log(status); return;

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
                id:'pendcarout_void',
                url:'/bookoutcars/poundsoutDetail/id/'+id+'/void/'+1,
                title:'作废车出库磅码单',
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>');
        }
    });

    $('#poundscarout_del').click(function(){
            var checkdata=$('#poundscarout-table').data('selectedDatas');
            var chks=$.CurrentNavtab.find("#poundscarout-table");
            var id = checkdata[0].sysno;
            var status = checkdata[0].poundsoutstatus;
            var carcheck = checkdata[0].carcheck;
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
                return;
            }

//            if(carcheck != 0)
//            {
//                BJUI.alertmsg('warn','<h4>该单据已车辆检查，不能删除！</h4>');
//                return;
//            }

            // console.log(status);return;
            if(checkdata && checkdata.length>0){
                if(checkdata.length>1){
                    BJUI.alertmsg('warn','<h4>删除时只能选择一条数据!<h4>');
                    return;
                }
                if (carcheck==2){
                    BJUI.alertmsg('warn','<h4>退单的单据不能删除!<h4>');
                    return;
                }
                if(status!=2){
                    BJUI.alertmsg('warn','<h4>该单据不能删除!</h4>');
                    return;
                }else{
                    BJUI.alertmsg('confirm','你确定删除吗',{
                        okCall: function(){
                        BJUI.ajax('doajax',{
                            url:'/bookoutcars/Poundscaroutdel/id/'+id,
                            loadingmask: true,
                            okCallback: function() {
                                BJUI.navtab('refresh','navab451');
                            }
                        });
                    } 
                });

                }

            }else{
                BJUI.alertmsg('warn','<h4>未选中数据！</h4>');
            }        
        });
    $('#poundscarout_back').click(function(){
        var checkdata=$('#poundscarout-table').data('selectedDatas');
        var chks=$.CurrentNavtab.find("#poundscarout-table");
        var id = checkdata[0].sysno;
        var status = checkdata[0].poundsoutstatus;
        var carcheck = checkdata[0].carcheck;
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
            return;
        }
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>退单时只能选择一条数据!<h4>');
                return;
            }
            if(carcheck==2){
                BJUI.alertmsg('confirm','你确定退单吗',{
                    okCall: function(){
                        BJUI.ajax('doajax',{
                            url:'/bookoutcars/Poundscaroutback/id/'+id,
                            loadingmask: true,
                            okCallback: function() {
                                BJUI.navtab('refresh','navab451');
                            }
                        });
                    }
                });
            }else{
                BJUI.alertmsg('warn','<h4>只有车辆退回和品检不合格的单据才能点击退单</h4>');
                return;
            }

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>');
        }
    });

    //编辑
    $('#poundscarout_edit').click(function(){
        var checkdata=$('#poundscarout-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>编辑时只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].stockin_sysno;
            var status = checkdata[0].poundsoutstatus;
            var carid = checkdata[0].carid;
            var poundid = checkdata[0].sysno;
            var carcheck = checkdata[0].carcheck;
            if (carcheck==2){
                BJUI.alertmsg('warn','<h4>编辑的单据不能删除!<h4>');
                return;
            }
            if(carcheck != 0)
            {
                BJUI.alertmsg('warn','<h4>该单据已车辆检查，不能编辑！</h4>');
                return;
            }
            // console.log(id);return;
            if(status!=2){
                BJUI.alertmsg('warn','<h4>只能编辑核单完成的单据!</h4>');
                return;
            }else{
                BJUI.navtab({
                    id:'navtab_out_car',
                    url:'/bookoutcars/Poundscaroutedit',
                    type: 'POST',
                    data:{id:id,carid:carid,poundid:poundid},
                    title:'编辑车出库磅码单',
                });
            }

        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>');
        }   
    });
</script>