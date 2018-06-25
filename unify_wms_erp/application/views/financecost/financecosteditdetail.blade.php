<div class="bjui-pageContent">
<form id="financecost-detail-form" action="/financecost/detailsubmit" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <input type="hidden" id="sysno" name="sysno" value="{{$sysno}}">
        <!-- 客户编号 -->
        <input type="hidden" id="customer_sysno" name="customer_sysno" value="{{$customer_sysno}}">
        <!-- 合同编号 -->
        <input type="hidden" id="contract_sysno" name="contract_sysno" value="{{$contract_sysno}}">
        <label class="row-label">客户</label>
        <div class="row-input ">
            <input type="text" id="customer_name" name="customer_name" value="{{$customer_name}}" readonly ></div>
        
        <label class="row-label">合同类型</label>
        <div class="row-input ">
            <input type="text" id="contracttype" name="contracttype" value="@if($contracttype=='1') 长约 @elseif($contracttype=='2') 短约 @elseif($contracttype=='3') 包罐 @elseif($contracttype=='4') 包罐容 @endif " readonly ></div>

        <label class="row-label">进货日期</label>
        <div class="row-input">
            <input type="text" id="instockdate" name="instockdate" value="{{$instockdate}}" onKeyUp="return totalcount();" readonly onblur="return totalcount();" >
        </div>

        <label class="row-label">船名</label>
        <div class="row-input">
            <input type="text" id="shipname" name="shipname" value="{{$shipname}}" readonly></div>

        <label class="row-label">进货数量</label>
        <div class="row-input">
            <input type="text" id="instockqty" name="instockqty" value="{{$instockqty}}"  readonly >
        </div>

        <label class="row-label">品名</label>
        <div class="row-input">
            <input type="text" name="goodsname" value="{{$goodsname}}" readonly >
        </div>

        <label class="row-label">费用类型</label>
        <div class="row-input">
            <input type="text" name="costname" value="{{$costname}}" readonly >
        </div>

        <label class="row-label">计费数量</label>
        <div class="row-input">
            <input type="text" name="costqty" value="{{$costqty}}" readonly >
        </div>

        <label class="row-label">计费单价</label>
        <div class="row-input">
            <input type="text" name="unitname" value="{{$unitname}}" readonly >
        </div>

        <label class="row-label">计价天数</label>
        <div class="row-input">
            <input type="text" name="costdateend" value="{{$costdateend}}" readonly >
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
        $('#financecost-detail-form').isValid(function(v){
            if (v) {
                var data  =  $("#financecost-detail-form").serializeJson();

                        BJUI.ajax('doajax', {
                            type: 'POST',
                            url: '/financecost/editfinancecost/',
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