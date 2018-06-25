<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="bookcarbackform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="2">
            <input type="hidden" id="bookcarbackdetaildata" name="bookcarbackdetaildata" value="">
            <input type="hidden" id="bookcarincarsdata" name="bookcarincarsdata" value="">
            <fieldset>
                <legend>入库预约单信息</legend>
                <br><br>
                <div class="bjui-row col-3">

                    <label class="row-label">入库预约单号</label>
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
                        <select name="obj.customer_sysno" id="bookcarin_customer_sysno" data-nextselect="#bookcarin_contract_sysno"
                                @if($mode !=''&&$mode !='edit') disabled @endif
                                data-refurl="/customer/customercontractJson2/id/{value}/contracttype/1,2,3,4" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customer_name" id="bookcarin_customername" value="{{$customer_name}}">
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


                    <label class="row-label">品质检查</label>
                    <div class="row-input required">
                        <select name="isqualitycheck" id="isqualitycheck" data-size="5"
                                data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-live-search="true" data-rule="required" data-width="100%">
                            <option value="1" @if($isqualitycheck == 1) selected @endif>是</option>
                            <option value="0" @if($isqualitycheck == 2) selected @endif>否</option>
                        </select>
                    </div>
                </div>

            </fieldset>
            <br>
            <br>

            <div class="remarks">
                <fieldset>
                    <legend>入库单明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="bookcarback-detail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($mode == ''||$mode=='edit'||$mode=='sure')
                                showToolbar: true,
                                toolbarCustom:$.CurrentNavtab.find('#bookcarin_tb'),
                                @endif
                                local: 'local',
                                addLocation: 'last',
                                dataUrl: '/bookcarin/adddetailJson/id/{{$id}}',
                            dataType: 'json',
                            jsonPrefix: 'obj',
                            paging: false,
                            fullGrid:true,
                            linenumberAll: true,
                            showTfoot:true,
                        }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'stockoutno',align:'center'}">出库单号</th>
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                    货物性质
                                </th>
                                <th data-options="{name:'unitname',align:'center',render:function(value){if(value=='') {return '吨'} else {return value}}}">
                                    计量单位
                                </th>
                                <th data-options="{name:'takegoodsnum',align:'center',calc:'sum'}">提单量</th>
                                <th data-options="{name:'bookinginqty',align:'center',calc:'sum'}">退回数量</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品id</th>
                                <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                                <th data-options="{name:'goodsnature_name',align:'center',hide:true}">货物性质</th>
                                <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>

            <div class="remarks">
                <fieldset>
                    <legend>车辆明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="bookcarin-cars-table" data-toggle="datagrid" data-options="{
                            filterThead:false,
                            @if($mode == ''||$mode=='edit'||$mode =='addcar')
                                showToolbar: true,
                                toolbarCustom:$.CurrentNavtab.find('#bookcarin_cars_tb'),
                                @endif
                                local: 'local',
                                addLocation: 'last',
                                dataUrl: '/bookcarin/addcarsdetailJson/id/{{$id}}',
                            dataType: 'json',
                            jsonPrefix: 'obj',
                            paging: false,
                            fullGrid:true,
                            linenumberAll: true,
                        }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'carid',align:'center'}">车牌号</th>
                                <th data-options="{name:'carname',align:'center'}">司机</th>
                                <th data-options="{name:'mobilephone',align:'center'}">手机</th>
                                <th data-options="{name:'idcard',align:'center'}">身份证</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
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
                        formData: {module:'bookcarback',action:'bookcarbackatt'},
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

            @if($mode == 'eye'||$mode == 'sure')
                <div class="remarks">
                    <fieldset>
                        <legend>仓储确认意见</legend>
                        <textarea name="confirmreason" data-toggle="autoheight" @if($mode=='eye') readonly @endif cols="auto" rows="3" placeholder="请在此处填写">{{$confirmreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            @if($mode == 'eye'||$mode == 'audit')
                <div class="remarks">
                    <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="auditreason" name="auditreason" data-toggle="autoheight" @if($mode=='eye') readonly @endif cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            @if($mode == 'eye'||$mode == 'back')
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
            <br><br>
            <div class="text-center btns-user">
                @if($mode == ''||$mode =='edit')
                    @if(!$docsource||$docsource==1)
                        <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(2)">暂存</button>&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(3)">提交</button>&nbsp;&nbsp;&nbsp;
                    @elseif($docsource==2)
                        <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(3)">提交</button>&nbsp;&nbsp;&nbsp;
                        @if($bookinginstatus==2)
                            <button type="button" class="btn btn-red btn-lg" onclick="bookcarbacksubmit(8)">驳回</button>&nbsp;&nbsp;&nbsp;
                        @endif
                    @endif
                @elseif($mode =='sure')
                    <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(5)">确认</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(6)">退回</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'audit')
                    <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(4)">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-red btn-lg" onclick="bookcarbacksubmit(6)">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'addattach')
                    <button type="button" class="btn btn-green btn-lg" onclick="saveaddattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'back')
                    <button type="button" class="btn btn-green btn-lg" onclick="bookcarbacksubmit(6)">退回</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'addcar')
                    <button type="button" class="btn btn-green btn-lg" onclick="bookcaribacksubmit(5)">登记车辆</button>&nbsp;&nbsp;&nbsp;
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
    <div id="bookcarin_tb">
        @if($mode!='sure')
            <button type="button" class="btn btn-blue" data-icon="plus" onclick="addbookcarbackdetail()">添加</button>
            <button type="button" class="btn btn-red" data-icon="times" onclick="delbookcarbackdetail()">删除</button>
        @endif
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editbbookcarbackdetail()">修改</button>
        <button type="button" id="bookcardetailmode" style="display: none">{{$mode}}</button>
    </div>
