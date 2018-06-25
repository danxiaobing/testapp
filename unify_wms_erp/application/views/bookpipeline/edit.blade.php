<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
        <br><br>
        <form id="pipeline-edit-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" id='pipeline_edit_id' name="id" value="{{$id}}">
            <input type="hidden" id="pipeline_edit_ca_address" name="ca_address" value="{{$ca_address}}">
            <input type="hidden" id="pipeline_edit_detaildata" name="detaildata" value="">
            <input type="hidden" id="pipeline_edit_bookingoutstatus" name="bookingoutstatus" value="@if($bookingoutstatus) {{$bookingoutstatus}} @else {{2}} @endif" >
            <fieldset>
                <legend>管出库预约单</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">预约单号</label>
                    <div class="row-input">
                            <input type="text" name="bookingoutno" id="pipeline_edit_bookingoutno" value="{{$bookingoutno or ''}}" readonly>
                    </div>

                    <label class="row-label">预约日期</label>
                    <div class="row-input">
                        <input type="text" name="bookingoutdate" id="pipeline_edit_bookingoutdate" data-toggle="datepicker" value="@if($bookingoutdate) {{ $bookingoutdate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>

                    <label class="row-label">操作状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" disabled>
                    </div>
                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <select name="customer_sysno" id="pipeline_edit_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" @if($type == 'audit' || $type == 'sendback' || $type == 'view' || $type == 'addatt') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="pipeline_edit_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <select name="docsource" id='pipeline_edit_docsource' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="1" @if(!$docsource || $docsource == 1) selected @endif>手工创建</option>
                            <option value="2" @if($docsource == 2) selected @endif>国烨云仓</option>
                        </select>
                    </div>
                    
                    <label class="row-label">提货单位</label>
                    <div class="row-input required">
                        <input type="text" name="receiveunitname" value="{{$receiveunitname}}" data-rule="required" >
                    </div>
                    
                    <label class="row-label">提货单号</label>
                    <div class="row-input required">
                        <input type="text" name="receivenumber" id="pipeline_edit_receivenumber" value="{{$receivenumber or ''}}" data-rule='required' @if($type == 'audit' || $type == 'sendback' || $type == 'view' || $type == 'addatt') readonly @endif>
                    </div>

                    <label class="row-label">提货区间</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" id='pipeline_edit_receivestart' name="receivestart" data-toggle="datepicker" value="{{$receivestart}}" placeholder="开始日期" data-rule="required" readonly>
                            <div class="input-group-addon">至</div>
                            <input type="text" class="form-control" id='pipeline_edit_receiveend' name="receiveend" data-toggle="datepicker" value="{{$receiveend}}" placeholder="结束日期" readonly>
                        </div>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="pipeline_edit_employee_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" @if($type == 'audit' || $type == 'sendback' || $type == 'view' || $type == 'addatt') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="pipeline_edit_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">管线分配</label>
                    <div class="row-input">
                        <select name="ispipelineorder"  data-size="5" data-toggle="selectpicker" data-width="100%" @if($type == 'audit' || $type == 'sendback' || $type == 'view' || $type == 'addatt') disabled @endif>
                            <option value="1" @if($ispipelineorder == 1 || !$ispipelineorder) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">品质检查</label>
                    <div class="row-input">
                        <select name="isqualitycheck" data-size="5" data-toggle="selectpicker" data-width="100%" @if($type == 'audit' || $type == 'sendback' || $type == 'view' || $type == 'addatt') disabled @endif>
                            <option value="1" @if($isqualitycheck == 1  || !$isqualitycheck) selected @endif>是</option>
                            <option value="2" @if($isqualitycheck == 2) selected @endif>否</option>
                        </select>
                    </div>
                    
                </div>
                <br>
            </fieldset>
        <div class="remarks">
            <fieldset>
                <legend>出库明细</legend>
                <div class="table-edit">
                    <table class="table table-bordered" id="pipeline-editdetail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($type != 'audit' && $type != 'sendback' && $type != 'addatt')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#pipeline_detail_tb'),
                            @endif
                            data:{{$detaillist}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            columnResize: false,
                            showTfoot: true,
                            fieldSortable: false,
                            local: 'local'
                            
                        }">
                        <thead>
                        <tr data-options="{name:'stock_sysno'}">
                            <th data-options="{name:'stockin_no',align:'center'}">入库单号</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库数量</th>
                            <th data-options="{name:'introduceqty',align:'center',calc:'sum'}">提单总量</th>
                            <th data-options="{name:'bookingoutqty',align:'center',calc:'sum'}">预提数量(吨)</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'transInstockqty',align:'center',hide:true}">货权转移对应入库单的入库量</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">储罐ID</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                            <th data-options="{name:'storagetankableqty',align:'center',hide:true}">储罐可用容量</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存类型</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <br>
        <div class="remarks">
            <fieldset class="bookoutfieldset">
                <legend>上传附件</legend> 
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookpipeline',action:'pipeline',doc_sysno:'{{$id}}'},
                            @if($docsource == '1' || !$docsource)
                            required: false,
                            @endif
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
<br><br>
            @if($type == 'audit' || $type == 'sendback')
            <div class="remarks">
            <fieldset>
                <legend> @if($bookingoutstatus == 2 && $docsource == 2)驳回意见@elseif($type == 'audit')审核意见@elseif($type == 'sendback')退回意见@endif</legend>
                <form id="pipeline-exam-form" action="/bookout/examJson/" method="POST" class="datagrid-edit-form" >
                    <input type="hidden" name="id" value="{{$id}}">
                    <input type="hidden" name="examstep" id="pipeline_audit_examstep" value="">
                    @if($type == 'sendback')
                    <input type="hidden" name="examidentify" value="back"> 
                    @endif
                    <textarea name="exammarks" id='pipeline_audit_exammarks' data-toggle="autoheight" rows="3" placeholder="请在此处填写@if($bookingoutstatus == 2 && $docsource == 2)驳回意见@elseif($type == 'audit')审核意见@elseif($type == 'sendback')退回意见@endif"></textarea>
                </form>
            </fieldset>
            </div>
            <br><br>
            @endif
            <div class="text-center ">
            @if($bookingoutstatus< 3 && $docsource != 2 && $type != 'view')
                <input type="button" onclick="pipelineSubmit(2)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="pipelineSubmit(4)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif

            @if($bookingoutstatus == 7 &&  $type != 'view')
                <input type="button" onclick="pipelineSubmit(7)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="pipelineSubmit(4)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif

            @if($bookingoutstatus == 2 && $docsource == 2)
                <input type="button" onclick="pipelineSubmit(4)" class="btn btn-green btn-lg" value="提交">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="bocarBack(8)" class="btn btn-red btn-lg">驳回</button>&nbsp;&nbsp;&nbsp;
            @endif
            <!-- 审核 -->
            @if($type == 'audit')
                <input type="button" onclick="pipelineExam(5)" class="btn btn-green btn-lg" value="审核通过">&nbsp;&nbsp;&nbsp;
                <input type="button" onclick="pipelineExam(7)" class="btn btn-red btn-lg" value="审核不通过">&nbsp;&nbsp;&nbsp; 
            @endif
            <!-- 添加附件 -->
            @if($type == 'addatt')
                <input type="button" onclick="pipelineExam('close')" class="btn btn-green btn-lg" value="保存附件">&nbsp;&nbsp;&nbsp;
            @endif
            <!-- 退回 -->
            @if($type == 'sendback')
                <input type="button" onclick="pipelineExam('sendback')" class="btn btn-red btn-lg" value="退回">&nbsp;&nbsp;&nbsp;
            @endif
                <button type="button" onclick="record()" class="btn btn-lg">查看操作记录</button>&nbsp;&nbsp;&nbsp;
            </div>
            <br><br>
            <div class="remarks showhide" style="display: none;">
                <fieldset>
                 <legend>操作记录明细</legend>
                    <div class="addTable">
                    </div>
                </fieldset>
            </div>
            <br><br><br><br><br><br><br><br><br><br>
    </div>
