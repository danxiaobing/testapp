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
                        <input type="text" name="writeoffdate" id="writeoffdate" disabled
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
                        <select name="customer_sysno" id="customer_sysno" data-size="5" disabled
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $wo_customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="customername"
                               value="{{$customer_name}}">
                    </div>

                    <label class="row-label">核销金额</label>
                    <div class="row-input">
                        <input type="text" name="writeoffcost" id="writeoffcost" value="{{$writeoffcost}}" readonly>
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
                        showToolbar: false,
                        local: 'local',
                        data:{{$detaillist}},
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        fullGrid:true,
                        showTfoot:true,
                        linenumberAll: true
                    }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'customername',align:'center'}">客户</th>
                                <th data-options="{name:'invoice_no',align:'center'}">开票通知单号</th>
                                <th data-options="{name:'invoicedate',align:'center'}">通知日期</th>
                                <th data-options="{name:'invoicecost',calc:'sum',align:'center'}">开票通知金额</th>
                                <th data-options="{name:'receivablecost',calc:'sum',align:'center'}">未收款金额</th>
                                <th data-options="{name:'writeoffcost',calc:'sum',align:'center'}">本次核销金额</th>
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
                <button onclick="showRecords()" class="btn btn-gray btn-lg" type="button">操作记录</button>
            </div>
            <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable"></div>
                </fieldset>
            </div>
            <br><br>
            <br><br>
            <br><br>
            <br><br>
            <br><br>
            <br><br>
        </form>
    </div>
</div>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //----------------------操作记录
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '17');

    //----------------------操作记录 end

</script>
