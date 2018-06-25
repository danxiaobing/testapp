<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
    <br> <br><br>
        <form id="inotice-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" id='invoce_id' name="id" value="{{$id}}">
            <input type="hidden" id="invoicedetaildata" name="invoicedetaildata" value="">
            <input type="hidden" id="coststatus" name="coststatus" value="@if($coststatus) {{$coststatus}} @else {{2}} @endif" >
            <fieldset>
                <legend>开票通知单</legend>
                <br><br>

                <div class="bjui-row col-3">
                    <label class="row-label">通知单号</label>
                    <div class="row-input">
                            <input type="text" id='noticeedit_invoiceno' name="invoiceno" value="{{$invoiceno}}" readonly>
                    </div>

                    <label class="row-label">日期</label>
                    <div class="row-input">
                        <input type="text" name="invoicedate" data-toggle="datepicker" value="@if($invoicedate) {{ $invoicedate }} @else {{date('Y-m-d')}} @endif" >
                    </div>

                    <label class="row-label">操作状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" readonly>
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <select name="customer_sysno" id="invoice_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" onchange="getInvoiceFee()">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="invoice_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">发票抬头</label>
                    <div class="row-input required">
                        <select name="invoice_company_sysno" id="invoice_company_sysno" data-rule="required" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%">
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
                    <div class="row-input required">
                        <input type="text" name="invoicegoodsname" value="{{$invoicegoodsname}}" data-rule="required" >
                    </div>

                    <label class="row-label">结算日期</label>
                    <div class="row-input required">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" name="coststartdate" data-toggle="datepicker" value="{{$coststartdate}}" id="coststartdate" placeholder="开始日期" data-rule="required" readonly onchange="getInvoiceFee()">
                            <div class="input-group-addon">to</div>
                            <input type="text" class="form-control" name="costenddate" data-toggle="datepicker" value="{{$costenddate}}" id="costenddate" placeholder="结束日期" data-rule="required" readonly onchange="getInvoiceFee()">
                        </div>
                    </div>

                    <!-- <label class="row-label">费用名称</label>
                    <div class="row-input required">
                        <select name="costtype" id="invoice_costtype"  data-size="5"  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" onchange="getInvoiceFee()">
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
                        <input type="radio" name="berthcost" value="1" data-toggle="icheck" data-label="是" @if($costtype == 2) checked @endif>
                        <input type="radio" name="berthcost" value="2" data-toggle="icheck" data-label="否" @if($costtype != 2 || !$costtype) checked @endif>
                    </div>

                    <label class="row-label">总金额</label>
                    <div class="row-input required">
                        <input type="text" id="invoce_total" name="costtotal" value="{{$costtotal}}" data-rule="required" readonly>
                    </div>

                    <label class="row-label">折扣总额</label>
                    <div class="row-input ">
                        <input type="text" name="costdiscount" id="invoice_costdiscount" value="{{$costdiscount}}" onchange="getCostdiscount(this)" data-rule="number range[0~]">
                    </div>

                    <label class="row-label">开票金额</label>
                    <div class="row-input required">
                        <input type="text" id="invoce_costinvoie" name="costinvoice" value="{{$costinvoice}}" data-rule="required" readonly>
                    </div>
                    <br>
                    <label class="row-label">备 注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo or ''}}</textarea>
                    </div>

                    <label class="row-label">品名</label>
                    <div class="row-input">
                        <select name="invoice_company_goodsname" id="invoice_company_goodsname" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" onchange="getInvoiceFee()" >
                            <option value="">请选择</option>
                            @foreach($goods_name as $item)
                                <option value="{{$item['sysno']}}" >{{$item['goodsname']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">船名</label>
                    <div class="row-input ">
                        <input type="text" name="invoice_company_shipname" id="invoice_company_shipname" value="" onchange="getInvoiceFee()">
                    </div>

                    <br>
                </div><br><br>
            </fieldset>
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>费用明细</legend>
                    <table class="table table-bordered" id="invoice-detail-table" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#invoice-detail-tb'),
                            toolbarItem: '',
                            data:{{$detaillist}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot: true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'costno',align:'center'}">费用单号</th>
                            <th data-options="{name:'customer_name',align:'center'}">客户</th>
                            <th data-options="{name:'costdate',align:'center'}">进货日期</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'takegoodsnum',align:'center'}">提单量(吨)</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'costname',align:'center'}">费用类型</th>
{{--                        <th data-options="{name:'isexceedfirst',align:'center',render:function(value){switch(value) { case '1': return '是'; default: return '否';  }}}">超出首期</th>
                            <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
                            <th data-options="{name:'unitname',align:'center'}">计量单位</th>--}}
                            <th data-options="{name:'created_at',align:'center'}">计费日期</th>
                            <th data-options="{name:'costqty',align:'center',render:function(value){if(value=='') {return '--'}}}">计费数量</th>
                            <th data-options="{name:'unitprice',align:'center'}">计费单价</th>
                            <th data-options="{name:'datenum',align:'center',render:function(value){if(value=='') {return '--'}}}">计价天数</th>
                            <th data-options="{name:'totalprice',align:'center',calc:'sum'}">金额(元)</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">储罐id</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div><br>
            <fieldset class="customerfieldset">
                <legend>上传发票</legend>
                <br>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'invoice',action:'notice',doc_sysno:'{{$id}}'},
                            required: false,
                            uploaded: '{{$uploaded}}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf',
                                mimeTypes: '.jpg,.png,.pdf'
                            }
                        }"
                >
                <br>
            </fieldset>
        </form>
        <br>

        <br>

            @if($coststatus < 3)
                <div class="text-center ">
                    <button type="button" onclick="inoticeSubmit(2)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="inoticeSubmit(3)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="record()" class="btn btn-sdefault btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
                </div>
            @endif
            @if($coststatus == 6)
                        <div class="text-center ">
                            <button type="button" onclick="inoticeSubmit(6)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                            <button type="button" onclick="inoticeSubmit(3)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                            <button type="button" onclick="record()" class="btn btn-sdefault btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
                        </div>
                    @endif
        <br><br>

          <!--操作记录-->
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
<div id="invoice-detail-tb">
    <button type="button" class="btn btn-red" onclick="subInvoiceDetail()" data-icon="close">删除</button>
