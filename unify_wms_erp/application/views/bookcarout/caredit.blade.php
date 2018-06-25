<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
        <br><br>
        <form id="bocar-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" id='bocar_edit_id' name="id" value="{{$id}}">
            <input type="hidden" name="firstfrom_sysno" value="{{$firstfrom_sysno}}">
            <input type="hidden" name="firstfrom_no" value="{{$firstfrom_no}}">
            <input type="hidden" name="bookingoutfather_sysno" value="{{$bookingoutfather_sysno}}">
            <input type="hidden" name="ca_url" value="{{$ca_address}}" id="bocar_edit_ca_url">
            <input type="hidden" id="bocar_edit_detaildata" name="detaildata" value="">
            <input type="hidden" id="bocar_edit_cardata" name="cardata" value="">
            <input type="hidden" id="bocar_edit_bookingoutstatus" name="bookingoutstatus" value="@if($bookingoutstatus) {{$bookingoutstatus}} @else {{2}} @endif" >
            <fieldset>
                <legend>车出库预约单</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">预约单号</label>
                    <div class="row-input">
                            <input type="text" name="bookingoutno" id="bocar_edit_bookingoutno" value="{{$bookingoutno or ''}}" readonly>
                    </div>

                    <label class="row-label">预约日期</label>
                    <div class="row-input">
                        <input type="text" name="bookingoutdate" id="bocar_edit_bookingoutdate" data-toggle="datepicker" value="@if($bookingoutdate) {{ $bookingoutdate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>

                    <label class="row-label">操作状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" disabled>
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="customer_sysno" id="bocar_edit_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" @if(!$istakeapart && $type != 'view' && $type != 'addatt' && $type != 'audit') onchange="showCustomerSample(this.value)" @else disabled @endif >
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="bocar_edit_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <select name="docsource" id='caredit_docsource' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="1" @if(!$docsource || $docsource == 1) selected @endif>手工创建</option>
                            <option value="2" @if($docsource == 2) selected @endif>国烨云仓</option>
                        </select>
                    </div>

                    <label class="row-label">提货单位</label>
                    <div class="row-input required">
                        <input type="text" name="receiveunitname" value="{{$receiveunitname}}" data-rule="required" @if($type == 'view' || $type == 'addatt' || $type == 'audit') readonly @endif>
                    </div>

                    <label class="row-label">提货单号</label>
                    <div class="row-input required">
                        <input type="text" name="receivenumber" id="bocar_edit_receivenumber" value="{{$receivenumber or ''}}" data-rule='required' @if($type == 'view' || $type == 'addatt' || $type == 'audit') readonly @endif>
                    </div>

                    <label class="row-label">提货区间</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" id='bocar_edit_receivestart' name="receivestart" data-toggle="datepicker" value="{{$receivestart}}" placeholder="开始日期" data-rule="required" readonly>
                            <div class="input-group-addon">至</div>
                            <input type="text" class="form-control" id='bocar_edit_receiveend' name="receiveend" data-toggle="datepicker" value="{{$receiveend}}" placeholder="结束日期" readonly>
                        </div>
                    </div>

                    <label class="row-label">是否逾期</label>
                    <div class="row-input">
                        <input type="text" id='bocar_edit_receiveover' name="receiveover" value="{{$receiveover}}" readonly>
                    </div>
                </div>
                <br>
            </fieldset>
        <div class="remarks">
            <fieldset>
                <legend>出库明细</legend>
                <div class="table-edit">
                    <table class="table table-bordered" id="bocar-editdetail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($type != 'view' && $type != 'addatt' && $type != 'audit')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#custom_bocar_detail_tb'),
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
                            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'inshipname',align:'center'}">船名</th>
                            <th data-options="{name:'instockqty',align:'center'}">入库数量</th>
                            <th data-options="{name:'introduceqty',align:'center'}">提单总量</th>
                            <th data-options="{name:'bookingoutqty',align:'center',calc:'sum'}">预提数量</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'quoteNum',align:'center',hide:true}">子级预约单总预约量</th>
                            <th data-options="{name:'firstfrom_sysno',align:'center',hide:true}">顶点入库单号ID</th>
                            <th data-options="{name:'transInstockqty',align:'center',hide:true}">货权转移对应入库单的入库量</th>
                            <th data-options="{name:'storagetankableqty',align:'center',hide:true}">储罐可用容量</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">罐号ID</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">货物规格ID</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存类型</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <br>
        <div class="remarks">
            <fieldset>
                <legend>出库车辆信息</legend>
                <div class="table-edit">
                    <table class="table table-bordered" id="bocar-editcar-table" data-toggle="datagrid" data-options="{
                                        include: 'carid,carname,mobilephone,weight,carmarks',
                                        filterThead:false,
                                        @if($type != 'view' && $type != 'addatt' && $type != 'audit')
                                        showToolbar: true,
                                        toolbarCustom:$.CurrentNavtab.find('#custom_bocar_car_tb'),
                                        @endif
                                        data:{{$carlist}},
                                        paging: false,
                                        linenumberAll: true,
                                        fullGrid:true,
                                        fieldSortable: false,
                                        local: 'local'
                                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'carid',align:'center'}">车牌号</th>
                            <th data-options="{name:'carname',align:'center'}">司机</th>
                            <th data-options="{name:'mobilephone',align:'center'}">手机号</th>
                            <th data-options="{name:'idcard',align:'center'}">身份证</th>
                            <th data-options="{name:'cartakeqty',align:'center'}">预提货数量(吨)</th>
                            <th data-options="{name:'carmarks',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <br>
        <div class="remarks">
            <fieldset>
                <legend>提货单样张</legend>
                <div class="bjui-row col-4"   id="uploader">
                    <ul class="filelist picthuild" id="bocar_sample_div">
                        @if( is_array($samples) &&  count($samples) > 0)
                            @foreach($samples as $sample)
                                 <li class="uploaded" >
                                    <p class="imgWrap" style="cursor:pointer;" data-toggle="dialog" data-options="{id:'bjui-dialog-view-upload-image', image:'/attachment/preview/id/{{$sample['sysno']}}', width:800, height:500, mask:true, title:'查看图片'}">
                                        <img src="/attachment/preview/id/{{$sample['sysno']}}">
                                    </p>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </fieldset>
        </div>
        
        <div class="remarks">
            <fieldset class="bookoutfieldset">
                <legend>@if($type != 'view')上传提货单@else提货单@endif</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookout',action:'car',doc_sysno:'{{$id}}'},
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
            @if($bookingoutstatus == 2 && $docsource == 2)
            <div class="remarks">
                <fieldset>
                    <legend>驳回意见</legend>
                    <form id="bocarExam-exam-form" action="/bookout/examJson/" method="POST" class="datagrid-edit-form" >
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="examstep" id="bocar_back_examstep" value=""> 
                        <textarea name="exammarks" id='bocar_back_exammarks' data-toggle="autoheight" rows="3" placeholder="请在此处填写驳回意见"></textarea>
                    </form>
                </fieldset>
            </div>
            <br><br>
            @endif
            @if($type == 'audit')
            <div class="remarks">
                <fieldset>
                    <legend>审核意见</legend>
                    <form id="bocarExam-exam-form" action="/bookout/examJson/" method="POST" class="datagrid-edit-form" >
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="examstep" id="bocar_back_examstep" value=""> 
                        <textarea name="exammarks" id='bocar_back_exammarks' data-toggle="autoheight" rows="3" placeholder="请在此处填写审核意见"></textarea>
                    </form>
                </fieldset>
            </div>
            @endif
            <div class="text-center ">
            @if($type != 'view' && $type != 'addatt')
                <!-- 暂存/提交 -->
                @if(($bookingoutstatus < 3 || $bookingoutstatus == 7) && $docsource != 2 )
                    <input type="button" onclick="bocarSubmit(2)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="bocarSubmit(4)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp; 
                @endif
                <!-- 审核通过/不通过 -->
                @if($type == 'audit')
                    <button type="button" onclick="bocarExam(5)" class="btn btn-green btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="bocarExam(7)" class="btn btn-red btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @endif
                <!-- 云仓提交/驳回 -->
                @if($bookingoutstatus == 2 && $docsource == 2)
                    <input type="button" onclick="bocarSubmit(4)" class="btn btn-green btn-lg" value="提交">&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="bocarExam(8)" class="btn btn-red btn-lg">驳回</button>&nbsp;&nbsp;&nbsp;  
                @endif
            @endif
                @if($type == 'view' && $docsource == 2 && $ca_address)
                    <button type="button" onclick="showCA()" class="btn btn-lg btn-blue">查看合同</button>&nbsp;&nbsp;&nbsp;
                @endif
                <!-- 添加附件 -->
                @if($type == 'addatt')
                    <input type="button" onclick="bocarExam('close')" class="btn btn-green btn-lg" value="保存附件">&nbsp;&nbsp;&nbsp;
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
            <br><br>
    </div>
