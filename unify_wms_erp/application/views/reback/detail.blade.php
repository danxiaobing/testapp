<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="reback-detail-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="status" name="status" value="">

            <!--base message start-->
            <fieldset>
                <legend>退货基本信息</legend>
                <br><br>
                <div class="bjui-row col-3">
                    <label class="row-label">退货单单号:</label>

                    <div class="row-input">
                        <input type="text" name="stockinno" value="@if($stockrebackno){{$stockrebackno}}@else{{系统自动生成}}@endif"
                               readonly>
                    </div>
                    <label class="row-label">退货日期:</label>

                    <div class="row-input required">
                        <input type="text" id="stockrebackdate" name="stockrebackdate"
                               value="@if($stockrebackdate){{date('Y-m-d',strtotime($stockrebackdate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date"
                               @if($stockinstatus ==3 || $stockinstatus==4 || $mode=='view')
                               readonly
                               @else
                               data-toggle="datepicker"
                                @endif
                        >
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <input type="hidden" id="stockinstatus" name="stockinstatus" value="{{$stockinstatus}}">
                        @if($stockinstatus == 2)
                            <input name="" value="暂存" readonly>
                        @elseif($stockinstatus == 3)
                            <input name="" value="待审核" readonly>
                        @elseif($stockinstatus == 4)
                            <input name="" value="已审核" readonly>
                        @elseif($stockinstatus == 6)
                            <input name="" value="退回" readonly>
                        @elseif($stockinstatus == 7)
                            <input name="" value="作废" readonly>
                        @else
                            <input name="" value="新建" readonly>
                        @endif
                    </div>


                    <label class="row-label">出库磅码单号</label>
                    <div class="row-input required">
                        <input type="hidden" name="poundsout_sysno" value="{{$poundsout_sysno}}">
                        <input type="text" name="poundsoutno" readonly value="{{$poundsoutno}}"
                               data-rule="required" @if($stockinstatus==2 || !$stockinstatus || $stockinstatus==6) data-toggle="findgrid" @endif data-options="{
                                obj:'obj',
                                includ:'sysno:sysno,poundsout_sysno:poundsout_sysno,poundsoutno:poundsoutno,carid:carid,goodsname:goodsname,realnumber:realnumber',
                                dialogOptions: {width:'800',height:'500',title:'出库磅码单信息',maxable:true,resizable:true,mask:true},
                                gridOptions: {
                                    tableWidth:'99.8%',
                                    local: 'local',
                                    paging: {pageSize:5},
                                    dataUrl: '/reback/poundsAllJson',
                                    columns: [
                                        {name:'sysno', label:'id'},
                                        {name:'poundsoutno', label:'磅码单号'},
                                        {name:'carid', label:'车牌号'},
                                        {name:'customername',label:'客户'},
                                        {name:'cartype',label:'槽车类型',render:function(value){if(value==1) {return '槽车'} else if(value==2) {return '隔舱车'} else if(value==3) {return '桶车'}}},
                                        {name:'goodsname',label:'品名'},
                                        {name:'beqty',label:'实提数量(KG)'},
                                    ],
                                    showLinenumber:false,
                                    fullGrid:true
                                },
                                afterSelect:function(data) {

                                      reloaddetail(data);

                                }
                            }" @if($stockinstatus == 3 || $stockinstatus==4 || $mode=='view') readonly @endif  placeholder="点放大镜按钮查找">
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5" data-toggle="selectpicker"
                                @if($mode == 'back' || $mode=='view' || $mode=='audit' || $stockinstatus==3) disabled
                                @endif data-live-search="true"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <br>

                    <label class="row-label">车牌号:</label>
                    <div class="row-input">
                        <input type="text" name="carid" value="{{$carid}}" readonly>
                    </div>

                    <label class="row-label">品名:</label>
                    <div class="row-input">
                        <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                        <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}" readonly>
                    </div>

                    <label class="row-label">实提数量(KG):</label>
                    <div class="row-input">
                        <input type="text" name="beqty" value="{{$takegoodsnumber}}" readonly>
                    </div>

                </div>
                <br><br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>退货明细</legend>
                    <table class="table table-bordered" id="reback-detail-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                         @if((!$stockinstatus ||$stockinstatus<3 || $stockinstatus==6) && $mode!='view' )
                          showToolbar: true,
                          toolbarCustom:$.CurrentNavtab.find('#reback_tnb'),
                          toolbarItem:'',
                        @endif
                        local: 'local',
                        dataUrl: '/reback/getdetailJson/id/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'stockoutno',align:'center'}">出库单号</th>
                            <th data-options="{name:'customername',align:'center'}">客户</th>
                            <th data-options="{name:'takegoodsno',align:'center'}">提单号</th>
                            <th data-options="{name:'takegoodscompany',align:'center'}">提单公司</th>
                            <th data-options="{name:'unitname',align:'center',render:function(value){if(value=='') {return 'KG'} else {return value}} }">
                                计量单位
                            </th>
                            <th data-options="{name:'inshipname',align:'center'}">入库船名</th>
                            <th data-options="{name:'realnumber',align:'center'}">提货数量</th>
                            <th data-options="{name:'bucketnumber',align:'center'}">提货桶数</th>
                            <th data-options="{name:'rebacknumber',align:'center'}">退回数量</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">产品id</th>
                            <th data-options="{name:'customer_sysno',align:'center',hide:true}">客户id</th>
                            <th data-options="{name:'pounddetail_sysno',align:'center',hide:true}">磅码单明细id</th>
                            <th data-options="{name:'stockoutdetail_sysno',align:'center',hide:true}">出库明细id</th>
                            <th data-options="{name:'stockout_sysno',align:'center',hide:true}">出库id</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存方式</th>
                            <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>
                            <th data-options="{name:'inshipname',align:'center',hide:true}">原始入库船名</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>
            <br>
            <!--project end-->
            <br>
            <!--upload start-->
            {{--<div class="comuser-add">--}}
                {{--<!-- 自带bug -->--}}

                {{--<!-- 临时解决end -->--}}

            {{--</div>--}}
            <!--upload end-->
            <div class="remarks">

                @if($mode == 'audit' || $mode=='view')
                    <fieldset>
                        <legend>操作</legend>
                        <textarea id="auditreason" name="auditreason" data-toggle="autoheight" cols="auto" rows="3"
                                  placeholder="请在此处填写审核意见" @if($mode=='view') readonly @endif>{{$auditreason}}</textarea>
                        <br>
                        <br>
                    </fieldset>
                @endif
            </div>
            <br><br>
            <div class="text-center btns-user">
                @if($mode=='audit')
                    <button id="stockshipinsubmit4" type="button" onclick="rebacksubmit(4)"
                            class="btn btn-green btn-lg">审核通过
                    </button>&nbsp;&nbsp;&nbsp;
                    <button id="stockshipinsubmit6" type="button" onclick="rebacksubmit(6)"
                            class="btn btn-red btn-lg">审核不通过
                    </button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode == 'edit' || !isset($mode) || !$mode)
                    @if($stockinstatus < 3 || $stockinstatus >= 5 || !$stockinstatus)
                        <button id="rebacksubmit2" type="button" onclick="rebacksubmit(2)"
                                class="btn btn-green btn-lg">保存
                        </button>&nbsp;&nbsp;&nbsp;
                        <button id="rebacksubmit3" type="button" onclick="rebacksubmit(3)"
                                class="btn btn-green btn-lg">提交
                        </button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($stockinstatus==4)
                        <button id="rebacksubmit16" type="button" onclick="Design(printfun_stockship_in)"
                                class="btn btn-green btn-lg">打印设计
                        </button>
                        <button id="rebacksubmit17" type="button" onclick="Setup(printfun_stockship_in)"
                                class="btn btn-green btn-lg">打印
                        </button>
                    @endif
                @endif
                <button type="button" onclick="showRecords()" class="btn btn-gray btn-lg">
                    操作记录明细
                </button>
            </div>
            <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">
                    </div>
                </fieldset>
            </div>
            <br><br>
        </form>
    </div>
