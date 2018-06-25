<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
     <br><br>
        <form id="pipeline_stockoutedit_form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" id="pipeline_stockoutedit_id" name="id" value="{{$id}}">
            <input type="hidden" id="pipeline_stockoutedit_detaildata" name="stockoutdetaildata">
            <input type="hidden" id="pipeline_stockoutedit_type" name="stockouttype" value="{{$stockouttype}}">
            <input type="hidden" id="pipeline_stockoutedit_status" name="stockoutstatus" value="@if($stockoutstatus) {{$stockoutstatus}} @else {{2}} @endif">

            <fieldset>
                <legend>出库单信息</legend>
                <br><br>

                <div class="bjui-row col-3">
                    <label class="row-label">出库单号</label>
                    <div class="row-input">
                        <input type="text" id='pipeline_stockoutedit_stockoutno' name="stockoutno" value="{{$stockoutno or ''}}" readonly>
                    </div>
                    <label class="row-label">出库日期</label>
                    <div class="row-input">
                        <input type="text" id='pipeline_stockoutedit_date' name="stockoutdate" data-toggle="datepicker"
                               value="@if($stockoutdate) {{ $stockoutdate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" readonly>
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <select name="docsource" id='pipeline_edit_docsource' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="1" @if(!$docsource || $docsource == 1) selected @endif>手工创建</option>
                            <option value="2" @if($docsource == 2) selected @endif>国烨云仓</option>
                        </select>
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <select name="customer_sysno" id="pipeline_stockoutedit_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customername" id="pipeline_stockoutedit_customername" value="{{$customername}}">
                    </div>

                    <input type="hidden" name="booking_out_sysno" value="{{$booking_out_sysno}}"/>

                    <label class="row-label">预约编号</label>
                    <div class="row-input">
                        <input type="text" id='pipeline_stockoutedit_bookingoutno' name="bookingoutno" value="{{$bookingoutno}}" readonly/>
                    </div>
                    
                    <label class="row-label">提货单位</label>
                    <div class="row-input">
                        <input type="text" name="takegoodscompany" value="{{$takegoodscompany}}" readonly/>
                    </div>

                    <label class="row-label">提货单号</label>
                    <div class="row-input">
                        <input type="text" id='pipeline_stockoutedit_takegoodsno' name="takegoodsno" value="{{$takegoodsno}}" readonly/>
                    </div>

                    <label class="row-label">提货区间</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" name="receivestart" data-toggle="datepicker" value="{{$receivestart}}" placeholder="开始日期" data-rule="required" readonly>
                            <div class="input-group-addon">至</div>
                            <input type="text" class="form-control" name="receiveend" data-toggle="datepicker" value="{{$receiveend}}" placeholder="结束日期" readonly>
                        </div>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="pipeline_stockoutedit_cs_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" @if($type == 'view' || $type == 'cancel' || $type == 'audit') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"  @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="pipeline_stockoutedit_cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">管线分配</label>
                    <div class="row-input">
                        <select name="ispipelineorder" id="pipeline_stockoutedit_ispipelineorder" data-size="5" data-toggle="selectpicker" data-width="100%" disabled>
                            <option value="1" @if($ispipelineorder == 1) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder == 2) selected @endif>否</option>
                        </select>
                    </div>
                    
                    <label class="row-label">品质检查</label>
                    <div class="row-input">
                        <select name="isqualitycheck" id="pipeline_stockoutedit_isqualitycheck" data-size="5" data-toggle="selectpicker" data-width="100%" disabled>
                            <option value="1" @if($isqualitycheck == 1) selected @endif>是</option>
                            <option value="2" @if($isqualitycheck == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">司磅员</label>
                    <div class="row-input">
                        <select name="sby_employee_sysno" id="pipeline_stockoutedit_sby_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $sby_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="sby_employeename" id="pipeline_stockoutedit_sby_employeename" value="{{$sby_employeename}}">
                    </div>

                    <label class="row-label">备注</label>
                    <div class="row-input">
                        <textarea  name="memo"  data-toggle="autoheight">{{$memo}}</textarea>
                    </div>
                </div>
                <br><br>
            </fieldset>
        <div class="remarks">
            <fieldset>
                <legend>出库单信息</legend>
                <div class="table-edit">

                    <table class="table table-bordered" id="pipeline-stockoutedit-receipt-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                           height:'100%',
                            filterThead:false,
                            @if($type != 'cancel' &&  $type != 'audit' && $type != 'view')
                            showToolbar:true,
                            toolbarCustom:$.CurrentNavtab.find('#pipeline_custom_stockoutedit_tb'),
                            @endif
                            data:{{$detaillist}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot: true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr data-options="{name:'stock_sysno'}"> 
                            <th data-options="{name:'stockin_no',align:'center'}">入库单号</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';  }}}">
                                货物性质
                            </th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库数量(吨)</th>
                            <th data-options="{name:'introduceqty',align:'center',calc:'sum'}">提单总数(吨)</th>
                            <th data-options="{name:'takeqty',align:'center',calc:'sum'}">预提提货数量(吨)</th>
                            <th data-options="{name:'tobeqty',align:'center',calc:'sum'}">通知提货数量(吨)</th>
                            @if($stockoutstatus == 8 || $stockoutstatus == 3 || $stockoutstatus == 4)
                            <th data-options="{name:'bussinesscheckqty',align:'center',calc:'sum'}">罐检数量(吨)</th>
                            @endif
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'bookout_detail_sysno',align:'center',hide:true}">预约单号id</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>
                            <th data-options="{name:'stockno',align:'center',hide:true}">库存单号</th>
                            <th data-options="{name:'stockqty',align:'center',hide:true}">可用数量</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">出货罐号id</th>
                            <th data-options="{name:'stockinshipname',align:'center',hide:true}">进货船名</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存来源</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <div class="remarks">
            <fieldset>
                <legend>管线明细</legend>
                <div class="table-edit">

                    <table class="table table-bordered" height="40+50*{{count($list)}}" data-toggle="datagrid" data-options="{
                           height:'100%',
                            filterThead:false,
                            data:{{$pipelineorder}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot: true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr>
                            <th data-options="{name:'goodsname',align:'center'}">品种</th>
                            <th data-options="{name:'wharf_pipelineno',align:'center'}">码头管线号</th>
                            <th data-options="{name:'area_pipelineno',align:'center'}">库区管线号</th>
                            <th data-options="{name:'beqty',align:'center'}">实际流量(吨)</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <div class="remarks">
            <fieldset>
                <legend>品质检查明细</legend>
                <div class="table-edit">

                    <table class="table table-bordered" height="40+50*{{count($list)}}" data-toggle="datagrid" data-options="{
                           height:'100%',
                            filterThead:false,
                            data:{{$qualitycheck}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr>
                            <th data-options="{name:'goodsname',align:'center'}">品种</th>
                            <th data-options="{name:'checktime',align:'center'}">品质检查时间</th>
                            <th data-options="{name:'ischecked',align:'center',render:function(value){switch(value) { case '1': return '是'; case '2':return '否'; default: return '';  }}}">是否合格</th>
                            <th data-options="{name:'isskip',align:'center',render:function(value){switch(value) { case '0': return '不用让步'; case '1': return '是'; case '2':return '否'; default: return '--';  }}}">是否让步</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="comuser-add-left">
            <fieldset class="customerfieldset">
                <legend>上传附件</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                    {
                        pick: {label: '点击选择图片'},
                        server: '/attachment/uploadjson',
                        fileNumLimit: 10,
                        formData: {module:'pipelineout',action:'pipeline'},
                        required: false,
                        uploaded: '{{ $uploaded }}',
                        basePath: '/attachment/preview/id/',
                        deletePath:'/attachment/deljson/',
                        accept: {
                            title: '图片',
                            extensions: 'jpg,png,pdf,txt',
                            mimeTypes: '.jpg,.png,.pdf,.txt'
                        }
                    }"
                >
            </fieldset>
        </div>
        </form>
        <div class="remarks">
        @if($type == 'cancel' || $type == 'audit')
            <fieldset>
                    <legend> @if($type == 'cancel')作废意见@else($type == 'audit')审核意见@endif</legend>
                    <form id="pipeline-stockout-exam-form" action="/stockout/examJson" method="POST" class="datagrid-edit-form" data-toggle="validate" data-data-type="json" data-validator-option="{stopOnError:false,timely:false}">
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="examstep" id="pipeline_stockoutedit_examstep" value="">
                        <textarea id="pipeline_stockoutedit_marks" name="stockoutmarks"  data-toggle="autoheight" rows="3" placeholder="@if($type == 'cancel')请在此处填写作废意见@else($type == 'audit')请在此处填写审核意见@endif"></textarea>
                        </div>
                    </form>
                
            </fieldset>
        @endif
            <br><br>
        <div class="text-center ">
                @if($stockoutstatus < 3 && $type != 'view' && $type != 'audit')
                    <button type="button" onclick="stockoutSubmit(2)" class="btn btn-success btn-lg">保存</button>
                    &nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="stockoutSubmit(8)" class="btn btn-success btn-lg">提交</button>
                @endif
                @if($stockoutstatus == 8 && $type != 'view')
                    <button type="button" onclick="executeBack(6)" class="btn btn-red btn-lg">退回</button>
                    &nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="stockoutSubmit(3)" class="btn btn-success btn-lg">提交</button>
                @endif
                @if($stockoutstatus == 6 && $type != 'view' && $type != 'audit')
                    <button type="button" onclick="stockoutSubmit(6)" class="btn btn-success btn-lg">保存</button>
                    &nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="stockoutSubmit(8)" class="btn btn-success btn-lg">提交</button>
                @endif
                @if($type == 'audit')
                    <button type="button" onclick="stockoutExam(4)" class="btn btn-success btn-lg">审核通过</button>
                    &nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="stockoutExam(6)" class="btn btn-red btn-lg">审核不通过</button>
                @endif
                @if($type == 'cancel')
                    <button type="button" onclick="stockoutExam(5)" class="btn btn-red btn-lg">作废</button>
                @endif
                <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>
        </div>
        <br><br>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable"></div>
            </fieldset>
        </div>
        <br><br>
        </div>
    </div>
</div>
@if($type != 'cancel' &&  $type != 'audit' && $type != 'view')
<div id="pipeline_custom_stockoutedit_tb">
    <button type="button" class="btn btn-green" data-icon="edit" onclick="editReceipt()">修改</button>
</div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function () {
        if (!{{$id}}) {
            $.ajax({
                url: '/bookout/getbookoutno/prefix/R4',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $.CurrentNavtab.find('#pipeline_stockoutedit_stockoutno').val(data);
                },
            });
        }

    });

    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '26');

    $('#ship_stockout_wharf_sysno').change(function(){
        $('#ship_stockout_wharfname').val($('#ship_stockout_wharf_sysno option:selected').text());
    })

    function editReceipt() {
        var receiptdata = $.CurrentNavtab.find('#pipeline-stockoutedit-receipt-table').data('selectedDatas');
        var status = $.trim($.CurrentNavtab.find("#pipeline_stockoutedit_status").val());
        if (receiptdata == '' || receiptdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.dialog({
            id: 'stockout-receipt-{{$id}}',
            data: receiptdata[0],
            type: 'POST',
            url: '/stockout/pipelineDetailEdit/cid/' + "{{$customer_sysno}}/stockoutstatus/" + status,
            title: '出库明细',
            width: 850,
            height: 480,
            mask:true,

        });
    }

    function stockoutSubmit(step) {
        var Obj = $.CurrentNavtab.find('#pipeline-stockoutedit-receipt-table').data('allData');

        var status = $.trim($.CurrentNavtab.find("#pipeline_stockoutedit_status").val());

        for (var i = 0; i < Obj.length; i++) {
            if (Obj[i].tobeqty == null) {
                BJUI.alertmsg('warn', '通知数量不能为空',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }
        if(status == 8 && step != 6){
            for (var i = 0; i < Obj.length; i++) {
                if (Obj[i].bussinesscheckqty == '') {
                    BJUI.alertmsg('warn', '罐检数量不能为空',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;
                }
            }
        }

        $.CurrentNavtab.find("#pipeline_stockoutedit_cs_employeename").val($.CurrentNavtab.find("#pipeline_stockoutedit_cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#pipeline_stockoutedit_sby_employeename").val($.CurrentNavtab.find("#pipeline_stockoutedit_sby_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#ship_stockout_zj_employeename").val($.CurrentNavtab.find("#ship_stockout_zj_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#ship_stockout_cc_employeename").val($.CurrentNavtab.find("#ship_stockout_cc_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#pipeline_stockoutedit_customername").val($.CurrentNavtab.find("#pipeline_stockoutedit_customer_sysno option:selected").text());
        $.CurrentNavtab.find('#pipeline_stockoutedit_customer_sysno').removeAttr("disabled");

        $.CurrentNavtab.find("#pipeline_stockoutedit_detaildata").val(JSON.stringify(Obj));

        $.CurrentNavtab.find("#pipeline_stockoutedit_status").val(step);
         
        $.CurrentNavtab.find("#pipeline_stockoutedit_ispipelineorder").removeAttr('disabled');
        $.CurrentNavtab.find("#pipeline_stockoutedit_isqualitycheck").removeAttr('disabled');

        BJUI.ajax('doajax',{
            url:'/stockout/checkstoragetank/step/'+step,
            data:{bookingdata : Obj},
            type:'POST',
            dataType: 'json',
            okCallback: function (json, options) {
                if(json.code == 300){
                    BJUI.alertmsg('confirm', json.msg, {okCall: function() {
                        submitForm();
                        }
                    });
                }else{
                    submitForm();
                }
            }
        });
    }

    function submitForm() {
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#pipeline_stockoutedit_form'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab520,navab521,navab558');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function stockoutExam(step) {
        $("#pipeline_stockoutedit_examstep").val(step);

        if (!$.CurrentNavtab.find("#pipeline_stockoutedit_marks").val() && step == 6) {
            BJUI.alertmsg('warn', '请先填写审核意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        if (!$.CurrentNavtab.find("#pipeline_stockoutedit_marks").val() && step == 5) {
            BJUI.alertmsg('warn', '请先填写作废意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#pipeline-stockout-exam-form'),
            validate: false,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab520,navab521');
                BJUI.navtab('closeCurrentTab', '');
                
            }
        });
    }

    function executeBack() {
        var id = $.trim($("#pipeline_stockoutedit_id").val());
        BJUI.ajax('doajax', {
            url: "/stockout/executeBack/id/" + id,
            okCallback: function (json, options) {
                if(json.code == 200){
                    BJUI.navtab('reloadFlag', 'navab520,navab521,navab558');
                    BJUI.navtab('closeCurrentTab', '');
                }else{
                    BJUI.alertmsg('warn', json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                }
            }
        });
    }
</script>
