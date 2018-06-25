<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
       <br><br>
        <form id="introduce-edit-form" action="{{$action}}" method="POST" class="datagrid-edit-form" @if($introductionstatus==1 || $introductionstatus==2)data-toggle="validate" @endif data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="introduce_edit_detaildata" name="detaildata" value="">
            <input type="hidden" id="introduce_edit_introductionstatus" name="introductionstatus" value="@if($introductionstatus) {{$introductionstatus}} @else {{2}} @endif" >
            <input type="hidden" name="father_introduction_sysno" value="{{$father_introduction_sysno}}">
            <input type="hidden" id="introduce_edit_freceiveend" name="freceiveend" value="{{$freceiveend}}">
            <input type="hidden" id="introduce_edit_freceivestart" name="freceiveend" value="{{$freceivestart}}">
            <fieldset>
                <legend>提单基本信息</legend>
                <br><br>

                <div class="bjui-row col-3">
                    <label class="row-label">单据编号</label>
                    <div class="row-input">
                            <input id="introduce_edit_introductionno" type="text" name="introductionno" value="{{ $introductionno }}" readonly>
                    </div>

                    <label class="row-label">创建时间</label>
                    <div class="row-input">
                        <input type="text" name="introductiondate" data-toggle="datepicker" value="@if($introductiondate) {{ $introductiondate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>

                    <label class="row-label">操作状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" readonly>
                    </div>

                    <label class="row-label">开单公司</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif">
                        <select name="customer_sysno" id="introduce_edit_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" onchange="showCustomerSample(this.value)" @if($type == 'view' || $type == 'audit' || $type == 'delay' || $type == 'trandown') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customername" id="introduce_edit_customername" value="{{$customername}}">
                    </div>

                    <label class="row-label">转让方</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif">
                        <input type="hidden" name="sale_customer_sysno" id="introduce_edit_sale_customer_sysno" value="{{$sale_customer_sysno}}" data-rule="required">
                        <input type="text" name="sale_customername" id="introduce_edit_sale_customername" value="{{$sale_customername}}" readonly>
                    </div>

                    <label class="row-label">受让方</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif">
                        <select name="buy_customer_sysno" id="introduce_edit_buy_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'delay') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $buy_customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="buy_customername" id="introduce_edit_buy_customername" value="{{$buy_customername}}">
                    </div>

                    <label class="row-label">提货单号</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif">
                        <input type="text" name="takegoodsno" value="{{$takegoodsno}}"  data-rule="required" @if($type == 'view' || $type == 'audit' || $type == 'delay') readonly @endif>
                    </div>
                    
                    <label class="row-label">提单类型</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif">
                        <select name="introductiontype" id="introduce_edit_introductiontype" data-size="5" data-toggle="selectpicker" data-rule="required" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'delay' || ($type == 'trandown' && $introductiontype == 1)) disabled @endif>
                            <option value="">请选择</option>
                            <option value="1" @if($introductiontype == 1) selected @endif>可撤销</option>
                            <option value="2" @if($introductiontype == 2) selected @endif>不可撤销</option>
                        </select>
                    </div>
                    
                    <label class="row-label">费用承担方</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif">
                        <select name="costtype" id="introduce_edit_costtype" data-size="5" data-toggle="selectpicker" data-width="100%" data-rule="required" onchange="costtypeChange()" @if($type == 'view' || $type == 'audit' || $type == 'delay' || ($type == 'trandown' && $introductiontype == 1)) disabled @endif>
                            <option value="">请选择</option>
                            <option value="1" @if($costtype == 1) selected @endif>转让方</option>
                            <option value="2" @if($costtype == 2) selected @endif>受让方</option>
                        </select>
                    </div>
                    
                    <label class="row-label" id="introduce_edit_receivestart_lable" style="display: none">提货区间</label>
                    <div class="row-input" id="introduce_edit_receivestart_div" style="display: none">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" id='introduce_edit_receivestart' name="receivestart" data-toggle="datepicker" value="{{$receivestart}}" placeholder="开始日期" data-rule="required" readonly>
                            <div class="input-group-addon">至</div>
                            <input type="text" class="form-control" id='introduce_edit_receiveend' name="receiveend" data-toggle="datepicker" value="{{$receiveend}}" placeholder="结束日期" readonly>
                        </div>
                    </div>

                    <label class="row-label" id="introduce_edit_freecostdate_lable" style="display: none">免仓期</label>
                    <div class="row-input" id="introduce_edit_freecostdate_div" style="display: none">
                        <input type="text" name="freecostdate" id='introduce_edit_freecostdate' value="{{$freecostdate or '' }}" readonly>
                    </div>

                    <label class="row-label" id="introduce_edit_lastamount_label" style="display: none">超期费(元/吨/天)</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif" id="introduce_edit_lastamount_div" style="display: none">
                        <input type="text" name="lastamount"  id="introduce_edit_lastamount" value="{{$lastamount or '' }}" data-rule="required;range[0~]" @if($type == 'view' || $type == 'audit' || $type == 'delay') readonly @endif>
                    </div>
                    
                    <label class="row-label" id="introduce_edit_lossrate_label" style="display: none">超期损耗(‰)</label>
                    <div class="row-input @if($type != 'view' && $type != 'audit') required @endif" id="introduce_edit_lossrate_div" style="display: none">
                        <input type="text" name="lossrate"  id="introduce_edit_lossrate" value="{{$lossrate or '' }}" data-rule="required;range[0~]" @if($type == 'view' || $type == 'audit' || $type == 'delay') readonly @endif style="width: 80%;">
                        <span>(/月)</span>
                    </div>

                </div>
                <br><br><br><br>
            </fieldset>


            <div class="remarks">
                <fieldset>
                    <legend>提单明细</legend>
                    <div class="table-edit">
                       <table class="table table-bordered" id="introduce-edit-detail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            @if($type != 'audit' && $type != 'view' && $type != 'delay')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#introduce_edit_detail_tb'),
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
                            showLinenumber: true,


                        }">
                        <thead>
                        <tr data-options="{name:'stock_sysno'}">
                            <th data-options="{name:'stockin_no',align:'center'}">入库单号</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'goodsqualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                            <th data-options="{name:'instockqty',align:'center'}">入库数量</th>
                            <th data-options="{name:'introduceqty',align:'center'}">提单总量</th>
                            @if($father_introduction_sysno)
                            <th data-options="{name:'tobeqty',align:'center'}">提单可用量</th>
                            @endif
                            <th data-options="{name:'takegoodsnum',align:'center',calc:'sum'}">提单数量</th>
                            @if($type == 'delay')
                            <th data-options="{name:'takegoodsqty',align:'center',calc:'sum'}">实提数量</th>
                            <th data-options="{name:'untakegoodsnum',align:'center',calc:'sum'}">结存量</th>
                            @endif
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格id</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">罐号ID</th>
                            <th data-options="{name:'storagetankableqty',align:'center',hide:true}">储罐可用容量</th>
                            <th data-options="{name:'firstfrom_sysno',align:'center',hide:true}">顶点入库单号ID</th>
                            <th data-options="{name:'introductiondetail_sysno',align:'center',hide:true}">父级明细ID</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存来源</th>
                            <th data-options="{name:'introductiontype',align:'center',hide:true}">库存来源</th>
                            <th data-options="{name:'firstdate',align:'center',hide:true}">首期到期日</th>
                            <th data-options="{name:'release_num',align:'center',hide:true}">报关量</th>
                        </tr>
                        </thead>
                    </table>
                    </div>
                </fieldset>
            </div>
                <br>
                <div class="comuser-add-left ">
                    <fieldset>
                        <legend>提货单样张</legend>
                        <div class="bjui-row col-4">
                            <ul class="filelist picthuild" id="introduce_sample_div">
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
                <div>
                    <!-- 用来规避样式问题 start -->
                    <fieldset class="customerfieldset" style="display: none">
                       <legend>上传提货单<span style="color: red;">*</span></legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                            {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'introduce',action:'takegoods',doc_sysno:'{{$id}}'},
                                required: false,
                                uploaded: '{{ $uploaded }}',
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
                <div class="comuser-add-right">
                    <fieldset class="customerfieldset" id="uploader1">
                       <legend>上传提货单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                            {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'introduce',action:'takegoods',doc_sysno:'{{$id}}'},
                                required: false,
                                uploaded: '{{ $uploaded }}',
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
        </form>
        <br><br>
        @if($type == 'audit')
        <div class="remarks">
            <fieldset style="clear: both;">
                <legend>审核意见</legend>
                <form id="introduce-audit-form" action="/introduce/examJson" method="POST" class="datagrid-edit-form" >
                    <input type="hidden" name="id" value="{{$id}}">
                    <input type="hidden" name="examstep" id="introduce_edit_examstep" value="">
                    <textarea id="introduce_edit_exammarks" name="exammarks" data-toggle="autoheight" rows="3" placeholder="请在此处填写审核意见" ></textarea>
                </form>
            </fieldset>
        </div>
        <br><br>
        @endif
        <div style="clear: both;">
            <div class="text-center">
            @if($introductionstatus< 3 && $type != 'view' && $type != 'delay')
                <input type="button" onclick="introduceSubmit(2)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="introduceSubmit(3)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($introductionstatus==6 && $type != 'view' && $type != 'delay')
                <input type="button" onclick="introduceSubmit(6)" class="btn btn-green btn-lg" value="保存">&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="introduceSubmit(3)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($type == 'audit')
                <button type="button" onclick="introduceExam(4)" class="btn btn-green btn-lg">审核通过</button>
                <button type="button" onclick="introduceExam(6)" class="btn btn-red btn-lg">审核不通过</button>      
            @endif
            @if($type == 'delay')
                <button type="button" onclick="introduceDelay('submit')" class="btn btn-green btn-lg">延期提交</button>
                <button type="button" onclick="introduceDelay('close')" class="btn btn-red btn-lg">关闭</button>    
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
@if($type != 'view' && $type != 'audit' && $type != 'delay')
<div id="introduce_edit_detail_tb">
    @if($type != 'trandown')
    <button type="button" class="btn btn-blue" onclick="addIntroduceDetail()" data-icon="plus">添加</button>
    @endif
    <button type="button" class="btn btn-red" onclick="delIntroduceDetail()" data-icon="close">删除</button>
    <button type="button" class="btn btn-green" onclick="editIntroduceDetail()" data-icon="edit">修改</button>
