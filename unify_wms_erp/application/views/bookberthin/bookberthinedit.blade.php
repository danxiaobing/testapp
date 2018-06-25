<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="bookberthinform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="4">
            <input type="hidden" id="bookberthindetaildata" name="bookberthindetaildata" value="">
            <fieldset>
                <legend>基本信息</legend>
                <br><br>
                <div class="bjui-row col-3">

                    <label class="row-label">预约单号</label>
                    <div class="row-input">
                        <input type="text" name="bookinginno" value="@if($bookinginno){{$bookinginno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">预约日期</label>
                    <div class="row-input required">
                        <input type="text" name="bookingindate" value="@if($bookingindate){{date('Y-m-d',strtotime($bookingindate))}}@else{{date('Y-m-d')}}@endif" @if($mode !=''&&$mode !='edit') disabled @endif data-toggle="datepicker" data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="bookinginstatus" name="bookinginstatus" value="{{$bookinginstatus}}" readonly>
                        @if($bookinginstatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($bookinginstatus == 3)
                            <input name="statusname" value="待确认" readonly>
                        @elseif($bookinginstatus == 4)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($bookinginstatus == 5)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($bookinginstatus == 6)
                            <input name="statusname" value="已完成" readonly>
                        @elseif($bookinginstatus == 7)
                            <input name="statusname" value="退回" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="bookberthin_customer_sysno" data-nextselect="#bookberthin_contract_sysno"
                                @if($mode !=''&&$mode !='edit') disabled @endif
                                data-refurl="/customer/customercontractJson2/id/{value}/contracttype/5" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customer_name" id="bookberthin_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">合同编号</label>
                    <div class="row-input required">
                        <select name="contract_sysno" id="bookberthin_contract_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%" @if($mode !=''&&$mode !='edit') disabled @endif>
                            <option value="">请选择</option>
                            <option value="{{$contract_sysno}}" @if($contract_sysno) selected @endif>{{$contract_no}}</option>
                        </select>
                        <input type="hidden" name="contract_no" id="bookberthin_contractno" value="{{$contract_no}}">
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <input type="hidden" name="docsource" value="@if($docsource){{$docsource}}@else{{1}}@endif">
                        <input type="text" value="@if($docsource ==2)国烨云仓@else手工创建@endif" readonly>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5" data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif data-live-search="true" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">船名</label>
                    <div class="row-input required">
                        <input type="text" name="inshipname" data-rule="required" value="{{$inshipname}}" @if($mode !=''&&$mode !='edit') readonly @endif data-toggle="findgrid" data-options="{
                        include: 'inshipname:shipname',
                        dialogOptions: {width:'800',height:'500',title:'船详细信息',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            height:'100%',
                            local: 'local',
                            paging: {pageSize:5},
                            dataUrl: '/supplier/shiplistJson/page/1/bar_status/1',
                            editUrl: '/supplier/shiplist',
                            columns: [
                                    {name:'sysno', label:'id',align:'center'},
                                    {name:'captain', label:'船长',align:'center'},
                                    {name:'shipname', label:'船名',align:'center'}
                                    ],
                            showLinenumber:false,
                            fullGrid:true
                                },
                        }" placeholder="点放大镜按钮查找">
                    </div>

                    <label class="row-label">泊位预约</label>
                    <div class="row-input required">
                        <select name="isberthorder" id="isberthorder" data-size="5" data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif data-width="100%">
                            <option value="1" @if(!isset($isberthorder)||$isberthorder == 1) selected @endif>是</option>
                            <option value="2" @if($isberthorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">管线预约</label>
                    <div class="row-input required">
                        <select name="ispipelineorder" id="ispipelineorder" data-size="5"  data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif data-width="100%">
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
                    <table class="table table-bordered" id="bookberthin-detail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($mode == ''||$mode=='edit'||$mode=='sure')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#bookberthaload_tb'),
                            @endif
                            local: 'local',
                            addLocation: 'last',
                            dataUrl: '/bookberthin/adddetailJson/id/{{$id}}',
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
                            <th data-options="{name:'bookinginqty',align:'center',calc:'sum'}">预计数量（吨）</th>
                            <th data-options="{name:'bookingindate',align:'center'}">预计到港日期</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品id</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
            </div>

            <fieldset class="customerfieldset">
                <legend>上传附件@if($mode == 'addattach')<span style="color: red">*</span>@endif</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                    {
                        pick: {label: '点击选择图片'},
                        server: '/attachment/uploadjson',
                        fileNumLimit: 10,
                        formData: {module:'bookberthin',action:'bookberthinatt'},
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

            @if(($mode == 'eye'||$mode == 'audit') && $bookinginstatus!=7)
            <div class="remarks">
            <fieldset>
                <legend>审核意见</legend>
                    <textarea id="auditreason" name="auditreason" data-toggle="autoheight" @if($mode=='eye') readonly @endif cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
            </fieldset>
            </div>
            @endif

            @if(($mode == 'eye'||$mode == 'back')&& $bookinginstatus==7)
            <div class="remarks">
                <fieldset>
                    <legend>退回意见<span style="color: red">*</span></legend>
                        <textarea id="backreason" name="backreason" data-toggle="autoheight" @if($mode=='eye') readonly @elseif($mode == 'back') data-rule="required" @endif cols="auto" rows="3" placeholder="请在此处填写退回意见">{{$backreason}}</textarea>
                </fieldset>
            </div>
            @endif

            @if($docsource==2&&$bookinginstatus==2)
                <div class="remarks">
                    <fieldset>
                        <legend>驳回意见</legend>
                        <textarea id="rejectreason" name="rejectreason" data-toggle="autoheight" @if($mode=='eye') readonly @endif cols="auto" rows="3" placeholder="请在此处填写驳回意见">{{$rejectreason}}</textarea>
                    </fieldset>
                </div>
            @endif
            <br>
            <br>
            <div class="text-center btns-user">
                @if($mode == ''||$mode =='edit')
                    @if(!$docsource||$docsource==1)
                        <button type="button" class="btn btn-green btn-lg" onclick="bookberthinsubmit(2)">暂存</button>&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-green btn-lg" onclick="bookberthinsubmit(4)">提交</button>&nbsp;&nbsp;&nbsp;
                    @elseif($docsource==2)
                        <button type="button" class="btn btn-green btn-lg" onclick="bookberthinsubmit(4)">提交</button>&nbsp;&nbsp;&nbsp;
                        @if($bookinginstatus==2)
                        <button type="button" class="btn btn-red btn-lg" onclick="bookberthinsubmit(8)">驳回</button>&nbsp;&nbsp;&nbsp;
                        @endif
                    @endif
                @elseif($mode == 'audit')
                    <button type="button" class="btn btn-green btn-lg" onclick="bookberthinsubmit(4)">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-red btn-lg" onclick="bookberthinsubmit(6)">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'addattach')
                        <button type="button" class="btn btn-green btn-lg" onclick="saveaddattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'back')
                    <button type="button" class="btn btn-green btn-lg" onclick="bookberthinsubmit(6)">退回</button>&nbsp;&nbsp;&nbsp;
                @endif
                    <button type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>&nbsp;
                @if($mode == 'eye' && $ca_no)
               <a href="{{$ca_address}}" target="_blank" class="btn btn-orange" style="height: 50px;line-height: 38px;">查看CA合同</a> @endif
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

@if($mode == ''||$mode=='edit'||$mode=='sure')
    <div id="bookberthaload_tb">
        @if($mode!='sure')
        <button type="button" class="btn btn-blue" data-icon="plus" onclick="addbookberthindetail()">添加</button>
        <button type="button" class="btn btn-red" data-icon="times" onclick="subbookberthindetail()">删除</button>
        @endif
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editbbookberthindetail()">修改</button>
        <button type="button" id="bookcardetailmode" style="display: none">{{$mode}}</button>
    </div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //操作记录
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '18');

</script>

<script type="text/javascript">
    function addbookberthindetail() {
        var customer_sysno = $.CurrentNavtab.find('#bookberthin_customer_sysno').val();
        var contract_sysno = $.CurrentNavtab.find('#bookberthin_contract_sysno').val();
        if (customer_sysno.length > 0 && contract_sysno.length > 0) {
            BJUI.dialog({
                id: 'bookberthin-detail-{{$id}}',
                url: '/bookberthin/bookberthindetailedit/handlestatus/add/cid/' + customer_sysno + '/coid/' + contract_sysno,
                mask:true,
                title: '增加靠泊装货单明细',
                width: 800,
                height: 400
            });
        } else {
            BJUI.alertmsg('warn', '请先选中客户和合同再添加明细单');
        }
        return;
    }

    function subbookberthindetail() {

        var selectdata = $.CurrentNavtab.find('#bookberthin-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $("#bookberthin-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#bookberthin-detail-table').datagrid('reload', {data: allData});
        }
    }

    function editbbookberthindetail(){
        var selectedDatas  =  $.CurrentNavtab.find("#bookberthin-detail-table").data('selectedDatas');
        var customer_sysno = $.CurrentNavtab.find('#bookberthin_customer_sysno').val();
        var contract_sysno = $.CurrentNavtab.find('#bookberthin_contract_sysno').val();
        var mode = $("#bookcardetailmode").html();

        if (selectedDatas != undefined && selectedDatas.length == 1&&customer_sysno.length > 0 && contract_sysno.length > 0) {
            BJUI.dialog({
                url:'/bookberthin/bookberthindetailedit/handlestatus/edit/cid/' + customer_sysno + '/coid/' + contract_sysno,
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0],mode:mode},
                mask:true,
                title:'靠泊装货明细',
                width:700,
                height:400
            });
        }else{
            BJUI.alertmsg('warn', '请选中一行进行修改');
        }
        return;
    }

    function bookberthinsubmit(step) {

        $.CurrentNavtab.find("#bookinginstatus").val(step);

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

        var detailObj = $.CurrentNavtab.find("#bookberthin-detail-table").data('allData');
        $.CurrentNavtab.find("#bookberthindetaildata").val(JSON.stringify(detailObj));

        $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#bookberthin_contractno").val($.CurrentNavtab.find("#bookberthin_contract_sysno option:selected").text());
        $.CurrentNavtab.find("#bookberthin_customername").val($.CurrentNavtab.find("#bookberthin_customer_sysno option:selected").text());

        $.CurrentNavtab.find('#bookberthin_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#bookberthin_contract_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#isberthorder').removeAttr("disabled");
        $.CurrentNavtab.find('#ispipelineorder').removeAttr("disabled");



        $('#bookberthinform').isValid(function (v) {
            if (v) {
                if (step == 8) {
                    BJUI.alertmsg('confirm', "是否驳回此预约单？", {okCall:function() {
                        submit();
                    }})
                } else {
                    BJUI.ajax('ajaxform', {
                        url: $.CurrentNavtab.find('#bookberthinform').attr('action'),
                        form: $.CurrentNavtab.find('#bookberthinform'),
                        validate: true,
                        loadingmask: true,
                        okCallback: function (json, options) {
                            BJUI.navtab('reloadFlag', 'navab493');
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
            form: $.CurrentNavtab.find('#bookberthinform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab219');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

</script>