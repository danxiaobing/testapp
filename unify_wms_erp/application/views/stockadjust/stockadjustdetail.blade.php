<div class="bjui-pageContent">
    <form id="stockadjust-detail-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <label class="row-label">库存</label>
        <div class="row-input required">
            <input type="hidden" name="stockinno" value="{{$stockinno}}" >
            <input type="text" name="sysno" value="{{$stock_sysno}}" readonly data-rule="required" data-toggle="findgrid" data-options="{
            dialogOptions: {width:'800',height:'500',title:'库存',maxable:true,resizable:true,mask:true},
            gridOptions: {
                tableWidth:'90%',                       
                local: 'local',
                paging: {pageSize:20},
                postData: {customer_sysno:{{$customer_sysno}},goods_sysno:{{$goods_sysno}}},
                dataUrl: '/stockadjust/customerstocklistJson',
                columns: [
                    {name:'sysno', label:'id'},
                    {name:'stockinno', label:'单号'},
                    {name:'shipname', label:'船名'},
                    {name:'qualityname', label:'货品规格'},
                    {name:'goodsnature', label:'货物性质',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }},
                    {name:'instockqty', label:'入库数量'},
                    {name:'stockqty', label:'结存量'}
                ],
                showLinenumber:false,
                fullGrid:true
            },
        }" placeholder="点放大镜按钮查找"></div>

        <label class="row-label">船名</label>
        <div class="row-input">
            <input type="text" name="shipname" value="{{$shipname}}" readonly >
        </div>

        <label class="row-label">货品规格</label>
        <div class="row-input">
            <input type="text" name="qualityname" value="{{$qualityname}}" readonly >
        </div>

        <label class="row-label">货物性质</label>
        <div class="row-input required">
            <select id="ajustgoodsnature" name="goodsnature" data-size="5" data-toggle="selectpicker" disabled data-rule="required" data-width="100%">
                <option>请选择</option>
                <option value="1" @if($goodsnature == 1) selected @endif>保税</option>
                <option value="2" @if($goodsnature == 2) selected @endif>外贸</option>
                <option value="3" @if($goodsnature == 3) selected @endif>内贸转出口</option>
                <option value="4" @if($goodsnature == 4) selected @endif>内贸内销</option>
            </select>
        </div>

        <label class="row-label">入库数量</label>
        <div class="row-input required">
            <input type="text" name="instockqty" value="{{$instockqty}}" readonly data-rule="required;number;range[0~]" style="width: 80%">
            <span>（吨）</span>
        </div>

        <label class="row-label">结存量</label>
        <div class="row-input required">
            <input type="text" name="stockqty" value="{{$stockqty}}" readonly data-rule="required;number;" style="width: 80%">
            <span>（吨）</span>
        </div>

        <label class="row-label">备注:</label>
        <div class="row-input">
            <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo}}</textarea>
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
    function saveBocarDetail() {
        var handlestatus = $("#handlestatus").html();
        $("#ajustgoodsnature").removeAttr("disabled");

        $('#stockadjust-detail-form').isValid(function(v){

            if (v) {
                var data  = $("#stockadjust-detail-form").serializeJson();
                var allData  = $("#stockadjust-detail-table").data('allData');
                data.stock_sysno = data.sysno;

                if (handlestatus == 'add') {
                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }
                    $('#stockadjust-detail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent');
                }else if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#stockadjust-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                    var obj = $.CurrentNavtab.find('#stockadjust-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#stockadjust-detail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }

            }else{
                console.log('no');
            }
        });
    }
</script>