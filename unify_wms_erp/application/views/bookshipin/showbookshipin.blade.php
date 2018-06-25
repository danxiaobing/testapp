<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="bookshipinform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" id="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="1">
            <input type="hidden" id="bookshipindetaildata" name="bookshipindetaildata" value="">
            <!--base message start-->
            <fieldset>
                <legend>入库预约单信息</legend>
                <br>
                <div class="bjui-row col-3">

                    <label class="row-label">入库预约单号</label>

                    <div class="row-input">
                        <input type="text" name="bookinginno"
                               value="@if($bookinginno){{$bookinginno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">预约日期</label>

                        <div class="row-input required">
                        <input type="text" name="bookingindate" id="bookingindate" readonly
                               value="@if($bookingindate){{date('Y-m-d',strtotime($bookingindate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>

                    <div class="row-input required">
                        <input type="hidden" id="bookinginstatus" name="bookinginstatus"
                               value="@if($bookinginstatus){{$bookinginstatus}}@else{{2}} @endif" readonly>
                        @foreach($bookinstatusnamelist as $item)
                            @if($item['id'] == $bookinginstatus)
                                <input type="text" name="bookinginstatusname" value="{{$item['name']}}" readonly>
                            @endif
                        @endforeach

                    </div>

                    <label class="row-label">客户</label>

                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="bookshipin_customer_sysno"
                                data-nextselect="#bookshipin_contract_sysno"
                                disabled
                                data-refurl="/customer/customercontractJson2/id/{value}" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customer_name" id="bookshipin_customername"
                               value="{{$customer_name}}">
                    </div>

                    <label class="row-label">合同编号</label>

                    <div class="row-input required">
                        <select name="contract_sysno" id="bookshipin_contract_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%" disabled>
                            <option value="">请选择</option>
                            <option value="{{$contract_sysno}}"
                                    @if($contract_sysno) selected @endif>{{$contract_no}}</option>
                        </select>
                        <input type="hidden" name="contract_no" id="bookshipin_contractno" value="{{$contract_no}}">
                    </div>

                    <label class="row-label">货品名称</label>
                    <div class="row-input">
                        <input type="text" id="bookshipincontractgoods" value="" readonly>
                    </div>

                    <label class="row-label">单据来源</label>

                    <div class="row-input">
                        <input type="hidden" name="docsource" value="@if($docsource){{$docsource}}@else{{1}}@endif">
                        <input type="text" value="@if($docsource ==2)国烨云仓@else手工创建@endif" readonly>
                    </div>

                    <label class="row-label">船舶代理</label>

                    <div class="row-input">
                        <input type="text" name="shipproxyname" value="{{$shipproxyname}}"
                               readonly>
                    </div>

                    <label class="row-label">客服专员</label>

                    <div class="row-input required">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5"
                                data-toggle="selectpicker" disabled
                                data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">商检单位</label>

                    <div class="row-input required">
                        <input type="text" name="businesscheckunitname" id="businesscheckunitname"
                               disabled
                               value="@if($businesscheckunitname){{$businesscheckunitname}}@else{{''}}@endif"
                               data-rule="required">
                    </div>

                    <label class="row-label">泊位预约</label>

                    <div class="row-input required">
                        <select name="isberthorder"  data-size="5"  data-toggle="selectpicker"  disabled   data-rule="required" data-width="100%">
                            <option value="1" @if($isberthorder==1 || !$isberthorder) selected @endif>是</option>
                            <option value="2" @if($isberthorder==2) selected @endif>否</option>
                        </select>
                    </div>


                    <label class="row-label">管线预约</label>

                    <div class="row-input required">
                        <select name="ispipelineorder"  data-size="5"  data-toggle="selectpicker"  disabled   data-rule="required" data-width="100%">
                            <option value="1" @if($ispipelineorder==1 || !$ispipelineorder) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder==2) selected @endif>否</option>
                        </select>
                    </div>


                    <label class="row-label">品质检查预约</label>

                    <div class="row-input required">
                        <select name="" id="cs_employee_sysno" data-size="5"
                                data-toggle="selectpicker"
                                disabled
                                data-rule="required" data-width="100%">
                            <option value="1" @if($isqualitycheck==1 || !$isqualitycheck) selected @endif>是</option>
                            <option value="2">否</option>
                        </select>
                        <input type="hidden" name="isqualitycheck" value="1">
                    </div>

                </div>
                <br></fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>入库单明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="bookshipin-detail-table" data-toggle="datagrid"
                               data-options="{
                        height:'100%',
                        filterThead:false,
                        showToolbar: false,
                        toolbarCustom:$.CurrentNavtab.find('#bookshipin_tb'),
                        local: 'local',
                        dataUrl: '/bookshipin/adddetailJson/id/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        fullGrid:true,
                        showTfoot:true,
                        linenumberAll: true
                    }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                                <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                    货物性质
                                </th>
