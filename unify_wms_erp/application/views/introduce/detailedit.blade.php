<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="introduce-detail-form"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json ">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">
                <input type="hidden" id="introduce_detail_handlestatus" name="handlestatus" value="{{$handlestatus or ''}}">
                <input type="hidden" name="goods_quality_sysno" value="{{$selectedDatas['goods_quality_sysno'] or ''}}">
                <!-- 父级提单可拆量-->
                <input type="hidden" id="introduce_detail_tobeqty" name="tobeqty" value="{{$selectedDatas['tobeqty'] or 0}}">
                <!-- 父级明细ID -->
                <input type="hidden" name="introductiondetail_sysno" value="{{$selectedDatas['introductiondetail_sysno'] or 0}}">
                <!-- 库存来源 -->
                <input type="hidden" id="introduce_detail_stocktype" name="stocktype" value="{{$selectedDatas['stocktype'] or 0}}">
                <!-- 报关量 -->
                <input type="hidden" id="introduce_detail_release_num" name="release_num" value="{{$selectedDatas['release_num'] or 0}}">
                <!-- 提单类型 -->
                <input type="hidden" name="introductiontype" value="{{$selectedDatas['introductiontype'] or 0}}">
                <!-- 首期到期日 -->
                <input type="hidden" name="firstdate" value="{{$selectedDatas['firstdate'] or ''}}">

                <label class="row-label">入库单号</label>
                <div class="row-input required">
                    <!-- 来源单据ID -->
                    <input type="hidden" name="firstfrom_sysno" value="@if($stockin_sysno != '') {{$stockin_sysno}} @elseif($stockin_sysno == '' && isset($selectedDatas['stockin_sysno'])) {{$selectedDatas['stockin_sysno']}} @endif">

                    <!-- 储罐ID -->
                    <input type="hidden" name="storagetank_sysno" value="@if($storagetank_sysno != '') {{$storagetank_sysno}} @elseif($storagetank_sysno == '' && isset($selectedDatas['storagetank_sysno'])) {{$selectedDatas['storagetank_sysno']}} @endif">
                    <!-- 可用数量 -->
                    <input type="hidden" id="introduce_detail_ableqty" name="ableqty" value="@if($ableqty != '') {{$ableqty}} @elseif($ableqty == '' && isset($selectedDatas['ableqty'])) {{$selectedDatas['ableqty']}} @endif">
                    <!-- 库存ID -->
                    <input type="hidden" name="sysno" value="@if($stock_sysno != '') {{$stock_sysno}} @elseif($stock_sysno == '' && isset($selectedDatas['stock_sysno'])) {{$selectedDatas['stock_sysno']}} @endif">

                    <!-- 储罐可用余量 -->
                    <input type="hidden" id='introduce_detail_storagetankableqty' name="storagetankableqty" value="@if($storagetankableqty != '') {{$storagetankableqty}} @elseif($storagetankableqty == '' && isset($selectedDatas['storagetankableqty'])) {{$selectedDatas['storagetankableqty']}} @endif">
                    <!-- 储罐货品ID -->
                    <input type="hidden" id="introduce_detail_storagetankgoods_sysno" name="storagetankgoods_sysno" value="@if($storagetankgoods_sysno != '') {{$storagetankgoods_sysno}} @elseif($storagetankgoods_sysno == '' && isset($selectedDatas['storagetankgoods_sysno'])) {{$selectedDatas['storagetankgoods_sysno']}} @endif">
                    <!-- 入库货品ID -->
                    <input type="hidden" id="introduce_detail_goods_sysno" name="goods_sysno" value="@if($goods_sysno != '') {{$goods_sysno}} @elseif($goods_sysno == '' && isset($selectedDatas['goods_sysno'])) {{$selectedDatas['goods_sysno']}} @endif">
                    
                    
                    
                    <input type="text" name="firstfrom_no" value="{{$selectedDatas['stockin_no'] or '' }}" readonly data-rule="required" data-toggle="findgridbtn" data-options="{
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
                                {name:'shipname', label:'船名'},
                                {name:'instockqty', label:'入库数量'},
                                {name:'introduceqty', label:'提单总量'},
                                {name:'stockqty', label:'余量'},
                                {name:'clockqty', label:'锁定数量'},
                                {name:'ableqty', label:'可用数量'},
                                {name:'release_num', label:'报关数量'}
                                
                            ],
                            fullGrid:true,
                            showLinenumber:false
                        }
                    }" placeholder="点放大镜按钮查找">
                </div>

                <label class="row-label">品名</label>
                <div class="row-input">
                    <input type="text" name="goodsname" value="{{$selectedDatas['goodsname'] or '' }}" readonly>
                </div>

                <label class="row-label">规格</label>
                <div class="row-input">
                    <input type="text" name="goodsqualityname" data-width="100%" value="{{$selectedDatas['goodsqualityname'] or '' }}" readonly>
                </div>

                <label class="row-label">货物性质</label>
                <div class="row-input">
                    <select name="goodsnature" id='introduce_detail_goodsnature' data-size="5" data-toggle="selectpicker" data-width="100%" disabled="disabled">
                        <option value="">请选择</option>
                        <option value="1" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '1') {{selected}} @endif>保税</option>
                        <option value="2" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '2') {{selected}} @endif>外贸</option>
                        <option value="3" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '3') {{selected}} @endif>内贸转出口</option>
                        <option value="4" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '4') {{selected}} @endif>内贸内销</option>
                    </select>
                </div>
                
                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text" name="shipname" value="{{$selectedDatas['shipname'] or '' }}" readonly>
                </div>

                <label class="row-label">储罐编号</label>
                <!-- <div class="row-input required">
                    <input readonly type="text" id='introduce_detail_storagetankname' name="storagetankname" value="{{$selectedDatas['storagetankname'] or '' }}"
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
                    <input readonly type="text" id='introduce_detail_storagetankname' name="storagetankname" value="{{$selectedDatas['storagetankname'] or '' }}">
                </div>

                <label class="row-label">计量单位</label>
                <div class="row-input">
                    <input type="text" name="unitname" value="{{$selectedDatas['unitname'] or '' }}" readonly>
                </div>

                <label class="row-label">入库数量</label>
                <div class="row-input">
                    <input type="text" name="instockqty" value="{{$selectedDatas['instockqty'] or ''}}" readonly>
                </div>

                <label class="row-label">提单总量</label>
                <div class="row-input">
                    <input type="text" name="introduceqty" value="{{$selectedDatas['introduceqty'] or ''}}" readonly>
                </div>

                <label class="row-label">提单数量</label>
                <div class="row-input required">
                    <input type="text" id="introduce_detail_takegoodsnum" name="takegoodsnum"  value="{{$selectedDatas['takegoodsnum'] or '' }}" data-rule="required number range[0~]">
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
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveIntroduceDetail()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>


