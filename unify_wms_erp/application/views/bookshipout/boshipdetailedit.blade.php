<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="boshipout-detail-form"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json ">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">
                <label class="row-label">库存</label>
                <div class="row-input required">
                    <!-- 来源单据ID -->
                    <input type="hidden" id='boshipout_detail_firstfrom_sysno' name="firstfrom_sysno" value="@if($stockin_sysno != '') {{$stockin_sysno}} @elseif($stockin_sysno == '' && isset($selectedDatas['stockin_sysno'])) {{$selectedDatas['stockin_sysno']}} @endif">
                    <!-- 来源类型 -->
                    <input type="hidden" name="stocktype" id='boshipout_detail_stocktype' value="@if($stocktype != '') {{$stocktype}} @elseif($stocktype == '' && isset($selectedDatas['stocktype'])) {{$selectedDatas['stocktype']}} @endif">

                    <!-- 储罐ID -->
                    <input type="hidden" name="storagetank_sysno" value="@if($storagetank_sysno != '') {{$storagetank_sysno}} @elseif($storagetank_sysno == '' && isset($selectedDatas['storagetank_sysno'])) {{$selectedDatas['storagetank_sysno']}} @endif">
                    <!-- 可用数量 -->
                    <input type="hidden" id="boshipout_detail_ableqty" name="ableqty" value="@if($ableqty != '') {{$ableqty}} @elseif($ableqty == '' && isset($selectedDatas['ableqty'])) {{$selectedDatas['ableqty']}} @endif">
                    <!-- 库存ID -->
                    <input type="hidden" name="sysno" value="@if($stock_sysno != '') {{$stock_sysno}} @elseif($stock_sysno == '' && isset($selectedDatas['stock_sysno'])) {{$selectedDatas['stock_sysno']}} @endif">

                    <!-- 储罐可用余量 -->
                    <input type="hidden" id='boshipout_detail_storagetankableqty' name="storagetankableqty" value="@if($storagetankableqty != '') {{$storagetankableqty}} @elseif($storagetankableqty == '' && isset($selectedDatas['storagetankableqty'])) {{$selectedDatas['storagetankableqty']}} @endif">
                    <!-- 储罐货品ID -->
                    <input type="hidden" id="boshipout_detail_storagetankgoods_sysno" name="storagetankgoods_sysno" value="@if($storagetankgoods_sysno != '') {{$storagetankgoods_sysno}} @elseif($storagetankgoods_sysno == '' && isset($selectedDatas['storagetankgoods_sysno'])) {{$selectedDatas['storagetankgoods_sysno']}} @endif">
                    <!-- 入库货品ID -->
                    <input type="hidden" id="boshipout_detail_goods_sysno" name="goods_sysno" value="@if($goods_sysno != '') {{$goods_sysno}} @elseif($goods_sysno == '' && isset($selectedDatas['goods_sysno'])) {{$selectedDatas['goods_sysno']}} @endif">
                    <!-- 货权转移对应的入库单的入库量 -->
                    <input type="hidden" id="boshipout_detail_transInstockqty" name="transInstockqty" value="@if($transInstockqty != '') {{$transInstockqty}} @elseif($transInstockqty == '' && isset($selectedDatas['transInstockqty'])) {{$selectedDatas['transInstockqty']}} @endif">

                    <input type="text" id='boshipout_detail_firstfrom_no' name="firstfrom_no" value="{{$selectedDatas['stockin_no'] or '' }}" readonly data-rule="required" data-toggle="findgridbtn" data-options="{
                            dialogOptions: {width:'1200',height:'600',title:'库存详情',maxable:true,resizable:true,mask:true},
                            gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'96%',
                            local: 'local',
                            paging: {pageSize:20},
                            data: {{$stocklist}} ,
                            columns: [
                                {name:'firstfrom_no', label:'入库单号',width:200},
                                {name:'stocktype', label:'来源',render:function(value){switch(value) { case '1': return  '库存'; case '2': return '介绍信';} }},
                                {name:'goodsname', label:'品名'},
                                {name:'storagetankname', label:'储罐编号'},
                                {name:'goodsqualityname', label:'规格',width:100},
                                {name:'goodsnature', label:'货物性质',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }},
                                {name:'unitname', label:'计量单位'},
                                {name:'inshipname', label:'船名'},
                                {name:'instockqty', label:'入库数量'},
                                {name:'introduceqty', label:'提单总量'},
                                {name:'stockqty', label:'余量'},
                                {name:'clockqty', label:'锁定数量'},
                                {name:'ableqty', label:'可用数量'},
                                {name:'release_num', label:'报关数量'}
                                
                            ],
                            fullGrid:true,
                            showLinenumber:false
                        },
                    }" placeholder="点放大镜按钮查找">
                </div>

                <label class="row-label">品名</label>
                <div class="row-input">
                    <input type="text" id='boshipout_detail_goodsname' name="goodsname" value=" {{$selectedDatas['goodsname'] or '' }}" readonly>
                </div>

                <label class="row-label">规格</label>
                <div class="row-input">
                    <input type="text" id='boshipout_detail_goods_qualityname' name="goodsqualityname" value=" {{$selectedDatas['qualityname'] or '' }}" readonly>
                </div>
                
                <label class="row-label">货物性质</label>
                <div class="row-input">
                    <select id="boshipout_detail_goodsnature" name="goodsnature" data-toggle="selectpicker"  data-width="100%" data-rule="required" disabled>
                        <option value=""></option>
                        <option value="1" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '1') {{selected}} @endif>保税</option>
                        <option value="2" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '2') {{selected}} @endif>外贸</option>
                        <option value="3" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '3') {{selected}} @endif>内贸转出口</option>
                        <option value="4" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '4') {{selected}} @endif>内贸内销</option>
                    </select>
                </div>

                <label class="row-label">计量单位</label>
                <div class="row-input">
                    <input type="text" name="unitname" value="{{$selectedDatas['unitname'] or '' }}" readonly>
                </div>

                <label class="row-label">储罐编号</label>
                <!-- <div class="row-input required">
                    <input readonly type="text" id='boshipout_detail_storagetankname' name="storagetankname" value="{{$selectedDatas['storagetankname'] or '' }}"
                           data-rule="required" data-toggle="findgrid" data-options="{
                            dialogOptions: {width:'800',height:'500',title:'储罐名称',maxable:true,resizable:true,mask:true},
                            gridOptions: {
                                width:'100%',
                                height:'100%',
                                tableWidth:'99.8%',
                                local: 'local',
                                paging: {pageSize:20},
                                dataUrl: '/storagetank/getstoragetank',
                                columns: [
                                    {name:'areaname', label:'片区名称'},
                                    {name:'storagetankname', label:'储罐名称'},
                                    {name:'storagetankgoodsname',label:'货品名称'},
                                    {name:'tank_stockqty',label:'当前存放量'},
                                    {name:'orderoutqty',label:'待出量'},
                                    {name:'storagetankableqty',label:'可用余量'},
                                ],
                                showLinenumber:true
                            },
                        }" placeholder="点放大镜按钮查找">
                </div> -->
                <div class="row-input">
                    <input readonly type="text" id='boshipout_detail_storagetankname' name="storagetankname" value="{{$selectedDatas['storagetankname'] or '' }}">
                </div>

                <label class="row-label">入库数量</label>
                <div class="row-input">
                    <input type="text" name="instockqty" value="{{$selectedDatas['instockqty'] or ''}}" readonly>
                </div>

                <label class="row-label">提单总量</label>
                <div class="row-input">
                    <input type="text" name="introduceqty" value="{{$selectedDatas['introduceqty'] or ''}}" readonly>
                </div>

                <label class="row-label">提货数量</label>
                <div class="row-input required">
                    <input type="text" id="boshipout_detail_bookingoutqty" name="bookingoutqty"  value="{{$selectedDatas['bookingoutqty'] or '' }}" data-rule="required number range[0~]">
                </div>
                
                <label class="row-label">船名</label>
                <div class="row-input required">
                <input type="text" name="shipname" value="{{$selectedDatas['shipname'] or ''}}" data-rule="required" data-toggle="findgrid"
                       data-options="{
                    include: 'shipname:shipname',
                    dialogOptions: {width:'800',height:'500',title:'船详细信息',maxable:true,resizable:true,mask:true},
                    gridOptions: {
                        height:'100%',
                        local: 'local',
                        paging: {pageSize:5},
                        dataUrl: '/supplier/shiplistJson/page/1/bar_status/1',
                        editUrl: '/supplier/shiplist',
                        columns: [
                            {name:'captain', label:'船长',align:'center'},
                            {name:'shipname', label:'船名',align:'center'}
                        ],
                        showLinenumber:false,
                        fullGrid:true
                    },
                }" placeholder="点放大镜按钮查找">
                </div>

                <label class="row-label">预计到港日期</label>
                <div class="row-input required">
                    <input type="text" name="shipokdate" data-toggle="datepicker" value="{{$selectedDatas['shipokdate'] or ''}}" data-rule="required" readonly>
                </div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea  name="memo"  data-toggle="autoheight" cols="auto" rows="3" >{{$selectedDatas['memo'] or '' }}</textarea>
                </div>
    
            </div>
            
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveBoshipDetail()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        
        <li id="boshipout_detail_handlestatus" style="display: none">{{$handlestatus or ''}}</li>
    </ul>
