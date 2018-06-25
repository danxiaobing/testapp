<div class="bjui-pageContent">
    <form id="reback-adddetail-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
                <input type="hidden" name="goods_sysno" value="{{$list['goods_sysno']}}">
                <input type="hidden" name="customer_sysno" value="{{$list['customer_sysno']}}">
                <input type="hidden" name="pounddetail_sysno" value="{{$list['pounddetail_sysno']}}">
                <input type="hidden" name="stockoutdetail_sysno" value="{{$list['stockoutdetail_sysno']}}">
                <input type="hidden" name="stockout_sysno" value="{{$list['stockout_sysno']}}">
                <input type="hidden" name="stocktype" value="{{$list['stocktype']}}">
                <input type="hidden" name="stock_sysno" value="{{$list['stock_sysno']}}">
            <label class="row-label">出库单号</label>
            <div class="row-input required">
                <input type="text" name="stockoutno" id="stockoutno" value="{{$list['stockoutno']}}" readonly>
            </div>


            <label class="row-label">客户:</label>
            <div class="row-input required">
                <input type="text" name="customername" id="customername" value="{{$list['customername']}}" readonly>
            </div>

            <label class="row-label">提单号:</label>
            <div class="row-input required">
                <input type="text" name="takegoodsno" id="takegoodsno" value="{{$list['takegoodsno']}}" readonly>
            </div>

            <label class="row-label">提单公司:</label>
            <div class="row-input required">
                <input type="text" name="takegoodscompany" id="takegoodscompany" value="{{$list['takegoodscompany']}}" readonly>
            </div>

            <label class="row-label">计量单位:</label>
            <div class="row-input required">
                <input type="text" name="unitname" id="unitname" value="{{$list['unitname']}}" readonly>
            </div>

            <label class="row-label">提货数量:</label>
            <div class="row-input required">
                <input type="text" name="realnumber" id="realnumber" value="{{$list['realnumber']}}" readonly>
            </div>

            <label class="row-label">提货桶数:</label>
            <div class="row-input required">
                <input type="text" name="bucketnumber" id="bucketnumber" value="{{$list['bucketnumber']}}" readonly>
            </div>


            <label class="row-label">退回数量</label>
            <div class="row-input required">
                <input type="text"id="rebacknumber" name="rebacknumber" value="{{$rebacknumber}}" @if($mode=='audit') readonly @endif data-rule="required;number;range[0~]">
            </div>

            <label class="row-label">备注:</label>
            <div class="row-input">
                <textarea name="memo" data-toggle="autoheight" @if($mode=='audit') readonly @endif  cols="auto" rows="3">{{$memo}}</textarea>
            </div>

            <input type="hidden" name="inshipname" id="inshipname" value="{{$list['inshipname']}}">

        </div>

    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveRebackDetail()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    function saveRebackDetail() {

        var handlestatus = $("#handlestatus").html();
        $('#reback-adddetail-form').isValid(function(v){
            if (v) {
                var data  = $("#reback-adddetail-form").serializeJson();
                var allData  = $("#reback-detail-table").data('allData');

                console.log(data);
                if(parseFloat(data.rebacknumber)>parseFloat(data.realnumber)){
                    BJUI.alertmsg('warn', '退回数量不能大于提货数量');
                    return;
                }


                if (handlestatus == 'add') {

                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }

                    $('#reback-detail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent');
                }else if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#reback-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                    var obj = $.CurrentNavtab.find('#reback-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#reback-detail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }
            }else{
                console.log('no');
            }
        });
    }
</script>