<script type="text/javascript">

    function saveIntroduceDetail() {

        $('#introduce_detail_goodsnature').removeAttr('disabled');
        $('#introduce_detail_goodsnature').selectpicker('refresh');
        $('#introduce_detail_goodsnature').selectpicker('render');

        var handlestatus=$('#introduce_detail_handlestatus').val();
        var takegoodsnum = parseFloat($("#introduce_detail_takegoodsnum").val());
        var ableqty = parseFloat($("#introduce_detail_ableqty").val());
        var storagetankableqty = parseFloat($("#introduce_detail_storagetankableqty").val());
        var storagetankname = $("#introduce_detail_storagetankname").val();
        var storagetankgoods_sysno = parseFloat($("#introduce_detail_storagetankgoods_sysno").val());
        var goods_sysno = parseFloat($("#introduce_detail_goods_sysno").val());
        var tobeqty = parseFloat($("#introduce_detail_tobeqty").val());
        if(tobeqty != 0 && takegoodsnum > tobeqty){
            BJUI.alertmsg('warn','提货量不能超过上家可转量',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(!storagetankname){
            BJUI.alertmsg('warn','请先选择储罐',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(storagetankgoods_sysno != goods_sysno && storagetankgoods_sysno){
            BJUI.alertmsg('warn','要出库货品和所选储罐中的货品不一致',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        
        if(takegoodsnum>ableqty){
            BJUI.alertmsg('warn','您的出库数量不能大于该库存可用数量',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        
        var data  = $("#introduce-detail-form").serializeJson();
        var allData  = $("#introduce-edit-detail-table").data('allData');

        data.stock_sysno=data.sysno;
        data.stockin_no = data.firstfrom_no;
        data.stockin_sysno = data.firstfrom_sysno;

        var stocktype = $('#introduce_detail_stocktype').val();
        var release_num = parseFloat($('#introduce_detail_release_num').val());
        var goodsnature = $('#introduce_detail_goodsnature option:selected').val();
        if(stocktype == 1 && takegoodsnum > release_num && goodsnature != 4){
            BJUI.alertmsg('warn','报关量不足',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(takegoodsnum > storagetankableqty){
            BJUI.alertmsg('confirm','选择的'+ storagetankname +'罐库存不够,是否继续', {okCall:function() {
                $('#introduce-detail-form').isValid(function(v){
                    if (v) {
                        if (handlestatus == 'add') {      

                            if(typeof  allData != 'undefined'){
                                allData.push(data);
                            }else{
                                allData = [data] ;
                            }
                            $('#introduce-edit-detail-table').datagrid('reload',  {data:allData});
                            BJUI.dialog('closeCurrent','boship-detail-{{$id}}');
                        }else if (handlestatus == 'edit') {
                            $.CurrentNavtab.find('#introduce-edit-detail-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                            var obj = $.CurrentNavtab.find('#introduce-edit-detail-table').data('allData');
                            obj["{{$selectedDatas['gridIndex']}}"] = data;
                            $('#introduce-edit-detail-table').datagrid('reload',  {data:obj});
                            BJUI.dialog('closeCurrent','');
                        }

                    }else{
                        console.log('no');
                    }
                });
            }})
        }else{
            $('#introduce-detail-form').isValid(function(v){
                if (v) {
                    if (handlestatus == 'add') {      

                        if(typeof  allData != 'undefined'){
                            allData.push(data);
                        }else{
                            allData = [data] ;
                        }
                        $('#introduce-edit-detail-table').datagrid('reload',  {data:allData});
                        BJUI.dialog('closeCurrent','boship-detail-{{$id}}');
                    }else if (handlestatus == 'edit') {
                        $.CurrentNavtab.find('#introduce-edit-detail-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                        var obj = $.CurrentNavtab.find('#introduce-edit-detail-table').data('allData');
                        obj["{{$selectedDatas['gridIndex']}}"] = data;
                        $('#introduce-edit-detail-table').datagrid('reload',  {data:obj});
                        BJUI.dialog('closeCurrent','');
                    }

                }else{
                    console.log('no');
                }
            });
        }

    }

</script>