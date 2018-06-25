<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="{{$prefix}}form" action="/booktrans/editJson" method="POST" class="datagrid-edit-form" data-data-type="json">            
            <input type="hidden" id="{{$prefix}}detaildata" name="detaildata" value="">
            <input type="hidden" id="{{$prefix}}sysno" name="sysno" value="{{$sysno}}">
            <input type="hidden" id="{{$prefix}}bookingtransstatus" name="bookingtransstatus" value="@if($bookingtransstatus) {{$bookingtransstatus}} @else {{2}} @endif" >

            <!--base message start-->
            <fieldset>
                <legend>货权转移预约单</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">预约单号</label>
                    <div class="row-input">
                        <input type="text" name="bookingtransno" value="@if($bookingtransno) {{$bookingtransno}} @else {{系统编码}} @endif" readonly>
                        </div>
                    <label class="row-label">预约日期</label>
                    <div class="row-input required">
                        <input type="text" name="bookingtransdate" value="@if($bookingtransdate){{$bookingtransdate}}@else{{date('Y-m-d')}}@endif" data-toggle="datepicker"  data-rule="required">
                    </div>
                     <label class="row-label">单据状态</label>
                    <div class="row-input required">
                            @if($bookingtransstatus==2)
                                <input name="" value="暂存" readonly>
                            @elseif($bookingtransstatus==3)
                                <input name="" value="已提交" readonly>
                            @elseif($bookingtransstatus==4)
                                <input name="" value="已审核" readonly>
                            @elseif($bookingtransstatus==5)
                                <input name="" value="已完成" readonly>
                            @elseif($bookingtransstatus==6)
                                <input name="" value="废弃" readonly>
                            @else
                                <input name="" value="新建" readonly>
                            @endif
                    </div>

                    <label class="row-label">转让方</label>
                    <div class="row-input required">
                        <input type="hidden" id ="{{$prefix}}sale_customername" name="sale_customername" value="{{$sale_customername}}" readonly >
                        <select name="sale_customer_sysno" id="{{$prefix}}sale_customer_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required" onchange="{{$prefix}}getReload(this.value)">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                    <option value="{{$item['sysno']}}" @if($item['sysno'] == $sale_customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <label class="row-label">受让方</label>
                    <div class="row-input required">
                        <input type="hidden" id ="{{$prefix}}buy_customername" name="buy_customername" value="{{$buy_customername}}" readonly>
                        <select name="buy_customer_sysno" id="{{$prefix}}buy_customer_sysno" data-nextselect="#{{$prefix}}contract_sysno" data-refurl="/customer/customercontractJson/id/{value}" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required" >
                            <option value="">请选择</option>                            
                            @foreach($customerlistContract as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $buy_customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>

                    </div>
                    
                    <label class="row-label">受让方合同编号</label>
                    <div class="row-input required">
                        <select name="contract_sysno" id="{{$prefix}}contract_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($contractlist['list'] as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $contract_sysno) selected @endif>{{$item['contractno']}}</option>
                            @endforeach
                            
                        </select>
                        <input type="hidden" name="contractno" id="{{$prefix}}contractno" value="{{$contractno}}">
                    </div>

                    <label class="row-label">受让方计费起始日</label>
                    <div class="row-input required">
                        <input type="text" name="buystartdate" value="@if($buymoneydate){{date('Y-m-d',strtotime($buymoneydate))}}@else{{date('Y-m-d')}}@endif" data-toggle="datepicker" data-rule="required;date">
                    </div>                   
                    <label class="row-label">单据来源</label>
                    <div class="row-input required">
                        <input type="hidden" name="docresource" value="1">
                        <input type="text" name="" value="手工创建" readonly>
                    </div>
                </div>
                <br></fieldset>
            <!--base message end-->
            <!--project start-->
            <fieldset>
            <table class="table table-bordered" id="{{$prefix}}detail-table" data-toggle="datagrid" data-options="{
                    gridTitle : '货权转移明细',
                    filterThead:false,
                    showToolbar: true,
                    @if(!$bookingtransstatus||$bookingtransstatus<3)
                    toolbarCustom: $.CurrentNavtab.find('#{{$prefix}}booktrans_tb'),
                    @endif
                    local: 'local',
                    dataUrl: '/booktrans/detailListJson/id/{{$sysno}}',
                    dataType: 'json',
                    jsonPrefix: 'obj',
                    paging: false,
                    linenumberAll: true,
                    fullGrid:true
                }">
                <thead>
                    <tr data-options="{name:'stock_sysno'}">
                        <th data-options="{name:'stockin_no',align:'center'}">来源单号</th>
                        <th data-options="{name:'instockdate',align:'center'}">入库日期</th>
                        <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                        <th data-options="{name:'goodsname',align:'center'}">品名</th>
                        <th data-options="{name:'qualityname',align:'center'}">规格</th>
                        <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                        <th data-options="{name:'stockqty',align:'center'}">可用库数量</th>
                        <th data-options="{name:'transqty',align:'center'}">转移数量</th>
                        <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                        <th data-options="{name:'memo',align:'center'}">备注</th>
                        <th data-options="{name:'stock_sysno',align:'center',hide:'true'}">库存ID</th>
                        <th data-options="{name:'stockno',align:'center',hide:'true'}">库存单号</th>
                    </tr>
                </thead>
            </table>
            <div class='divCount'>
                合计数量：<span id="transcount"></span>
            </div>
            </fieldset>

            <!--project end-->
            
            <!--upload start-->
            <div class="comuser-add" style="margin-top:15px;">
                <!-- 自带bug -->
                <div style="display: none">
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'booktrans',action:'edit'},
                            required: false,
                            uploaded: '',
                            basePath: '',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                            }
                        }"
                    >
                </div>
                <!-- 临时解决end -->
                <div class="comuser-add-left">
                <fieldset class="booktransfieldset">
                   <legend>上传预约回单<span class='red'>*</span></legend>
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'booktrans',action:'edit-1'},
                            required: false,
                            uploaded: '{{ $attach1 }}',
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
                </div>
                <div class="comuser-add-right">
                 <fieldset>
                   <legend>提货单样单</legend>
                <div class="bjui-row col-4"   id="uploader">
                    <ul class="filelist" id="botans_sample_div">
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
            </div>
            <!--upload end-->
                
            <div class="remarks">
            @if($bookingtransstatus==3)
            <fieldset>
                   <legend>操作</legend>
                   <textarea name="stockmarks" id="{{$prefix}}stockmarks" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$stockmarks}}</textarea>
               </fieldset>
            </div>
            @endif
            <div class="text-center btns-user">
            @if(!$bookingtransstatus||$bookingtransstatus<3)
                <button  type="button" onclick="{{$prefix}}submit(2)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                <button  type="button" onclick="{{$prefix}}submit(3)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($bookingtransstatus==3)
                <button  type="button" onclick="{{$prefix}}audit(4)" class="btn btn-info btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                <button  type="button" onclick="{{$prefix}}audit(2)" class="btn btn-danger btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
            @endif
            </div>
            <fieldset>
                <legend>操作记录</legend>
                <br>
                <div>
                    <!--operation start-->
                    <table class="table table-bordered" id="stockcarin-operation-table" data-toggle="datagrid" data-options="{
                                gridTitle : '操作记录明细',
                                filterThead:false,
                                local: 'local',
                                postData: {id:'{{$sysno}}',doctype:'3'},
                                dataUrl: '/log/doclogJson',
                                paging: false,
                                linenumberAll: true,
                                fullGrid:true
                            }">
                        <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'opertype',align:'center',render:function(value){if(value=='1') {return '新建'} else if(value=='2') {return '提交'} else if(value=='3') {return '审核通过'} else if(value=='4') {return '审核未通过'} else if(value=='5') {return '作废'} else  {return '新建'}}}">操作</th>
                                <th data-options="{name:'operdesc',align:'center'}">备注</th>
                                <th data-options="{name:'operemployeename',align:'center'}">操作人</th>
                                <th data-options="{name:'opertime',align:'center'}">操作时间</th>
                            </tr>
                        </thead>
                    </table>
                    <!--operation end-->
                </div>
            <br></fieldset>
        </form>
    </div>
