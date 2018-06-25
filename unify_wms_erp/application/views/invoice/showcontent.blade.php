<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
        <form action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <fieldset>
                <legend>开票通知单</legend>
                <br>

                <div class="bjui-row col-3">
                    <label class="row-label">通知单号</label>
                    <div class="row-input">
                            <input type="text" id='invoice_print_invoiceno' name="invoiceno" value="{{$invoiceno}}" readonly>
                    </div>

                    <label class="row-label">日期</label>
                    <div class="row-input">
                        <input type="text" name="invoicedate" data-toggle="datepicker" value="@if($invoicedate) {{ $invoicedate }} @else {{date('Y-m-d')}} @endif"  readonly>
                    </div>

                    <label class="row-label">操作状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" readonly>
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <select name="customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id='invoice_print_customer_name' name="customer_name" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">发票抬头</label>
                    <div class="row-input">
                        <select name="invoice_company_sysno" id="invoice_company_sysno" data-rule="required" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($companylist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $invoice_company_sysno) selected @endif>{{$item['companyname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="invoice_companyname" id="invoice_companyname" value="{{$invoice_companyname}}">
                    </div>

                    <label class="row-label">开票公司</label>
                    <div class="row-input">
                        <input type="text" name="base_companyname" value="{{$base_companyname}}" readonly>
                        <input type="hidden" name="base_company_sysno" value="{{$base_company_sysno}}">
                    </div>
                    
                    <label class="row-label">开票品名</label>
                    <div class="row-input">
                        <input type="text" id='invoice_print_invoicegoodsname' name="invoicegoodsname" value="{{$invoicegoodsname}}" readonly>
                    </div>

                    <label class="row-label">结算日期</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" id='invoice_print_coststartdate' name="coststartdate" data-toggle="datepicker" value="{{$coststartdate}}" placeholder="开始日期" readonly readonly>
                            <div class="input-group-addon">to</div>
                            <input type="text" class="form-control" id='invoice_print_costenddate' name="costenddate" data-toggle="datepicker" value="{{$costenddate}}"  placeholder="结束日期" readonly readonly>
                        </div>
                    </div>

                    <!-- <label class="row-label">费用名称</label>
                    <div class="row-input">
                        <select name="costtype" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%"  disabled>
                            <option value="">请选择</option>
                            <option value="0" @if('0' == $costtype) selected @endif>仓储费</option>
                            <option value="-1" @if('-1' == $costtype) selected @endif>管道输送费</option>
                            @foreach($othercost as $key => $value)
                            <option value="{{$value['sysno']}}" @if($value['sysno'] == $costtype) selected @endif>{{$value['othercostname']}}</option>
                            @endforeach
                        </select>
                    </div> -->
                    
                    <label class="row-label">靠泊装卸费</label>
                    <div class="row-input">
                        <input type="radio" name="berthcost" value="1" data-toggle="icheck" data-label="是" @if($costtype == 2) checked @endif disabled>
                        <input type="radio" name="berthcost" value="2" data-toggle="icheck" data-label="否" @if($costtype != 2) checked @endif disabled>
                    </div>
                    
                    <label class="row-label">总金额</label>
                    <div class="row-input">
                        <input type="text" name="costtotal" value="{{$costtotal}}" readonly>
                    </div>

                    <label class="row-label">折扣总额</label>
                    <div class="row-input ">
                        <input type="text" name="costdiscount" value="{{$costdiscount}}" readonly="">
                    </div>

                    <label class="row-label">开票金额</label>
                    <div class="row-input">
                        <input type="text" id='invoice_print_costinvoice' name="costinvoice" value="{{$costinvoice}}" readonly>
                    </div>
                    @if($type == 'view')
                    <label class="row-label">发票号</label>
                    <div class="row-input">
                        <input type="text" name="invoicenumber" value="{{$invoicenumber}}" readonly>
                    </div>

                    <label class="row-label">未收款金额</label>
                    <div class="row-input">
                        <input type="text" name="unreceivablecost" value="{{$unreceivablecost}}" readonly>
                    </div>

                    <label class="row-label">已收款金额</label>
                    <div class="row-input">
                        <input type="text" name="receivablecost" value="{{$receivablecost}}" readonly>
                    </div>

                    <label class="row-label">已开发票总金额</label>
                    <div class="row-input">
                        <input type="text" name="hasinvoicecost" value="{{$hasinvoicecost}}" readonly>
                    </div>

                    <label class="row-label">未开发票总金额</label>
                    <div class="row-input">
                        <input type="text" name="uninvoicecost" value="{{$uninvoicecost}}" readonly>
                    </div>
                    @endif
                    <br>
                    <label class="row-label">备 注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3" readonly>{{$memo or ''}}</textarea>
                    </div>
                    <br>
                </div>
            </fieldset>
                <div class="remarks">
                    <fieldset>
                        <legend>费用明细</legend>

                        <table class="table table-bordered" id="invoicedetail-table" data-toggle="datagrid" data-options="{
                                filterThead:false,
                                showToolbar: false,
                                toolbarItem: '',
                                data:{{$detaillist}},
                                paging: false,
                                linenumberAll: true,
                                fullGrid:true,
                                fieldSortable: false,
                                local: 'local'
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'costno',align:'center'}">费用单号</th>
                                <th data-options="{name:'costname',align:'center'}">费用类型</th>
                                <th data-options="{name:'isexceedfirst',align:'center',render:function(value){switch(value) { case '1': return '是'; default: return '否';  }}}">超出首期</th>
                                <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">储罐id</th>
                                <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'customer_name',align:'center'}">客户</th>
                                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                                <th data-options="{name:'costqty',align:'center'}">计费数量</th>
                                <th data-options="{name:'unitprice',align:'center'}">单价</th>
                                <th data-options="{name:'totalprice',align:'center'}">金额(元)</th>
                            </tr>
                            </thead>
                        </table>
                    </fieldset>
                </div>

            <br>
            <fieldset>
                <legend>上传发票</legend>
                <br>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'invoice',action:'notice',doc_sysno:'{{$id}}'},
                            required: false,
                            uploaded: '{{ $uploaded }}',
                            basePath: '/attachment/preview/id/',
                            @if($type == 'addinvoice')
                            deletePath:'/attachment/deljson/',
                            @else
                            deletePath:'/attachment/deljson/type/1',
                            @endif
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf',
                                mimeTypes: '.jpg,.png,,.pdf'
                            }
                        }"
                >

            </fieldset>
        </form>
            <fieldset>
                
            @if($type == 'addinvoice')
                <div class="text-center ">
                    <button type="button" onclick="inoticeSubmit(2)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="showRecords()" class="btn btn-success btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
                </div>
            @endif
            @if($coststatus == 3 && $type == 'check')
                <legend>操作</legend>
                <br>
                    <form id="inotice-exam-form" action="/invoice/examJson" method="POST" class="datagrid-edit-form" >
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="examstep" id="examstep" value="">
                        <div class="bjui-row col-4">
                            <label class="row-label">审核意见</label>
                                <textarea name="exammarks" id="exammarks" data-toggle="autoheight" cols="100" rows="3" placeholder="请在此处填写审核意见" ></textarea>
                        </div>
                    </form>
                <br>
                <div class="text-center ">
                    <button type="button" onclick="inoticeExam(4)" class="btn btn-info btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="inoticeExam(6)" class="btn btn-danger btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="showRecords()" class="btn btn-success btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
                </div>
            @endif
            @if($coststatus == 4 && $type == 'cancel')
                <legend>操作</legend>
                <br>
                    <form id="inotice-exam-form" action="/invoice/examJson" method="POST" class="datagrid-edit-form" >
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="examstep" id="examstep" value="">
                        <div class="bjui-row col-4">
                            <label class="row-label">作废意见</label>
                                <textarea name="exammarks" id="exammarks" data-toggle="autoheight" cols="60" rows="1" placeholder="请在此处填写审核意见" ></textarea>
                        </div>
                    </form>
                <div class="text-center ">
                    <button type="button" onclick="inoticeExam(5)" class="btn btn-info btn-lg">作废</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="inoticeExam('cancel')" class="btn btn-danger btn-lg">取消</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="showRecords()" class="btn btn-success btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
                </div>
            @endif
            @if($type == 'view')
                <div class="text-center ">
                    <button type="button" onclick="showRecords()" class="btn btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
                </div>
            @endif
            @if($coststatus==4 && $type == 'print')
            <div class="text-center ">
                <button type="button" onclick="Design(printfun_shipout_edit)" class="btn btn-success btn-lg">打印设计</button>
                <button type="button" onclick="Setup(printfun_shipout_edit)" class="btn btn-success btn-lg">打印 </button>
            </div>
            @endif
                
            </fieldset>
            <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable"></div>
            </fieldset>
        </div>
    </div>
