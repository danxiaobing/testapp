<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
    <br/><br/>
        <form id='addinvoice-form' action="/invoice/addInvoiceJson" method="POST" class="datagrid-edit-form"  data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <fieldset>
                <legend>开票通知单</legend>
                <br>

                <div class="bjui-row col-3">
                    <label class="row-label">通知单号</label>
                    <div class="row-input">
                            <input type="text" name="invoiceno" value="{{$invoiceno}}" readonly>
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
                    <div class="row-input required">
                        <select name="customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">开票单位</label>
                    <div class="row-input">
                        <select name="company_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($companylist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $base_company_sysno) selected @endif>{{$item['companyname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="companyname" value="{{$base_companyname}}">
                    </div>
                    
                    <label class="row-label">开票品名</label>
                    <div class="row-input required">
                        <input type="text" name="invoicegoodsname" value="{{$invoicegoodsname}}" data-rule="required" readonly>
                    </div>

                    <label class="row-label">结算日期</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" name="coststartdate" data-toggle="datepicker" value="{{$coststartdate}}" placeholder="开始日期" readonly readonly>
                            <div class="input-group-addon">to</div>
                            <input type="text" class="form-control" name="costenddate" data-toggle="datepicker" value="{{$costenddate}}"  placeholder="结束日期" readonly readonly>
                        </div>
                    </div>

                    <!-- <label class="row-label">费用名称</label>
                    <div class="row-input required">
                        <select name="costtype" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%"  disabled>
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
                    <div class="row-input required">
                        <input type="text" name="costtotal" value="{{$costtotal}}" data-rule="required" readonly>
                    </div>

                    <label class="row-label">折扣总额</label>
                    <div class="row-input ">
                        <input type="text" name="costdiscount" value="{{$costdiscount}}" readonly>
                    </div>

                    <label class="row-label">开票金额</label>
                    <div class="row-input required">
                        <input type="text" id='addinvoice_costinvoice' name="costinvoice" value="{{$costinvoice}}" data-rule="required" readonly>
                    </div>

                    <label class="row-label">发票号</label>
                    <div class="row-input required">
                        <input type="text" name="invoicenumber" value="{{$invoicenumber}}" data-rule="required">
                    </div>

                    <label class="row-label">已开发票总金额</label>
                    <div class="row-input required">
                        <input type="text" id='addinvoice_hasinvoicecost' name="hasinvoicecost" value="{{$hasinvoicecost}}"  data-rule="required;number range[0~]">
                    </div>

                    <label class="row-label">未开发票总金额</label>
                    <div class="row-input">
                        <input type="text" id='addinvoice_uninvoicecost' name="uninvoicecost" value="{{$uninvoicecost}}" readonly>
                    </div>

                    <label class="row-label">未收款金额</label>
                    <div class="row-input">
                        <input type="text" name="unreceivablecost" value="{{$unreceivablecost}}" readonly>
                    </div>

                    <label class="row-label">已收款金额</label>
                    <div class="row-input">
                        <input type="text" name="receivablecost" value="{{$receivablecost}}" readonly>
                    </div>
                    <br>
                    <label class="row-label">备 注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo or ''}}</textarea>
                    </div>

                    <br>
                </div>
            </fieldset>
            <div class="remarks">
                <fieldset>
                    <legend>费用明细</legend>

                    <table class="table table-bordered" data-toggle="datagrid" data-options="{
                            filterThead:false,
                            showToolbar: false,
                            height:'100%',
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
            <div class="remarks">
            <fieldset>
                <legend>上传发票</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'invoice',action:'notice',doc_sysno:'{{$id}}'},
                            required: false,
                            uploaded: '{{ $uploaded }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf',
                                mimeTypes: '.jpg,.png,,.pdf'
                            }
                        }"
                >
            </fieldset>
            </div>
        </form>

        <br/><br/>        
        <div class="text-center ">
            <button type="button" onclick="inoticeSubmit()" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            <button type="button" onclick="showRecords()" class="btn btn-lg">操作记录</button>&nbsp;&nbsp;&nbsp;
        </div>


            <br/><br/>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
             <legend>操作记录明细</legend>
                <div class="addTable">

                </div>
            </fieldset>
        </div>
        <br/><br/>

    </div>
</div>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    addLog($.CurrentNavtab.find('.hideshow'), {{$id}}, '14');

    function inoticeSubmit() {
        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#addinvoice-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                
                BJUI.navtab('reloadFlag', 'navab315');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    $('#addinvoice_hasinvoicecost').blur(function(event) {
        var costinvoice = parseFloat($('#addinvoice_costinvoice').val());
        var hasinvoicecost = parseFloat($('#addinvoice_hasinvoicecost').val());
        var costinvoice = parseFloat($('#addinvoice_costinvoice').val());
        if (hasinvoicecost>costinvoice) {
            BJUI.alertmsg('warn', '已开发票金额不能大于开票金额',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        if(hasinvoicecost){
            var uninvoicecost = Math.floor((costinvoice-hasinvoicecost) * 100)/100;
            $('#addinvoice_uninvoicecost').val(uninvoicecost);
        }else{
            $('#addinvoice_uninvoicecost').val('');
        }    
    });

</script>