</div>


@if((!$stockinstatus ||$stockinstatus<3 || $stockinstatus==6) && $mode!='view' )
<div id="reback_tnb">
    {{--<button type="button"  class="btn btn-green" data-icon="edit" onclick="editReback()">修改</button>--}}
     <input type="hidden" value="" id="reback_detail_data" name="reback_detail_data">
</div>
@endif
<div id="div_stockshipin" style="display: none;">

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

    <table class='table-dy' border=0 cellSpacing=0 cellPadding=0 width="100%" height="200" bordercolor="#000000"
           style="border-collapse:collapse">
        <caption><b><font face="黑体" size="8">{{$companyname}}</font></b><br><font face="黑体" size="4">(入库单)</font>
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
<script src="static/common/js/custom.js"></script>
<script src="static/common/js/common.js"></script>
<script type="text/javascript">

    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //----------------------操作记录

    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '31');

    //----------------------操作记录 end
</script>

<script type="text/javascript">

    //选择后加载明细reload明细
    function reloaddetail(data){
       // console.log(data);
        var pound_sysno = data.sysno;
        BJUI.ajax('doajax', {
            url: 'reback/getdetail',
            type:'POST',
            data: {sysno:pound_sysno},
            loadingmask: true,
            okCallback: function(json, options) {
                $.CurrentNavtab.find('#reback-detail-table').datagrid('reload', {data: json});
                console.log('返回内容：\n'+ JSON.stringify(json))
            }
        })
    }

