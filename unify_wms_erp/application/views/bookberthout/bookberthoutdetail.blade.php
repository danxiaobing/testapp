<div class="bjui-pageContent">
    <form id="bookberthout-detail-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <label class="row-label">品名</label>
        <div class="row-input required">
            <input type="hidden" name="sysno" id="obj_goodsname" value="{{$goods_sysno}}">
            <input type="text" name="goodsname" value="{{$goodsname}}" id="g_goodsname" readonly data-rule="required" data-toggle="findgrid" data-options="{
            dialogOptions: {width:'800',height:'500',title:'货品资料',maxable:true,resizable:true,mask:true},
            gridOptions: {
                tableWidth:'90%',                       
                local: 'local',
                paging: {pageSize:20},
                postData: {customer_sysno:{{$customer_sysno}},contract_sysno:{{$contract_sysno}}},
                dataUrl: '/customer/customergoodslistJson',
                columns: [
                    {name:'sysno', label:'id'},
                    {name:'goodsno', label:'货品编号'},
                    {name:'goodsname', label:'货品名称'}
                ],
                showLinenumber:false,
                fullGrid:true
            },
        }" placeholder="点放大镜按钮查找"></div>

        <label class="row-label">货物性质</label>
        <div class="row-input required">
            <select name="goodsnature" data-size="5" data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') readonly @endif data-rule="required" data-width="100%">
                <option value="">请选择</option>
                <option value="1" @if($goodsnature == 1) selected @endif>保税</option>
                <option value="2" @if($goodsnature == 2) selected @endif>外贸</option>
                <option value="3" @if($goodsnature == 3) selected @endif>内贸转出口</option>
                <option value="4" @if($goodsnature == 4) selected @endif>内贸内销</option>
            </select>
        </div>

        <label class="row-label">预计数量</label>
        <div class="row-input required">
            <input type="text" name="bookingoutqty" value="{{$bookingoutqty}}" @if($mode=='sure') readonly @endif data-rule="required;number;range[0~]" style="width: 80%">
            <span>（吨）</span>
        </div>

        <label class="row-label">船名</label>
        <div class="row-input required">
            <input type="text" name="shipname" value="{{$shipname}}" data-rule="required" data-toggle="findgrid"
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
                                {name:'sysno', label:'id',align:'center'},
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
            <input type="text" name="shipokdate" value="{{$shipokdate}}" @if($mode=='sure') readonly @endif data-rule="required" >
        </div>

        <label class="row-label">备注:</label>
        <div class="row-input">
            <textarea name="memo" data-toggle="autoheight" @if($mode=='sure') readonly @endif  cols="auto" rows="3">{{$memo}}</textarea>
        </div>

    </div>

</form>
    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveBocarDetail()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>

<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    function saveBocarDetail() {
        var handlestatus = $("#handlestatus").html();
        $('#bookberthout-detail-form').isValid(function(v){
            if (v) {
                var data  = $("#bookberthout-detail-form").serializeJson();
                var allData  = $("#bookberthout-detail-table").data('allData');
                data.goods_sysno = data.sysno;

                if (handlestatus == 'add') {
                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }
                    $('#bookberthout-detail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent');
                }else if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#bookberthout-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                    var obj = $.CurrentNavtab.find('#bookberthout-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#bookberthout-detail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }
            }else{
                console.log('no');
            }
        });
    }
</script>