</div>

<div id="invoice_print_edit" style="display: none;">
    <style>
        .table-dy {
            border: 1px solid #000;
        }
        th{
            border: none;
            height: 50px;
        }
        .table-dy td {
            border: 1px solid #000;
            height: 50px;
            text-align: center;
        }

        .t_head td{
            border: none;
        }

    </style>

    <table class='table-dy' border=0 cellSpacing=0 cellPadding=0 width='100%' height="200" bordercolor="#000000" style="border-collapse:collapse">

        <tr>
            <td style="width: 10%;"><b>No</b></td>
            <td style="width: 40%;"></td>
            <td style="width: 10%;"><b>时间</b></td>
            <td style="width: 40%;"></td>
        </tr>
        <tr>
            <td><b>货主</b></td>
            <td></td>
            <td><b>合同编号</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>开票单位</b></td>
            <td></td>
            <td><b>船名</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>货品名称</b></td>
            <td></td>
            <td><b>罐号</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>结算期限</b></td>
            <td></td>
            <td><b>仓储费用</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>备注</b></td>
            <td colspan="3"></td>
        </tr>

        <tr>
            <th><b>制表:</b></th>
            <th><b>审核:</b></th>
            <th><b>总经理:</b></th>
            <th><b></b></th>
        </tr>
    </table>

