<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="clearstockform" action="{{$action}}" method="POST" class="datagrid-edit-form"  data-data-type="json" data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id? $id:$id='0'}}">
            <input type="hidden" name="clearstockdetail" value=""><!-- 清库详情值-->
            <input type="hidden" name="printdata" value="{{$printdata}}"><!--打印值-->
            <!--base message start-->
            <fieldset>
                <legend>清库单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">清库单号</label>
                    <div class="row-input">
                        <input type="text" id="stockclearno"name="stockclearno" value="@if($stockclearno){{$stockclearno}}@else{{'系统编码'}} @endif" readonly>
                        <input type="hidden" id="sysno" name="sysno" value="@if($sysno){{$sysno}} @endif" readonly>
                    </div>

                    <label class="row-label">清库日期</label>
                    <div class="row-input required"><input type="text" name="stockcleardate" value="@if($stockcleardate){{date('Y-m-d',strtotime($stockcleardate))}}@else{{date('Y-m-d')}}@endif"  data-rule="required;date"  @if($stockclearstatus !=7 && $stockclearstatus !=4 ) data-toggle="datepicker"  @endif  @if($stockclearstatus==7 || $stockclearstatus ==4) readonly @endif ></div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" name="stockclearstatus" value="@if($stockclearstatus) {{$stockclearstatus}} @else {{'2'}} @endif" readonly>
                        <input type="text" name="stockclearstatusname" value="@if($stockclearstatusname[$stockclearstatus]) {{$stockclearstatusname[$stockclearstatus]}} @else {{'新建'}} @endif" readonly></div>
                    <input type="hidden" name="clearstatus" value="{{$stockclearstatus}}">
                    <label class="row-label">客户:</label>
                    <div class="row-input required">
                        <select name="customer_sysno" data-size="5" id="customer_sysno" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required" @if($stockclearstatus==7 || $stockclearstatus ==4 || $stockclearstatus ==3) disabled @endif>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($stockclearstatus==7 || $stockclearstatus ==4 || $stockclearstatus ==3) <input type="hidden" name="customer_sysno" value="{{$customer_sysno}}"> @endif
                    <label class="row-label">客服专员:</label>
                    <div class="row-input required">
                        <select name="cs_employee_sysno" data-size="5" id="cs_employee_sysno" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required" @if($stockclearstatus==7 || $stockclearstatus ==4 || $stockclearstatus ==3) disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                            <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                        </select>
                        @if($stockclearstatus==7 || $stockclearstatus ==4 || $stockclearstatus ==3)

                            <input type="hidden" name="cs_employee_sysno" value="{{$cs_employee_sysno}}">
                            <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                        @endif
                    </div>
                </div><br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>清库单明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="clearstock-detail-table" data-toggle="datagrid" data-options="{
                        height : '100%',
                        filterThead:false,
                        @if($stockclearstatus==2 || $stockclearstatus==6 || $stockclearstatus =='')
                                showToolbar: true,
                              toolbarCustom:$.CurrentNavtab.find('#clearstock_table'),
                              @endif
                                local: 'local',
                                dataUrl: '/clearstock/adddetail/id/{{$id}}',
                        dataType: 'json',
                        paging: false,
                        fullGrid:true,
                        linenumberAll: true,
                        showTfoot:true
                    }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'stockin_no',align:'center',width:100}">单号</th>
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'instockdate',align:'center'}">入库时间</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'instockqty',align:'center',calc:'sum'}">商检量</th>
                                <th data-options="{name:'stockqty',align:'center',calc:'sum'}">结存量</th>
                                <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
                                <th data-options="{name:'tankclearqty',align:'center'}">清库量</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'outstockqty',align:'center',hide:'true'}">实发数量</th>
                                <th data-options="{name:'sysno',align:'center',hide:'true'}">库存Id</th>
                                <th data-options="{name:'stockin_sysno',align:'center',hide:'true'}">所属库存类型Id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>

                {{--配图上传--}}
                <div class="remarks">
                    <fieldset>
                        <legend>上传清库单<span class='red'></span></legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                            {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'clearstock',action:'clear-edit'},
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
                <div class="clearfix"></div>
                <!--project end-->
                @if($stockclearstatus == 3 )
                    <div class="remarks">
                        <fieldset>
                            <legend>审核意见</legend>
                            <textarea id="clearstockoperdesc" name="operdesc" data-toggle="autoheight" rows="3" placeholder="请在此处填写退回意见">{{$operdesc}}</textarea>
                        </fieldset>
                    </div>
                @endif
                @if($stockclearstatus == 4 || $stockclearstatus == 7)
                    <div class="remarks">
                        <fieldset>
                            <legend>操作作废意见</legend>
                            <textarea id="clearstockabandonreason" name="abandonreason" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写作废意见"  @if($stockclearstatus == 7) disabled @endif>{{$abandonreason}}</textarea>
                        </fieldset>
                    </div>
                @endif
                <br><br>
                <div class="text-center btns-user">
                    @if( $stockclearstatus < 3 || $stockclearstatus==6)
                        <button id="clearstocksubmit1" type="button" onclick="clearstocksubmit(2)" class="btn btn-green btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                        <button id="clearstocksubmit2" type="button" onclick="clearstocksubmit(3)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if( $stockclearstatus == 3)
                        <button id="clearstocksubmit3" type="button" onclick="clearstocksubmit(4)" class="btn btn-green btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                        <button id="clearstocksubmit4" type="button" onclick="clearstocksubmit(6)" class="btn btn-red btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($stockclearstatus == 4 && $print!=1)
                        <button id="clearstocksubmit7" type="button" onclick="clearstocksubmit(7)" class="btn btn-red btn-lg">作废</button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($stockclearstatus==4 && $print==1)
                        <button id="stockshipinsubmit6" type="button" onclick="Design(printfun_stock_ship_in)" class="btn btn-green btn-lg">打印设计</button>
                        <button id="stockshipinsubmit6" type="button" onclick="Setup(printfun_stock_ship_in)" class="btn btn-green btn-lg">打印</button>
                    @endif
                    <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>
                </div>
                <br><br>
                <!--操作记录-->
                <div class="remarks hideshow" style="display: none;">
                    <fieldset>
                        <legend>操作记录明细</legend>
                        <div class="addTable">

                        </div>
                    </fieldset>
                </div>
                <br><br> <br><br> <br><br>
            </div>
        </form>
    </div>
