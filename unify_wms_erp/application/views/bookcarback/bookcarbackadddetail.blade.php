<div class="bjui-pageContent">
    <form id="bookcarin-detail-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
            <input type="hidden" name="sout.stock_sysno" value="{{$stock_sysno}}">
            <label class="row-label">出库单号</label>
            <div class="row-input required">
                <input type="text" name="sout.stockoutno" value="{{$stockoutno}}" id="stockoutno" readonly data-rule="required" data-toggle="findgrid" data-options="{
                group: 'sout',
                include:'stockout_sysno:stockout_sysno,goodsname:goodsname,goods_sysno:goods_sysno,goodsqualityname:goodsqualityname,goods_quality_sysno:goods_quality_sysno,goodsnature_name:goodsnature_name,goodsnature:goodsnature,tobeqty:tobeqty,stockoutno:stockoutno,stock_sysno:stock_sysno',
                dialogOptions: {width:'800',height:'500',title:'货品资料',maxable:true,resizable:true,mask:true},
                gridOptions: {
                    tableWidth:'90%',
                    local: 'local',
                    paging: {pageSize:12},
                    postData: {customer_sysno:{{$customer_sysno}} },
                    dataUrl: '/bookcarback/stockoutinglistJson',
                    columns: [
                        {name:'stockout_sysno', label:'id'},
                        {name:'stockoutno', label:'出库单号'},
                        {name:'firstfrom_no', label:'入库单号'},
                        {name:'goodsname', label:'货品名称'},
                        {name:'customername', label:'客户'},
                        {name:'tobeqty', label:'提单量'},
                    ],
                    showLinenumber:false,
                    fullGrid:true
                },
        }" placeholder="点放大镜按钮查找"></div>

            <label class="row-label">品名</label>
            <div class="row-input required">
                <input type="text" name="sout.goodsname" id="goodsname" value="{{$goodsname}}" readonly>
                <input type="hidden" name="sout.goods_sysno" id="goods_sysno" value="{{$goods_sysno}}" readonly>
            </div>

            <label class="row-label">规格</label>
            <div class="row-input required">
                <input type="text" name="sout.goodsqualityname" id="goodsqualityname" value="{{$qualityname}}" readonly>
                <input type="hidden" name="sout.goods_quality_sysno" id="goods_quality_sysno" value="{{$goods_quality_sysno}}">
            </div>

            <label class="row-label">货物性质</label>
            <div class="row-input required">
                <input type="text" name="sout.goodsnature_name" id="goodsnature_name" value="{{$goodsnature_name}}" readonly>
                <input type="hidden" name="sout.goodsnature" id="goodsnature" value="{{$goodsnature}}">
            </div>

            <label class="row-label">计量单位</label>
            <div class="row-input required">
                <input type="text" name="unitname" value="吨" readonly>
            </div>

            <label class="row-label">提单量</label>
            <div class="row-input required">
                <input type="text" name="sout.tobeqty" value="{{$takegoodsnum}}" readonly>
            </div>


            <label class="row-label">退回数量</label>
            <div class="row-input required">
                <input type="text" name="bookinginqty" value="{{$bookinginqty}}" @if($mode=='sure') readonly @endif data-rule="required;number;range[0~]">
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
        $('#bookcarin-detail-form').isValid(function(v){
            if (v) {
                var data  = $("#bookcarin-detail-form").serializeJson();
                var allData  = $("#bookcarback-detail-table").data('allData');
                data['goods_sysno'] = data['sout.goods_sysno'];
                data['stockoutno'] = data['sout.stockoutno'];
                data['goodsname'] = data['sout.goodsname'];
                data['goods_sysno'] = data['sout.goods_sysno'];
                data['qualityname'] = data['sout.goodsqualityname'];
                data['goods_quality_sysno'] = data['sout.goods_quality_sysno'];
                data['goodsnature'] = data['sout.goodsnature'];
                data['takegoodsnum'] = data['sout.tobeqty'];
                data['goodsnature_name'] = data['sout.goodsnature_name'];
                data['stock_sysno'] = data['sout.stock_sysno'];
                console.log(data);

                if (handlestatus == 'add') {

                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }

                    $('#bookcarback-detail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent');
                }else if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#bookcarback-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                    var obj = $.CurrentNavtab.find('#bookcarback-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#bookcarback-detail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }
            }else{
                console.log('no');
            }
        });
    }
</script>