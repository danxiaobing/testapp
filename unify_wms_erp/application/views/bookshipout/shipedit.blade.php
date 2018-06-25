<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
       <br><br>
        <form id="boship-edit-form" action="{{$action}}" method="POST" class="datagrid-edit-form" @if($bookingoutstatus==1 || $bookingoutstatus==2)data-toggle="validate" @endif data-data-type="json">
            <input type="hidden" id='boship_edit_id' name="id" value="{{$id}}">
            <input type="hidden" id="boship_edit_detaildata" name="detaildata" value="">
            <input type="hidden" name="ca_url" value="{{$ca_address}}" id="boship_edit_ca_url">
            <input type="hidden" id="boship_edit_bookingoutstatus" name="bookingoutstatus" value="@if($bookingoutstatus) {{$bookingoutstatus}} @else {{2}} @endif" >
            <fieldset>
                <legend>船出库预约单</legend>
                <br>

                <div class="bjui-row col-3">
                    <label class="row-label">预约单号</label>
                    <div class="row-input">
                            <input id="boship_edit_bookingoutno" type="text" name="bookingoutno" value="@if($bookingoutno) {{$bookingoutno}}  @endif" readonly>
                    </div>

                    <label class="row-label">预约日期</label>
                    <div class="row-input">
                        <input type="text" id='boship_edit_bookingoutdate' name="bookingoutdate" data-toggle="datepicker" value="@if($bookingoutdate) {{ $bookingoutdate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>

                    <label class="row-label">操作状态</label>
                    <div class="row-input">
                        <input type="text" id='boship_edit_status' name="status" value="{{ $status }}" readonly>
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="customer_sysno" id="boship_edit_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" onchange="showCustomerSample(this.value)" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="boship_edit_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="boship_edit_employee_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="boship_edit_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">单据来源</label>
                    <div class="row-input">
                        <select name="docsource" id='shipedit_docsource' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="1" @if(!$docsource || $docsource == 1) selected @endif>手工创建</option>
                            <option value="2" @if($docsource == 2) selected @endif>国烨云仓</option>
                        </select>
                    </div>
                    
                    <label class="row-label">商检单位</label>
                    <div class="row-input">
                        <input type="text" name="businesscheckunitname" value="{{$businesscheckunitname}}" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') readonly @endif>
                    </div>

                    <label class="row-label">船舶代理</label>
                    <div class="row-input">
                        <input type="text" name="shipproxyname" value="{{$shipproxyname}}" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') readonly @endif>
                    </div>

                    <label class="row-label">提货单号</label>
                    <div class="row-input required">
                        <input type="text" id="boship_edit_receivenumber" name="receivenumber" value="{{$receivenumber}}"  data-rule="required" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') readonly @endif>
                    </div>
                    
                    <label class="row-label">提货单位</label>
                    <div class="row-input required">
                        <input type="text" id="boship_edit_receiveunitname" name="receiveunitname" value="{{$receiveunitname}}"  data-rule="required" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') readonly @endif>
                    </div>

                    <label class="row-label">提货区间</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" id='boship_edit_receivestart' name="receivestart" data-toggle="datepicker" value="{{$receivestart}}" placeholder="开始日期" data-rule="required" readonly>
                            <div class="input-group-addon">至</div>
                            <input type="text" class="form-control" id='boship_edit_receiveend' name="receiveend" data-toggle="datepicker" value="{{$receiveend}}" placeholder="结束日期" readonly>
                        </div>
                    </div>

                    <label class="row-label">是否逾期</label>
                    <div class="row-input">
                        <input type="text" id='boship_edit_receiveover' name="receiveover" value="{{$receiveover}}" readonly>
                    </div>
                    
                    <label class="row-label">管线分配</label>
                    <div class="row-input">
                        <select name="ispipelineorder"  data-size="5" data-toggle="selectpicker" data-width="100%" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') disabled @endif>
                            <option value="1" @if($ispipelineorder == 1 || !$ispipelineorder) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">泊位分配</label>
                    <div class="row-input">
                        <select name="isberthorder"  data-size="5" data-toggle="selectpicker" data-width="100%" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') disabled @endif>
                            <option value="1"  @if($isberthorder == 1 || !$isberthorder) selected @endif>是</option>
                            <option value="2" @if($isberthorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">品质检查</label>
                    <div class="row-input">
                        <select name="isqualitycheck" data-size="5" data-toggle="selectpicker" data-width="100%" @if($type == 'audit' || $type == 'view' || $type == 'addatt' || $type == 'sendback') disabled @endif>
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
                       <table class="table table-bordered" id="boship-edit-detail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($type != 'audit' && $type != 'view' && $type != 'addatt' && $type != 'sendback')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#custom_boship_edit_detail_tb'),
                            @endif
                            data:{{$detaillist}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showCheckboxcol: false,
                            columnResize: false,
                            local: 'local',
                            fieldSortable: false,
                            showTfoot: true,


                        }">
                        <thead>
                        <tr data-options="{name:'stock_sysno'}">
                            <th data-options="{name:'stockin_no',align:'center'}">入库单号</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                            <th data-options="{name:'unitname',align:'center'}">计量单位</th> 
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'instockqty',align:'center'}">入库数量</th>
                            <th data-options="{name:'introduceqty',align:'center'}">提单总量</th>
                            <th data-options="{name:'bookingoutqty',align:'center',calc:'sum'}">提货数量</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'shipokdate',align:'center'}">预计到港日期</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">罐号ID</th>
                            <th data-options="{name:'storagetankableqty',align:'center',hide:true}">储罐可用容量</th>
                            <th data-options="{name:'firstfrom_sysno',align:'center',hide:true}">顶点入库单号ID</th>
                            <th data-options="{name:'transInstockqty',align:'center',hide:true}">货权转移对应入库单的入库量</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">货物规格ID</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存类型</th>
                        </tr>
                        </thead>
                    </table>
                    </div>
                </fieldset>
            </div>

                <div class="remarks">
                    <fieldset>
                        <legend>提货单样张</legend>
                        <div class="bjui-row col-4">
                            <ul class="filelist picthuild" id="boship_sample_div">
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
            @if($bookingoutstatus <=4 || $bookingoutstatus == 7 || $bookingoutstatus == 5)
                <div>
                <!-- 用来规避样式问题 start -->
                <fieldset class="customerfieldset" style="display: none">
                   <legend>上传提货单<span style="color: red;">*</span></legend>
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookout',action:'ship_uploader',doc_sysno:'{{$id}}'},
                            required: false,
                            uploaded: '{{ $uploaded1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf,txt',
                                mimeTypes: '.jpg,.png,.pdf,.txt'
                            }
                        }">
                </fieldset>
                </div>
                <!-- end -->
                <div class="comuser-add-left ">
                    <fieldset class="customerfieldset" id="uploader1">
                       <legend>@if($type != 'audit' && $type != 'view' && $type != 'sendback')上传提货单@else提货单@endif</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                            {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'bookout',action:'ship_uploader',doc_sysno:'{{$id}}'},
                                @if($docsource == '1'  || !$docsource)
                                required: false,
                                @endif
                                uploaded: '{{ $uploaded1 }}',
                                basePath: '/attachment/preview/id/',
                                deletePath:'/attachment/deljson/',
                                accept: {
                                    title: '图片',
                                    extensions: 'jpg,png,pdf,txt',
                                    mimeTypes: '.jpg,.png,.pdf,.txt'
                                }
                            }">
                    </fieldset>
                </div>
            @endif
        </form>
        <br><br>
        @if($bookingoutstatus == 2 && $docsource == 2)
        <div class="remarks">
            <fieldset>
                <legend>驳回意见</legend>
                <form id="boship-audit-form" action="/bookout/examJson" method="POST" class="datagrid-edit-form" >
                    <input type="hidden" name="id" value="{{$id}}">
                    <input type="hidden" name="examstep" id="boship_edit_examstep" value="">
                        <textarea name="exammarks" id='boship_edit_exammarks' data-toggle="autoheight" rows="3" placeholder="请在此处填写驳回意见"></textarea>
                </form>
            </fieldset>
        </div>
        <br><br>
        @endif
        @if($type == 'audit' || $type == 'sendback')
        <div class="remarks">
            <fieldset style="clear: both;">
                <legend>@if($type == 'audit')审核意见@elseif($type == 'sendback')退回意见@endif</legend>
                <form id="boship-audit-form" action="/bookout/examJson" method="POST" class="datagrid-edit-form" >
                    <input type="hidden" name="id" value="{{$id}}">
                    <input type="hidden" name="examstep" id="boship_edit_examstep" value="">
                    @if($type == 'sendback')
                    <input type="hidden" name="examidentify" value="back">
                    @endif
                    <textarea id="boship_edit_exammarks" name="exammarks" data-toggle="autoheight" rows="3" placeholder="请在此处填写@if($type == 'audit')审核@elseif($type == 'sendback')退回@endif意见" ></textarea>
                </form>
            </fieldset>
        </div>
        <br><br>
        @endif
        <div style="clear: both;">
            <div class="text-center">
            @if($bookingoutstatus< 3 && $docsource != 2 && $type != 'view' )
                <input type="button" onclick="boshipSubmit(2)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="boshipSubmit(4)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($bookingoutstatus==7 && $type != 'view')
                <input type="button" onclick="boshipSubmit(7)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="boshipSubmit(4)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($type == 'audit')
                <button type="button" onclick="boshipExam(5)" class="btn btn-green btn-lg">审核通过</button>
                <button type="button" onclick="boshipExam(7)" class="btn btn-red btn-lg">审核不通过</button>      
            @endif
            @if($type == 'sendback')
                <input type="button" onclick="boshipExam(7)" class="btn btn-red btn-lg" value="退回">
                <input type="button" onclick="boshipExam('close')" class="btn btn-green btn-lg" value="取消">
            @endif
            @if($type == 'addatt')
                <button type="button" onclick="boshipExam('close')" class="btn btn-green btn-lg">保存附件</button>
            @endif
            @if($bookingoutstatus == 2 && $docsource == 2 && $type != 'view')
                <input type="button" onclick="boshipSubmit(4)" class="btn btn-green btn-lg" value="提交">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="boshipExam(8)" class="btn btn-red btn-lg">驳回</button>&nbsp;&nbsp;&nbsp; 
            @endif
            @if($docsource == 2 && $ca_address)
                <button type="button" onclick="showCA()" class="btn btn-lg btn-blue">查看合同</button>&nbsp;&nbsp;&nbsp;
            @endif
                <button type="button" onclick="record()" class="btn btn-lg">查看操作记录</button>&nbsp;&nbsp;&nbsp;
            </div>
        </div>
        <br><br>
        <div class="remarks showhide" style="display: none;">
            <fieldset>
             <legend>操作记录明细</legend>
                <div class="addTable">
                </div>
            </fieldset>
        </div>
        <br><br> <br><br>
   </div>
