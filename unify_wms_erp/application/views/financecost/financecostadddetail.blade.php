<div class="bjui-pageContent">
<form id="financecost-detail-form" action="/financecost/detailsubmit" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <input type="hidden" name="customer_sysno" value="{{$customer_sysno}}">
        <input type="hidden" name="customer_name" value="{{$customer_name}}">
        <input type="hidden" name="contract_sysno" value="{{$contract_sysno}}">
        <input type="hidden" name="contract_no" value="{{$contract_no}}">

        <label class="row-label">费用名称</label>
        <div class="row-input required">
            <input type="hidden" name="costtype" value="{{$costtype}}" data-rule="required">
            <input type="text" name="costname" value="{{$costname}}" readonly data-toggle="findgridbtn" data-options="{
            include: 'costtype:costtype,costname:othercostname,unitprice:costamount,unitname:unitname',
            dialogOptions: {width:'800',height:'500',title:'费用名称',maxable:true,resizable:true,mask:true},
            gridOptions: {
                width:'100%',
                height:'100%',
                tableWidth:'95%',                       
                local: 'local',
                paging: {pageSize:20},
                dataUrl: '/othercost/contractdatail/contract_sysno/'+{{$contract_sysno}},
                columns: [
                    {name:'othercostname', label:'费用名称'},
                    {name:'costamount', label:'费用价格'},
                    {name:'unitname', label:'计量单位'}
                ],
                showLinenumber:true
            },
        }" placeholder="点击查找"></div>
        
        <label class="row-label">单价</label>
        <div class="row-input required">
            <input type="text" id="unitprice" name="unitprice" value="{{$unitprice}}" readonly data-rule="required;number"></div>

        <label class="row-label">计费数量</label>
        <div class="row-input required">
            <input type="text" id="costqty" name="costqty" value="{{$costqty}}" onKeyUp="return totalcount();" onblur="return totalcount();" data-rule="required;number;not0">
        </div>

        <label class="row-label">计量方式</label>
        <div class="row-input">
            <input type="text" id="unitname" name="unitname" value="{{$unitname}}" readonly></div>

        <label class="row-label">金额</label>
        <div class="row-input">
            <input type="text" id="totalprice" name="totalprice" value="{{$totalprice}}" onblur="return totalcount();" readonly data-rule="required">
        </div>

        <label class="row-label">入库单号</label>
        <div class="row-input">
            <input type="hidden" name="instock_sysno" value="{{$instock_sysno}}">
            <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
            <input type="hidden" name="goods_quality_sysno" value="{{$goods_quality_sysno}}">
            <input type="hidden" name="goodsnature" value="{{$goodsnature}}">
            <input type="hidden" name="storagebank_sysno" value="{{$storagebank_sysno}}">
            <input type="hidden" name="isstoragetank" value="{{$isstoragetank}}">
            <input type="hidden" name="isexceedfirst" value="{{$isexceedfirst}}">
            <input type="hidden" name="storagetankname" value="{{$storagetankname}}">
            <input type="hidden" name="qualityname" value="{{$qualityname}}">
            
            <input type="text" name="stockinno" value="{{$stockinno}}" readonly data-toggle="findgridbtn" data-options="{
            include: 'instock_sysno:sysno,stockinno:stockinno,contract_sysno:contract_sysno,goods_sysno:goods_sysno,goodsname:goodsname,customer_name:customername,customer_sysno:customer_sysno,isstoragetank:isstoragetank,isexceedfirst:isexceedfirst,goods_quality_sysno:goods_quality_sysno,goodsnature:goodsnature,storagebank_sysno:storagebank_sysno,storagetankname:storagetankname,qualityname:qualityname,shipname:shipname,stockindate:stockindate,instockqty:instockqty',
            dialogOptions: {width:'800',height:'500',title:'费用单明细',maxable:true,resizable:true,mask:true},
            gridOptions: {
                width:'100%',
                height:'100%',
                tableWidth:'95%',                       
                local: 'local',
                paging: {pageSize:20},
                dataUrl: '/financecost/othercostdetail/cid/{{$customer_sysno}}/contract_sysno/{{$contract_sysno}}',
                columns: [
                    <!-- {name:'sysno', label:'入库id'}, -->
                    {name:'stockinno', label:'入库单号'},
                    {name:'stockindate', label:'入库日期'},
                    {name:'goodsname', label:'货品名称'},
                    {name:'shipname', label:'船名'}
                ],
                showLinenumber:true
            },
        }" placeholder="点击查找"></div>

        <label class="row-label">货品名称</label>
        <div class="row-input">
            <input type="text" name="goodsname" value="{{$goodsname}}" readonly >
        </div>

        <label class="row-label">进货日期</label>
        <div class="row-input">
            <input type="text" name="stockindate" value="{{$stockindate}}" readonly >
        </div>

        <label class="row-label">进货数量</label>
        <div class="row-input">
            <input type="text" name="instockqty" value="{{$instockqty}}" readonly >
        </div>

        <label class="row-label">船名</label>
        <div class="row-input">
            <input type="text" name="shipname" value="{{$shipname}}" readonly >
        </div>

        <label class="row-label"></label>
        <div class="row-input">
        </div>

        <label class="row-label">备注:</label>
        &nbsp;&nbsp;
        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo}}</textarea>

    </div>

</form>
    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="adddetailsubmit()">保存</button></li>
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

        $('#financecost-detail-form').isValid(function(v){
            if (v) {
                var data  = $("#financecost-detail-form").serializeArray();

                var json = serializeObject(data);


                var allData  = $("#financecost-detail-table").data('allData');

                if(typeof  allData != 'undefined'){
                    allData.push(json);
                }else{
                    allData = [json] ;
                }

                $('#financecost-detail-table').datagrid('reload',  {data:allData});
                var test = JSON.stringify(json); 
                // console.log(test);
                BJUI.dialog('closeCurrent','cost-receipt-{{$id}}');
            }else{
                console.log('no');
            }

        });
    }

    function serializeObject(a)
    {
        var o = {};
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    
</script>