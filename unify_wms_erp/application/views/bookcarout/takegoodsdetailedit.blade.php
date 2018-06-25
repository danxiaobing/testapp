<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="takegoods-detail-form"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json ">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">
                <input type="hidden" name="storagetank_sysno" value="@if($storagetank_sysno != '') {{$storagetank_sysno}} @elseif($storagetank_sysno == '' && isset($selectedDatas['storagetank_sysno'])) {{$selectedDatas['storagetank_sysno']}} @endif">
                    <!-- 可用数量 -->
                    <input type="hidden" id="takegoods_detail_ableqty" name="ableqty" value="@if($ableqty != '') {{$ableqty}} @elseif(isset($selectedDatas['ableqty'])) {{$selectedDatas['ableqty']}} @endif">
                    <!-- 库存记录ID -->
                    <input type="hidden" name="sysno" value="@if($stock_sysno != '') {{$stock_sysno}} @elseif($stock_sysno == '' && isset($selectedDatas['stock_sysno'])) {{$selectedDatas['stock_sysno']}} @endif">
                    <!-- 第一次入库ID -->
                    <input type="hidden" name="firstfrom_sysno" value="@if($stockin_sysno != '') {{$stockin_sysno}} @elseif($stockin_sysno == '' && isset($selectedDatas['stockin_sysno'])) {{$selectedDatas['stockin_sysno']}} @endif">
                    <!-- 预约明细中预约总量 -->
                    <input type="hidden" name="bookoutqty" value="{{$selectedDatas['bookoutqty'] or 0}}">
                    <!-- 子级预约单预约总量 -->
                    <input type="hidden" id='takegoods_detail_quoteNum' name="quoteNum" value="{{$selectedDatas['quoteNum'] or 0}}">
                    <!-- 货品ID -->
                    <input type="hidden" name="goods_sysno" value="{{$selectedDatas['goods_sysno'] or 0}}">
                    <!-- 父级预约明细ID -->
                    <input type="hidden" name="fbookingout_detail_sysno" value="{{$selectedDatas['fbookingout_detail_sysno'] or 0}}">
                <label class="row-label">入库单号</label>
                <div class="row-input ">
                    <input type="text" id='takegoods_detail_firstfrom_no' name="firstfrom_no" value="{{$selectedDatas['stockin_no'] or ''}}" readonly >
                </div>
                
                <label class="row-label">品名</label>
                <div class="row-input">
                     <input type="text" id='takegoods_detail_goodsname' name="goodsname" value="{{$selectedDatas['goodsname'] or ''}}" readonly>
                </div>

                <label class="row-label">规格</label>
                <div class="row-input required">
                    <select id='takegoods_detail_qualityname' name="qualityname" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required">
                        <option value="">请选择</option>
                        @foreach ($goodsqualitylist as $item)
                        <option value="{{$item['sysno']}}" @if($item['qualityname'] == $selectedDatas['qualityname']) selected @endif>{{$item['qualityname']}}</option>     
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货物性质</label>
                <div class="row-input required">
                    <select id='takegoods_detail_goodsnature' name="goodsnature" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required">
                        <option value="">请选择</option>
                        <option value="1" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '1') {{selected}} @endif>保税</option>
                        <option value="2" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '2') {{selected}} @endif>外贸</option>
                        <option value="3" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '3') {{selected}} @endif>内贸转出口</option>
                        <option value="4" @if(isset($selectedDatas['goodsnature']) && $selectedDatas['goodsnature'] == '4') {{selected}} @endif>内贸内销</option>
                    </select>
                    <!-- <input type="hidden" name="goods_nature" value="{{$selectedDatas['goodsqualityname'] or '' }}" readonly> -->
                </div>

                <label class="row-label">计量单位</label>
                <div class="row-input">
                    <input type="text" name="unitname" value="{{$selectedDatas['unitname'] or ''}}" readonly>
                </div>

                <label class="row-label">储罐编号</label>
                <div class="row-input ">
                    <input readonly type="text" id='bocarout_detail_storagetankname' name="storagetankname" value="{{$selectedDatas['storagetankname'] or '' }}" data-toggle="findgrid" data-options="{
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
                </div>

                <label class="row-label">入库数量</label>
                <div class="row-input">
                    <input type="text" id="takegoods_detail_instockqty" name="instockqty" value="{{$selectedDatas['instockqty'] or ''}}" readonly="">
                </div>

                <label class="row-label">源单通知数量</label>
                <div class="row-input required">
                    <input type="text" id="takegoods_detail_sourcenum" name="sourcenum" value="{{$selectedDatas['sourcenum'] or ''}}" readonly>
                </div>

                <label class="row-label">提单量</label>
                <div class="row-input required">
                    <input type="text" id="takegoods_detail_takegoodsnum" name="takegoodsnum" value="{{$selectedDatas['takegoodsnum'] or ''}}" data-rule="required;number  range[0~];">
                </div>
                
                <label class="row-label">结存量</label>
                <div class="row-input">
                    <input type="text" id="takegoods_detail_untakegoodsnum" name="untakegoodsnum" value="{{$selectedDatas['untakegoodsnum'] or ''}}" readonly>
                </div>

                <label class="row-label">实提数量</label>
                <div class="row-input">
                    <input type="text" id="takegoods_detail_takegoodsqty" name="takegoodsqty" value="{{$selectedDatas['takegoodsqty'] or 0}}" readonly>
                </div>
                
                <label class="row-label"></label>
                <div class="row-input">
                    
                </div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea  name="memo"  data-toggle="autoheight" cols="auto" rows="3"></textarea>
                </div>

            </div>

        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveBocarDetail()">保存</button></li>
        <li id="handlestatus" style="display: none">{{$handlestatus or ''}}</li>
    </ul>
