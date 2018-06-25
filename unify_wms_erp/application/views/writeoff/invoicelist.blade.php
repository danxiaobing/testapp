<div class="bjui-pageContent">
    <form id="writeoff_invoice_detail-form" data-toggle="validate" data-data-type="json">
        <input type="hidden" value="" name="sysno">
        <div class="bjui-row col-2">
            <label class="row-label">开票通知单</label>
            <div class="row-input required">
                <input type="text" name="invoiceno" id="invoiceno" readonly data-rule="required" data-toggle="findgrid"
                       data-options="{
            include: 'customer_name:customer_name,sysno:sysno,receivablecost:receivablecost,writeoffcost:writeoffcost,remaincostbycu:remaincostbycu,invoiceno:invoiceno,invoicedate:invoicedate,coststartdate:coststartdate,costenddate:costenddate,costinvoice:costinvoice,unreceivablecost:unreceivablecost',
            dialogOptions: {title:'货品资料',width:1000,height:600,maxable:true,resizable:true,mask:true,loadingmask:true},
            gridOptions: {
            tableWidth:'98%',
            height:'100%',
            local: 'local',
            paging: {pageSize:20},
            postData: {wo_customer_sysno:{{$wo_customer_sysno}},invoice_sysno:'{{$invoice_sysno}}',bar_status:4},
            dataUrl: '/writeoff/noticelistJson',
            columns: [
                {name:'sysno',label:'编号',align:'center'},
                {name:'customer_name', label:'客户',align:'center'},
                {name:'invoiceno', label:'开票通知单',align:'center'},
                {name:'invoicedate',label:'通知日期',align:'center'},
                {name:'coststartdate',label:'结算日期开始',align:'center'},
                {name:'costenddate',label:'结束日期开始',align:'center'},
                {name:'costinvoice',label:'开票金额',align:'center'},
                {name:'unreceivablecost',label:'未收款金额',align:'center'},
            ],
                showLinenumber:false
            },
            afterSelect:function(data) {
                 var cost = 0;
                 var Obj = $.CurrentNavtab.find('#writeoff-invoice-table').data('allData');
                 for(var i = 0 ; i<Obj.length;i++){
                    var cost = cost + parseInt(Obj[i].hxcost);
                    if(Obj[i].invoiceno == $('#invoiceno').val()){
                        $('#unreceivablecost').val( parseInt($('#unreceivablecost').val())- parseInt(Obj[i].hxcost) );
                    }
                 }
                 $('#remaincost').val( $('#remaincost').val() - cost ); {{--动态改变用户余额--}}
                               },
                               }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">通知日期</label>
            <div class="row-input required">
                <input name="invoicedate" id="invoicedate" readonly type="text" data-rule="required">
            </div>
            <label class="row-label">结算开始</label>
            <div class="row-input required">
                <input name="coststartdate" id="coststartdate" readonly type="text" data-rule="required">
            </div>
            <label class="row-label">结算结束</label>
            <div class="row-input required">
                <input name="costenddate" id="costenddate" readonly type="text" data-rule="required">
            </div>

            <label class="row-label">开票金额</label>
            <div class="row-input required">
                <input name="costinvoice" id="costinvoice" readonly type="text" data-rule="required">
            </div>
            <label class="row-label">未收款金额</label>
            <div class="row-input">
                <input name="unreceivablecost" id="unreceivablecost" readonly type="text">
            </div>
            <label class="row-label">核销金额</label>
            <div class="row-input required">
                <input name="hxcost" type="text" id="hxcost" data-rule="required;range(0~);not0;">
            </div>
            <label class="row-label">客户余额</label>
            <div class="row-input required">
                <input name="remaincostbycu" readonly id="remaincost" type="text"> {{--余量--}}
            </div>
            <input name="receivablecost" type="hidden">{{--已收款总金额--}}
            <input name="writeoffcost" type="hidden">{{--已核销总金额--}}
            <input name="customer_name" type="hidden">
        </div>

    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            <button type="button" onclick="formtj()" class="btn-green" data-icon="save">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
    </ul>
</div>

<script type="text/javascript">
    function formtj() {
        var coststartdate = $("#coststartdate").val();                      //结算开始
        var costenddate = $("#costenddate").val();                          //结算结束
        var costinvoice = parseFloat($("#costinvoice").val());                //开票金额
        var unreceivablecost = parseFloat($("#unreceivablecost").val());      //未收款金额
        var hxcost = parseFloat($("#hxcost").val());                          //本次核销金额
        var remaincost = parseFloat($("#remaincost").val());                  //用户金额余量
        var flag = '';
        if (hxcost > remaincost) {
            BJUI.alertmsg('warn', '客户可用余额不足！');
            flag = 'false';
        } else if (hxcost > unreceivablecost) {
            BJUI.alertmsg('warn', '本次核销金额不能大于未收款金额！');
            flag = 'false';
        }
        if (flag == 'false') {
            return false;
        } else {
            $('#writeoff_invoice_detail-form').isValid(function (v) {
                if (v) {
                    remaincost = remaincost - hxcost;
                    $("#remaincost").val(remaincost);
                    var data = $("#writeoff_invoice_detail-form").serializeJson();

                    var allData = $("#writeoff-invoice-table").data('allData');

                    if (typeof  allData != 'undefined') {
                        allData.push(data);
                    } else {
                        allData = [data];
                    }

                    $('#writeoff-invoice-table').datagrid('reload', {data: allData});
                    BJUI.dialog('closeCurrent', 'writeoff_invoice-detaila');
                } else {
                    console.log('error');
                }
            });
        }

    }

</script>