</div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function(){
        if (!{{$id}}) {
            $.ajax({
                url: '/bookout/getbookoutno/prefix/J2',
                type: 'GET',
                dataType: 'json',
                success:function(data){
                    $('#introduce_edit_introductionno').val(data);
                },
            });
        } 

        $("#introduce_edit_customer_sysno").change(function(){
            var v=$("#introduce_edit_customer_sysno option:selected");

            $("#introduce_edit_customername").val(v.text());
            //转让方
            $("#introduce_edit_sale_customername").val(v.text());
            $("#introduce_edit_sale_customer_sysno").val(v.val());
            var selectdata  =  $.CurrentNavtab.find('#introduce-edit-detail-table').data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                $.CurrentNavtab.find('#introduce-edit-detail-table').datagrid('delAllRows', i );
            }
        });
        costtypeChange();
        
    });

    function costtypeChange(){
        var costtype = $("#introduce_edit_costtype option:selected").val();
        var introductiontype = $("#introduce_edit_introductiontype option:selected").val();
        if(costtype == 1){
            if(introductiontype == 1){
                $("#introduce_edit_receivestart_lable,#introduce_edit_receivestart_div").show();
                $("#introduce_edit_receivestart").attr('data-rule',"required");
                $("#introduce_edit_freecostdate_lable,#introduce_edit_freecostdate_div,#introduce_edit_lastamount_label,#introduce_edit_lastamount_div,#introduce_edit_lossrate_label,#introduce_edit_lossrate_div").hide();
                $("#introduce_edit_lastamount,#introduce_edit_lossrate").attr('data-rule',"range[0~]");
                $("#introduce_edit_lastamount,#introduce_edit_lossrate,#introduce_edit_freecostdate").val('');
            }else{
                allHide();
            }
        }else if (costtype == 2) {
            $("#introduce_edit_lastamount_label,#introduce_edit_lastamount_div,#introduce_edit_lossrate_label,#introduce_edit_lossrate_div,#introduce_edit_receivestart_lable,#introduce_edit_receivestart_div,#introduce_edit_freecostdate_lable,#introduce_edit_freecostdate_div").show();
            $("#introduce_edit_lastamount_div,#introduce_edit_lossrate_div").attr('class',"row-input required");
            $("#introduce_edit_lastamount,#introduce_edit_lossrate").attr('data-rule',"required;range[0~]");
            $("#introduce_edit_receivestart").attr('data-rule',"required");
        }else{
            allHide();
        }
    }

    function allHide() {
        $("#introduce_edit_lastamount_label,#introduce_edit_lastamount_div,#introduce_edit_lossrate_label,#introduce_edit_lossrate_div,#introduce_edit_receivestart_lable,#introduce_edit_receivestart_div,#introduce_edit_freecostdate_lable,#introduce_edit_freecostdate_div").hide();
        $("#introduce_edit_lastamount_div,#introduce_edit_lossrate_div").attr('class',"row-input");
        $("#introduce_edit_lastamount,#introduce_edit_lossrate,#introduce_edit_receivestart").attr('data-rule',"range[0~]");
        $("#introduce_edit_lastamount,#introduce_edit_lossrate,#introduce_edit_receivestart,#introduce_edit_receiveend,#introduce_edit_freecostdate").val('');
    }

    $("#introduce_edit_introductiontype").change(function(){
        var introductiontype = $("#introduce_edit_introductiontype option:selected").val();
        if(introductiontype == 1){
            $('#introduce_edit_costtype').selectpicker('val', '1');  
            $('#introduce_edit_costtype').prop('disabled', true);
            $('#introduce_edit_costtype').selectpicker('refresh');
            costtypeChange();
        }else{
            $('#introduce_edit_costtype').selectpicker('val', '');  
            $('#introduce_edit_costtype').prop('disabled', false);
            $('#introduce_edit_costtype').selectpicker('refresh');
            costtypeChange();
        }
    })

    function showCustomerSample(cid) {

        if(cid=='')
            return;
        BJUI.ajax('doajax', {
            url: '/bookout/customerSampleJson/',
            data:{cid: cid },
            loadingmask: false,
            okCallback: function(json, options) {
                
                $.CurrentNavtab.find('#introduce_sample_div').empty();
                for(i = 0; i < json.length; i ++){
                    var obj = json[i];
                    var uploadedAttr = ' style="cursor:pointer;" data-toggle="dialog" data-options="{id:\'bjui-dialog-view-upload-image\', image:\''+ '/attachment/preview/id/'+ obj.sysno +'\', width:800, height:500, mask:true, title:\'查看图片\'}"' ,
                            li = $('<li class="uploaded" >' +
                                    '<p class="imgWrap" '+ uploadedAttr +'>' +
                                    '<img src="/attachment/preview/id/'+obj.sysno +'">' +
                                    '</p>'+
                                    '</li>');

                    $.CurrentNavtab.find('#introduce_sample_div').append(li);
                }
            }
        })
    }

    $("#introduce_edit_receivestart,#introduce_edit_receiveend").change(function(){
        var receivestart = $('#introduce_edit_receivestart').val();
        var receiveend = $('#introduce_edit_receiveend').val();
        var freceiveend = $('#introduce_edit_freceiveend').val();
        var freceivestart = $('#introduce_edit_freceivestart').val();
        
        var freecostdate = '';
        if(receivestart && receiveend){
            receivestart = Date.parse(new Date(receivestart).toLocaleDateString())/1000 ;
            receiveend = Date.parse(new Date(receiveend).toLocaleDateString())/1000 ;
            freceivestart = Date.parse(new Date(freceivestart).toLocaleDateString())/1000 ;
            if(receivestart > receiveend){
                BJUI.alertmsg('warn', '结束时间不能小于开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            if(freceiveend){
                freceiveend = Date.parse(new Date(freceiveend).toLocaleDateString())/1000 ;
                if(freceiveend < receiveend){
                    BJUI.alertmsg('warn', '结束时间不能超出上级免仓期区间',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;
                }
            }
            if(freceivestart){
                freceiveend = Date.parse(new Date(freceiveend).toLocaleDateString())/1000 ;
                if(receivestart < freceivestart){
                    BJUI.alertmsg('warn', '开始时间不能小于上级开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;
                }
            }
            
            freecostdate = (receiveend - receivestart)/60/60/24 + 1;
            $('#introduce_edit_freecostdate').val(freecostdate);
        }else{
            if(receivestart){
                receivestart = Date.parse(new Date(receivestart).toLocaleDateString())/1000 ;
                freceivestart = Date.parse(new Date(freceivestart).toLocaleDateString())/1000 ;
                if(freceivestart){
                    freceiveend = Date.parse(new Date(freceiveend).toLocaleDateString())/1000 ;
                    if(receivestart < freceivestart){
                        BJUI.alertmsg('warn', '开始时间不能小于上级开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
                        return;
                    }
                }
            }
            $('#introduce_edit_freecostdate').val('--');
        }
    })

    function addIntroduceDetail() {
        var customer_sysno =  $.CurrentNavtab.find('#introduce_edit_customer_sysno').val();
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'introduce-detail-{{$id}}',
                url:'/introduce/detailedit/cid/'+customer_sysno+'/handlestatus/add',
                type:"POST",
                title:'提单明细',
                width:1100,
                height:600,
                mask:true,

            });
        }else{
            BJUI.alertmsg('warn', '请先选择开单公司',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        return;
    }

    function editIntroduceDetail() {
        var selectedDatas  =  $.CurrentNavtab.find("#introduce-edit-detail-table").data('selectedDatas');
        if (selectedDatas == '' || selectedDatas == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var customer_sysno =  $.CurrentNavtab.find('#introduce_edit_customer_sysno').val();
        
        BJUI.dialog({
            id:'introduce-detail-{{$id}}',
            url:'/introduce/detailedit/cid/'+customer_sysno+'/handlestatus/edit',
            type:'POST',
            data:{selectedDatasArray:selectedDatas[0]},
            title:'提单明细',
            width:1100,
            height:600,
            mask:true,
        });
    }

    function delIntroduceDetail() {
        var selectdata  =  $.CurrentNavtab.find('#introduce-edit-detail-table').data('selectedDatas');
        if (selectdata == '' || selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var allData  = $("#introduce-edit-detail-table").data('allData');
        for (var i = selectdata.length - 1; i >= 0; i--) {
            allData = allData.remove(selectdata[i].gridIndex);
        }
        $.CurrentNavtab.find('#introduce-edit-detail-table').datagrid('reload',  {data:allData});
    }

    function introduceSubmit(step) {
        var Obj = $.CurrentNavtab.find('#introduce-edit-detail-table').data('allData');
        var buy_customer_sysno = $("#introduce_edit_buy_customer_sysno option:selected").val();
        var sale_customer_sysno = $("#introduce_edit_sale_customer_sysno").val();
        if(buy_customer_sysno == sale_customer_sysno){
            BJUI.alertmsg('warn', '转让方和受让方不能是同一家公司',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        var receivestart = $('#introduce_edit_receivestart').val();
        var receiveend = $('#introduce_edit_receiveend').val(); 
        var freceiveend = $('#introduce_edit_freceiveend').val();
        var freceivestart = $('#introduce_edit_freceivestart').val();
        receivestart = Date.parse(new Date(receivestart).toLocaleDateString())/1000 ;
        receiveend = Date.parse(new Date(receiveend).toLocaleDateString())/1000 ;
        freceivestart = Date.parse(new Date(freceivestart).toLocaleDateString())/1000 ;
        if(receivestart > receiveend){
            BJUI.alertmsg('warn', '结束时间不能小于开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        if(freceiveend){
            freceiveend = Date.parse(new Date(freceiveend).toLocaleDateString())/1000 ;
            if(freceiveend < receiveend){
                BJUI.alertmsg('warn', '结束时间不能超出上级免仓期区间',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }

        if(freceivestart){
            freceiveend = Date.parse(new Date(freceiveend).toLocaleDateString())/1000 ;
            if(receivestart < freceivestart){
                BJUI.alertmsg('warn', '开始时间不能小于上级开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }
        
        if(Obj.length == 0){
            BJUI.alertmsg('warn', '请填写出库预约明细',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var arr = new Array();
        for (var i = 0; i < Obj.length; i++) {
            if($.trim(Obj[i].introductiontype)==0){
                continue;
            }
            arr.push(Obj[i]);
        }

        if(arr.length != 0){
            for (var i = arr.length - 1; i >= 1; i--) {
                if($.trim(arr[i].introductiontype)!=$.trim(arr[0].introductiontype)){
                    BJUI.alertmsg('warn', "提单类型必须一致",{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }
            }
        }
        for (var i = 0; i < Obj.length; i++) {
            if($.trim(Obj[i].takegoodsnum)==0 || $.trim(Obj[i].takegoodsnum)==''){
                BJUI.alertmsg('warn', "提单数量不能为0",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        }
        for (var i = Obj.length - 1; i >= 1; i--) {
            if($.trim(Obj[i].goodsname)!=$.trim(Obj[0].goodsname)){
                BJUI.alertmsg('warn', "只能添加同一种货品",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if($.trim(Obj[i].stocktype)==$.trim(Obj[0].stocktype) && $.trim(Obj[i].stock_sysno)==$.trim(Obj[0].stock_sysno)){
                BJUI.alertmsg('warn', "同一批次货物只能添加一条明细",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

        };
        var introductiontype = $("#introduce_edit_introductiontype option:selected").val();
        if(introductiontype == 1){
            $('#introduce_edit_freecostdate').val(0);
        }
        $("#introduce_edit_introductionstatus").val(step);
        $("#introduce_edit_customer_sysno").removeAttr('disabled');  
        $("#introduce_edit_introductiontype").removeAttr('disabled');
        $("#introduce_edit_costtype").removeAttr('disabled');
        $("#introduce_edit_customername").val($("#introduce_edit_customer_sysno option:selected").text());
        $("#introduce_edit_buy_customername").val($("#introduce_edit_buy_customer_sysno option:selected").text());
        
        $("#introduce_edit_detaildata").val(JSON.stringify(Obj));
        submitEditForm();

        // $.ajax({
        //     url: '/bookout/controlgoods',
        //     type: 'POST',
        //     dataType: 'json',
        //     data: {obj: Obj},
        //     success:function(data){
        //         if (data.code==200) {
        //             var controlgoodsInfo = '';
        //             if(data.message[1] != 0 || data.message[2] !=0 ||data.message[3] !=0){
        //                 if(data.message[1] != 0){
        //                         controlgoodsInfo +=  ' 欠费超信用期限'+data.message[1]+'天;';
        //                     }
        //                     if(data.message[2] != 0){
        //                         controlgoodsInfo +=  ' 欠费超信用额度'+data.message[2]+';' ;
        //                     }
        //                     if(data.message[3] != 0){
        //                         controlgoodsInfo += ' 欠费超控货比重'+data.message[3]+';';
        //                     }
        //                 BJUI.alertmsg('confirm', controlgoodsInfo , {okCall:function() {
        //                     submitEditForm();
        //                 }
        //                 });
        //             }else{
        //                 submitEditForm();
        //             }
        //         }else if (data.code==300) {
        //             BJUI.alertmsg('confirm','控货异常',{okCall:function(){
        //                 submitEditForm();
        //             }})
        //         }
        //     },
        // });

         
    }

function submitEditForm(){
    //提交表单
    BJUI.ajax('ajaxform', {
        url: "{{$action}}",
        form: $.CurrentNavtab.find('#introduce-edit-form'),
        validate: true,
        loadingmask: true,
        okCallback: function(json, options) {
            BJUI.navtab('reloadFlag', 'navab532,navab533');
            BJUI.navtab('closeCurrentTab', '');  
        }
    });   

}

function introduceExam(step) {

        $.CurrentNavtab.find("#introduce_edit_examstep").val(step);

        if (!$('#introduce_edit_exammarks').val() && step == 6) {
            BJUI.alertmsg('warn', '请先填写审核意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#introduce-audit-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag','navab532,navab533');
                BJUI.navtab('closeCurrentTab', '');  
            }
        });
    }
function introduceDelay(status){
    if(status == 'close'){
        BJUI.navtab('closeCurrentTab', '');
        return;
    }
    var receivestart = $('#introduce_edit_receivestart').val();
    var receiveend = $('#introduce_edit_receiveend').val(); 
    var freecostdate = $('#introduce_edit_freecostdate').val();
    receivestart = Date.parse(new Date(receivestart).toLocaleDateString())/1000 ;
    receiveend = Date.parse(new Date(receiveend).toLocaleDateString())/1000 ;
    if(receivestart > receiveend){
        BJUI.alertmsg('warn', '结束时间不能小于开始时间',{displayPosition:'middlecenter',displayMode:'fade'});
        return;
    }
    BJUI.ajax('doajax', {
        url: 'introduce/introduceDelay/id/' + {{$id}},
        type:'POST',
        data:{receivestart:receivestart,receiveend:receiveend,freecostdate:freecostdate},
        loadingmask: true,
        okCallback: function(json, options) {
            if(json.code == 200){
                BJUI.alertmsg('info', '延期成功',{displayPosition:'middlecenter',displayMode:'fade'});
                BJUI.navtab('reloadFlag','navab532');
                BJUI.navtab('closeCurrentTab', '');
                return;
            }else{
                BJUI.alertmsg('warn', json.msg ,{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }
    })

}

/*--------------------------操作记录优化-----------------------------*/
    var jl=0;
     function record() {

        if(jl==0){

            console.log(jl);

             addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '29');
        }

        jl++;
           
        $.CurrentNavtab.find('.showhide').toggle(500);

    }

</script>