@endif
@if($mode == ''||$mode=='edit'||$mode =='addcar')
    <div id="bookcarin_cars_tb">
        <button type="button" class="btn btn-blue" data-icon="plus" onclick="addbookcarincars()">添加</button>
        <button type="button" class="btn btn-red" data-icon="times" onclick="delbookcarindetail()">删除</button>
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editbookcarincars()">修改</button>
    </div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '1');

</script>

<script type="text/javascript">
    //添加
    function addbookcarbackdetail() {
        var customer_sysno = $.CurrentNavtab.find('#bookcarin_customer_sysno').val();
        var detailObj = $.CurrentNavtab.find("#bookcarback-detail-table").data('allData');

        if (customer_sysno.length > 0 ) {
            if(detailObj.length==0 || detailObj==null || detailObj==undefined){
                BJUI.dialog({
                    id: 'sotckcarin-detail-{{$id}}',
                    url: '/bookcarback/bookcarbackdetailedit/handlestatus/add/cid/' + customer_sysno ,
                    mask:true,
                    title: '增加入库单明细',
                    width: 800,
                    height: 400
                });
            }else {
                BJUI.alertmsg('warn', '只能添加一条明细');
            }

        } else {
            BJUI.alertmsg('warn', '请先选中客户再添加明细单');
        }
        return;
    }

    //删除
    function delbookcarbackdetail() {

        var selectdata = $.CurrentNavtab.find('#bookcarback-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $("#bookcarback-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#bookcarback-detail-table').datagrid('reload', {data: allData});
        }
    }

    //修改
    function editbbookcarbackdetail(){
        var selectedDatas  =  $.CurrentNavtab.find("#bookcarback-detail-table").data('selectedDatas');
        var customer_sysno = $.CurrentNavtab.find('#bookcarin_customer_sysno').val();
        var mode = $("#bookcardetailmode").html();

        if (selectedDatas != undefined && selectedDatas.length == 1&&customer_sysno.length > 0 ) {
            BJUI.dialog({
                url:'/bookcarback/bookcarbackdetailedit/handlestatus/edit/cid/' + customer_sysno,
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0],mode:mode},
                mask:true,
                title:'入库单明细',
                width:700,
                height:400
            });
        }else{
            BJUI.alertmsg('warn', '请选中一行进行修改');
        }
        return;
    }

    //添加车辆
    function addbookcarincars() {
        var customer_sysno = $.CurrentNavtab.find('#bookcarin_customer_sysno').val();
        if (customer_sysno) {
            BJUI.dialog({
                url: '/bookcarin/bookcarineditcarsdetail/handlestatus/add/cid/' + customer_sysno,
                mask:true,
                title: '增加车辆明细',
                width: 700,
                height: 400

            });
        } else {
            BJUI.alertmsg('warn', '请先选中客户再添加车辆');
        }
        return;
    }
    //删除车辆
    function subbookcarincars() {

        var selectdata = $.CurrentNavtab.find('#bookcarin-cars-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $("#bookcarin-cars-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#bookcarin-cars-table').datagrid('reload', {data: allData});
        }
    }

    //编辑车辆
    function editbookcarincars(){

        var selectedDatas  =  $.CurrentNavtab.find("#bookcarin-cars-table").data('selectedDatas');
        var customer_sysno = $.CurrentNavtab.find('#bookcarin_customer_sysno').val();

        if (selectedDatas != undefined && selectedDatas.length == 1&&customer_sysno.length > 0 ) {
            BJUI.dialog({
                url: '/bookcarin/bookcarineditcarsdetail/handlestatus/edit/cid/' + customer_sysno,
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                mask:true,
                title: '增加车辆明细',
                width: 600,
                height: 300

            });
        }else {
            BJUI.alertmsg('warn', '请先选中客户再修改车辆');
        }
        return;
    }

    //表单提交
    function bookcarbacksubmit(step) {

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

        var detailObj = $.CurrentNavtab.find("#bookcarback-detail-table").data('allData');
        $.CurrentNavtab.find("#bookcarbackdetaildata").val(JSON.stringify(detailObj));
        var carObj = $.CurrentNavtab.find("#bookcarin-cars-table").data('allData');
        $.CurrentNavtab.find("#bookcarincarsdata").val(JSON.stringify(carObj));

        $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#bookcarin_contractno").val($.CurrentNavtab.find("#bookcarin_contract_sysno option:selected").text());
        $.CurrentNavtab.find("#bookcarin_customername").val($.CurrentNavtab.find("#bookcarin_customer_sysno option:selected").text());

        $.CurrentNavtab.find('#bookcarin_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#bookcarin_contract_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#issave').removeAttr("disabled");
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#isbusinesscheck').removeAttr("disabled");
        $.CurrentNavtab.find('#businesschecktype').removeAttr("disabled");



        $('#bookcarbackform').isValid(function (v) {
            if (v) {
                if (step == 8) {
                    BJUI.alertmsg('confirm', "是否驳回此预约单？", {okCall:function() {
                        submitback();
                    }})
                } else {
                    submitback()
                }
            }
        })
    }

    function submitback(){
        BJUI.ajax('ajaxform', {
            url: $.CurrentNavtab.find('#bookcarbackform').attr('action'),
            form: $.CurrentNavtab.find('#bookcarbackform'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab219,navab440,navab441,navab262');
                BJUI.navtab('closeCurrentTab', 'navab545');
            }
        });
    }

    function saveaddattach(){
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#bookcarbackform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab219');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }



    $("#bookcarin_contract_sysno").change(function (){
        var contract_sysno = $("#bookcarin_contract_sysno").val();
        BJUI.ajax('doajax', {
            url:'/contract/contractgoodsjson/id/'+contract_sysno,
            loadingmask: true,
            okCallback: function(json, options) {
                var str = json_array(json).join(',');
                $("#contractgoods").val(str);
            }
        });
    })

    $(function(){
        var contract_sysno = $("#bookcarin_contract_sysno").val();
        if(contract_sysno){
            BJUI.ajax('doajax', {
                url:'/contract/contractgoodsjson/id/'+contract_sysno,
                loadingmask: true,
                okCallback: function(json, options) {
                    var str = json_array(json).join(',');
                    $("#contractgoods").val(str);
                }
            });
        }
    });

    function json_array(data){
        var len=eval(data).length;
        var arr=[];
        for(var i=0;i<len;i++){
            arr[i]=data[i].goodsname;
        }
        return arr;
    }
</script>