</div>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function () {
        if (!{{$id}}) {
            $.ajax({
                url: '/bookout/getbookoutno/prefix/K',
                type: 'GET',
                dataType: 'json',
                success:function(data){
                    $('#noticeedit_invoiceno').attr({
                        value: data,
                    });

                },
            });
        }
    });

    $('input:radio').on('ifChecked',function(){
        getInvoiceFee();
    })
    function getInvoiceFee(){
        var b = $('#invoice_company_shipname').val();
        BJUI.ajax('doajax', {
            url: '/invoice/detailjson/',
            data:{id: $.CurrentNavtab.find('#invoice_customer_sysno').val(), coststartdate:$.CurrentNavtab.find('#coststartdate').val(), costenddate:$.CurrentNavtab.find('#costenddate').val(),berthcost:$('input:radio:checked').val(),companygoodsname:$('#invoice_company_goodsname').find("option:selected").html(),companyshipname:b},
            loadingmask: true,
            okCallback: function(json, options) {
                $.CurrentNavtab.find('#invoice-detail-table').datagrid('reload',  {data:json});
                countCostinvoice(json);
            }
        });
    }

    function getCostdiscount(){
        var costdiscount = $("#invoice_costdiscount").val();
        var total = $("#invoce_total").val();

        if(total == 0 || total == ''){
            total = 0;
            $("#invoce_costinvoie").val('');
            return;
        }
        $("#invoce_costinvoie").val(total - costdiscount);
    }



    function inoticeSubmit(step) {

        var Obj =    $.CurrentNavtab.find('#invoice-detail-table').data('allData');

        if(typeof  Obj == 'undefined'){
            BJUI.alertmsg('info', '请选择费用明细');
            return;
        }

        $("#invoice_companyname").val($.CurrentNavtab.find("#invoice_company_sysno option:selected").text());
        $("#invoice_customername").val($.CurrentNavtab.find("#invoice_customer_sysno option:selected").text());
//        $("#invoice_companygoodsname").val($.CurrentNavtab.find("#invoice_company_goodsname option:selected").text());


        $("#invoicedetaildata").val(JSON.stringify(Obj));
        $("#coststatus").val(step);

        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#inotice-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab315');
                BJUI.navtab('closeCurrentTab', '');
            }
        });

    }

    function inoticeExam(step) {
    if(step==1 && $('#exammarks').val()==''){
        $('#exammarks').attr('data-rule','required');
        BJUI.alertmsg('info', "请填写审核意见");
        return false;
    }else{
        $('#exammarks').removeAttr('data-rule');

    }
        $.CurrentNavtab.find("#examstep").val(step);

        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#inotice-exam-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab315');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function subInvoiceDetail() {
        var selectdata  =  $.CurrentNavtab.find('#invoice-detail-table').data('selectedDatas');
        var id = $.CurrentNavtab.find('#invoce_id').val();
        var allData  = $("#invoice-detail-table").data('allData');
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var total = $.CurrentNavtab.find('#invoce_total').val();
        var costinvoie = $.CurrentNavtab.find('#invoce_costinvoie').val();

        if(total<0 || costinvoie<0){
            BJUI.alertmsg('warn','总金额或开票金额不能为负数',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if (id != 0 ) {
            BJUI.ajax('doajax', {
                url: '/invoice/deldetail/id/'+id+'/sysno/' + selectdata[0]['sysno'],
                loadingmask: false,
                okCallback: function(json, options) {
                    if(json.code == 200){
                        for (var i = selectdata.length - 1; i >= 0; i--) {
                            allData = allData.remove(selectdata[i].gridIndex);
                        }
                        countCostinvoice(allData);
                        $.CurrentNavtab.find('#invoice-detail-table').datagrid('reload',  {data:allData});
                    }else{
                        BJUI.alertmsg('warn', '删除失败',{displayPosition:'middlecenter',displayMode:'fade'});
                        return ;
                    }
                }
            })
        }else{
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }

            countCostinvoice(allData);
            $.CurrentNavtab.find('#invoice-detail-table').datagrid('reload',  {data:allData});
        }



    };

    //计算开票金额
    function countCostinvoice(data) {
        var total = 0;
        for(i=0;i < data.length; i ++){
            total+= parseFloat(data[i].totalprice);
        }

        $("#invoce_total").val(Math.round(total*1000)/1000);

        var discount  = $("#invoice_costdiscount").val();
        if(discount ==''){
            discount = 0;
        }
        if(total == 0){
            $("#invoce_costinvoie").val('');
            return;
        }
        $("#invoce_costinvoie").val(Math.round((total - discount)*1000)/1000);
    }

    /*--------------------------操作记录优化-----------------------------*/
    var jl=0;
    function record() {

    if(jl==0){

        console.log(jl);

         addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '14');
    }

    jl++;

    $.CurrentNavtab.find('.showhide').toggle(500);

}
</script>