</div>

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">

    addLog($.CurrentNavtab.find('.hideshow'), {{$id}}, '14');

    function inoticeExam(step) {
        if (step == 'cancel') {
            BJUI.navtab('closeCurrentTab', '');
            return;
        }
        if(step==6 && $('#exammarks').val()==''){
            BJUI.alertmsg('warn', "请填写审核意见",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(step==5 && $('#exammarks').val()==''){
            BJUI.alertmsg('warn', "请填写作废意见",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        $.CurrentNavtab.find("#examstep").val(step);

        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#inotice-exam-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab315','navab455');
                BJUI.navtab('closeCurrentTab', '');
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
    var printfun_shipout_edit = function CreateStockIn() {
        var Obj = $("#invoicedetail-table").data('allData');
        var date = new Date();
        var outdate = date.toLocaleString();
        var invoiceno = $("#invoice_print_invoiceno").val();
        var customer_name = $("#invoice_print_customer_name").val();
        var invoice_companyname = $("#invoice_companyname").val();
        var costdate = $("#invoice_print_coststartdate").val() + "至" + $("#invoice_print_costenddate").val();
        var costinvoice = $("#invoice_print_costinvoice").val();
        var invoicegoodsname = $("#invoice_print_invoicegoodsname").val();
        
        
        var shipname = Obj[0].shipname ? Obj[0].shipname : '';
        var storagetankname = Obj[0].storagetankname ? Obj[0].storagetankname : '';
        for (var i = 1; i < Obj.length; i++) {
            if(Obj[i].shipname && Obj[0].shipname != Obj[i].shipname){
                shipname = shipname + ',' + Obj[i].shipname;
            }
            if (Obj[i].storagetankname && Obj[0].storagetankname != Obj[i].storagetankname) {
                 storagetankname = storagetankname + ',' + Obj[i].storagetankname;
             }    
        }
      
        var title =  "江阴恒阳化工储运有限公司开票通知单";

        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "开票通知单");
        LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");

        LODOP.ADD_PRINT_TABLE("10%", "1%", "96%", "98%", document.getElementById("invoice_print_edit").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(10, 180, 800, 50, title);
        LODOP.SET_PRINT_STYLEA(2, "FontName", "黑体");
        LODOP.SET_PRINT_STYLEA(2, "FontSize", 30);
        LODOP.ADD_PRINT_TEXT(90, 130, 250, 24, invoiceno);
        LODOP.ADD_PRINT_TEXT(140, 130, 250, 24, customer_name);
        LODOP.ADD_PRINT_TEXT(190, 130, 250, 24, invoice_companyname);
        LODOP.ADD_PRINT_TEXT(240, 130, 250, 24, invoicegoodsname);
        LODOP.ADD_PRINT_TEXT(290, 130, 250, 24, costdate);

        // LODOP.ADD_PRINT_TEXT(338, 130, 250, 24, );
        // LODOP.ADD_PRINT_TEXT(389, 130, 250, 24, );

        LODOP.ADD_PRINT_TEXT(88, 660, 250, 24, outdate);

        LODOP.ADD_PRINT_TEXT(188, 660, 250, 24, shipname);
        LODOP.ADD_PRINT_TEXT(238, 660, 250, 24, storagetankname);
        LODOP.ADD_PRINT_TEXT(288, 660, 250, 24, costinvoice);


    }


</script>