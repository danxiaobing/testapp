<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">

        <form id="stockshipinform" action="{{$action}}" method="POST"
              class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="1">
            <input type="hidden" id="stockshipindetaildata" name="stockshipindetaildata" value="">
            <!--base message start-->
            <fieldset>
                <legend>入库单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">入库单号</label>

                    <div class="row-input">
                        <input type="text" name="stockinno" value="@if($stockinno){{$stockinno}}@else{{系统自动生成}}@endif"
                               readonly>
                    </div>
                    <label class="row-label">入库日期</label>

                    <div class="row-input required">
                        <input type="text" name="stockindate" disabled
                               value="@if($stockindate){{date('Y-m-d',strtotime($stockindate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date">
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="stockinstatus" name="stockinstatus"
                               value="@if($stockinstatus) {{$stockinstatus}} @else {{2}} @endif" readonly>
                        @foreach($stockinstatusnamelist as $item)
                            @if($item['id'] == $stockinstatus)
                                <input type="text" name="stockinstatusname" value="{{$item['name']}}" readonly>
                            @endif
                        @endforeach
                    </div>
                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="stockshipin_customer_sysno"
                                data-size="5" data-toggle="selectpicker" data-live-search="true"
                                data-rule="required" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>
                                    {{$item['customername']}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customername" id="stockshipin_customername"
                               value="{{$customername}}">
                    </div>
                    <label class="row-label">预约单号</label>

                    <div class="row-input required">
                        <input type="hidden" name="booking_in_sysno"
                               value="@if($booking_in_sysno){{$booking_in_sysno}}@else{{''}}@endif">
                        <input type="text" name="bookingin_no"
                               value="@if($bookingin_no){{$bookingin_no}}@else{{''}}@endif" readonly>
                    </div>
                    <label class="row-label">合同编号</label>
                    <div class="row-input required">
                        <input type="hidden" name="contract_sysno"
                               value="@if($contract_sysno){{$contract_sysno}}@else{{1}} @endif">
                        <input type="text" name="contractno"
                               value="@if($contractno){{$contractno}}@else{{''}}@endif" readonly>
                    </div>
                    <label class="row-label">客服专员</label>
                    <div class="row-input ">
                        <select name="cs_employee_sysno" id="cs_employee_sysno"
                                data-size="5" data-toggle="selectpicker"
                                disabled data-live-search="true" data-rule="required"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cc_employee_sysno) selected @endif>
                                    {{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>
                    <label class="row-label">质计</label>
                    <div class="row-input ">
                        <select name="zj_employee_sysno" id="zj_employee_sysno"
                                data-size="5" data-toggle="selectpicker" disabled data-live-search="true"
                                data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $zj_employee_sysno) selected @endif>{{$item['employeename']}}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="zj_employeename" id="zj_employeename" value="{{$zj_employeename}}">
                    </div>
                    <label class="row-label">仓储</label>
                    <div class="row-input ">
                        <select name="cc_employee_sysno" id="cc_employee_sysno" data-size="5"
                                data-toggle="selectpicker" disabled
                                data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cc_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cc_employeename" id="cc_employeename" value="{{$cc_employeename}}">
                    </div>

                    <label class="row-label">靠岸码头</label>

                    <div class="row-input">
                        <select name="wharf_sysno" id="wharf_sysno" data-size="5" data-toggle="selectpicker"
                                 disabled
                                data-live-search="true"
                                
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($wharflist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $wharf_sysno) selected @endif>{{$item['wharfname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="wharfname" id="wharfname" value="{{$wharfname}}">
                    </div>


                    <label class="row-label">卸货时间</label>
                    <div class="row-input">
                        <input type="text" name="wharf_date" disabled
                               value="@if($wharf_date){{date('Y-m-d',strtotime($wharf_date))}}@endif"
                               data-rule="date">
                    </div>

                    <br>
                    <label class="row-label">泊位分配</label>

                    <div class="row-input required">
                        <select name=""  data-size="5"  data-toggle="selectpicker" disabled data-rule="required" data-width="100%">
                            <option value="1" @if($isberthorder==1 || !$isberthorder) selected @endif>是</option>
                            <option value="2" @if($isberthorder==2) selected @endif>否</option>
                        </select>
                        <input type="hidden" name="isberthorder" value="{{$isberthorder}}">
                    </div>


                    <label class="row-label">管线分配</label>

                    <div class="row-input required">
                        <select name=""  data-size="5"  data-toggle="selectpicker" disabled  data-rule="required" data-width="100%">
                            <option value="1" @if($ispipelineorder==1 || !$ispipelineorder) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder==2) selected @endif>否</option>
                        </select>
                        <input type="hidden" name="ispipelineorder" value="{{$ispipelineorder}}">
                    </div>


                    <label class="row-label">品质检查</label>

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
                    <label class="row-label">提单数量(吨)</label>

                    <div class="row-input">
                        <input type="text" name="takegoodsnum"   readonly
                        value="{{$takegoodsnum}}" data-rule="number;range[0~]">
                    </div>

                    <label class="row-label">船检数量(吨)</label>

                    <div class="row-input required">
                        <input type="text" name="shipcheckqty" id="shipcheckqty"  readonly 
                        value="{{$shipcheckqty}}" data-rule="required number range[0~]">
                    </div>

                    <label class="row-label">送货公司</label>

                    <div class="row-input">
                        <input type="text" name="deliverycompany" id="deliverycompany"  @if($type == 'back' || $type=='review' || $type == 'register' ) readonly @endif
                        value="{{$deliverycompany}}" data-rule="">
                    </div>

                    <label class="row-label">司磅员</label>

                    <div class="row-input">
                        <select name="sby_employee_sysno" id="sby_employee_sysno" data-size="5"
                                data-toggle="selectpicker"  disabled
                                data-live-search="true"  data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == ($sby_employee_sysno || $type?$sby_employee_sysno:$load_user['employee_sysno'])) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="sby_employeename" id="sby_employeename" value="{{$sby_employeename}}">
                    </div>

                    <label class="row-label">备注:</label>
                    &nbsp;&nbsp;
                    <textarea name="memo" data-toggle="autoheight" cols="27.5" rows="3"  readonly >{{$memo}}</textarea>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
            <br><br>
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>入库单明细</legend>
                    <table class="table table-bordered" id="stockshipin-detail-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar: false,
                        toolbarCustom:$.CurrentNavtab.find('#stockshipin_tb'),
                        local: 'local',
                        data: '{{$detaillist}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',
                        render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'}
                        else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                货物性质
                            </th>
                            <th data-options="{name:'tobeqty',align:'center'}">通知数量</th>
                            <th data-options="{name:'takegoodsnum',align:'center',hide:true}">提单数量</th>
                            <th data-options="{name:'shipcheckqty',align:'center',hide:true}">船检数量</th>
                            <th data-options="{name:'beqty',align:'center'}">商检数量</th>
                            <th data-options="{name:'unitname',align:'center',
                                render:function(value){if(value=='') {return '吨'} else {return value}} }">计量单位
                            </th>
                            <!-- <th data-options="{name:'release_no',align:'center'}">放行单号</th> -->
                            <th data-options="{name:'storagetankname',align:'center'}">进货罐号</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">产品id</th>
                            <th data-options="{name:'storagebank_sysno',align:'center',hide:true}">进货罐号</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>

            </div>
            <!--project end-->
            <br>
            
            @if($ispipelineorder==1)
            <!-- 管线明细start -->
            <div class="remarks">
                <fieldset>
                    <legend>管线明细</legend>
                    <div class="table-edit">

                        <table class="table table-bordered" id="stockshipin-pipelineorder-table" height="40+50*{{count($list)}}" data-toggle="datagrid" data-options="{
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
            <!-- 管线明细end -->
            @endif
            <br>

            @if($isqualitycheck==1)
            <!-- 品质检查单start -->

            <div class="remarks">
                <fieldset>
                    <legend>品质检查明细</legend>
                    <table class="table table-bordered" id="stockshipin-qualitycheck-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar:false,
                        local: 'local',
                        data: '{{$qualitycheck}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">品种</th>
                            <th data-options="{name:'checktime',align:'center'}">品质检查时间</th>
                            <th data-options="{name:'ischecked',align:'center',render:function(value){switch(value) { case '1': return '是'; case '2':return '否'; default: return '';  }}}">是否合格</th>
                            <th data-options="{name:'isskip',align:'center',render:function(value){switch(value) { case '0': return '不用让步'; case '1': return '是'; case '2':return '否'; default: return '--';  }}}">是否让步</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>

            <!-- 品质检查单end -->
            @endif
            <br>
            
            @if($isberthorder==1)
            <!-- 泊位明细start -->
            <div class="remarks">
                <fieldset>
                    <legend>泊位明细</legend>
                    <div class="table-edit">

                        <table class="table table-bordered" id="stockshipin-berthorder-table" height="40+50*{{count($list)}}" data-toggle="datagrid" data-options="{
                               height:'100%',
                                filterThead:false,
                                data:'{{$berthorder}}',
                                paging: false,
                                linenumberAll: true,
                                fullGrid:true,
                                fieldSortable: false,
                                local: 'local'
                            }">
                            <thead>
                            <tr>
                                <th data-options="{name:'berthname',align:'center'}">泊位号</th>
                                <th data-options="{name:'wharfname',align:'center'}">码头</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'planintime',align:'center'}">计划靠泊时间</th>
                                <th data-options="{name:'planouttime',align:'center'}">计划离泊时间</th>
                                <th data-options="{name:'beintime',align:'center'}">实际靠泊时间</th>
                                <th data-options="{name:'beouttime',align:'center'}">实际离泊时间</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>
            
            <!-- 泊位明细end -->
            @endif


            <br><br>
            <!--upload start-->
            <div class="comuser-add clearfix">
                <!-- 自带bug -->
                <div style="display: none">
                    <input type="file" data-name="attachment[]" data-toggle="webuploader"
                           data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'stockshipin',action:'uploading'},
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
                    <fieldset class="customerfieldset">
                        <legend>商检报告</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'stockshipin',action:'uploading'},
                            required: false,
                            uploaded: '{{ $uploaded1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:@if($type =='edit' || $type =='back' || $type=='addattach')
                                '/attachment/deljson/'
                            @else
                                '/attachment/deljson/type/1'
                            @endif,
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
                    <fieldset class="customerfieldset" id="bookshipin_release">
                        <legend>放行单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'stockshipin',action:'release_no'},
                            required: false,
                            uploaded: '{{ $uploaded2 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:@if($type =='edit' || $type =='back' || $type=='addattach')
                                '/attachment/deljson/'
                            @else
                                '/attachment/deljson/type/1'
                            @endif,
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                            }
                        }"
                        >
                    </fieldset>
                </div>
            </div>
            <!--upload end-->
        </form>
        <div class="clearfix"></div>
        <br><br>
        <!--start-->
        <div class="text-center">
{{--            @if($stockinstatus==4)
                <button id="stockshipinsubmit6" type="button" onclick="Design(printfun_showstockshipin)"
                        class="btn btn-success btn-lg">打印设计
                </button>
                <button id="stockshipinsubmit6" type="button" onclick="Setup(printfun_showstockshipin)"
                        class="btn btn-success btn-lg">开始打印
                </button>
            @endif--}}
            <button type="button" class="btn btn-gray btn-lg" onclick="showRecords()">操作记录明细</button>
        </div>
        <br><br>
        <div class="remarks hideshow" style="display: none">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable">
                </div>
            </fieldset>
        </div>
        <!--end-->
    </div>