<!--                                 <th data-options="{name:'release_no',align:'center'}">放行编号</th>
                                <th data-options="{name:'declaration',align:'center'}">报关单号</th> -->
                                <th data-options="{name:'unitname',align:'center',width:100,render:function(value){if(value=='') {return '吨'} else {return value}}}">
                                    计量单位
                                </th>
                                <th data-options="{name:'bookinginqty',calc:'sum',align:'center'}">数量</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'bookingindate',align:'center'}">预计到港日期</th>
                                <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">产品id</th>
                                <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">进货罐号</th>
                                <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
                <!--project end-->
                <!--upload start-->
                <div class="comuser-add" style="display: none">
                    <!-- 自带bug -->
                    <div>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookshipin',action:'booking'},
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
                </div>
                <!-- 临时解决end -->
                <div class="comuser-add-left">
                    <fieldset class="customerfieldset">
                        <legend>上传船入库预约单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookshipin',action:'booking'},
                            required: false,
                            uploaded: '{{ $uploaded1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/type/1',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                            }
                        }"
                        >
                    </fieldset>
                </div>
<!--                 <div class="comuser-add-right">
    <fieldset class="customerfieldset" id='bookshipin_release'>
        <legend>上传放行单</legend>
        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
        {
            pick: {label: '点击选择图片'},
            server: '/attachment/uploadjson',
            fileNumLimit: 10,
            formData: {module:'bookshipin',action:'release_no'},
            required: false,
            uploaded: '{{ $uploaded2 }}',
            basePath: '/attachment/preview/id/',
            deletePath:'/attachment/deljson/type/1',
            accept: {
                title: '图片',
                extensions: 'jpg,png,txt,pdf',
                mimeTypes: '.jpg,.png,.txt,.pdf'
            }
        }"
        >
    </fieldset>
</div>
<div class="clearfix"></div>
<div class="comuser-add-left">
    <fieldset class="customerfieldset" id='bookshipin_declaration'>
        <legend>上传报关单</legend>
        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
        {
            pick: {label: '点击选择图片'},
            server: '/attachment/uploadjson',
            fileNumLimit: 10,
            formData: {module:'bookshipin',action:'declaration'},
            required: false,
            uploaded: '{{ $uploaded3 }}',
            basePath: '/attachment/preview/id/',
            deletePath:'/attachment/deljson/type/1',
            accept: {
                title: '图片',
                extensions: 'jpg,png,txt,pdf',
                mimeTypes: '.jpg,.png,.txt,.pdf'
            }
        }"
        >
    </fieldset>