</div>
@if(!$bookingtransstatus||$bookingtransstatus<3)
<div id="{{$prefix}}booktrans_tb">
    <button type="button" class="btn btn-blue" onclick="addbooktrans()"><i class="fa fa-plus"></i> 添加</button>
    <button type="button" class="btn btn-red" onclick="subbooktrans()"><i class="fa fa-minus"></i> 移除</button>
</div>
@endif
<script src="/static/common/js/custom.js"></script>

<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
</script>

<script type="text/javascript">

$(function(){
	//选中text值
	
    $("#{{$prefix}}sale_customer_sysno").change(function(){
        text = $(this).find("option:selected").text();
        $("#{{$prefix}}sale_customername").val(text);        
        
    });
    $("#{{$prefix}}buy_customer_sysno").change(function(){
        text = $(this).find("option:selected").text();
        $("#{{$prefix}}buy_customername").val(text); 
    });
    $("#{{$prefix}}contract_sysno").change(function(){
    	text = $(this).find("option:selected").text();
        $("#{{$prefix}}contractno").val(text); 
    })
});

function {{$prefix}}getReload(cid){
    BJUI.ajax('doajax', {
        url: '/booktrans/detailListJson/id/'+$('#{{$prefix}}sysno').val(),
        //data:{id: $('#sysno').val()},
        loadingmask: true,
        okCallback: function(json, options) {
            $.CurrentNavtab.find('#{{$prefix}}detail-table').datagrid('reload',  {data:json});
            BJUI.ajax('doajax', {
            url: '/bookout/customerSampleJson/',
            data:{cid: cid },
            loadingmask: false,
            okCallback: function(json, options) {
                $.CurrentNavtab.find('#botans_sample_div').empty();
                for(i = 0; i < json.length; i ++){
                    var obj = json[i];
                    var uploadedAttr = ' style="cursor:pointer;" data-toggle="dialog" data-options="{id:\'bjui-dialog-view-upload-image\', image:\''+ '/attachment/preview/id/'+ obj.sysno +'\', width:800, height:500, mask:true, title:\'查看图片\'}"' ,
                            li = $('<li class="uploaded" >' +
                                    '<p class="imgWrap" '+ uploadedAttr +'>' +
                                    '<img src="/attachment/preview/id/'+obj.sysno +'">' +
                                    '</p>'+
                                    '</li>');

                    $.CurrentNavtab.find('#botans_sample_div').append(li);
                }
            }
        });
        }
    });
}

