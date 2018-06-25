<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="supplement-detail-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="step" name="step" value="">
            <input type="hidden" id="supplement_detail_data" name=" supplement_detail_data" value="">

            <!--base message start-->
            <fieldset>
                <legend>补入基本信息</legend>
                <br><br>
                <div class="bjui-row col-3">
                    <label class="row-label">补入单号</label>

                    <div class="row-input">
                        <input type="text" name="supplementno" value="@if($supplementno){{$supplementno}}@else{{系统自动生成}}@endif"
                               readonly>
                    </div>
                    <label class="row-label">入库日期</label>

                    <div class="row-input required">
                        <input type="text" id="supplementdate" name="supplementdate"
                               value="@if($supplementdate){{date('Y-m-d',strtotime($supplementdate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date"
                               @if($supplementstatus ==3 || $supplementstatus==4 || $mode=='view' || $mode=='audit')
                               readonly
                               @else
                               data-toggle="datepicker"
                                @endif
                        >
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <input type="hidden" id="supplementstatus" name="supplementstatus" value="{{$supplementstatus}}">
                        @if($supplementstatus == 2)
                            <input name="" value="暂存" readonly>
                        @elseif($supplementstatus == 3)
                            <input name="" value="待审核" readonly>
                        @elseif($supplementstatus == 4)
                            <input name="" value="已审核" readonly>
                        @elseif($supplementstatus == 5)
                            <input name="" value="作废" readonly>
                        @elseif($supplementstatus == 6)
                            <input name="" value="退回" readonly>
                        @else
                            <input name="" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">客户</label>

                    <div class="row-input required">
                        <select name="customer_sysno" id="customer_sysno"
                                @if($supplementstatus ==3 || $supplementstatus==4 || $mode=='view' || $mode=='audit')
                                disabled @endif
                                data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customername" id="customername" value="{{$customer_name}}">
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


                    <label class="row-label">入库单号</label>
                    <div class="row-input required">
                        <input type="text" id="stockinno" name="stockinno"
                        @if(!$mode ||  $supplementstatus==2)
                        onclick="findgrid_stockinlist(this);" @endif  value="{{$stockinno}}"  data-rule="required" placeholder="点击选择入库单" readonly>
                        <input type="hidden" id="stockin_sysno" name="stockin_sysno" value="{{$stockin_sysno}}"  readonly>
                    </div>

                    <label class="row-label">品名:</label>
                    <div class="row-input required">
                        <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                        <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}" readonly>
                    </div>

                    <label class="row-label">船名:</label>
                    <div class="row-input required">
                        <input type="text" name="shipname" value="{{$shipname}}" readonly>
                    </div>


                </div>
                <br><br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>补充明细</legend>
                    <table class="table table-bordered" id="supplement-detail-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        @if(!$mode ||  $supplementstatus==2)
                          showToolbar: true,
                          toolbarCustom:$.CurrentNavtab.find('#supplement_tnb'),
                          toolbarItem:'',
                        @endif
                        local: 'local',
                        dataUrl: '/supplement/getdetailJson/id/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                货物性质
                            </th>
                            <th data-options="{name:'unitname',align:'center',render:function(value){return '吨'}}">计量单位</th>
                            <th data-options="{name:'supplementtype',align:'center',render:function(value){if(value==1){return '补充库存'} else if(value==2){return '扣减库存' } }}">补充方式</th>
                            <th data-options="{name:'bussinesscheckqty',align:'center'}">商检数量</th>
                            <th data-options="{name:'beqty',align:'center'}">补充数量</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">产品id</th>
                            <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">储罐id</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                            <th data-options="{name:'goodsname',align:'center',hide:true}">品名</th>
                            <th data-options="{name:'shipname',align:'center',hide:true}">船名</th>
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
                    <button id="supplememntsubmit4" type="button" onclick="supplememntsubmit(4)"
                            class="btn btn-green btn-lg">审核通过
                    </button>&nbsp;&nbsp;&nbsp;
                    <button id="supplememntsubmit6" type="button" onclick="supplememntsubmit(6)"
                            class="btn btn-red btn-lg">审核不通过
                    </button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode == 'edit' || !isset($mode) || !$mode)
                    @if($stockinstatus < 3 || $stockinstatus >= 5 || !$stockinstatus)
                        <button id="supplememntsubmit2" type="button" onclick="supplememntsubmit(2)"
                                class="btn btn-green btn-lg">保存
                        </button>&nbsp;&nbsp;&nbsp;
                        <button id="supplememntsubmit3" type="button" onclick="supplememntsubmit(3)"
                                class="btn btn-green btn-lg">提交
                        </button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($stockinstatus==4)
                        <button id="supplememntsubmit16" type="button" onclick="Design(printfun_stockship_in)"
                                class="btn btn-green btn-lg">打印设计
                        </button>
                        <button id="supplememntsubmit17" type="button" onclick="Setup(printfun_stockship_in)"
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


