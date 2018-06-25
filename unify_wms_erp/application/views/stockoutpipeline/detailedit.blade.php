<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="pipeline-stockout-receipe-form"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">

                <input type="hidden" name="sysno" value="{{$sysno}}">
                <input type="hidden" name="stock_sysno" value="{{$stock_sysno}}">
                <input type="hidden" id='pipeline_receipe_goodssysno' name="goods_sysno" value="{{$goods_sysno}}">
                <input type="hidden" name="goods_quality_sysno" value="{{$goods_quality_sysno}}">
                <input type="hidden" name="storagetank_sysno" value="{{$storagetank_sysno}}">
                <input type ="hidden" name="bookout_detail_sysno" value="{{$bookout_detail_sysno}}">
                <input type="hidden" name="goodsnature" value="{{$goodsnature}}">
                <input type="hidden" id="pipeline_receipe_stockqty" name="stockqty" value="{{$stockqty}}">
                <input type="hidden" name="stockin_sysno" value="{{$stockin_sysno}}">
                <input type="hidden" name="stocktype" value="{{$stocktype}}">
                <input type="hidden" name="instockqty" value="{{$instockqty}}">
                <input type="hidden" name="introduceqty" value="{{$introduceqty}}">
                <!-- 储罐货品ID -->
                <input type="hidden" id="pipeline_receipe_storagetankgoods_sysno" name="storagetankgoods_sysno" value="{{$goods_sysno}}">
                <!-- 储罐可用余量 -->
                <input type="hidden" id='ship_storagetankableqty' name="storagetankableqty" value="{{$storagetankableqty}}">

        
                <label class="row-label">来源单号</label>
                <div class="row-input">
                    <input type="text" name="stockin_no" value="{{$stockin_no}}" readonly>
                </div>

                <label class="row-label">品名</label>
                <div class="row-input">
                    <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                </div>

                <label class="row-label">规格</label>
                <div class="row-input">
                    <input type="text" name="qualityname" value="{{$qualityname}}" readonly>
                </div>

                <label class="row-label">货物性质</label>
                <div class="row-input">
                    <input type="text" name="goodsnaturemark" value="{{$goodsnaturemark}}" readonly>
                </div>

                <label class="row-label">预约提货数量</label>
                <div class="row-input">
                    <input type="text" name="takeqty" value="{{$takeqty}}" readonly>
                </div>

                <label class="row-label">通知提货数量</label>
                <div class="row-input @if($stockoutstatus != 8) required @endif">
                    <input type="text" name="tobeqty" value="{{$tobeqty}}" @if($stockoutstatus == 8) readonly @else data-rule='required' @endif>
                </div>

                <label class="row-label">储罐编号</label>
                <!-- <div class="row-input">
                    <input readonly type="text" name="storagetankname" value="{{$storagetankname}}"
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
                    <input readonly type="text" name="storagetankname" value="{{$storagetankname}}">
                </div>
                @if($stockoutstatus == 8)
                <label class="row-label">罐检数量</label>
                <div class="row-input required">
                    <input type="text" name="bussinesscheckqty" value="{{$bussinesscheckqty}}" data-rule='required;number range[0~]'>
                </div>
                @endif
                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea  name="memo"  data-toggle="autoheight">{{$memo}}</textarea>
                </div>

            </div>
                      
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="button" class="btn-green" data-icon="save" onclick="subOutReceipe()">保存</button></li>
    </ul>
</div>

<script type="text/javascript">
    function subOutReceipe() {

        var stockqty = parseFloat($("#pipeline_receipe_stockqty").val());
        var  beqty = parseFloat($("#beqty").val());
        var goods_sysno = $("#pipeline_receipe_goodssysno").val();
        var storagetankgoods_sysno = $("#pipeline_receipe_storagetankgoods_sysno").val();
        
        if (goods_sysno != storagetankgoods_sysno) {
            BJUI.alertmsg('warn','要出库货品和所选储罐中的货品不一致',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        $('#pipeline-stockout-receipe-form').isValid(function(v){
            if (v) {
                var data  =  $("#pipeline-stockout-receipe-form").serializeJson();
                if(data.bookout_detail_sysno =='')
                        data.bookout_detail_sysno = data.sysno;
                $.CurrentNavtab.find('#pipeline-stockoutedit-receipt-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                var obj = $.CurrentNavtab.find('#pipeline-stockoutedit-receipt-table').data('allData');
                obj["{{$gridIndex}}"] = data;
                $('#ship-stockout-receipt-table').datagrid('reload',  {data:obj});
                BJUI.dialog('closeCurrent','');
            }else{
                console.log('no');
            }
        });

    }

</script>