</div>
<br><br>
@if($type != 'audit'&& $type != 'sendback' && $type != 'view' && $type != 'addatt')
<div id="pipeline_detail_tb">
    <button type="button" class="btn btn-blue"  onclick="addPipelineDetail()" data-icon="plus">添加</button>
    <button type="button" class="btn btn-red" onclick="delPipelineDetail()" data-icon="close">删除</button> 
    <button type="button" class="btn btn-green"  onclick="editPipelineDetail()" data-icon="edit">修改</button>
</div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function(){
        if (!{{$id}}) {
            $.ajax({
                url: '/bookout/getbookoutno/prefix/R3',
                type: 'GET',
                dataType: 'json',
                success:function(data){
                    $('#pipeline_edit_bookingoutno').attr({
                        value: data,
                    });

                },
            });
        }

        $("#pipeline_edit_customer_sysno").change(function(){
            var v=$("#pipeline_edit_customer_sysno option:selected");

            $("#pipeline_edit_customername").val(v.text());
            $.CurrentNavtab.find('#pipeline-editdetail-table').datagrid('reload',  {data:[]});
        }); 

        
    });


    function addPipelineDetail() {

        var Obj = $.CurrentNavtab.find('#pipeline-editdetail-table').data('allData');
        var customer_sysno =  $.CurrentNavtab.find('#pipeline_edit_customer_sysno').val();
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'bookpipeline-detail-{{$id}}',
                url:'/bookout/pipelineDetailedit/cid/'+customer_sysno+'/handlestatus/add',
                type:"POST",
                data:{Obj: Obj},
                title:'预约单详情',
                width:900,
                height:550,
                mask:true
            });
        }else{
            BJUI.alertmsg('warn', '请先选中客户再添加出库预约单',{displayPosition:'middlecenter',displayMode:'fade'});
            return ;
        }
    };

    function editPipelineDetail() {
        var selectedDatas  =  $.CurrentNavtab.find("#pipeline-editdetail-table").data('selectedDatas');
        var customer_sysno =  $.CurrentNavtab.find('#pipeline_edit_customer_sysno').val();
        if (selectedDatas == ''||selectedDatas == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var Obj = $.CurrentNavtab.find('#pipeline-editdetail-table').data('allData');
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'bocar-detail-{{$id}}',
                url: '/bookout/pipelineDetailedit/cid/'+customer_sysno+'/handlestatus/edit',
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0],Obj:Obj},
                title:'预约单详情',
                width:900,
                height:600,
                mask:true,
            });
        }else{
            BJUI.alertmsg('warn', '请先选中客户再进行修改',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        return;
        
    }
    
    function delPipelineDetail() {
        var selectdata  =  $.CurrentNavtab.find('#pipeline-editdetail-table').data('selectedDatas');
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var allData  = $("#pipeline-editdetail-table").data('allData');
        for (var i = selectdata.length - 1; i >= 0; i--) {
            allData = allData.remove(selectdata[i].gridIndex);
        }
        $.CurrentNavtab.find('#pipeline-editdetail-table').datagrid('reload',  {data:allData});
    };



    function pipelineSubmit(step) {
        var Obj =$.CurrentNavtab.find('#pipeline-editdetail-table').data('allData');
        $.CurrentNavtab.find('#pipeline_edit_customer_sysno').removeAttr('disabled');
        var receivestart = $.CurrentNavtab.find('#pipeline_edit_receivestart').val();
        var receiveend = $.CurrentNavtab.find('#pipeline_edit_receiveend').val();
        receivestart = Date.parse(new Date(receivestart).toLocaleDateString())/1000 ;
        receiveend = Date.parse(new Date(receiveend).toLocaleDateString())/1000 ;

        if(!receivestart){
            BJUI.alertmsg('warn', '开始时间必须填写',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        if(receivestart > receiveend){
            BJUI.alertmsg('warn', '结束时间不能小于开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        if(Obj.length == 0){
            BJUI.alertmsg('warn', '请填写出库预约明细',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        for (var i = 0; i < Obj.length; i++) {
            if (Obj[i].stockin_sysno == null || Obj[i].storagetankname == null || Obj[i].goodsname == null || Obj[i].qualityname == null || Obj[i].goodsnature == null || Obj[i].bookingoutqty == null) {
                BJUI.alertmsg('warn', '请先补全出库预约明细信息',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }

        for (var i = Obj.length - 1; i >= 1; i--) {
            if($.trim(Obj[i].goodsname)!=$.trim(Obj[0].goodsname)||$.trim(Obj[i].qualityname)!=$.trim(Obj[0].qualityname)){
                BJUI.alertmsg('warn', "只能添加一种货品的同一规格",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if($.trim(Obj[i].stocktype)==$.trim(Obj[0].stocktype) && $.trim(Obj[i].stock_sysno)==$.trim(Obj[0].stock_sysno)){
                BJUI.alertmsg('warn', "同一批次货物只能添加一条明细",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        };

        $("#pipeline_edit_bookingoutstatus").val(step);
        $("#pipeline_edit_customername").val($("#pipeline_edit_customer_sysno option:selected").text());
        $("#pipeline_edit_employeename").val($("#pipeline_edit_employee_sysno option:selected").text());
        $.CurrentNavtab.find('#pipeline_edit_docsource').removeAttr("disabled");
        $("#pipeline_edit_detaildata").val(JSON.stringify(Obj));

        submitForm();
        /*
        $.ajax({
            url: '/bookout/controlgoods',
            type: 'POST',
            dataType: 'json',
            data: {obj: Obj},
            success:function(data){
                if (data.code==200) {
                    var controlgoodsInfo = '';
                    if(data.message[1] != 0 || data.message[2] !=0 ||data.message[3] !=0){
                        if(data.message[1] != 0){
                                controlgoodsInfo +=  ' 欠费超信用期限'+data.message[1]+'天;';
                            }
                            if(data.message[2] != 0){
                                controlgoodsInfo +=  ' 欠费超信用额度'+data.message[2]+';' ;
                            }
                            if(data.message[3] != 0){
                                controlgoodsInfo += ' 欠费超控货比重'+data.message[3]+';';
                            }
                        BJUI.alertmsg('confirm', controlgoodsInfo , {okCall:function() {
                            submitForm();
                        }
                        });
                    }else{
                        submitForm();
                    }
                }else if (data.code==300) {
                    BJUI.alertmsg('confirm','控货异常',{okCall:function(){
                        submitForm();
                    }})
                }
            },
        });  
        */   
    };

    function submitForm(){
        //提交表单
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#pipeline-edit-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab507,navab506');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function pipelineExam(step) {
        if(step == 'close'){
            BJUI.navtab('closeCurrentTab', '');
            return;
        }
        var ca_address = $('#pipeline_edit_ca_address').val();
        $.CurrentNavtab.find('#pipeline_edit_docsource').removeAttr("disabled");
        var docsource = $('#pipeline_edit_docsource').val();
        var msg = '';
        if (step == 5) {
            $.CurrentNavtab.find("#pipeline_audit_examstep").val(5);
        }else{
            $.CurrentNavtab.find("#pipeline_audit_examstep").val(7);
        }
        if (!$('#pipeline_audit_exammarks').val() && step != 5) {
            if(step == 7){
                msg = '请先填写审核意见！';
            }
            if (step == 'sendback') {
                msg = '请填写退回意见！';
                step = 7;
            }
            BJUI.alertmsg('warn', msg,{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        
        
        if(ca_address != '' && docsource == '2'){
            BJUI.setRegional('progressmsg', 'CA读取中……');
        }



        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#pipeline-exam-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag','navab506,navab507,navab520');
                BJUI.navtab('closeCurrentTab', '');  
            }
        });
    }


/*--------------------------操作记录优化-----------------------------*/
var jl=0;
 function record() {

    if(jl==0){
         addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '24');
    }

    jl++;
       
    $.CurrentNavtab.find('.showhide').toggle(500);

}


</script>