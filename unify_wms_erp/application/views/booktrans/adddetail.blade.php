<div class="bjui-pageContent">
<form id="{{$prefix}}adddetail" action="/booktrans/detailsubmit" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-1">
        <label class="row-label">库存单号</label>
        <div class="row-input required">
            <input type="hidden" name="goodsnature" value="{{$goodsnature}}">
            <input type="hidden" name="unit_sysno" value="{{$unit_sysno}}">
            <input type="hidden" name="unitname" value="{{$unitname}}">
            <input type="hidden" name="storagetank_sysno" value="{{$storagetank_sysno}}">
            <input type="hidden" name="sysno" value="{{$sysno}}">
        
            <input type="text" id="stockno" name="stockno" value="" readonly data-rule="" data-toggle="findgrid" data-options="{
                dialogOptions: {width:'1200',height:'500',title:'库存详细信息',maxable:true,resizable:true,mask:true},
                gridOptions: {
                width:'100%',
                height:'100%',
                tableWidth:'99.8%', 
                local: 'local',
                paging: {pageSize:20},
                dataUrl: '/booktrans/stockListJson/cid/'+$('#{{$prefix}}sale_customer_sysno').val(),
                columns: [
                   {name:'sysno', label:'id',width:40},
                    {name:'stockno', label:'库存单号'},
                    {name:'stockin_no', label:'来源单号'},
                    {name:'stockindate', label:'入库日期',width:120},
                    {name:'storagetankname', label:'罐号'},
                    {name:'goodsname', label:'品名',width:100},
                    {name:'goodsqualityname', label:'规格',width:100},
                    {name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} },label:'货物性质'},
                    {name:'stockqty', label:'可用数量'},
                     {name:'unitname', label:'单位'},
                ],
                fullGrid:true,
                showLinenumber:false
            },
            empty:false,
        }"placeholder="点放大镜按钮查找">
        
        </div>

        <label class="row-label">来源单号</label>
        <div class="row-input required">
            <input type="text" name="stockin_no" value="" readonly>
        </div>

        <label class="row-label">入库日期</label>
        <div class="row-input required">
            <input type="text" name="stockindate" value="" readonly>
        </div>

        <label class="row-label">罐号</label>
        <div class="row-input required">
            <input type="text" name="storagetankname" value="" readonly>
        </div>

        <label class="row-label">品名</label>
        <div class="row-input required">
            <input type="text" name="goodsname" value="" readonly>
        </div>

        <label class="row-label">规格</label>
        <div class="row-input required">
            <input type="text" name="goodsqualityname" value="" readonly>
        </div>

        <label class="row-label">可用数量</label>
        <div class="row-input required">
            <input type="text" name="stockqty" value="" readonly>
        </div>

        <label class="row-label">转移数量</label>
        <div class="row-input required">
            <input type="text" id="transqty" name="transqty" value="" data-rule="required;number;range[0~]">
        </div>

        <label class="row-label">备注:</label>
        <div class="row-input">
            <textarea name="memo" data-toggle="autoheight" cols="auto" rows="1"></textarea>
        </div>
    </div>

</form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-green" data-icon="save" onclick="savetransDetail()">保存</button></li>
    </ul>
</div>

<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">

    function savetransDetail() {
        var stockno = $("#stockno").val();
        var stockqty = parseFloat($("#stockqty").val());
        var transqty = parseFloat($("#transqty").val());

        if(stockno==''){
            BJUI.alertmsg('info','请先选择库存');
            $("#stockno").focus;
            return false;
        }

        if(transqty>stockqty){
            BJUI.alertmsg('info','您的转移数量不能大于该库存可用数量');
            $("#transqty").focus;
            return false;
        }

        $('#{{$prefix}}adddetail').isValid(function(v){
            if (v) {
                var data  = $("#{{$prefix}}adddetail").serializeJson();
                var allData  = $("#{{$prefix}}detail-table").data('allData');
                data.stock_sysno = data.sysno;
                data.qualityname=data.goodsqualityname;
                data.instockdate = data.stockindate;

                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }

                $('#{{$prefix}}detail-table').datagrid('reload',  {data:allData});
                BJUI.dialog('closeCurrent');
            }else{
                console.log('no');
            }
        });
    }

</script>