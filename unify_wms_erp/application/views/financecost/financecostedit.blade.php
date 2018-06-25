<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="financecostform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json" data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="costtype" name="costtype" value="{{$costtype or 2}}">
            <input type="hidden" id="financecostdetaildata" name="financecostdetaildata" value="">
            <!--base message start-->
            <fieldset>
                <legend>客户信息查询</legend>
                <br>
                <div class="bjui-row col-2">
                    
                    <label class="row-label">客户</label>

                    <div class="row-input required">
                        <select name="customer_sysno" id="cost_customer_sysno"
                                data-nextselect="#cost_contract_sysno"
                                @if($coststatus != 2 && $id !=0) disabled @elseif($mode=='eye'||$mode =='audit') disabled @endif
                                data-refurl="/customer/customercontractJson2/id/{value}" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="cost_customername"
                               value="{{$customer_name}}">
                    </div>

                    <label class="row-label">合同编号</label>

                    <div class="row-input">
                        <select name="contract_sysno" id="cost_contract_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" 
                                data-width="100%" @if($coststatus != 2 && $id !=0) disabled @elseif($mode=='eye'||$mode =='audit') disabled @endif>
                            <option value="">请选择</option>
                            <option value="{{$contract_sysno}}"
                                    @if($contract_sysno) selected @endif>{{$contract_no}}</option>
                        </select>
                        <input type="hidden" name="contract_no" id="cost_contractno" value="{{$contract_no}}">
                    </div>
                    
                </div>
                <br></fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                 <fieldset>
                   <legend>添加杂费</legend>
                    <div class="table-edit">
                            <table class="table table-bordered" id="financecost-detail-table" data-toggle="datagrid" data-options="{
                                        tableWidth:'100%',
                                        height:'100%',
                                        filterThead:false,
                                        showToolbar: true,
                                        toolbarCustom:$.CurrentNavtab.find('#financecost_tb'),
                                        local: 'local',
                                        dataUrl: '/financecost/adddetailJson/id/{{$id}}',
                                        dataType: 'json',
                                        jsonPrefix: 'obj',
                                        paging: false,
                                        linenumberAll: true,
                                        hScrollbar:false,
                                        showTfoot:true
                                    }">
                                <thead>
                                    <tr data-options="{name:'sysno'}">
                                        <th data-options="{name:'customer_name',align:'center'}">客户名称</th>
                                        <th data-options="{name:'shipname',align:'center'}">船名</th>
                                        <th data-options="{name:'stockindate',align:'center'}">进货时间</th>
                                        <th data-options="{name:'instockqty',align:'center'}">进库数量</th>
                                        <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
                                        <th data-options="{name:'costname',align:'center'}">费用名称</th>
                                        <th data-options="{name:'unitname',align:'center',render:function(value){return value ? value : '吨'}}">计量单位</th>
                                        <th data-options="{name:'costqty',align:'center',calc:'sum'}">计费数量</th>
                                        <th data-options="{name:'unitprice',align:'center',calc:'sum'}">单价</th>
                                        <th data-options="{name:'totalprice',align:'center',calc:'sum'}">金额(元)</th>
                                        <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
                                        <th data-options="{name:'stockinno',align:'center'}">入库单号</th>
                                        <th data-options="{name:'memo',align:'center'}">备注</th>
                                        <th data-options="{name:'instock_sysno',align:'center',hide:true}">入库id</th>
                                        <th data-options="{name:'isstoragetank',align:'center',hide:true}">是否包罐</th>
                                        <th data-options="{name:'isexceedfirst',align:'center',hide:true}">是否超出首期</th>
                                        <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                                        <th data-options="{name:'goodsnature',align:'center',hide:true}">性质</th>
                                        <th data-options="{name:'storagebank_sysno',align:'center',hide:true}">罐号</th>
                                        <th data-options="{name:'customer_sysno',align:'center',hide:true}">客户id</th>
                                        <th data-options="{name:'contract_sysno',align:'center',hide:true}">合同id</th>
                                        <th data-options="{name:'costtype',align:'center',hide:true}">费用名称id</th>
                                        
                                    </tr>
                                </thead>
                            </table>
                    </div>
                 </fieldset>
            </div>
            <br><br>
           <div class="text-center btns-user">
            <button id="financecostsubmit1" type="button" onclick="financecostsubmit(2)" class="btn btn-success btn-lg">立即生成</button>
            <button id="financecostsubmit1" type="button" onclick="showRecords()" class="btn btn-defalut btn-lg">查看操作记录</button>
           </div>

           <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">
                        
                     </div>
                </fieldset>
            </div>
        </form>
    </div>
    <br><br><br><br><br><br><br><br>