</div>
@if($back != 'back' && $type != 'audit' && $type != 'view' && $type != 'addatt' && $type != 'sendback')
<div id="custom_boship_edit_detail_tb">
    <button type="button" class="btn btn-blue" onclick="addBoshipDetail()" data-icon="plus">添加</button>
    <button type="button" class="btn btn-red" onclick="subBoshipDetail()" data-icon="close">删除</button>
    <button type="button" class="btn btn-green" onclick="editBoshipDetail()" data-icon="edit">修改</button>
</div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function(){
        if (!{{$id}}) {
            $.ajax({
                url: '/bookout/getbookoutno/prefix/D1',
                type: 'GET',
                dataType: 'json',
                success:function(data){
                    $('#boship_edit_bookingoutno').val(data);
                },
            });
        } 

        $("#boship_edit_customer_sysno").change(function(){
            var v=$("#boship_edit_customer_sysno option:selected");

            $("#boship_edit_customername").val(v.text());
            var selectdata  =  $.CurrentNavtab.find('#boship-edit-detail-table').data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                $.CurrentNavtab.find('#boship-edit-detail-table').datagrid('delAllRows', i );
            }
        });
        
    });

    function showCustomerSample(cid) {

        if(cid=='')
            return;
        BJUI.ajax('doajax', {
            url: '/bookout/customerSampleJson/',
            data:{cid: cid },
            loadingmask: false,
            okCallback: function(json, options) {
                
                $.CurrentNavtab.find('#boship_sample_div').empty();
                for(i = 0; i < json.length; i ++){
                    var obj = json[i];
                    var uploadedAttr = ' style="cursor:pointer;" data-toggle="dialog" data-options="{id:\'bjui-dialog-view-upload-image\', image:\''+ '/attachment/preview/id/'+ obj.sysno +'\', width:800, height:500, mask:true, title:\'查看图片\'}"' ,
                            li = $('<li class="uploaded" >' +
                                    '<p class="imgWrap" '+ uploadedAttr +'>' +
                                    '<img src="/attachment/preview/id/'+obj.sysno +'">' +
                                    '</p>'+
                                    '</li>');

                    $.CurrentNavtab.find('#boship_sample_div').append(li);
                }
            }
        })
    }

        $.CurrentNavtab.find('#boship_edit_receiveend').blur(function(event) {
            var receiveend = $.trim($.CurrentNavtab.find('#boship_edit_receiveend').val());
            receiveend = Date.parse(new Date(receiveend).toLocaleDateString())/1000;
            var timestamp = new Date();
            timestamp = Date.parse(timestamp.toLocaleDateString())/1000;

            if(receiveend < timestamp){
                $.CurrentNavtab.find('#boship_edit_receiveover').val('是');
            }else{
                $.CurrentNavtab.find('#boship_edit_receiveover').val('否');
            }
        });
        


    function addBoshipDetail() {

        var customer_sysno =  $.CurrentNavtab.find('#boship_edit_customer_sysno').val();
        var Obj = $.CurrentNavtab.find('#boship-edit-detail-table').data('allData');
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'boship-detail-{{$id}}',
                url:'/bookout/boshipdetailedit/cid/'+customer_sysno+'/handlestatus/add',
                type:"POST",
                data:{Obj: Obj},
                title:'预约单详情',
                width:900,
                height:600,
                mask:true,

            });
        }else{
            BJUI.alertmsg('warn', '请先选择客户再添加',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        return;
    }

    function editBoshipDetail() {
        var selectedDatas  =  $.CurrentNavtab.find("#boship-edit-detail-table").data('selectedDatas');
        if (selectedDatas == '' || selectedDatas == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var Obj = $.CurrentNavtab.find('#boship-edit-detail-table').data('allData');
        // var bookoutqty = 0;
        // if(Obj.length > 1){
        //     for (var i = 0; i < Obj.length; i++) {
        //         bookoutqty += parseFloat(Obj[i]['bookingoutqty']);
        //     }
        //     bookoutqty = bookoutqty-selectedDatas[0]['bookingoutqty'];
        // }
 
        var customer_sysno =  $.CurrentNavtab.find('#boship_edit_customer_sysno').val();
   
        BJUI.dialog({
            id:'boship-detail-{{$id}}',
            url:'/bookout/boshipdetailedit/cid/'+customer_sysno+'/handlestatus/edit',
            type:'POST',
            data:{selectedDatasArray:selectedDatas[0],Obj:Obj},
            title:'预约单详情',
            width:900,
            height:600,
            mask:true,
        });
    }

    function subBoshipDetail() {
        var selectdata  =  $.CurrentNavtab.find('#boship-edit-detail-table').data('selectedDatas');
        if (selectdata == '' || selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var allData  = $("#boship-edit-detail-table").data('allData');
        for (var i = selectdata.length - 1; i >= 0; i--) {
            allData = allData.remove(selectdata[i].gridIndex);
        }
        $.CurrentNavtab.find('#boship-edit-detail-table').datagrid('reload',  {data:allData});
    }

    function boshipSubmit(step) {
        var Obj = $.CurrentNavtab.find('#boship-edit-detail-table').data('allData');
        var receivestart = $.CurrentNavtab.find('#boship_edit_receivestart').val();
        var receiveend = $.CurrentNavtab.find('#boship_edit_receiveend').val();
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
            return;
        }

        for (var i = 0; i < Obj.length; i++) {
            if (Obj[i].stockin_sysno == null || Obj[i].storagetankname == null || Obj[i].goodsname == null || Obj[i].qualityname == null || Obj[i].goodsnature == null || Obj[i].unitname == null || Obj[i].instockqty == null || Obj[i].bookingoutqty == null || Obj[i].shipname == null || Obj[i].shipokdate == null) {
                BJUI.alertmsg('warn', '请先补全出库预约明细信息',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }

        for (var i = Obj.length - 1; i >= 1; i--) {
            if($.trim(Obj[i].goodsname)!=$.trim(Obj[0].goodsname)||$.trim(Obj[i].qualityname)!= $.trim(Obj[0].qualityname)){
                BJUI.alertmsg('warn', "只能添加一种货品的同一规格",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if($.trim(Obj[i].stocktype)==$.trim(Obj[0].stocktype) && $.trim(Obj[i].stock_sysno)==$.trim(Obj[0].stock_sysno)){
                BJUI.alertmsg('warn', "同一批次货物只能添加一条明细",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        };
        var docsource = $("#shipedit_docsource option:selected").val();
        
        $("#boship_edit_bookingoutstatus").val(step);
        $("#boship_edit_customername").val($("#boship_edit_customer_sysno option:selected").text());
        $("#boship_edit_employeename").val($("#boship_edit_employee_sysno option:selected").text());
        $.CurrentNavtab.find('#shipedit_docsource').removeAttr("disabled");
        $("#boship_edit_detaildata").val(JSON.stringify(Obj));

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
         
    }

function submitForm(){
    //提交表单
    BJUI.ajax('ajaxform', {
        url: "{{$action}}",
        form: $.CurrentNavtab.find('#boship-edit-form'),
        validate: true,
        loadingmask: true,
        okCallback: function(json, options) {
            BJUI.navtab('reloadFlag', 'navab225,navab445');
            BJUI.navtab('closeCurrentTab', '');  
        }
    });   

}

function boshipExam(step) {
        var car_url = $('#boship_edit_ca_url').val();
        var docsource = $('#shipedit_docsource').val();
        if(step == 'close'){
            BJUI.navtab('closeCurrentTab', '');
            return;
        }
        $.CurrentNavtab.find("#boship_edit_examstep").val(step);
        if (!$('#boship_edit_exammarks').val() && step == 8) {
            BJUI.alertmsg('warn', '请先填写驳回意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        if (!$('#boship_edit_exammarks').val() && step == 7) {
            BJUI.alertmsg('warn', '请先填写审核意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        if(car_url != '' && docsource == '2'){
            BJUI.setRegional('progressmsg', 'CA读取中……');
        }

        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#boship-audit-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag','navab445,navab225,navab269');
                BJUI.navtab('closeCurrentTab', '');  
            }
        });
    }
    function showCA() {
        var url = $('#boship_edit_ca_url').val();
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