<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="stockcarinform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="2">
            <input type="hidden" id="stockcarindetaildata" name="stockcarindetaildata" value="">
            <input type="hidden" id="stockcarincarsdata" name="stockcarincarsdata" value="">
            <input type="hidden" name="booking_in_sysno" value="@if($booking_in_sysno){{$booking_in_sysno}}@else{{''}}@endif">

            <!--base message start-->
            <fieldset>
                <legend>入库单信息</legend>
                <div class="bjui-row col-3">

                    <label class="row-label">入库单号</label>
                    <div class="row-input">
                        <input type="text" name="stockinno" value="@if($stockinno){{$stockinno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">入库日期</label>
                    <div class="row-input required">
                        <input type="text" name="stockindate" value="@if($stockindate){{$stockindate}}@else{{date('Y-m-d')}}@endif" readonly data-toggle="datepicker"  data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="stockinstatus" name="stockinstatus" value="{{$stockinstatus}}">
                        @if($stockinstatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($stockinstatus == 3)
                            <input name="statusname" value="入库中" readonly>
                        @elseif($stockinstatus == 4)
                            <input name="statusname" value="已完成" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="stockcarin_customer_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-rule="required" data-width="100%" disabled >
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customername" id="stockcarin_customername" value="{{$customername}}">
                    </div>

                    <label class="row-label">合同编号</label>
                    <div class="row-input required">
                        <input type="hidden" name="contract_sysno" value="@if($contract_sysno){{$contract_sysno}} @endif">
                        <input type="text" name="contractno" value="@if($contractno){{$contractno}}@else{{''}}@endif" readonly>
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <input type="hidden" name="docsource" value="@if($docsource){{$docsource}}@else{{1}}@endif">
                        <input type="text" value="@if($docsource ==2)国烨云仓@else手工创建@endif" readonly>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%"  @if($mode =='eye'||$mode =='confirm'||$mode =='addcar') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">卸货单号</label>
                    <div class="row-input">
                        <input type="text" name="takegoodsno" value="@if($mode ==''){{$bookcarininfo['takegoodsno']}}@else{{$takegoodsno}}@endif" readonly>
                    </div>

                    <label class="row-label">商检</label>
                    <div class="row-input required">
                        <select name="isbusinesscheck" id="isbusinesscheck" data-size="5"
                                data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-live-search="true" data-rule="required" data-width="100%">
                            <option value="1" @if($isbusinesscheck == 1) selected @endif>是</option>
                            <option value="0" @if($isbusinesscheck == 0) selected @endif>否</option>
                        </select>
                    </div>

                    <span class="isbusinesscheckview">

                        <label class="row-label">检验方式</label>
                        <div class="row-input" id="businesschecktypeview">
                            <select name="businesschecktype" id="businesschecktype" data-size="5" @if($mode !=''&&$mode !='edit') disabled @endif
                            data-toggle="selectpicker" data-live-search="true" data-width="100%">
                                <option value="">请选择</option>
                                <option value="1" @if($businesschecktype == 1) selected @endif>送样</option>
                                <option value="2" @if($businesschecktype == 2) selected @endif>取样</option>
                            </select>
                        </div>

                        <label class="row-label">商检单位</label>
                        <div class="row-input" id="businesscheckunitnameview">
                            <input type="text" name="businesscheckunitname" id="businesscheckunitname" value="{{$businesscheckunitname}}" @if($mode !=''&&$mode !='edit') readonly @endif>
                        </div>
                    </span>


                </div>
                <br>
            </fieldset>
            <div class="remarks">
                <fieldset>
                    <legend>货品明细</legend>
                    <table class="table table-bordered" id="stockcarin-detail-table" height="40+50*{{count($list)}}" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($mode == ''||$mode == 'edit')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#stockcarin_tb'),
                            @endif
                            local: 'local',
                            data:'{{$detaillist}}',
                            dataType: 'json',
                            jsonPrefix: 'obj',
                            paging: false,
                            fullGrid:true,
                            linenumberAll: true,
                            showTfoot:true,
                        }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                            <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                货物性质
                            </th>
                            <th data-options="{name:'tobeqty',align:'center',calc:'sum'}">通知数量</th>
                            <th data-options="{name:'unitname',align:'center',render:function(value){if(value=='') {return '吨'} else {return value}} }">
                                计量单位
                            </th>
                            <th data-options="{name:'storagetankname',align:'center'}">进货罐号</th>
                            <th data-options="{name:'goodsreceiptdate',align:'center'}">预计日期</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'obj.goods_sysno',align:'center',hide:true}">产品id</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">进货罐id</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                            <th data-options="{name:'beqty',align:'center',calc:'sum',hide:true}">实际数量</th>
                            <th data-options="{name:'waitbeqty',align:'center',calc:'sum',hide:true}">待入库数量</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>
            <div class="remarks">
            <fieldset>
                <legend>车辆信息</legend>
                <table class="table table-bordered" id="stockcarin-cars-table" data-toggle="datagrid" data-options="{
                    filterThead:false,
                    @if(!$booking_in_sysno||$mode=='addcar'||$mode=='edit')
                    showToolbar: true,
                    toolbarCustom:$.CurrentNavtab.find('#custom_stockcarin_cars_tb'),
                    @endif
                    local: 'local',
                    data:'{{$carlist}}',
                    dataType: 'json',
                    jsonPrefix: 'obj',
                    paging: false,
                    fullGrid:true,
                    linenumberAll: true
                }">
                    <thead>
                    <tr data-options="{name:'sysno'}">
                        <th data-options="{name:'carid',align:'center'}">车牌号</th>
                        <th data-options="{name:'carname',align:'center'}">司机</th>
                        <th data-options="{name:'mobilephone',align:'center'}">手机</th>
                        <th data-options="{name:'idcard',align:'center',width:200}">身份证</th>
                        <th data-options="{name:'memo',align:'center'}">备注</th>
                    </tr>
                    </thead>
                </table>
            </fieldset>
            </div>
            @if($mode =='addattach'||$mode =='eye')
            <fieldset class="customerfieldset">
                <legend>附件</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                {
                    pick: {label: '点击选择图片'},
                    server: '/attachment/uploadjson',
                    fileNumLimit: 10,
                    formData: {module:'stockcarin',action:'sciattach'},
                    required: true,
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
            @endif

            @if($mode =='addcar')
            <div class="remarks">
                <fieldset>
                    <legend>变更原因</legend>
                    <textarea name="changecarreason" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写变更原因">{{$changecarreason}}</textarea>
                </fieldset>
            </div>
            @endif
            <br><br>
            <div class="text-center btns-user">
                @if($mode =='addattach'|| $mode=='eye')
                    <button type="button" class="btn btn-success btn-lg" onclick="addstnattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode =='addcar')
                    <button type="button" class="btn btn-success btn-lg" onclick="stockcarinsubmit(5)">增加车辆</button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode == ''||$mode == 'edit')
                    <button type="button" class="btn btn-success btn-lg" onclick="stockcarinsubmit(3)">提交</button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode == 'confirm')
                        <button type="button" class="btn btn-success btn-lg" onclick="stockcarinsubmit(4)">完成入库</button>&nbsp;&nbsp;&nbsp;
                @endif
                    <button type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>&nbsp;
            </div>
            <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">

                    </div>
                </fieldset>
            </div>
            <br><br><br><br><br><br>
        </form>
    </div>
