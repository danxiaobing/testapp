<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="writeoff_form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="writeoff_invoice_detail" id="writeoff_invoice_detail" value="">
            <!--base message start-->
            <fieldset>
                <legend>核销单信息</legend>
                <br><br>

                <div class="bjui-row col-3">

                    <label class="row-label">核销单编号</label>

                    <div class="row-input">
                        <input type="text" name="writeoffno"
                               value="@if($writeoffno){{$writeoffno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">核销日期</label>

                    <div class="row-input required">
                        <input type="text" name="writeoffdate" id="writeoffdate"
                               value="@if($writeoffdate){{date('Y-m-d',strtotime($writeoffdate))}}@else{{date('Y-m-d')}}@endif"
                               data-toggle="datepicker" data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>

                    <div class="row-input required">
                        @if($id)
                            <input type="text" name="writestatusname" style="text-align: center;font-weight:bold;"
                                   value="完成" readonly>
                        @else
                            <input type="text" name="writestatusname" style="text-align: center;font-weight:bold;"
                                   value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">开票抬头</label>

                    <div class="row-input required">
                        <select name="wo_customer_sysno" id="wo_customer_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $wo_customer_sysno) selected @endif>{{$item['companyname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="wo_customer_name" id="wo_customer_name"
                               value="">
                    </div>

                    <label class="row-label">{{--核销金额--}}</label>
                    <div class="row-input">
                        <input type="hidden" name="writeoffcost" id="writeoffcost" readonly>
                    </div>
                </div>
                <br><br>
            </fieldset>
            <!--base message end-->
            <br>
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>开票通知信息</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="writeoff-invoice-table" data-toggle="datagrid"
                               data-options="{
                        height:'100%',
                        filterThead:false,
                        showToolbar: true,
                        toolbarCustom:$.CurrentNavtab.find('#writeoff_invoice_div'),
                        local: 'local',
                        dataUrl: '/bookshipin/adddetailJson/id/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        fullGrid:true,
                        showTfoot:true,
                        linenumberAll: true
                    }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'customer_name',align:'center'}">客户</th>
                                <th data-options="{name:'invoiceno',align:'center'}">开票通知单号</th>
                                <th data-options="{name:'invoicedate',align:'center'}">通知日期</th>
                                <th data-options="{name:'costinvoice',calc:'sum',align:'center'}">开票通知金额</th>
                                <th data-options="{name:'unreceivablecost',calc:'sum',align:'center'}">未收款金额</th>
                                <th data-options="{name:'hxcost',calc:'sum',align:'center'}">本次核销金额</th>
                                <th data-options="{name:'receivablecost',align:'center',hide:true}">已收款总金额</th>
                                <th data-options="{name:'writeoffcost',align:'center',hide:true}">已核销总金额</th>
                                <th data-options="{name:'remaincost',align:'center',hide:true}">余量</th>
                                <th data-options="{name:'sysno',align:'center',hide:true}">编号</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>
            <!--project end-->
            <br>
            <br><br>
            <div class="text-center btns-user">
                <button type="button" onclick="destroy()" class="btn btn-green btn-lg">立即核销</button>
                <button onclick="showRecords()" class="btn btn-gray btn-lg" type="button">操作记录</button>
            </div>
            <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable"></div>
                </fieldset>
            </div>
            <br><br><br><br><br><br><br><br>
        </form>
    </div>
</div>

<div id="writeoff_invoice_div">
    <button type="button" class="btn btn-green" id="add_writeoff_invoice" onclick="addinvoicedetail()" data-icon="plus">
        添加
    </button>
    <button type="button" class="btn btn-red" id="del_writeoff_invoice" onclick="delinvoicedetail()" data-icon="times">
        删除
    </button>
</div>


<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //----------------------操作记录
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '17');

    //----------------------操作记录 end

    //----------------------开票通知单
    function addinvoicedetail() {
        var wo_customer_sysno = $.CurrentNavtab.find('#wo_customer_sysno').val();
        var checkdata = $.CurrentNavtab.find('#writeoff-invoice-table').data('allData');
        var invoice_sysno = [];
        for (var i = checkdata.length - 1; i >= 0; i--) {
            invoice_sysno.push(checkdata[i].sysno);
        }
        if (wo_customer_sysno.length > 0) {
            BJUI.dialog({
                id: 'writeoff_invoice-detail',
                width: 700,
                height: 350,
                url: '/writeoff/addinvoice/cid/' + wo_customer_sysno+'/invoice_sysno/'+invoice_sysno,
                mask: true,
                loadingmask: true,
                title: '增加开票通知单'
            });
        } else {
            BJUI.alertmsg('warn', '请先选中客户,再添加开票通知单！', {displayPosition: 'middlecenter', displayMode: 'fade'});

        }
    }
    function delinvoicedetail() {
        var checkdata = $.CurrentNavtab.find('#writeoff-invoice-table').data('selectedDatas');
        if (checkdata) {
            var allData = $.CurrentNavtab.find("#writeoff-invoice-table").data('allData');
            for (var i = checkdata.length - 1; i >= 0; i--) {
                allData = allData.remove(checkdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#writeoff-invoice-table').datagrid('reload', {data: allData});
        } else {
            BJUI.alertmsg('warn', '<h4>请选择有效数据！</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
    }
    //--------------------客户姓名
    var name = "请选择";
    $("#wo_customer_sysno").change(function () {
        name = $("#wo_customer_sysno option:selected").text();
        $("#wo_customer_name").val(name);
        $('#writeoff-invoice-table').datagrid('refresh', true);
    });


    //----------------------End
    //----------------------表单提交
    function destroy() {
        var Obj = $.CurrentNavtab.find('#writeoff-invoice-table').data('allData');
        $.CurrentNavtab.find("#writeoff_invoice_detail").val(JSON.stringify(Obj));
        var cost = 0;
        for (var i = 0; i < Obj.length; i++) {
            cost += parseFloat(Obj[i].hxcost);
        }
        $("#writeoffcost").val(cost);
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#writeoff_form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('reloadFlag', 'navab458');
            }
        });
    }
    //----------------------End

</script>