function {{$prefix}}audit(status){
    if(status==2 && $('#{{$prefix}}stockmarks').val()==''){
        $('#{{$prefix}}stockmarks').attr('data-rule','required');
        BJUI.alertmsg('info', "请填写审核意见");
    }else{
        var Obj = $("#{{$prefix}}detail-table").data('allData');
        $("#{{$prefix}}detaildata").val(JSON.stringify(Obj));

        BJUI.ajax('doajax', {
            url: '/booktrans/auditJson/',
            data:{sysno: $("#{{$prefix}}sysno").val(),detaildata:$("#{{$prefix}}detaildata").val(),status:status,stockmarks:$("#{{$prefix}}stockmarks").val()},
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                //console.log('返回内容1：\n'+ JSON.stringify(json))
                BJUI.navtab('closeCurrentTab');
                BJUI.navtab('refresh', 'navab237');
                BJUI.navtab('refresh', 'navab236');
            }
        });
    }

}

    function {{$prefix}}submit(step) {
        var Obj = $("#{{$prefix}}detail-table").data('allData');
        $("#{{$prefix}}detaildata").val(JSON.stringify(Obj));
        $("#{{$prefix}}bookingtransstatus").val(step);
       
        var attachnum = 0;
        $.CurrentNavtab.find('.booktransfieldset').each(function(){
            if($(this).find(".filelist > li").length > 0)
            {
                attachnum = attachnum +1;
            }
        });
        // console.log(attachnum);
        if(step==3)
        {
            if(attachnum<1)
            {
                BJUI.alertmsg('info', '请先上传附件再提交表单！');
                return;
            }
        }

        BJUI.ajax('ajaxform', {
            url: '/booktrans/editJson',
            form: $.CurrentNavtab.find('#{{$prefix}}form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                //    console.log('返回内容1：\n'+ JSON.stringify(json))
                BJUI.navtab('closeCurrentTab');
                BJUI.navtab('refresh', 'navab237');
                BJUI.navtab('refresh', 'navab236');
            }
        });
    }

    function addbooktrans(){
        var customer_sysno =  $.CurrentNavtab.find('#{{$prefix}}sale_customer_sysno').val();
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'sotckstrans-detail-{{$id}}',
                url:'/booktrans/editDetail/id/{{$sysno or '0'}}/prefix/{{$prefix}}',
                title:'添加货权转移明细',
                onClose:function(){
                    //合计数量
                    var transcount = 0;
                    var allData = $("#bookstrans-detail-table").data('allData');
                    if(allData!=undefined){
                        for (var i = allData.length - 1; i >= 0; i--) {
                            newcount = parseFloat(transcount)+parseFloat(allData[i].transqty);
                        };
                    }
                    $.CurrentNavtab.find('#transcount').html(transcount);
                },
                with:1000,
                height:500,
                mask:true
            });
        }else{
            BJUI.alertmsg('info', '请先选择转让方客户再添加明细单');
        }
        return;
    }

    function subbooktrans(){
        var selectdata  =  $.CurrentNavtab.find('#{{$prefix}}detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('info', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            var allData  = $("#{{$prefix}}detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#{{$prefix}}detail-table').datagrid('reload',  {data:allData});
        }
    }

</script>