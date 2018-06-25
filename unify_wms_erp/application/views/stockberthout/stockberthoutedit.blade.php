<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="stockberthoutform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="4">
            <input type="hidden" id="stockberthoutdetaildata" name="stockberthoutdetaildata" value="">
            <fieldset>
                <legend>基本信息</legend>
                <br><br>
                <div class="bjui-row col-3">
                    <label class="row-label">预约单号</label>
                    <div class="row-input">
                        <input type="text" name="stockoutno" value="@if($stockoutno){{$stockoutno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">卸货日期</label>
                    <div class="row-input required">
                        <input type="text" id="stockoutdate" name="stockoutdate" value="@if($stockoutdate){{date('Y-m-d',strtotime($stockoutdate))}}@else{{date('Y-m-d')}}@endif" @if($mode !=''&&$mode !='edit') disabled @endif data-toggle="datepicker" data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="stockoutstatus" name="stockoutstatus" value="{{$stockoutstatus}}" readonly>
                        @if($stockoutstatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($stockoutstatus == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($stockoutstatus == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($stockoutstatus == 5)
                            <input name="statusname" value="作废" readonly>
                        @elseif($stockoutstatus == 6)
                            <input name="statusname" value="退回" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="stockberthout_customer_sysno" data-nextselect="#stockberthout_contract_sysno"
                                @if($mode !=''&&$mode !='edit') disabled @endif
                                data-refurl="/customer/customercontractJson2/id/{value}" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customer_name" id="stockberthout_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">合同编号</label>
                    <div class="row-input required">
                        <select name="contract_sysno" id="stockberthout_contract_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%" @if($mode !=''&&$mode !='edit') disabled @endif>
                            <option value="">请选择</option>
                            <option value="{{$contract_sysno}}" @if($contract_sysno) selected @endif>{{$contractno}}</option>
                        </select>
                        <input type="hidden" name="contractno" id="stockberthout_contractno" value="{{$contractno}}">
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <input type="hidden" name="docsource" value="@if($docsource){{$docsource}}@else{{1}}@endif">
                        <input type="text" value="@if($docsource ==2)国烨云仓@else手工创建@endif" readonly>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5"
                                data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-live-search="true" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">船名</label>
                    <div class="row-input">
                        <input type="text" name="inshipname" value="{{$inshipname}}" readonly>
                    </div>

                    <label class="row-label">泊位预约</label>
                    <div class="row-input required">
                        <select name="isberthorder" id="isberthorder" data-size="5"
                                data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-live-search="true" data-rule="required" data-width="100%">
                            <option value="1" @if(!isset($isberthorder)||$isberthorder == 1) selected @endif>是</option>
                            <option value="2" @if($isberthorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">管线预约</label>
                    <div class="row-input required">
                        <select name="ispipelineorder" id="ispipelineorder" data-size="5"
                                data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-live-search="true" data-rule="required" data-width="100%">
                            <option value="1" @if($ispipelineorder == 1) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder == 2) selected @endif>否</option>
                        </select>
                    </div>
                </div>
                <br>
                <br>
            </fieldset>

            <div class="remarks">
                <fieldset>
                    <legend>装卸货品明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="stockberthout-detail-table" data-toggle="datagrid" data-options="{
                                height:'100%',
                                filterThead:false,
                                @if($mode == ''||$mode=='edit'||$mode=='sure' || $mode=='waitedit')
                                showToolbar: true,
                                toolbarCustom:$.CurrentNavtab.find('#stockberthout_tb'),
                                @endif
                                local: 'local',
                                addLocation: 'last',
                                @if($mode == 'waitedit')
                                data: '{{$detaillist}}',
                                @else
                                dataUrl: '/stockberthout/adddetailJson/id/{{$id}}',
                                @endif
                                dataType: 'json',
                                jsonPrefix: 'obj',
                                paging: false,
                                fullGrid:true,
                                linenumberAll: true,
                                showTfoot:true,
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'tobeqty',align:'center',calc:'sum'}">预计数量（吨）</th>
                                <th data-options="{name:'beqty',align:'center',calc:'sum'}">实际数量（吨）</th>
                                <th data-options="{name:'shipbookingdate',align:'center'}">预计到港日期</th>
                                <th data-options="{name:'shipactualdate',align:'center'}">实际到港日期</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>

            @if($ispipelineorder==1)

            <div class="remarks">
                <fieldset>
                    <legend>管线明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="stockberthoutpip-detail-table" data-toggle="datagrid" data-options="{
                                height:'100%',
                                filterThead:false,
                                local: 'local',
                                addLocation: 'last',
                                dataUrl: '/stockberthout/getpipeJson/id/{{$booking_out_sysno}}',
                                dataType: 'json',
                                jsonPrefix: 'obj',
                                paging: false,
                                fullGrid:true,
                                linenumberAll: true,
                                showTfoot:true,
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'wharf_pipelineno',align:'center'}">码头管线号</th>
                                <th data-options="{name:'area_pipelineno',align:'center'}">库区管线号</th>
                                <th data-options="{name:'beqty',align:'center'}">实际流量（吨）</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>
            @endif

            @if($isberthorder==1)
            <div class="remarks">
                <fieldset>
                    <legend>泊位明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="stockberthoutber-detail-table" data-toggle="datagrid" data-options="{
                                height:'100%',
                                filterThead:false,
                                local: 'local',
                                addLocation: 'last',
                                dataUrl: '/stockberthout/getbethJson/id/{{$booking_out_sysno}}',
                                dataType: 'json',
                                jsonPrefix: 'obj',
                                paging: false,
                                fullGrid:true,
                                linenumberAll: true,
                                showTfoot:true,
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'berthname',align:'center'}">泊位号</th>
                                <th data-options="{name:'wharfname',align:'center'}">码头</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'planintime',align:'center'}">计划靠泊时间</th>
                                <th data-options="{name:'planouttime',align:'center'}">计划离泊时间</th>
                                <th data-options="{name:'beintime',align:'center'}">实际靠泊时间</th>
                                <th data-options="{name:'beouttime',align:'center'}">实际离泊时间</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>
            @endif

            <fieldset class="customerfieldset">
                <legend>上传附件@if($mode == 'addattach')<span style="color: red">*</span>@endif</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                    {
                        pick: {label: '点击选择图片'},
                        server: '/attachment/uploadjson',
                        fileNumLimit: 10,
                        formData: {module:'stockberthout',action:'stockberthoutatt'},
                        @if($mode == 'addattach')
                        required: true,
                        @else
                        required: false,
                        @endif
                        uploaded: '{{ $uploaded1 }}',
                        basePath: '/attachment/preview/id/',
                        deletePath:'/attachment/deljson/',
                        accept: {
                            title: '图片',
                            extensions: 'jpg,png,txt,pdf',
                            mimeTypes: '.jpg,.png,.txt,.pdf'
                        }
                    }"
                >
            </fieldset>

            @if($mode == 'eye'||$mode == 'audit')
                <div class="remarks">
                    <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="auditreason" name="auditreason" data-toggle="autoheight" @if($mode=='eye') readonly @endif cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            @if($mode == 'back')
                <div class="remarks">
                    <fieldset>
                        <legend>退回意见<span style="color: red">*</span></legend>
                        <textarea id="backreason" name="backreason" data-toggle="autoheight" @if($mode=='eye') readonly @elseif($mode == 'back') data-rule="required" @endif cols="auto" rows="3" placeholder="请在此处填写退回意见">{{$backreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            @if($mode == 'eye'||$mode == 'blank')
                <div class="remarks">
                    <fieldset>
                        <legend>作废意见<span style="color: red">*</span></legend>
                        <textarea id="abandonreason" name="abandonreason" data-toggle="autoheight" @if($mode=='eye') readonly @elseif($mode == 'back') data-rule="required" @endif cols="auto" rows="3" placeholder="请在此处填写退回意见">{{$abandonreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            <br>
            <br>
            <div class="text-center btns-user">
                @if($mode == ''||$mode =='edit')
                    @if(!$docsource||$docsource==1)
                        <button type="button" class="btn btn-green btn-lg" onclick="stockberthoutsubmit(2)">暂存</button>&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-green btn-lg" onclick="stockberthoutsubmit(3)">提交</button>&nbsp;&nbsp;&nbsp;
                    @elseif($docsource==2)
                        <button type="button" class="btn btn-green btn-lg" onclick="stockberthoutsubmit(3)">提交</button>&nbsp;&nbsp;&nbsp;
                    @endif
                @elseif($mode == 'audit')
                    <button type="button" class="btn btn-green btn-lg" onclick="stockberthoutsubmit(4)">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-red btn-lg" onclick="stockberthoutsubmit(6)">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'addattach')
                    <button type="button" class="btn btn-green btn-lg" onclick="saveaddattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'back')
                    <button type="button" class="btn btn-red btn-lg" onclick="stockberthoutsubmit(6)">退回</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'blank')
                    <button type="button" class="btn btn-red btn-lg" onclick="stockberthoutsubmit(5)">作废</button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode =='waitedit')
                        <button type="button" class="btn btn-green btn-lg" onclick="stockberthoutcreate(2)">保存</button>&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-green btn-lg" onclick="stockberthoutcreate(3)">提交</button>&nbsp;&nbsp;&nbsp;
                 @endif
                <button type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>&nbsp;
                @if($mode == 'eye' && $ca_no)
                    <a href="{{$ca_address}}" target="_blank" class="btn btn-orange" style="height: 50px;line-height: 38px;">查看CA合同</a>
                @endif


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
</div>

@if($mode == ''||$mode=='edit'||$mode=='sure' || $mode=='waitedit')
    <div id="stockberthout_tb">
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editbstockberthoutdetail()">修改</button>
        <button type="button" id="bookcardetailmode" style="display: none">{{$mode}}</button>
    </div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //操作记录
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '28');

</script>

<script type="text/javascript">
    //修改
    function editbstockberthoutdetail(){
        var selectedDatas  =  $.CurrentNavtab.find("#stockberthout-detail-table").data('selectedDatas');
        var customer_sysno = $.CurrentNavtab.find('#stockberthout_customer_sysno').val();
        var contract_sysno = $.CurrentNavtab.find('#stockberthout_contract_sysno').val();
        var mode = $("#bookcardetailmode").html();

        if (selectedDatas != undefined && selectedDatas.length == 1&&customer_sysno.length > 0 && contract_sysno.length > 0) {
            BJUI.dialog({
                url:'/stockberthout/stockberthoutdetailedit/handlestatus/edit/cid/' + customer_sysno + '/coid/' + contract_sysno,
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0],mode:mode},
                mask:true,
                title:'靠泊卸货明细',
                width:700,
                height:400
            });
        }else{
            BJUI.alertmsg('warn', '请选中一行进行修改');
        }
        return;
    }

    //提交
    function stockberthoutsubmit(step) {

        $.CurrentNavtab.find("#stockoutstatus").val(step);

        if (step == 6) {
            $.CurrentNavtab.find("#backreason").attr("data-rule", "required");
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "required");
        } else if (step == 4) {
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "a");
        } else if (step == 8) {
            $.CurrentNavtab.find("#rejectreason").attr("data-rule", "required");
        } else if (step == 3) {
            $.CurrentNavtab.find("#rejectreason").attr("data-rule", "a");
        }

        var detailObj = $.CurrentNavtab.find("#stockberthout-detail-table").data('allData');
        $.CurrentNavtab.find("#stockberthoutdetaildata").val(JSON.stringify(detailObj));

        $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#stockberthout_contractno").val($.CurrentNavtab.find("#stockberthout_contract_sysno option:selected").text());
        $.CurrentNavtab.find("#stockberthout_customername").val($.CurrentNavtab.find("#stockberthout_customer_sysno option:selected").text());

        $.CurrentNavtab.find('#stockberthout_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#stockberthout_contract_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#issave').removeAttr("disabled");
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#stockoutdate').removeAttr("disabled");

        $('#stockberthoutform').isValid(function (v) {
            if (v) {
                    BJUI.ajax('ajaxform', {
                        url: $.CurrentNavtab.find('#stockberthoutform').attr('action'),
                        form: $.CurrentNavtab.find('#stockberthoutform'),
                        validate: true,
                        loadingmask: true,
                        okCallback: function (json, options) {
                            BJUI.navtab('reloadFlag', 'navab500,navab524');
                            BJUI.navtab('closeCurrentTab', '');
                        }
                    });
            }
        })
    }

    //生成出库单
    function stockberthoutcreate(step) {

        $.CurrentNavtab.find("#stockoutstatus").val(step);


        var detailObj = $.CurrentNavtab.find("#stockberthout-detail-table").data('allData');
        $.CurrentNavtab.find("#stockberthoutdetaildata").val(JSON.stringify(detailObj));

        $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#stockberthout_contractno").val($.CurrentNavtab.find("#stockberthout_contract_sysno option:selected").text());
        $.CurrentNavtab.find("#stockberthout_customername").val($.CurrentNavtab.find("#stockberthout_customer_sysno option:selected").text());

        $.CurrentNavtab.find('#stockberthout_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#stockberthout_contract_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#issave').removeAttr("disabled");
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");



        $('#stockberthoutform').isValid(function (v) {
            if (v) {
                if (step == 8) {
                    BJUI.alertmsg('confirm', "是否驳回此预约单？", {okCall:function() {
                        submit();
                    }})
                } else {
                    BJUI.ajax('ajaxform', {
                        url: $.CurrentNavtab.find('#stockberthoutform').attr('action'),
                        form: $.CurrentNavtab.find('#stockberthoutform'),
                        validate: true,
                        loadingmask: true,
                        okCallback: function (json, options) {
                            BJUI.navtab('reloadFlag', 'navab500');
                            BJUI.navtab('closeCurrentTab', '');
                        }
                    });
                }
            }
        })
    }

    function saveaddattach(){
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#stockberthoutform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab500');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

</script>