//修改
    function editReback() {

        var data = $.CurrentNavtab.find('#reback-detail-table').data('selectedDatas');
        console.log(data);
        if (data == undefined || data.length == 0 || data == null || data =='') {
            BJUI.alertmsg('warn', "请先选择退货明细");
        } else {
            BJUI.dialog({
                id: 'reback-{{$id}}',
                data: {datadetail:data[0]},
                type: 'POST',
                url: '/reback/rebackdetailedit/handlestatus/edit/stockinstatus/' + '{{$stockinstatus}}'+'/mode/'+'{{$mode}}'+'/id/'+{{$id}},
                title: '退货明细',
                width: 700,
                height: 500,
                mask: true
            });
        }
        return;
    }

    //提交表单
    function rebacksubmit(step) {
        if (step == 6) {
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "required");
        } else {
            $.CurrentNavtab.find('#reback-detail-form').data('validator').options.ignore = '#auditreason';
        }
        var Obj = $.CurrentNavtab.find("#reback-detail-table").data('allData');
        var num = 0;
        for (var i = Obj.length - 1; i >= 0; i--) {

            if (!Obj[i].rebacknumber) {
                BJUI.alertmsg('warn', '请填写退回数量');
                return;
            }
        }


        $.CurrentNavtab.find("#reback_detail_data").val(JSON.stringify(Obj));
        $.CurrentNavtab.find("#status").val(step);

        if($.CurrentNavtab.find("#cs_employee_sysno option:selected").val()!=''){
            $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        }

        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");
        BJUI.ajax('ajaxform', {
            url: '{{$atcion}}',
            form: $.CurrentNavtab.find('#reback-detail-form'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('refresh', 'navab550');
                BJUI.navtab('refresh', 'navab551');
            }
        });
    }


</script>


<!--云打印-->
<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<script language="javascript" type="text/javascript">
    var LODOP; //声明为全局打印变量
    //打印入库单字段布局
    var printfun_stockship_in = function CreateStockIn() {
        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "入库单");
        LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");
        LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", document.getElementById("div_stockshipin").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(94, 372, 130, 29, "{{$customername}}");
        LODOP.ADD_PRINT_TEXT(145, 372, 130, 26, "{{date('Y-m-d',strtotime($stockindate))}}");
        LODOP.ADD_PRINT_TEXT(195, 372, 131, 24, "{{$detailshipname}}");
        LODOP.ADD_PRINT_TEXT(242, 372, 131, 26, "@if($contractno){{$contractno}}@else{{''}}@endif");
        LODOP.ADD_PRINT_TEXT(293, 373, 130, 27, "{{$detailgoodsname}}");
        LODOP.ADD_PRINT_TEXT(343, 376, 130, 30, "{{$detailstoragebankname}}");
        LODOP.ADD_PRINT_TEXT(390, 377, 132, 28, "{{$beqty}}吨");
        LODOP.ADD_PRINT_TEXT(150, 704, 100, 237, "{{$memo}}");
//        LODOP.PREVIEW();

    }


</script>