</div>
<br><br>
@if($type != 'view' && $type != 'addatt' && $type != 'audit')
<div id="custom_bocar_detail_tb">
    <button type="button" class="btn btn-blue" onclick="addBocarDetail()" data-icon="plus">添加</button>
    <button type="button" class="btn btn-red" onclick="subBocarDetail()" data-icon="close">删除</button>
    <button type="button" class="btn btn-green" onclick="editBocarDetail()" data-icon="edit">修改</button>
</div>
<div id="custom_bocar_car_tb">
    <button type="button" class="btn btn-blue"  onclick="addBocarCar()" data-icon="plus">添加</button>
    <button type="button" class="btn btn-red" onclick="subBocarCar()" data-icon="close">删除</button> 
    <button type="button" class="btn btn-green"  onclick="editBocarCar()" data-icon="edit">修改</button>
</div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function(){
        if (!{{$id}}) {
            $.ajax({
                url: '/bookout/getbookoutno/prefix/C1',
                type: 'GET',
                dataType: 'json',
                success:function(data){
                    $('#bocar_edit_bookingoutno').attr({
                        value: data,
                    });

                },
            });
        }

        $("#bocar_edit_customer_sysno").change(function(){
            var v=$("#bocar_edit_customer_sysno option:selected");

            $("#bocar_edit_customername").val(v.text());
            $.CurrentNavtab.find('#bocar-editdetail-table').datagrid('reload',  {data:[]});
        }); 

        
    });

    $.CurrentNavtab.find('#bocar_edit_receiveend').blur(function(event) {
            var receiveend = $.trim($.CurrentNavtab.find('#bocar_edit_receiveend').val());
            receiveend = Date.parse(new Date(receiveend).toLocaleDateString())/1000;
            var timestamp = new Date();
            timestamp = Date.parse(timestamp.toLocaleDateString())/1000;

            if(receiveend < timestamp){
                $.CurrentNavtab.find('#bocar_edit_receiveover').val('是');
            }else{
                $.CurrentNavtab.find('#bocar_edit_receiveover').val('否');
            }
        });

    function showCustomerSample(cid) {

        if(cid=='')
            return;
        BJUI.ajax('doajax', {
            url: '/bookout/customerSampleJson/',
            data:{cid: cid },
            loadingmask: false,
            okCallback: function(json, options) {
                $.CurrentNavtab.find('#bocar_sample_div').empty();
                for(i = 0; i < json.length; i ++){
                    var obj = json[i];
                    var uploadedAttr = ' style="cursor:pointer;" data-toggle="dialog" data-options="{id:\'bjui-dialog-view-upload-image\', image:\''+ '/attachment/preview/id/'+ obj.sysno +'\', width:800, height:500, mask:true, title:\'查看图片\'}"' ,
                            li = $('<li class="uploaded" >' +
                                    '<p class="imgWrap" '+ uploadedAttr +'>' +
                                    '<img src="/attachment/preview/id/'+obj.sysno +'">' +
                                    '</p>'+
                                    '</li>');

                    $.CurrentNavtab.find('#bocar_sample_div').append(li);
                }
            }
        })
    };

    function addBocarDetail() {

        var Obj = $.CurrentNavtab.find('#bocar-editdetail-table').data('allData');
        var customer_sysno =  $.CurrentNavtab.find('#bocar_edit_customer_sysno').val();
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'bocar-detail-{{$id}}',
                url:'/bookout/bocardetailedit/cid/'+customer_sysno+'/handlestatus/add',
                type:"POST",
                data:{Obj: Obj},
                title:'预约单详情',
                width:900,
                height:600,
                mask:true
            });
        }else{
            BJUI.alertmsg('warn', '请先选中客户再添加出库预约单',{displayPosition:'middlecenter',displayMode:'fade'});
            return ;
        }
    };

    function editBocarDetail() {
        var selectedDatas  =  $.CurrentNavtab.find("#bocar-editdetail-table").data('selectedDatas');
        var customer_sysno =  $.CurrentNavtab.find('#bocar_edit_customer_sysno').val();
        if (selectedDatas == ''||selectedDatas == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var Obj = $.CurrentNavtab.find('#bocar-editdetail-table').data('allData');

        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'bocar-detail-{{$id}}',
                url: '/bookout/bocardetailedit/cid/'+customer_sysno+'/handlestatus/edit',
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
    
    function subBocarDetail() {
        var selectdata  =  $.CurrentNavtab.find('#bocar-editdetail-table').data('selectedDatas');
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var allData  = $("#bocar-editdetail-table").data('allData');
        for (var i = selectdata.length - 1; i >= 0; i--) {
            allData = allData.remove(selectdata[i].gridIndex);
        }
        $.CurrentNavtab.find('#bocar-editdetail-table').datagrid('reload',  {data:allData});
    };

    function addBocarCar() {

        BJUI.dialog({
            id:'bocar-car-{{$id}}',
            url:'/bookout/bocarcaredit/handlestatus/add',
            title:'车辆信息',
            width:800,
            height:480,
            mask:true

        });

        return;
    };
    function editBocarCar() {
        var selectedDatas  =  $.CurrentNavtab.find('#bocar-editcar-table').data('selectedDatas');
        if (selectedDatas == '' || selectedDatas == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.dialog({
            id:'bocar-car-{{$id}}',
            url:'/bookout/bocarcaredit/handlestatus/edit',
            type:'POST',
            data:{selectedDatasArray:selectedDatas[0]},
            title:'车辆信息',
            width:800,
            height:480,
            mask:true,
        });

    };

    function subBocarCar() {
        var selectdata  =  $.CurrentNavtab.find('#bocar-editcar-table').data('selectedDatas');

        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var allData  = $("#bocar-editcar-table").data('allData');
        for (var i = selectdata.length - 1; i >= 0; i--) {
            allData = allData.remove(selectdata[i].gridIndex);
        }
        $.CurrentNavtab.find('#bocar-editcar-table').datagrid('reload',  {data:allData});

    };

    function bocarSubmit(step) {
        var Obj =$.CurrentNavtab.find('#bocar-editdetail-table').data('allData');
        $.CurrentNavtab.find('#bocar_edit_customer_sysno').removeAttr('disabled');
        var receivestart = $.CurrentNavtab.find('#bocar_edit_receivestart').val();
        var receiveend = $.CurrentNavtab.find('#bocar_edit_receiveend').val();
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
            if (Obj[i].stockin_sysno == null || Obj[i].storagetankname == null || Obj[i].goodsname == null || Obj[i].qualityname == null || Obj[i].goodsnature == null || Obj[i].unitname == null || Obj[i].instockqty == null || Obj[i].bookingoutqty == null) {
                BJUI.alertmsg('warn', '请先补全出库预约明细信息',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }

        for (var i = Obj.length - 1; i >= 1; i--) {
            if($.trim(Obj[i].goodsname)!=$.trim(Obj[0].goodsname)||$.trim(Obj[i].qualityname)!=$.trim(Obj[0].qualityname)){
                BJUI.alertmsg('warn', "只能添加一种货品的同一规格",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if($.trim(Obj[i].stocktype)==$.trim(Obj[0].stocktype) && $.trim(Obj[i].stock_sysno)==$.trim(Obj[0].stock_sysno) ){
                BJUI.alertmsg('warn', "同一批次货物只能添加一条明细",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        };

        var cartableData =$.CurrentNavtab.find('#bocar-editcar-table').data('allData');
        if(cartableData.length == 0){
            BJUI.alertmsg('warn', '请填写出库车辆信息',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        $("#bocar_edit_bookingoutstatus").val(step);
        $("#bocar_edit_customername").val($("#bocar_edit_customer_sysno option:selected").text());
        $("#bocar_employeename").val($("#bocar_employee_sysno option:selected").text());
        $.CurrentNavtab.find('#caredit_docsource').removeAttr("disabled");
        $("#bocar_edit_detaildata").val(JSON.stringify(Obj));

        var carObj = $.CurrentNavtab.find('#bocar-editcar-table').data('allData');
        if(typeof  carObj != 'undefined'){
            $("#bocar_edit_cardata").val(JSON.stringify(carObj));
        }

        var docsource = $("#caredit_docsource option:selected").val();
        submitCarEditForm();
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

    function submitCarEditForm(){
        //提交表单
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#bocar-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab231,navab446');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function bocarExam(step) {
        var ca_url = $('#bocar_edit_ca_url').val();
        var docsource = $('#caredit_docsource').val();

        if(step == 'close'){
            BJUI.navtab('closeCurrentTab', '');
            return;
        }
        if (step == 7 && !$('#bocar_back_exammarks').val()) {
            BJUI.alertmsg('warn', '请先填写审核意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        if (step == 8 && !$('#bocar_back_exammarks').val()) {
            BJUI.alertmsg('warn', '请先填写驳回意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        if(ca_url != '' && docsource == '2'){
            BJUI.setRegional('progressmsg', 'CA读取中……');
        }

        $.CurrentNavtab.find("#bocar_back_examstep").val(step);
        var cardata =$.CurrentNavtab.find('#bocar-editcar-table').data('allData');
        BJUI.ajax('ajaxform', {
            url:$.CurrentNavtab.find('#bocarExam-exam-form').attr('action'),
            form: $.CurrentNavtab.find('#bocarExam-exam-form'),
            validate: false,
            loadingmask: true,
            okCallback: function(json, options) {
                if(step == 5){
                    updateCarinfo(cardata);
                }
                BJUI.navtab('reloadFlag','navab231,navab446,navab276');
                BJUI.navtab('closeCurrentTab', '');
                  
            }
        });
    }
    //更新车辆信息
    function updateCarinfo(cardata) {
        var cardata = cardata;
        BJUI.ajax('doajax',{
            url:"/supplier/updateCarinfo",
            type:'POST',
            data:{cardata,cardata},
        });
    }
    //查看CA
    function showCA() {
        var url = $('#bocar_edit_ca_url').val();
        if(url != '' && url != null){
            window.open(url);
        }    
    }

/*--------------------------操作记录优化-----------------------------*/
var jl=0;
 function record() {

    if(jl==0){

        console.log(jl);

         addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '2');
    }

    jl++;
       
    $.CurrentNavtab.find('.showhide').toggle(500);

}


</script>