<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="receivableform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" id="id" name="id" value="{{$id}}">
            <input type="hidden" name="receivablestatus" id="receivablestatus1" value="{{$receivablestatus}}">
            <!--base message start-->
            <fieldset>
                <legend>收款单信息</legend>
                <div class="bjui-row col-3">

                    <label class="row-label">收款单编号</label>
                    <div class="row-input">
                            <input type="text" name="receivablen" value="@if($receivableno){{$receivableno}} @else {{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">收款单日期</label>
                    <div class="row-input required">
                        <input type="text" name="receivabledate" value="@if($receivabledate){{date('Y-m-d',strtotime($receivabledate))}}@else{{date('Y-m-d')}}@endif" data-toggle="datepicker" data-rule="required;date" readonly  @if($receivablestatus>2 && $receivablestatus!=5) disabled @endif  >
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        @if($receivablestatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($receivablestatus == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($receivablestatus == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($receivablestatus == 5)
                            <input name="statusname" value="退回" readonly>
                        @elseif($receivablestatus ==6 )
                            <input name="statusname" value="作废" readonly>
                        @elseif($receivablestatus ==7 )
                            <input name="statusname" value="已核销" readonly>                            
                        @else
                            <input name="statusname" value="新增" readonly>
                    @endif
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select data-nextselect="#base_company_sysno" data-refurl="/receivable/compayJson/id/{value}" name="customer_sysno" id="receivable_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" @if($receivablestatus>2 && $receivablestatus!=5) disabled="true" @endif >
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="customername" value="{{$customername}}">
                        <input type="hidden" name="customerid" id="customerid" value="">
                    </div>

                    <label class="row-label">开票单位</label>
                    <div class="row-input required">
                        <select name="base_company_sysno" id="base_company_sysno" data-rule="required" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" @if($receivablestatus > 2 && $receivablestatus!=5) disabled @endif >

                            <option value="">请选择</option>
                            <option value="{{$base_company_sysno}}"
                                    @if($base_company_sysno) selected @endif>{{$base_companyname}}</option>
                            {{--<option value="">请选择</option>--}}
                            {{--@foreach($companylist as $item)--}}
                                {{--<option value="{{$item['sysno']}}" @if($item['companyname'] == $base_companyname) selected @endif>{{$item['companyname']}}</option>--}}
                            {{--@endforeach--}}
                        </select>                    
                        <input type="hidden" name="base_companyname" id="base_companyname" value="{{$base_companyname}}">
                    </div>

                    <label class="row-label">结算方式</label>
                    <div class="row-input required">
                        <input type="hidden" name="settlement_name" id="settlement_name" value="{{$base_settlement_sysno}}">
                        <select name="settlement_sysno" id="settlement_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" @if($receivablestatus > 2 && $receivablestatus!=5) disabled @endif >
                            @foreach($settlementlist as $item)
                                <option value="{{$item['sysno']}}"  @if($item['settlementname'] == $settlementname) selected @endif>{{$item['settlementname']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label ">收款金额</label>
                    <div class="row-input required">
                        <input type="text" name="costreceivable" id="costreceivable" data-rule="required;range[0~]" 
                        value="{{$costreceivable}}" @if($receivablestatus>2 && $receivablestatus!=5) readonly @endif >
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
             @if($receivablestatus >=3 && $receivablestatus!=5)
            <div class="remarks">
                <fieldset>
                        <legend>操作</legend>
                        <textarea id="stockshipinmarks" name="stockmarks" data-toggle="autoheight" cols="auto" rows="3" @if($void) placeholder="请在此处填写作废意见" @endif placeholder="请在此处填写审核意见">{{$stockmarks}}</textarea>                  
                </fieldset>
            </div>
            @endif
              <div class="text-center btns-user">
                        @if($receivablestatus < 3 || $receivablestatus==5)
                            <button id="stockshipinsubmit1" type="button" onclick="receivablesubmit(2)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                            <button id="stockshipinsubmit2" type="button" onclick="receivablesubmit(3)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                            @endif
                            @if($receivablestatus ==3 && !$void)
                            <button id="stockshipinsubmit3" type="button" onclick="receivablesubmit(4)" class="btn btn-info btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                            <button id="stockshipinsubmit4" type="button" onclick="receivablesubmit(5)" class="btn btn-danger btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                            @endif
                            @if($void)
                                <button id="poundscarins_void" type="button" class="btn btn-red btn-lg" >作废</button>
                            &nbsp;&nbsp;&nbsp;
                            @endif                              
                            <button  type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
            </div>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
<script src="/static/common/js/custom.js"></script>
<script src="/static/common/js/common.js"></script>

<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'});
//操作记录
addLog($.CurrentNavtab.find('.addTable'),{{$id or 0}}, '15');

</script>

<script type="text/javascript">

    function clickcustomer(){
        var customer_sysno = $("#receivable_customer_sysno").val();
        console.log('222');return false;
    }
    function receivablesubmit(step) {

        if(step==5){
            if($("#stockshipinmarks").val()==''){
                BJUI.alertmsg('warn', '请填写审核意见',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }
        $('#receivablestatus1').val(step);

        $.CurrentNavtab.find('#customername').val($.CurrentNavtab.find('#receivable_customer_sysno option:selected').text());
        $.CurrentNavtab.find('#base_companyname').val($.CurrentNavtab.find('#base_company_sysno option:selected').text());
        $.CurrentNavtab.find('#customerid').val($.CurrentNavtab.find('#receivable_customer_sysno option:selected').attr('value'));
        $.CurrentNavtab.find('#settlement_name').val($.CurrentNavtab.find('#settlement_sysno option:selected').text());
        // console.log($('#base_company_sysno option:selected').text()); return;
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#receivableform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag','navab321');
                BJUI.navtab('closeCurrentTab', '');                
            }
        })
    }

    $('#poundscarins_void').click(function(){
            var id = $('#id').val();
            var stockmarks = $('#stockshipinmarks').val();
            var costreceivable = $('#costreceivable').val();
            var customer_sysno = $('#receivable_customer_sysno option:selected').attr('value');

            if($("#stockshipinmarks").val()==''){
                BJUI.alertmsg('warn', '请填写作废意见',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }            
            BJUI.ajax('doajax',{
            url: '/receivable/recevableVoid/id/'+id+'/stockmarks/'+stockmarks+'/costreceivable/'+costreceivable+'/customer_sysno/'+customer_sysno,
            okCallback: function(json){
                    BJUI.navtab('reloadFlag','navab321');
                    BJUI.navtab('closeCurrentTab','');
            }
        });
    });

</script>