</div>


<script type="text/javascript">

    $("#boshipout_detail_ship_sysno").change(function () {
        var shipdata = $("#boshipout_detail_ship_sysno option:selected")
        $("#boshipout_detail_shipname").val(shipdata.text());
    });

    function saveBoshipDetail() {
        var handlestatus=$('#boshipout_detail_handlestatus').html();
        var bookingoutqty = parseFloat($("#boshipout_detail_bookingoutqty").val());
        var ableqty = parseFloat($("#boshipout_detail_ableqty").val());
        var storagetankableqty = parseFloat($("#boshipout_detail_storagetankableqty").val());
        var storagetankname = $("#boshipout_detail_storagetankname").val();
        var storagetankgoods_sysno = parseFloat($("#boshipout_detail_storagetankgoods_sysno").val());
        var goods_sysno = parseFloat($("#boshipout_detail_goods_sysno").val());
        var stocktype = parseFloat($("#boshipout_detail_stocktype").val());

        if(!storagetankname){
            BJUI.alertmsg('warn','请先选择储罐',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(storagetankgoods_sysno != goods_sysno && storagetankgoods_sysno){
            BJUI.alertmsg('warn','要出库货品和所选储罐中的货品不一致',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        
        if(bookingoutqty>ableqty){
            BJUI.alertmsg('warn','您的出库数量不能大于该库存可用数量',{displayPosition:'middlecenter',displayMode:'fade'});
            $("#boshipout_detail_bookingoutqty").focus;
            return false;
        }

        $("#boshipout_detail_goodsnature").removeAttr('disabled');

        var data  = $("#boshipout-detail-form").serializeJson();
        var allData  = $("#boship-edit-detail-table").data('allData');
        var goodsnature = $("#boshipout_detail_goodsnature option:selected").val();
        data.stock_sysno=data.sysno;
        data.stockin_no = data.firstfrom_no;
        data.stockin_sysno = data.firstfrom_sysno;
        var goods_quality_name = $("#boshipout_detail_goods_qualityname").val();
        data.qualityname = goods_quality_name; 
        if(data.stocktype == 1 && goodsnature != 4){
            BJUI.ajax('doajax', {
                url: 'bookout/getDeclareNum',
                type:'POST',
                data:{stockin_sysno:data.stockin_sysno},
                loadingmask: true,
                okCallback: function(json, options) {
                    if((json.release_num >= bookingoutqty && json.islimitout == 1) || json.islimitout == 2){
                        submitShipDetailForm(bookingoutqty,storagetankableqty,allData,data,handlestatus,stocktype);
                    }else if(json.release_num < bookingoutqty && json.islimitout == 1){
                        BJUI.alertmsg('warn','货品报关量小于预约出库量',{displayPosition:'middlecenter',displayMode:'fade'});
                        return false;
                    }
                }
            })
        }else{
            submitShipDetailForm(bookingoutqty,storagetankableqty,allData,data,handlestatus,stocktype);
        }

    }
    function submitShipDetailForm(bookingoutqty,storagetankableqty,allData,data,handlestatus,stocktype) {
        var storagetankname = $("#boshipout_detail_storagetankname").val();
        if(bookingoutqty > storagetankableqty && stocktype == 1){
            BJUI.alertmsg('confirm','选择的'+ storagetankname +'罐库存不够,是否继续', {okCall:function() {
                $('#boshipout-detail-form').isValid(function(v){
                    if (v) {
                        if (handlestatus == 'add') {      

                            if(typeof  allData != 'undefined'){
                                allData.push(data);
                            }else{
                                allData = [data] ;
                            }
                            $('#boship-edit-detail-table').datagrid('reload',  {data:allData});
                            BJUI.dialog('closeCurrent','boship-detail-{{$id}}');
                        }else if (handlestatus == 'edit') {
                            $.CurrentNavtab.find('#boship-edit-detail-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                            var obj = $.CurrentNavtab.find('#boship-edit-detail-table').data('allData');
                            obj["{{$selectedDatas['gridIndex']}}"] = data;
                            $('#boship-edit-detail-table').datagrid('reload',  {data:obj});
                            BJUI.dialog('closeCurrent','');
                        }

                    }else{
                        console.log('no');
                    }
                });
            }})
        }else{
            $('#boshipout-detail-form').isValid(function(v){
                if (v) {
                    if (handlestatus == 'add') {      

                        if(typeof  allData != 'undefined'){
                            allData.push(data);
                        }else{
                            allData = [data] ;
                        }
                        $('#boship-edit-detail-table').datagrid('reload',  {data:allData});
                        BJUI.dialog('closeCurrent','boship-detail-{{$id}}');
                    }else if (handlestatus == 'edit') {
                        $.CurrentNavtab.find('#boship-edit-detail-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                        var obj = $.CurrentNavtab.find('#boship-edit-detail-table').data('allData');
                        obj["{{$selectedDatas['gridIndex']}}"] = data;
                        $('#boship-edit-detail-table').datagrid('reload',  {data:obj});
                        BJUI.dialog('closeCurrent','');
                    }

                }else{
                    console.log('no');
                }
            });
        }
    }

</script>