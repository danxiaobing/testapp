<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" class="datagrid-edit-form" action="" method="post"  data-data-type="json">
        <input type="hidden" name="id" value="{{$id}}">

        <div class="bjui-row col-1">
            <label class="row-label">品名:</label>
            <div class="row-input required">
                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                <input type="text" name="goodsname" value="{{$goodsname}}"  readonly
                       data-rule="required" data-toggle="findgrid" data-options="{
                        dialogOptions: {width:'800',height:'500',title:'商品名称',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:20},
                            dataUrl: '/goods/getGoodsandprice',
                            columns: [
                                {name:'goodsname', label:'商品名称'},
                                {name:'unitname',label:'单位名称'},
                                {name:'rate_waste',label:'内控损耗'},
                                {name:'islongterm',label:'长期品种'},
                                {name:'storagetank_categoryname',label:'不能存放的材质'},
                            ],
                            showLinenumber:true
                        },
                    }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">是否启用阶梯价</label>
            <div class="row-input required">
                <select name="isladder" data-toggle="selectpicker" id="contract_isladder" data-rule="required" data-width="100%" data-size="10">
                    <option value="">请选择</option>
                    <option value="0" @if($isladder==0) selected @endif>否</option>
                    <option value="1" @if($isladder==1) selected @endif>是</option>
                </select>
            </div>

            <div id="contract_isladder_div" @if(!isset($isladder)||$isladder==0)style="display: none"@endif>
                <label class="row-label">阶梯内最小吨数</label>
                <div class="row-input required">
                    <input type="text" name="ladderstart" id="ladderstart" value="{{$ladderstart}}" style="width:85%;"><span>&nbsp;&nbsp;吨</span>
                </div>
                <label class="row-label">阶梯内最大吨数</label>
                <div class="row-input">
                    <input type="text" name="ladderend" id="ladderend" value="{{$ladderend}}" style="width:85%;"><span>&nbsp;&nbsp;吨</span>
                </div>
            </div>

            <label class="row-label">外贸卸船</label>
            <div class="row-input">
                <input type="text" name="berthcostforeign" id="berthcostforeign" value="{{$berthcostforeign}}" style="width:80%;"><span>&nbsp;元/吨</span>
            </div>

            <label class="row-label">内贸卸船</label>
            <div class="row-input">
                <input type="text" name="berthcostdomestic" id="berthcostdomestic" value="{{$berthcostdomestic}}" style="width:80%;"><span>&nbsp;元/吨</span>
            </div>

            <label class="row-label">装船</label>
            <div class="row-input">
                <input type="text" name="berthcost" id="berthcost" value="{{$berthcost}}" style="width:80%;"><span>&nbsp;元/吨</span>
            </div>

            <div class="bjui-row col-1">
            <label class="row-label">备注</label>
            <div class="row-input">
                <textarea name="memo">{{$memo}}</textarea>
            </div>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            @if($handlestatus=='edit')
                <button type="button" id="nocontractgoods_edit" class="btn-green" data-icon="save">保存</button>
            @else
                <button type="button" id="nocontractgoods_save" class="btn-green" data-icon="save">保存</button>
            @endif
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>
<script>
    $("#nocontractgoods_save").click(function (){
        $('#treeform').isValid(function(v){
            if(v){
                var data  = $("#treeform").serializeJson();
                var allData  = $.CurrentNavtab.find("#goods-detail-table").data('allData');

                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }
                $.CurrentNavtab.find('#goods-detail-table').datagrid('reload',  {data:allData});

                BJUI.dialog('closeCurrent');
            }else{
                console.log('no');
            }
        })
    })

    $("#nocontractgoods_edit").click(function (){
        $('#treeform').isValid(function(v){
            if(v){
                var data  = $("#treeform").serializeJson();
                $.CurrentNavtab.find('#goods-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                var obj = $.CurrentNavtab.find('#goods-detail-table').data('allData');
                obj["{{$gridIndex}}"] = data;
                $.CurrentNavtab.find('#goods-detail-table').datagrid('reload', {data:obj});
                BJUI.dialog('closeCurrent');
            }
        })
    })


    $('#contract_isladder').change(function(){
        var type = $(this).val();

        if(type==0)
        {
            $('#contract_isladder_div').hide();
            $("#ladderstart").attr("data-rule",'a');
            $("#ladderstart").val('');
            $("#ladderend").val('');
        }else{
            $('#contract_isladder_div').show();
            $("#ladderstart").attr("data-rule",'number required');
        }
    });

</script>