</div>
@if($stockclearstatus ==2 ||  $stockclearstatus =='' ||  $stockclearstatus==6)
    <div id="clearstock_table">
        <button type="button" class="btn btn-blue" onclick="addclearstock()"><i class="fa fa-plus"></i> 添加</button>
        <button type="button" class="btn btn-red" onclick="removeclearstock()"><i class="fa fa-close"></i> 移除</button>
    </div>
@endif



<div id="div_stock_ship_in" style="display: none;" >
    <style>
        .table-dy,th{border:none;height: 50px;}
        .table-dy td{border: 1px solid #000;height:50px;text-align: center;}
        .t_head td{border: none;}
    </style>


    <table class='table-dy' border=0 cellSpacing=0 cellPadding=0 width='100%'  height="200" bordercolor="#000000" style="border-collapse:collapse">
        <caption><b><font face="黑体" size="8">{{$companyname}}</font></b><br><font face="黑体" size="4">(损/溢证明单)</font></caption>
        <thead>
        <tr class="t_head">
            <td><b>客户名称:</b></td>
            <td ></td>
            <td ><b>编号:</b></td>
            <td ></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="width: 12.5%;"><b>入库日期</b></td>
            <td style="width: 12.5%;"><b>品名</b></td>
            <td style="width: 12.5%;"><b>运输工具</b></td>
            <td style="width: 12.5%;"><b>提单量</b></td>
            <td style="width: 12.5%;"><b>商检量</b></td>
            <td style="width: 12.5%;"><b>实发量</b></td>
            <td style="width: 12.5%;"><b>损/溢量</b></td>
            <td style="width: 12.5%;"><b>损/溢率</b></td>
        </tr>
        <tr>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
            <td style="width: 12.5%;"></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th ><b>审核:</b></th>
            <th ><b>制单:</b></th>
            <th ><b>商务:</b></th>
            <th ><b>质计:</b></th>
            <th ><b>MIS:</b></th>
            <th ><b></b></th>
            <th ><b></b></th>
            <th ><b>盖章:</b></th>
        </tr>
        </tfoot>
    </table>

</div>
<script src="/static/common/js/custom.js"></script>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">

    //操作记录显示|隐藏
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '11');

    //切换用户数据切换当前用户的数据
    $("#customer_sysno").change(function(){
        var customer_sysno =  $.CurrentNavtab.find('#customer_sysno').val();
        $.CurrentNavtab.find('#clearstock-detail-table').datagrid('reload',  {data:[]});
    });

    //添加清库详情
    function addclearstock() {
        var customer_sysno =  $.CurrentNavtab.find('#customer_sysno').val();//客户姓名
        if(customer_sysno.length>0){
            BJUI.dialog({
                id:'clearstock-select-{{$id}}',
                url:'clearstock/adddata/customer_sysno/'+customer_sysno,
                title:'清库单详情',
                maxable:false,
                minable:false,
                width:1200,
                height:500,
                mask:true
            });
        }
        else{
            BJUI.alertmsg('warn','<h4>请先选中客户再选择入库单号!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    //选中移除数据
    function removeclearstock() {
        var selectdata = $.CurrentNavtab.find("#clearstock-detail-table").data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            //   BJUI.alertmsg('info', BJUI.getRegional('datagrid.selectMsg'));
            return false;
        }else{
            var allData  = $("#clearstock-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#clearstock-detail-table').datagrid('reload',{data:allData});
        }
    }


    //提交保存操作
    function clearstocksubmit(step) {
        var clearstock_employeename = $.CurrentNavtab.find("#cs_employee_sysno option:selected").text();
        $.CurrentNavtab.find("#stockclearno").val();
        $.CurrentNavtab.find("#sysno").val();
        $.CurrentNavtab.find("#cs_employeename").val(clearstock_employeename);//客服姓名
        $.CurrentNavtab.find("Input[name='stockclearstatus']").val(step);//操作值
        var Obj = $.CurrentNavtab.find("#clearstock-detail-table").data('allData');//获取清库详情数据
        $.CurrentNavtab.find("Input[name='clearstockdetail']").val(JSON.stringify(Obj));//详情数据绑定

        if(Obj=='' || typeof(Obj)=='undefined' || Obj==null || !Obj){
            BJUI.alertmsg('warn','<h4>清库明细不能为空!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            if(step==4 || step==6 || step==7){
                if(step==6){ //审核驳回验证备注
                    $.CurrentNavtab.find("#clearstockoperdesc").attr("data-rule","required");
                }
                if(step==7){ //审核驳回验证备注
                    $.CurrentNavtab.find("#clearstockabandonreason").attr("data-rule","required");
                }
                $.CurrentNavtab.find('#clearstockform').attr('action',"/clearstock/auditJson");
                if(step==4){
                    $.CurrentNavtab.find("#clearstockoperdesc").attr("data-rule","a");
                }
            }
            var  controlproportion = Obj[0].controlproportion;
            var  instockqty = Obj[0].instockqty;
            var  okqty = Obj[0].okqty;
            console.log(Obj);

            if(step==4){
                if( parseFloat(okqty) > parseFloat(controlproportion)*parseFloat(instockqty)/1000){
                    BJUI.alertmsg('confirm', '您选择的库存余量大于内控损耗量，请确定是否清库' , {okCall: function() {
                       submit_clearstock()
                    }
                    });
                }else {
                   submit_clearstock()
                }
            }else {
                //写ajax提交
              submit_clearstock()
            }
        }
    }
    function  submit_clearstock(){
        //写ajax提交
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#clearstockform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                //console.log('返回内容1：\n'+ JSON.stringify(json))
                BJUI.navtab('reloadFlag', 'navab297');
                BJUI.navtab('closeCurrentTab','navab296');

            }
        });
    }

    //添加数据筛选
    function addclearstocks() {
        var customer_sysno =  $.CurrentNavtab.find('#customer_sysno').val();//客户姓名
        var clearstock_employeename = $.CurrentNavtab.find("#cs_employee_sysno option:selected").text();
        $.CurrentNavtab.find("#cs_employeename").val(clearstock_employeename);//客服姓名
        if(customer_sysno.length > 0){
            BJUI.dialog({
                id:'clearstock-select-{{$id}}',
                url:'/clearstock/adddata/customer_sysno/'+customer_sysno,
                title:'增加库存信息',
                width:600,
                height:300,
                mask:true
            });
        }else{
            BJUI.alertmsg('warn','<h4>请先选中客户再选择入库单号!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
    }


</script>


<!--云打印-->
<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object  id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<script language="javascript" type="text/javascript">
    var LODOP; //声明为全局打印变量
    //打印入库单字段布局
    var printfun_stock_ship_in=function CreateStockIn(){
        LODOP=getLodop();
        LODOP.PRINT_INITA(0,0,1200,600,"损/溢证明单");
        LODOP.SET_PRINT_PAGESIZE(2,0,0,"A4");
        LODOP.ADD_PRINT_TABLE("2%","1%","96%","98%",document.getElementById("div_stock_ship_in").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0,0,0,800,600,"");
        LODOP.ADD_PRINT_TEXT(194,25,100,20,"{{$printdata['intime']}}");
        LODOP.ADD_PRINT_TEXT(193,151,100,20,"{{$printdata['goodsname']}}");
        LODOP.ADD_PRINT_TEXT(192,284,100,20,"{{$printdata['worktool']}}");
        LODOP.ADD_PRINT_TEXT(192,417,100,20,"{{$printdata['takegoodsnum']}}");
        LODOP.ADD_PRINT_TEXT(191,542,100,20,"{{$printdata['instockqty']}}");
        LODOP.ADD_PRINT_TEXT(193,675,100,20,"{{$printdata['outstockqty']}}");
        LODOP.ADD_PRINT_TEXT(195,803,100,20,"{{$printdata['sunliang']}}");
        LODOP.ADD_PRINT_TEXT(195,931,100,20,"{{$printdata['ganlv']}}"+'‰');
        LODOP.ADD_PRINT_TEXT(94,129,135,25,"{{$printdata['customername']}}");
        LODOP.ADD_PRINT_TEXT(95,371,334,24,"{{$printdata['stockclearno']}}");
//        LODOP.PREVIEW();

    }


</script>