</div> -->
            </div>
            <!--upload end-->
           
            <div class="clearfix"></div>
            <br><br>
            <div class="text-center btns-user">
                @if($bookinginstatus == 5 || $bookinginstatus ==6)
                <button id="stockshipinsubmit6" type="button" onclick="bookshipinPrint()" class="btn btn-success btn-lg">打印设计</button>&nbsp;&nbsp;&nbsp;&nbsp;
                <button id="stockshipinsubmit6" type="button" onclick="bookshipinPrint()" class="btn btn-success btn-lg">打印入库预约单</button>&nbsp;&nbsp;&nbsp;&nbsp;
                @endif
                <button type="button" onclick="showRecords()" class="btn btn-gray btn-lg">操作记录</button>
                @if($ca_no)<a href="{{$ca_address}}" target="_blank" class="btn btn-orange" style="height: 50px;line-height: 38px;">查看CA合同</a> @endif
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

<div id="div_showbookshipin" style="display: none;">

    <style>
        .table-dy, th {
            border: none;
            height: 50px;
        }

        .table-dy td {
            border: 1px solid #000;
            height: 50px;
            text-align: center;
        }

        .t_head td {
            border: none;
        }
    </style>

</div>


<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'})
    //----------------------操作记录
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '1');

    //----------------------操作记录 end
</script>

<script type="text/javascript">

    $(function () {

        $("#isbusinesscheck").change(function () {
            var v = $("#isbusinesscheck").val();
            if (v == 0) {
                $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input");
                $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "");
                $.CurrentNavtab.find(".isbusinesscheckview").hide();
            }
            else {
                $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input required");
                $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "required");
                $.CurrentNavtab.find(".isbusinesscheckview").show();
            }
        });

        $("#bookshipin_customer_sysno").change(function () {
            var v = $("#bookshipin_customer_sysno option:selected");

            $("#bookshipin_customername").val(v.text());
            $.CurrentNavtab.find('#bookshipin-detail-table').datagrid('reload', {data: []});
        });

    });
if("{{$id}}")
{
    $(function(){
        var contract_sysno = $("#bookshipin_contract_sysno").val();
        BJUI.ajax('doajax', {
            url:'/contract/contractgoodsjson/id/'+contract_sysno,
            loadingmask: true,
            okCallback: function(json, options) {
                console.log(json);
                var str = json_array(json).join(',');
               $("#bookshipincontractgoods").val(str);
            }
        });
    });
}
    function json_array(data){
        var len=eval(data).length;
        var arr=[];
        for(var i=0;i<len;i++){
            arr[i]=data[i].goodsname;
        }
        return arr;
    }
</script>

<!--云打印-->
<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<script language="javascript" type="text/javascript">

    function bookshipinPrint(type) {
        var id=$.CurrentNavtab.find("#id").val();

        BJUI.ajax('doajax', {
            url: "/bookshipin/executePrint/id/"+id,
            loadingmask: true,
            okCallback: function(json, options) {
                if(json.code == 300){
                    BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }else{

                    var LODOP; //声明为全局打印变量
                    //打印入库单字段布局
                    var date = new Date();
                    var now = date.getFullYear()+"-" + (date.getMonth()+1) + "-" + date.getDate();

                    var CreateStockIn = function CreateStockIn() {
                        data = json;
                        LODOP = getLodop();
                        LODOP.PRINT_INITA(0, 0, 1200, 600, "船入库单");
                        // LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");

                        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
                        LODOP.SET_PRINT_STYLEA(2, "FontName", "黑体");
                        LODOP.SET_PRINT_STYLEA(2, "FontSize", 30);

                        LODOP.ADD_PRINT_TEXT(90, 130, 200, 24, now);
                        LODOP.ADD_PRINT_TEXT(140, 130, 200, 24, data.shipname);
                        LODOP.ADD_PRINT_TEXT(140, 380, 200, 24, data.goodsname);
                        LODOP.ADD_PRINT_TEXT(140, 630, 200, 24, data.beqty);
                        LODOP.ADD_PRINT_TEXT(190, 130, 200, 24, data.storagetankname);

                    }
                    Setup(CreateStockIn)

                }
            }
        })

    };
</script>