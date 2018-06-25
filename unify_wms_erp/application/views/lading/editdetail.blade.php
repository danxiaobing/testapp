<div class="bjui-pageContent">
<form id="lading-edit-detail-form" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <input type="hidden" id="sysno" name="sysno" value="{{$sysno}}">
        <label class="row-label">费用承担方</label>
        <div class="row-input ">
            <input type="text" id="customer_name" name="customer_name" value="{{$customer_name}}" readonly >
        </div>

        <label class="row-label">超期天数</label>
        <div class="row-input">
            <input type="text" name="costqty" value="" readonly >
        </div>

        <label class="row-label">超期吨数</label>
        <div class="row-input">
            <input type="text" name="costdateend" value="{{$costqty}}" readonly >
        </div>

        <label class="row-label">单价</label>
        <div class="row-input">
            <input type="text" name="unitname" value="{{$unitprice}}" readonly >
        </div>

        <label class="row-label">实际金额</label>
        <div class="row-input">
            <input type="text" name="totalprice" value="{{$totalprice}}" readonly >
        </div>

        <label class="row-label">修改金额</label>
        <div class="row-input required">
            <input type="text" id="oldtotalprice" name="oldtotalprice" value="{{$oldtotalprice}}" data-rule="required;number" >
        </div>

        <label class="row-label"></label>
        <div class="row-input">
        </div>


    </div>

</form>
    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="adddetailsubmit()">确定</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>

<script type="text/javascript">

    function totalcount() {
        if($('#costqty').val()>0 && $('#unitprice').val()>0)
        {
            var total = parseFloat($('#unitprice').val())*parseFloat($('#costqty').val());
            total = Math.round(1000*(total))/1000.0;
        }
        else
        {
            var total = 0;
        }
        
        $('#totalprice').val(total);
    }
    
    function adddetailsubmit() {
        $('#lading-edit-detail-form').isValid(function(v){
            if (v) {
                var data  =  $("#lading-edit-detail-form").serializeJson();
                BJUI.ajax('doajax', {
                    type: 'POST',
                    url: '/lading/editDetailFinancecost/',
                    data: {data: data},
                    okCallback: function (json, options) {
                        $('#editOk').submit();
                        BJUI.dialog('closeCurrent','');
                    }
                });
            }else{
                console.log('no');
            }
        });
    }
</script>