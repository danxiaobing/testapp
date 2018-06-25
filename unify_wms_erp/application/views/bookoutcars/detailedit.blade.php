<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="bookoutcars-pound-form" class="datagrid-edit-form" data-toggle="validate"
              data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">

            <div class="bjui-row col-1">

                <label class="row-label">出库单号</label>

                <div class="row-input">
                    <input type="text" name="stockoutno" value="{{$stockoutno}}" readonly>
                </div>

                <label class="row-label">客户名称</label>

                <div class="row-input">
                    <input type="text" name="customername" value="{{$customername}}" readonly>
                </div>

                <label class="row-label">提货单号</label>

                <div class="row-input">
                    <input type="text" name="takegoodsno" value="{{$takegoodsno}}" readonly>
                </div>

                <label class="row-label">提货公司</label>

                <div class="row-input">
                    <input type="text" name="takegoodscompany" value="{{$takegoodscompany}}" readonly>
                </div>

                <label class="row-label">计量单位</label>

                <div class="row-input">
                    <input type="text" name="unitname" value="KG" readonly>
                </div>

                <label class="row-label">提货数量(KG)</label>

                <div class="row-input required">
                    <input type="text" name="realnumber" id="declare_release_num" value="{{intval($realnumber)}}" data-rule="required;number;digits">
                </div>

                <label class="row-label">提货桶数</label>

                <div class="row-input">
                    <input type="text" name="bucketnumber"  value="{{intval($bucketnumber)}}" data-rule="digits" >
                </div>

            </div>
    
            <input type="hidden" name="customer_sysno" value="{{$customer_sysno}}">
            <input type="hidden" name="stockoutdetail_sysno" value="{{$stockoutdetail_sysno}}">
            <input type="hidden" name="goodsname" value="{{$goodsname}}">
            <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
            <input type="hidden" name="stockout_sysno" value="{{$stockout_sysno}}">
            <input type="hidden" name="takeqty" value="{{$takeqty}}">
            <input type="hidden" name="inshipname" value="{{$inshipname}}">
            <input type="hidden" name="cartakeqty" value="{{$cartakeqty}}">
            <input type="hidden" name="storagetank_sysno" value="{{$storagetank_sysno}}">
            <input type="hidden" name="storagetankname" value="{{$storagetankname}}">
            <input type="hidden" name="sysno" value="{{$sysno}}">

        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            <button type="button" class="btn-green" data-icon="save" onclick="saveReceipe()">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
    </ul>
</div>


<script type="text/javascript">
	 function saveReceipe() {

        $('#bookoutcars-pound-form').isValid(function (v) {
            if (v) {
                var data = $("#bookoutcars-pound-form").serializeJson();

                var obj = $('#Bookoutcars-detail-pound-table').data('allData');
                if(parseFloat(data.cartakeqty) < parseFloat(data.realnumber)){
                    BJUI.alertmsg('warn', '提货数量大于车的预计提货数量，请重新填写提货数量');
                    return ;
                }
                if(parseFloat(data.takeqty) < parseFloat(data.realnumber)){
                    BJUI.alertmsg('warn', '提货数量不能超过待提数量');
                    return ;
                }
                // console.log(obj);return;
                if("{{$gridIndex}}"){
                    obj["{{$gridIndex}}"] = data;
                }
                $('#Bookoutcars-detail-pound-table').datagrid('updateRow', "{{$gridIndex}}", data); //更新当前编辑行
                var num = 0;
                $(obj).each(function(i,n){
                    num+=parseFloat( n.realnumber=='' ?0 :n.realnumber);
                });
                $('#takeqty').val(num);
                BJUI.dialog('closeCurrent', '');

            } else {
                console.log('no');
            }

        });


    }


</script>