</div>

<div id="financecost_tb">
    @if($mode!='eye' && $mode !='audit')
    <button type="button" class="btn btn-blue" onclick="addfinancecost()"><i class="fa fa-plus"></i> 添加</button>
    <button type="button" class="btn btn-red" onclick="subfinancecost()"><i class="fa fa-times"></i> 删除</button>
    <!-- <button type="button" class="btn btn-green" onclick="editfinancecost()"><i class="fa fa-edit"></i>修改</button> -->
    @endif
</div>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
// JS API 调用日期选择器
$.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})

//打开记录
addLog($.CurrentNavtab.find('.addTable'), {{$id}} , '13');

</script>

<script type="text/javascript">
    $(function(){
        $("#cost_customer_sysno").change(function(){
            var v=$("#cost_customer_sysno option:selected");

            $("#cost_customername").val(v.text());
            $.CurrentNavtab.find('#financecost-detail-table').datagrid('reload',  {data:[]});
        });
        $("#cost_contract_sysno").change(function(){
            var v=$("#cost_contract_sysno option:selected");

            $("#cost_contractno").val(v.text());
        });
    });

    function addfinancecost() {
        var customer_sysno =  $.CurrentNavtab.find('#cost_customer_sysno').val();
        var contract_sysno =  $.CurrentNavtab.find('#cost_contract_sysno').val();
        var customer_name =  $.CurrentNavtab.find('#cost_customername').val();
        var contract_no =  $.CurrentNavtab.find('#cost_contractno').val();
        if(contract_no.length<=0)
        {
            var contract_sysno = 0;
        }
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'cost-receipt-{{$id}}',
                url:'/financecost/adddetail/customer_sysno/'+customer_sysno+'/contract_sysno/'+contract_sysno+'/customer_name/'+customer_name+'/contract_no/'+contract_no,
                title:'费用单明细',
                mask:true,
                width:700,
                height:500
            });
        }else{
            BJUI.alertmsg('warn','请先选中客户',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        return;
    }

    function subfinancecost() {

        var selectdata  =  $.CurrentNavtab.find('#financecost-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            var allData  = $("#financecost-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#financecost-detail-table').datagrid('reload',  {data:allData});
        }
    }

    function editfinancecost() {
        var selectedDatas  =  $.CurrentNavtab.find("#financecost-detail-table").data('selectedDatas');
        var customer_sysno =  $.CurrentNavtab.find('#cost_customer_sysno').val();
        var contract_sysno =  $.CurrentNavtab.find('#cost_contract_sysno').val();

        if (selectedDatas != undefined && selectedDatas.length == 1&&customer_sysno.length > 0 && contract_sysno.length > 0) {
            BJUI.dialog({
                id:'cost-receipt-{{$id}}',
                url:'/financecost/adddetail/cid/'+customer_sysno+'/contract_sysno/'+contract_sysno,
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                title:'费用单明细',
                width:700,
                height:500,
                mask:true
            });
        }else{
            BJUI.alertmsg('warn','请选中一行进行修改',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        return;
    }

    function financecostsubmit(step) {
        var costtype = $.CurrentNavtab.find('#costtype').val();
        if(costtype==1)
        {
            console.log(costtype);
            BJUI.alertmsg('info', '仓储费不能修改');
            return;
        }
        if(step==1){
            $.CurrentNavtab.find("#financecostmarks").attr("data-rule","required");
        }
        if(step==4 || step==6){   
          $.CurrentNavtab.find('#financecostform').attr('action',"/financecost/auditJson");
        }

        $.CurrentNavtab.find('#cost_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#cost_contract_sysno').removeAttr("disabled");

        var Obj = $.CurrentNavtab.find("#financecost-detail-table").data('allData');

        $.CurrentNavtab.find("#financecostdetaildata").val(JSON.stringify(Obj));

        $.CurrentNavtab.find("#coststatus").val(step);

        BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentNavtab.find('#financecostform'),
                validate: true,
                loadingmask: true,
                okCallback:function (json, options) {
                    BJUI.navtab('refresh', 'navab455');
                    BJUI.navtab('closeCurrentTab', '');
            }

        });
    }
</script>