</div>

@if($mode == ''||$mode == 'edit')
<div id="stockcarin_tb">
    <button type="button" class="btn btn-green" data-icon="edit" onclick="editStockcarin()"><i class="fa fa-edit"></i> 修改</button>
</div>
@endif

@if(!$booking_in_sysno||$mode=='addcar'||$mode=='edit')
<div id="custom_stockcarin_cars_tb">
    <button type="button" class="btn btn-blue" onclick="addstockcarincars()"><i class="fa fa-plus"></i>添加</button>
    @if($mode!='addcar')
    <button type="button" class="btn btn-red" onclick="delstockcarincars()"><i class="fa fa-times"></i>删除</button>
    <button type="button" class="btn btn-green" onclick="editstockcarincars()"><i class="fa fa-edit"></i>修改</button>
    @endif
</div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'})
    //操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '4');
</script>

<script type="text/javascript">
    $(function () {
        $("#isbusinesscheck").change(function () {
            var v = $("#isbusinesscheck").val();
            if (v == 0) {
                $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input");
                $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "");
                $.CurrentNavtab.find("#businesscheckunitnameview").attr("class", "row-input");
                $.CurrentNavtab.find("#businesscheckunitname").attr("data-rule", "");

                $.CurrentNavtab.find(".isbusinesscheckview").hide();
            }
            else {
                $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input required");
                $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "required");
                $.CurrentNavtab.find("#businesscheckunitnameview").attr("class", "row-input required");
                $.CurrentNavtab.find("#businesscheckunitname").attr("data-rule", "required");

                $.CurrentNavtab.find(".isbusinesscheckview").show();
            }
        });
        //商检初始化
        var v = $("#isbusinesscheck").val();
        if (v == 0) {
            $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input");
            $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "");
            $.CurrentNavtab.find("#businesscheckunitnameview").attr("class", "row-input");
            $.CurrentNavtab.find("#businesscheckunitname").attr("data-rule", "");

            $.CurrentNavtab.find(".isbusinesscheckview").hide();
        }
        else {
            $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input required");
            $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "required");
            $.CurrentNavtab.find("#businesscheckunitnameview").attr("class", "row-input required");
            $.CurrentNavtab.find("#businesscheckunitname").attr("data-rule", "required");
            $.CurrentNavtab.find(".isbusinesscheckview").show();
        }
    });

    function addStockcarin() {
        var customer_sysno = $.CurrentNavtab.find('#stockcarin_customer_sysno').val();
        if (customer_sysno.length > 0) {
            BJUI.dialog({
                id: 'sotckcarin-detail-{{$id}}',
                url: '/stockcarin/adddetail/cid/' + customer_sysno,
                mask:true,
                title: '增加入库单明细',
                width: 700,
                height: 550
            });
        } else {
            BJUI.alertmsg('info', '请先选中客户再添加明细单');
        }
        return;
    }

    function editStockcarin() {

        var receiptdata = $.CurrentNavtab.find('#stockcarin-detail-table').data('selectedDatas');
        if (receiptdata == undefined || receiptdata.length == 0) {
            BJUI.alertmsg('info', "请先选择入库明细");
        } else {
            BJUI.dialog({
                id: 'stockin-receipt-{{$id}}',
                url: '/stockcarin/detailedit/cid/' + "{{$customer_sysno}}",
                type: 'POST',
                data: receiptdata[0],
                mask:true,
                title: '入库单明细',
                width: 700,
                height: 600
            });
        }
        return;
    }

    function addstockcarincars() {
        var customer_sysno = $.CurrentNavtab.find('#stockcarin_customer_sysno').val();
        if (customer_sysno.length > 0) {
            BJUI.dialog({
                url: '/stockcarin/stockcarineditcarsdetail/handlestatus/add/cid/' + customer_sysno,
                mask:true,
                title: '增加车辆明细',
                width: 600,
                height: 300
            });
        } else {
            BJUI.alertmsg('info', '请先选中客户再添加车辆明细');
        }
        return;
    }

    function delstockcarincars() {
        var selectdata  =  $.CurrentNavtab.find('#stockcarin-cars-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            var allData  = $("#stockcarin-cars-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#stockcarin-cars-table').datagrid('reload',  {data:allData});
        }
    }

    function editstockcarincars(){
        var selectedDatas  =  $.CurrentNavtab.find("#stockcarin-cars-table").data('selectedDatas');
        var customer_sysno = $.CurrentNavtab.find('#stockcarin_customer_sysno').val();
        if (selectedDatas!=''&&selectedDatas!=null&&customer_sysno.length > 0) {
            BJUI.dialog({
                url: '/stockcarin/stockcarineditcarsdetail/handlestatus/edit/cid/' + customer_sysno,
                mask:true,
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                title: '修改车辆明细',
                width: 600,
                height: 300
            });
        } else {
            BJUI.alertmsg('info', '请先选中一行再修改车辆明细');
        }
        return;
    }

    function stockcarinsubmit(step) {

        $.CurrentNavtab.find("#stockinstatus").val(step);

        $.CurrentNavtab.find('#stockcarin_customer_sysno').removeAttr("disabled");

        var detailObj = $.CurrentNavtab.find("#stockcarin-detail-table").data('allData');
        $.CurrentNavtab.find("#stockcarindetaildata").val(JSON.stringify(detailObj));
        var carObj = $.CurrentNavtab.find("#stockcarin-cars-table").data('allData');
        $.CurrentNavtab.find("#stockcarincarsdata").val(JSON.stringify(carObj));

        $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#zj_employeename").val($.CurrentNavtab.find("#zj_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#cc_employeename").val($.CurrentNavtab.find("#cc_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#stockcarin_customername").val($.CurrentNavtab.find("#stockcarin_customer_sysno option:selected").text());


        BJUI.ajax('ajaxform', {
            url: $.CurrentNavtab.find('#stockcarinform').attr('action'),
            form: $.CurrentNavtab.find('#stockcarinform'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab262,navab264,navab442');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function addstnattach(){
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#stockcarinform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {

                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }
</script>


