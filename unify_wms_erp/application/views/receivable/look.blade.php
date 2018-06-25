<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form action="{{$action}}" method="POST" class="datagrid-look-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="receivablestatus" value="{{$receivablestatus or ''}}" >
            <!--base message start-->
            <fieldset>
                <legend>收款单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">收款单编号</label>
                    <div class="row-input">
                            <input type="text" name="receivablen" value="@if($receivableno){{$receivableno}} @else {{系统自动生成}} @endif" readonly>
                        </div>
                    <label class="row-label">收款单日期</label>
                    <div class="row-input required">
                        <input type="text" name="receivabledate" value="@if($receivabledate){{date('Y-m-d',strtotime($receivabledate))}}@else{{date('Y-m-d')}}@endif" data-toggle="datepicker" data-rule="required;date"   disabled  >
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" name="stockretankstatus" value="0">
                        @if($receivablestatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($receivablestatus == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($receivablestatus == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($receivablestatus == 5)
                            <input name="statusname" value="已核销" readonly>
                        @elseif($receivablestatus ==6 )
                            <input name="statusname" value="作废" readonly>
                        @else
                            <input name="statusname" value="新增" readonly>
                    @endif
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%"  disabled  >
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" value="{{$customername}}">
                    </div>
                    

                    <label class="row-label">收款单位</label>
                    <div class="row-input required">
                        <select name="base_company_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" disabled  >
                            @foreach($companylist as $item)
                                <option value="{{$item['sysno']}}" @if($item['companyname'] == $base_companyname) selected @endif >{{$item['companyname']}}</option>
                            @endforeach
                        </select>                    
                        <input type="hidden" name="base_companyname" value="" readonly="">
                        <!-- <input type="hidden" name="base_company_sysno" id="base_company_sysno" value="{{$base_companyname}}"> -->
                    </div>

                    <label class="row-label">结算方式</label>
                    <div class="row-input required">
                        <input type="hidden" name="settlement.sysno" value="{{$base_settlement_sysno}}" data-rule="required">
                        <input type="text" name="settlement.settlementname" value="{{$settlementname}}" readonly disabled data-toggle="findgridbtn" data-options="{
                        group: 'settlement',
                        include: 'settlementname:settlementname, sysno :sysno',
                        dialogOptions: {width:'800',height:'500',title:'结算方式',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:20},
                            dataUrl: '/settlement/datail',
                            columns: [
                                {name:'sysno', label:'id'},
                                {name:'settlementname', label:'结算方式名称'}
                            ],
                            showLinenumber:false
                        },
                    }" placeholder="点击查找"></div>

                    <label class="row-label ">收款金额</label>
                    <div class="row-input required">
                        <input type="text" name="costreceivable" data-rule="required" value="{{$costreceivable}}"  disabled  >
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
             <div class="text-center btns-user">
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
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
</script>

<script type="text/javascript">

    //----------------------操作记录 
    // $.CurrentNavtab.find('.hideshow').slideUp();
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '15');

    // function showH() {
    //     $.CurrentNavtab.find('.hideshow').toggle(500);

    // }

    //----------------------操作记录 end


</script>