</div>


<script type="text/javascript">

    $('#takegoods_detail_takegoodsnum,#takegoods_detail_untakegoodsnum').blur(function() {
        var takegoodsnum = $('#takegoods_detail_takegoodsnum').val();
        if(takegoodsnum){
            $('#takegoods_detail_untakegoodsnum').val(takegoodsnum);
        }
        
    })
    function saveBocarDetail() {
        var sourcenum = parseFloat($("#takegoods_detail_sourcenum").val());
        var takegoodsnum = parseFloat($("#takegoods_detail_takegoodsnum").val());
        var quoteNum = parseFloat($("#takegoods_detail_quoteNum").val());
        var handlestatus = $("#handlestatus").html();

        if(takegoodsnum>sourcenum){
            BJUI.alertmsg('warn','提货数量不能大于源单通知数量',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if((takegoodsnum + quoteNum )>sourcenum){
            BJUI.alertmsg('warn','总提货量超出了源单通知数量',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var data  = $("#takegoods-detail-form").serializeJson();
        var allData  = $("#bocar-editdetail-table").data('allData');

        data.stock_sysno = data.sysno;
        data.stockin_no = data.firstfrom_no;
        data.stockin_sysno = data.firstfrom_sysno;
        var goods_quality_name = $("#takegoods_detail_qualityname option:selected").text();
        data.qualityname = goods_quality_name;  
        // var goodsnature = $("#takegoods_detail_goodsnature option:selected").val();
        // data.qualityname = goodsnature;
        

        $('#takegoods-detail-form').isValid(function(v){
            if (v) {
                    
                if (handlestatus == 'add') {

                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }

                    $('#bocar-editdetail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent', 'bocar-detail-{{$id}}');
                }
                if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#bocar-editdetail-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                    var obj = $.CurrentNavtab.find('#bocar-editdetail-table').data('allData');
                    obj["{{$selectedDatas['gridIndex']}}"] = data;
                    $('#bocar-editdetail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }

                    
            }else{
                console.log('no');
            }

            });
        

    }


</script>