</div>

<div id="stockshipin_tb">
    {{--@if(!$booking_in_sysno)
       <button type="button" class="btn btn-blue" data-icon="plus" onclick="addStockshipin()">添加</button>
       @else
       <button type="button" class="btn btn-blue" data-icon="edit" onclick="editStockshipin()">修改</button>
       @endif--}}
</div>

<div id="div_showstockshipin" style="display: none;">

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

    <table class='table-dy' border=0 cellSpacing=0 cellPadding=0 width="100%" height="200"
           bordercolor="#000000" style="border-collapse:collapse">
        <caption>
            <b><font face="黑体" size="8">{{$companyname}}</font></b><br>
            <font face="黑体" size="4">(入库单)</font>
        </caption>
        <thead>
        <tr>
            <td width="33%"><b>货主名称</b></td>
            <td width="33%"></td>
            <td width="32%"><b>备注</b></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="33%"><b>入库日期</b></td>
            <td width="33%"></td>
            <td width="34%" rowspan='6'></td>
        </tr>
        <tr>
            <td width="33%"><b>运输工具</b></td>
            <td width="33%"></td>
        </tr>
        <tr>
            <td width="33%"><b>仓储合同号</b></td>
            <td width="33%"></td>
        </tr>
        <tr>
            <td width="33%"><b>货品名称</b></td>
            <td width="33%"></td>
        </tr>
        <tr>
            <td width="33%"><b>储罐编号</b></td>
            <td width="33%"></td>
        </tr>
        <tr>
            <td width="33%"><b>入库数量</b></td>
            <td width="33%"></td>
        </tr>

        </tbody>
        <tfoot>
        <tr>
            <th width="33%"><b>审核:</b></th>
            <th width="33%"><b>制单:</b></th>
            <th width="33%"><b>计量:</b></th>
        </tr>
        <tr>
            <th width="100%" colspan="3"><b>(盖章有效)</b></th>
        </tr>
        </tfoot>
    </table>