@if(!$mode ||  $supplementstatus==2)
<div id="supplement_tnb">
    <button type="button" class="btn btn-blue" onclick="addSupplement()" data-icon="plus">添加</button>
    <button type="button"  class="btn btn-green" data-icon="edit" onclick="editSupplement()">修改</button>
    <button type="button" class="btn btn-red" onclick="delSupplement()" data-icon="fa-close">删除</button>

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

    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '32');

    //----------------------操作记录 end
</script>

<script type="text/javascript">
    $('#customer_sysno').on('change',function(){
        $('#stockinno').val('');
        $('#stockin_sysno').val('');
    })

    /*
     * 绑定入库单
     * */
    function findgrid_stockinlist(obj) {
      var customer_sysno = $.CurrentNavtab.find('#customer_sysno option:selected').val();
     if(!customer_sysno || customer_sysno=='' || customer_sysno==null){
     BJUI.alertmsg('warn','<h4>请先选择客户!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
     return;
     }
     BJUI.findgrid({
     include: 'sysno:sysno,stockinno:stockinno,stockin_sysno:stockin_sysno,goods_sysno:goods_sysno,goodsname:goodsname,shipname:shipname',
     dialogOptions: {width:'1000',height:'500',title:'入库详细信息',maxable:true,resizable:true,mask:true},
     gridOptions:{
     width:'80%',
     tableWidth:'97%',
     local: 'local',
     paging: {pageSize:10},
     dataUrl: '/supplement/getstockinList/customer_sysno/'+customer_sysno,
     columns: [
     {name:'sysno', label:'id',align:'center'},
     {name:'stockinno', label:'入库单号',align:'center'},
     {name:'goodsname', label:'商品名称',align:'center'},
     {name:'bussinesscheckqty', label:'商检量',align:'center'},
     {name:'beqty', label:'实际库存',align:'center'},
     {name:'shipname', label:'船名',align:'center'},
     ],
     showLinenumber:false,
         },
     afterSelect:function(data) {
         relodetail(data);
     }
         })
     }
    //选择入库单后返回方法
    function  relodetail(data){
        $('#supplement-detail-table').datagrid('reload',  {data:[]});
    };

    //明细添加功能
    function addSupplement() {
        var stockinno = $.CurrentNavtab.find("#stockinno").val();
        var stockin_sysno = $.CurrentNavtab.find("#stockin_sysno").val();
        var allData = $("#supplement-detail-table").data('allData');
       if(typeof  allData == 'undefined' || allData.length==0){
            if (!stockinno || stockinno == '' || stockinno == null || stockinno == undefined) {
                BJUI.alertmsg('warn', '<h4>请先选择入库单！</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
                return;
            } else {
                BJUI.dialog({
                    url: '/supplement/supplementedit/type/add/stockin_sysno/' + stockin_sysno,
                    title: '增加补充明细',
                    mask: true,
                    width: 800,
                    height: 400
                });
                return;
            }
    }else {
           BJUI.alertmsg('warn','<h4>只能添加一条明细!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
           return;
         }
      }
    //修改
    function editSupplement() {
        var stockinno = $.CurrentNavtab.find("#stockinno").val();
        var stockin_sysno = $.CurrentNavtab.find("#stockin_sysno").val();
        var data = $.CurrentNavtab.find('#supplement-detail-table').data('selectedDatas');
        console.log(data);
        if (data == undefined || data.length == 0) {
            BJUI.alertmsg('warn', "请先选择补充明细");
        } else {
            BJUI.dialog({
                id: 'reback-{{$id}}',
                data: {datadetail:data[0]},
                type: 'POST',
                url: '/supplement/supplementedit/type/edit/stockin_sysno/' + stockin_sysno,
                title: '退货明细',
                width: 700,
                height: 500,
                mask: true
            });
        }
        return;
    }

    //提交表单
    function supplememntsubmit(step) {
        if (step == 6) {
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "required");
        } else {
            $.CurrentNavtab.find('#supplement-detail-form').data('validator').options.ignore = '#auditreason';
        }
        var Obj = $.CurrentNavtab.find("#supplement-detail-table").data('allData');

        for (var i = Obj.length - 1; i >= 0; i--) {
            if (!Obj[i].beqty) {
                BJUI.alertmsg('warn', '请填写补充数量');
                return;
            }
        }


        $.CurrentNavtab.find("#supplement_detail_data").val(JSON.stringify(Obj));
        $.CurrentNavtab.find("#step").val(step);

        if($.CurrentNavtab.find("#cs_employee_sysno option:selected").val()!=''){
            $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        }

        if($.CurrentNavtab.find("#customer_sysno option:selected").val()!=''){
            $.CurrentNavtab.find("#customername").val($.CurrentNavtab.find("#customer_sysno option:selected").text());
        }
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");

        BJUI.ajax('ajaxform', {
            url: '{{$atcion}}',
            form: $.CurrentNavtab.find('#supplement-detail-form'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('refresh', 'navab567');
                BJUI.navtab('refresh', 'navab566');
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