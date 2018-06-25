<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="clearstockform" action="{{$action}}" method="POST" class="datagrid-edit-form"  data-data-type="json" data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id? $id:$id='0'}}">
            <input type="hidden" name="clearstockdetail" value=""><!-- 清库详情值-->
            <input type="hidden" name="printdata" value="{{$printdata}}"><!--打印值-->
            <!--base message start-->
            <fieldset>
                <legend>清库单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">清库单号</label>
                    <div class="row-input">
                        <input type="text" name="stockclearno" value="@if($stockclearno){{$stockclearno}}@else{{'系统编码'}} @endif" readonly>
                    </div>

                    <label class="row-label">清库日期</label>
                    <div class="row-input required"><input type="text" name="stockcleardate" value="@if($stockcleardate){{date('Y-m-d',strtotime($stockcleardate))}}@else{{date('Y-m-d')}}@endif" data-toggle="datepicker" data-rule="required;date"></div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" name="stockclearstatus" value="@if($stockclearstatus) {{$stockclearstatus}} @else {{'2'}} @endif" readonly>
                        <input type="text" name="stockclearstatusname" value="@if($stockclearstatusname[$stockclearstatus]) {{$stockclearstatusname[$stockclearstatus]}} @else {{'新建'}} @endif" readonly></div>

                    <label class="row-label">客户:</label>
                    <div class="row-input required">
                        <select name="customer_sysno" data-size="5" id="customer_sysno" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">客服专员:</label>
                    <div class="row-input required">
                        <select name="cs_employee_sysno" data-size="5" id="cs_employee_sysno" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required" disabled>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                            <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                        </select>
                    </div>
                </div><br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>清库单明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="clear-detail" data-toggle="datagrid" data-options="{
                        height : '100%',
                        filterThead:false,
                        showToolbar: false,
                        toolbarCustom:$.CurrentNavtab.find('#clearstock_table'),
                        local: 'local',
                        dataUrl: '/clearstock/adddetail/id/{{$id}}',
                        dataType: 'json',
                        paging: false,
                        fullGrid:true,
                        linenumberAll: true
                    }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'stockin_no',align:'center',width:100}">单号</th>
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'instockdate',align:'center'}">入库时间</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'instockqty',align:'center',calc:'sum'}">商检量</th>
                                <th data-options="{name:'stockqty',align:'center',calc:'sum'}">结存量</th>
                                <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
                                <th data-options="{name:'tankclearqty',align:'center'}">清库量</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                {{--<th data-options="{name:'controlproportion',align:'center',hide:'true'}">控货比重</th>--}}
                                <th data-options="{name:'stockin_sysno',align:'center',hide:'true'}">所属库存类型Id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>

            <!--project end-->
            @if($stockclearstatus >=3  )
                <div class="remarks">
                    <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="clearstockoperdesc" name="operdesc" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写审核意见" readonly>{{$operdesc}}</textarea>
                    </fieldset>
                </div>
            @endif
            @if($stockclearstatus == 4 || $stockclearstatus == 7)
                <div class="remarks">
                    <fieldset>
                        <legend>操作作废意见</legend>
                        <textarea id="clearstockabandonreason" name="abandonreason" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写作废意见" readonly>{{$abandonreason}}</textarea>
                    </fieldset>
                </div>
            @endif
            <br><br>
            <div class="text-center btns-user">
                <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>
            </div>
            <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">

                    </div>
                </fieldset>
            </div>
            <div style="height: 500px;"></div>
        </form>
    </div>
</div>

<script src="/static/common/js/custom.js"></script>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">

    //操作记录显示|隐藏
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '11');
    //切换用户数据切换当前用户的数据
    $("#customer_sysno").change(function(){
        var customer_sysno =  $.CurrentNavtab.find('#customer_sysno').val();
        $.CurrentNavtab.find('#clear-detail').datagrid('reload',  {data:[]});
    });

    //添加清库详情
    function addclearstock() {
        var customer_sysno =  $.CurrentNavtab.find('#customer_sysno').val();//客户姓名
        if(customer_sysno.length>0){
            BJUI.dialog({
                id:'clearstock-select-{{$id}}',
                url:'clearstock/adddata/customer_sysno/'+customer_sysno,
                title:'清库单详情',
                maxable:false,
                minable:false,
                width:1200,
                height:500,
                mask:true
            });
        }
        else{
            BJUI.alertmsg('info', '请先选中客户再选择入库单号');
        }

    }
    //添加数据筛选
    function addclearstocks() {
        var customer_sysno =  $.CurrentNavtab.find('#customer_sysno').val();//客户姓名
        var clearstock_employeename = $.CurrentNavtab.find("#cs_employee_sysno option:selected").text();
        $.CurrentNavtab.find("#cs_employeename").val(clearstock_employeename);//客服姓名
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'clearstock-select-{{$id}}',
                url:'/clearstock/adddata/customer_sysno/'+customer_sysno,
                title:'增加库存信息',
                width:600,
                height:300,
                mask:true
            });
        }else{
            BJUI.alertmsg('info', '请先选中客户再选择入库单号');
        }
    }

    //选中移除数据
    function removeclearstock() {
        var selectdata = $.CurrentNavtab.find("#clear-detail").data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('info', BJUI.getRegional('datagrid.selectMsg'));
            return false;
        }else{
            var allData  = $("#clear-detail").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#clear-detail').datagrid('reload',{data:allData});
        }
    }
    //提交保存操作
    function clearstocksubmit(step) {
        var clearstock_employeename = $.CurrentNavtab.find("#cs_employee_sysno option:selected").text();
        $.CurrentNavtab.find("#cs_employeename").val(clearstock_employeename);//客服姓名
        $.CurrentNavtab.find("Input[name='stockclearstatus']").val(step);//操作值
        var Obj = $.CurrentNavtab.find("#clear-detail").data('allData');//获取清库详情数据
        $.CurrentNavtab.find("Input[name='clearstockdetail']").val(JSON.stringify(Obj));//详情数据绑定

        if(Obj){
            if(step==4 || step==5 ){
                if(step==5){ //审核驳回验证备注
                    $.CurrentNavtab.find("#clearstockoperdesc").attr("data-rule","required");
                }
                $.CurrentNavtab.find('#clearstockform').attr('action',"/clearstock/examineJson");
            }
            //写ajax提交
            BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentNavtab.find('#clearstockform'),
                validate: true,
                loadingmask: true,
                okCallback: function(json, options) {
                    //    console.log('返回内容1：\n'+ JSON.stringify(json))
                    BJUI.navtab('closeCurrentTab','navab296');
                    BJUI.navtab('refresh', 'navab296');
                }
            });
        }else {
            BJUI.alertmsg('info','清库明细不能为空！！！');
            return false;
        }

    }

</script>