</div>


<script src="static/common/js/common.js"></script>
<script type="text/javascript">

    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});
    //----------------------操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '3');
    //----------------------操作记录 end

</script>


<!--云打印-->
<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<script language="javascript" type="text/javascript">

    var LODOP; //声明为全局打印变量   
    console.log({{$table_html}});
    //打印入库单字段布局
    var printfun_showstockshipin = function CreateStockIn() {
        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "入库单");
        LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");
        LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", document.getElementById("div_showstockshipin").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(100, 402, 130, 29, "{{$customername}}");
        LODOP.ADD_PRINT_TEXT(150, 402, 130, 26, "{{date('Y-m-d',strtotime($stockindate))}}");
        LODOP.ADD_PRINT_TEXT(200, 402, 131, 24, "{{$detailshipname}}");
        LODOP.ADD_PRINT_TEXT(247, 402, 131, 26, "@if($contractno){{$contractno}}@else{{''}}@endif");
        LODOP.ADD_PRINT_TEXT(298, 402, 130, 27, "{{$detailgoodsname}}");
        LODOP.ADD_PRINT_TEXT(348, 402, 130, 30, "{{$detailstoragebankname}}");
        LODOP.ADD_PRINT_TEXT(398, 402, 132, 28, "{{$beqty}}吨");
        LODOP.ADD_PRINT_TEXT(155, 784, 100, 237, "{{$memo}}");
//        LODOP.PREVIEW();

    }


</script> 