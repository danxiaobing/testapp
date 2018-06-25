<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="{{$prefix}}form" action="/stocktrans/editJson" method="POST" class="datagrid-edit-form"
              data-data-type="json">
            <input type="hidden" id="{{$prefix}}detaildata" name="detaildata" value="">
            <input type="hidden" id="{{$prefix}}stocktransstatus" name="stocktransstatus" value="">
            <input type="hidden" id="{{$prefix}}sysno" name="sysno" value="{{$sysno}}">
            <input type="hidden" name="booktrans_sysno" value="{{$booktrans_sysno}}">
            <input type="hidden" name="bookingtransno" value="{{$bookingtransno}}">

            <!--base message start-->
            <fieldset>
                <legend>货权转移信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">货权转移单号</label>
                    <div class="row-input">
                        <input type="text" name="stocktransno"
                               value="@if($stocktransno) {{$stocktransno}} @else {{系统自动生成}} @endif" readonly>
                    </div>
                    <label class="row-label">转移日期</label>
                    <div class="row-input required">
                        <input type="text" class="buyfree" id="stocktransdate" name="stocktransdate"
                               value="@if($stocktransdate){{$stocktransdate}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required" readonly>
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        @if($stocktransstatus==2 ) <input type="text" value="暂存" readonly>
                        @elseif($stocktransstatus==3 ) <input type="text" value="待审核" readonly>
                        @elseif($stocktransstatus==4 ) <input type="text" value="已审核" readonly>
                        @elseif($stocktransstatus==5 ) <input type="text" value="已完成" readonly>
                        @elseif($stocktransstatus==6 ) <input type="text" value="退回" readonly>
                        @else     <input type="text" value="新建" readonly>
                        @endif
                        <input type="hidden" id="ststatus" name="ststatus" value="{{$stocktransstatus}}">
                    </div>
                    <label class="row-label">选择合同方</label>
                    <div class="row-input">
                        <input type="radio" name="cost_contract_type" value="1" data-toggle="icheck" data-rule="checked"
                               @if(!$cost_contract_type || $cost_contract_type== 1) checked @endif data-label="转让方合同"
                               disabled>
                        <input type="radio" name="cost_contract_type" value="2" data-toggle="icheck"
                               @if($cost_contract_type== 2) checked @endif data-label="受让方合同" disabled>
                        <input type="hidden" id="cost_contract_type" name="edit_cost_contract_type"
                               value="{{$cost_contract_type}}" readonly>
                    </div>
                    <label class="row-label">转让方</label>
                    <div class="row-input required">
                        <select name="sale_customer_sysno" id="{{$prefix}}sale_customer_sysno" data-size="5"
                                data-nextselect="#{{$prefix}}sale_contract_sysno"
                                data-refurl="/stocktrans/customercontractJson/id/{value}" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" data-rule="required" onchange="getReload()"
                                disabled="disabled">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $sale_customer_sysno) selected @endif>{{$item['customername']}}</option>
                                @if($item['sysno'] == $sale_customer_sysno)
                                    <?php $sale_customername = $item['customername'];?>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" id="{{$prefix}}sale_customername" class="prefix_sale_customername"
                               name="sale_customername" value="{{$sale_customername}}" readonly>
                        <input type="hidden" id="prefix_sale_customer_sysno" value="{{$sale_customer_sysno}}" readonly>
                    </div>
                    <label class="row-label">受让方</label>
                    <div class="row-input required">

                        <select name="buy_customer_sysno" id="{{$prefix}}buy_customer_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-width="100%"
                                data-rule="required" data-nextselect="#{{$prefix}}contract_sysno"
                                data-refurl="/stocktrans/customercontractJson/id/{value}" disabled="disabled">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $buy_customer_sysno) selected @endif>{{$item['customername']}}</option>
                                @if($item['sysno'] == $buy_customer_sysno)
                                    <?php $buy_customername = $item['customername'];?>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" id="{{$prefix}}buy_customername" name="buy_customername"
                               value="{{$buy_customername}}" readonly>
                    </div>
                    <span id="sale_contract_sysno1">
                    <label class="row-label">转让方合同编号</label>
                    <div class="row-input required">
                        <select name="sale_contract_sysno" id="{{$prefix}}sale_contract_sysno"
                                class="stocktrank_sale_contrank_id" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-rule="" data-width="100%" disabled="disabled">
                            <option value="">请选择</option>
                            @foreach($contractlist['list'] as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $sale_contract_sysno) selected @endif>{{$item['contractnodisplay']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="sale_contractno" id="{{$prefix}}sale_contractno"
                               value="{{$sale_contractno}}">
                    </div>
                   </span>
                    <span id="buy_contract_sysno2" hidden>
                    <label class="row-label">受让方合同编号</label>
                    <div class="row-input required">
                        <select name="contract_sysno" id="{{$prefix}}contract_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="" data-width="100%"
                                disabled="disabled">
                            <option value="{{$contract_sysno or ''}}">{{$contractno or '请选择'}}</option>
                            @foreach($contractlist['list'] as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $contract_sysno) selected @endif>{{$item['contractnodisplay']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="contractno" id="{{$prefix}}contractno" value="{{$contractno}}">
                        <input type="hidden" name="contract_id" id="{{$prefix}}contract_id" value="{{$contract_sysno}}">
                    </div>
                    </span>

                    <label class="row-label">受让方计费起始日</label>
                    <div class="row-input required">
                        <input type="text" class="buyfree" id="buystartdate" name="buystartdate"
                               value="@if($buystartdate){{date('Y-m-d',strtotime($buystartdate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required">
                    </div>
                    <label class="row-label">免仓期天数</label>
                    <div class="row-input required">
                        <input type="text" name="freecostdate" id="freedate" data-rule="range[0~]"
                               value="@if($freecostdate) {{$freecostdate}} @else {{'0'}} @endif" readonly>
                    </div>
                    <label class="row-label">单据来源</label>
                    <div class="row-input required">
                        @if($docsource==1 || $docsource=='') <input type="text" value="手工创建" readonly>
                        @elseif($docsource==2 ) <input type="text" value="国烨云仓" readonly>
                        @elseif($docsource==3 ) <input type="text" value="初始化导入" readonly>
                        @endif
                        <input type="hidden" id="ststatus" name="ststatus" value="{{$docsource}}">
                    </div>
                </div>
                <br></fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>货品明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered prefix_retankdetail" id="{{$prefix}}detail-table"
                               height="100+50*{{count($list)}}" data-toggle="datagrid" data-options="{
                        tableWidth:'100%',
                        filterThead:false,
                        showToolbar: true,

                                local: 'local',
                               addLocation: 'last',
                               dataUrl: '/stocktrans/detailListJson/id/{{$sysno}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',

                                paging: false,
                                linenumberAll: true,
                                showTfoot:true
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'stockin_no',align:'center'}">来源单号</th>
                                {{--<th data-options="{name:'stockno',align:'center'}">库存单号</th>--}}
                                {{--<th data-options="{name:'instockdate',align:'center',hide:'true'}">入库日期</th>--}}
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">品名id</th>
                                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                    货物性质
                                </th>
                                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                                <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库数量</th>
                                {{--  <th data-options="{name:'stockqty',align:'center',calc:'sum'}">可用库数量</th>--}}
                                <th data-options="{name:'transqty',align:'center',calc:'sum'}">转移数量</th>
                                <th data-options="{name:'storagetankname',align:'center',hide:'true'}">罐号</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                @if(!$booktrans_sysno)
                                    <th data-options="{name:'out_stock_sysno',align:'center',hide:'true'}">入库单ID</th>
                                @else
                                    <th data-options="{name:'stock_sysno',align:'center',hide:'true'}">入库单ID</th>
                                @endif
                                <th data-options="{name:'stockno',align:'center',hide:'true'}">库存单编号</th>
                                <th data-options="{name:'firstfrom_sysno',align:'center',hide:'true'}">第一次入库单id</th>
                                <th data-options="{name:'contract_sysno',align:'center',hide:'true'}">合同id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>
            <!--project end-->
            <div class="comuser-add" style="margin-top:15px;">                <!-- 自带bug -->
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
                    <fieldset class="transfieldset">
                        <legend>上传货权转移单<span class='red'>*</span></legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'stocktrans',action:'attach-1'},
                            required: false,
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
                        <legend>获取提货单<span class='red'>*</span></legend>
                        <div class="bjui-row col-4" id="uploader">
                            <ul class="filelist" id="botans_sample_div">
                                @if( is_array($samples) &&  count($samples) > 0)
                                    @foreach($samples as $sample)
                                        <li class="uploaded">
                                            <p class="imgWrap" style="cursor:pointer;" data-toggle="dialog"
                                               data-options="{id:'bjui-dialog-view-upload-image', image:'/attachment/preview/id/{{$sample['sysno']}}', width:800, height:500, mask:true, title:'查看图片'}">
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
                @if($handle=='handle' && $docsource==2 && $stocktransstatus==2)
                    <fieldset>
                        <legend>处理意见</legend>
                        <textarea name="stockmarks" id="{{$prefix}}stockmarks" data-toggle="autoheight" cols="auto"
                                  rows="3" placeholder="请在此处填写处理意见">{{$stockmarks}}</textarea>
                    </fieldset>
                @else
                    @if($stocktransstatus==3)
                        <fieldset>
                            <legend>操作</legend>
                            <textarea name="stockmarks" id="{{$prefix}}stockmarks" data-toggle="autoheight" cols="auto"
                                      rows="3" placeholder="请在此处填写审核意见">{{$stockmarks}}</textarea>
                        </fieldset>
                    @endif
                @endif
                <div class="text-center btns-user">
                    <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录
                    </button>&nbsp;
                </div>
            </div>

            <br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">

                    </div>
                </fieldset>
            </div>
            <br>
        </form>
    </div>
</div>
<script src="/static/common/js/custom.js"></script>
<script src="/static/common/js/common.js"></script>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'})
</script>
<script type="text/javascript">
    //操作记录显示|隐藏
    addLog($.CurrentNavtab.find('.addTable'), {{$sysno}}, '9');

    $(function () {
        //编辑时显示合同方编号
        var cost_contract_type = $("input[type='radio']:checked").val();
        // var cost_contract_type = $('#cost_contract_type').val();
        if (cost_contract_type == 1) {
            $.CurrentNavtab.find("#buy_contract_sysno2").hide();
            $.CurrentNavtab.find("#sale_contract_sysno1").show();
        } else if (cost_contract_type == 2) {
            $.CurrentNavtab.find("#sale_contract_sysno1").hide();
            $.CurrentNavtab.find("#buy_contract_sysno2").show();
        }
    })
    $(function () {
        //选中text值
        $("#{{$prefix}}sale_customer_sysno").change(function () {
            text = $(this).find("option:selected").text();
            $("#{{$prefix}}sale_customername").val(text);
            // 得到转让方id
            text = $(this).find("option:selected").val();
            $("#prefix_sale_customer_sysno").val(text);
            //清空明细
            $.CurrentNavtab.find('#{{$prefix}}detail-table').datagrid('reload', {data: []});

        });

        //添加转让方合同编号
        $("#{{$prefix}}sale_contract_sysno").change(function () {
            text = $(this).find("option:selected").text();
            $("#{{$prefix}}sale_contractno").val(text);
        })


        $("#{{$prefix}}buy_customer_sysno").change(function () {
            text = $(this).find("option:selected").text();
            $("#{{$prefix}}buy_customername").val(text);
        });
        $("#{{$prefix}}contract_sysno").change(function () {
            text = $(this).find("option:selected").text();
            $("#{{$prefix}}contractno").val(text);
        });

    });


    $("input:radio[name='cost_contract_type']").on('ifChecked', function () {
        var V = $(this).val();
        if (V == 1) {
            $.CurrentNavtab.find("#buy_contract_sysno2").hide();
            $.CurrentNavtab.find("#sale_contract_sysno1").show();
        } else {
            $.CurrentNavtab.find("#sale_contract_sysno1").hide();
            $.CurrentNavtab.find("#buy_contract_sysno2").show();
        }
    })

    //明细修改功能
    function editStockrank() {
        var selectedDatas = $.CurrentNavtab.find("#{{$prefix}}detail-table").data('selectedDatas');
        var prefix_sale_c_sysno = $('#prefix_sale_customer_sysno').val();
        if (prefix_sale_c_sysno == '') {
            BJUI.alertmsg('info', '请选择转让方');
            return false;
        }
        if (typeof(selectedDatas) != 'undefined' && selectedDatas.length == 1) {
//            console.log(selectedDatas);
            BJUI.dialog({
                url: '/stocktrans/stocktrankedit/prefix/edit/',
                type: 'POST',
                data: {selectedDatasArray: selectedDatas[0], sale_customer_sysno: prefix_sale_c_sysno},
                title: '修改货权转移明细',
                width: 900,
                height: 600,
                mask: true
            });
        } else {
            BJUI.alertmsg('info', '请选中一行进行修改');
        }

    }

    function getReload() {

        // $.CurrentNavtab.find('#{{$prefix}}detail-table').datagrid('reload',  {data:[]});
        {{--BJUI.ajax('doajax', {--}}
        {{--url: '/stocktrans/detailListJson/id/'+$('#{{$prefix}}sysno').val(),--}}
        {{--//data:{id: $('#sysno').val()},--}}
        {{--loadingmask: true,--}}
        {{--okCallback: function(json, options) {--}}
        {{--$.CurrentNavtab.find('#{{$prefix}}detail-table').datagrid('reload',  {data:[]});--}}
        {{--}--}}
        {{--});--}}
    }

    function {{$prefix}}audit(status) {
        if (status == 6) {
            var result = isnull();
            // console.log(result);
            if (!result) {
                return false;
            }
        }
        BJUI.ajax('doajax', {
            url: '/stocktrans/auditJson/',
            data: {sysno: $("#{{$prefix}}sysno").val(), status: status, stockmarks: $("#{{$prefix}}stockmarks").val()},
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                console.log('返回内容1：\n' + JSON.stringify(json))
                BJUI.navtab('reloadFlag', 'navab283');
                BJUI.navtab('reloadFlag', 'navab285');
                BJUI.navtab('closeCurrentTab');
            }
        });
    }

    function {{$prefix}}submit(step) {
        var Obj = $("#{{$prefix}}detail-table").data('allData');
        $("#{{$prefix}}detaildata").val(JSON.stringify(Obj));
        $("#{{$prefix}}stocktransstatus").val(step);

        var ridaov = $("input:radio[name='cost_contract_type']:checked").val();
        if (ridaov == 1) {
            var saleVal = $('#{{$prefix}}sale_contract_sysno').val();
            if (saleVal == '') {
                BJUI.alertmsg('info', '转让方合同编号不能为空');
                return false;
            }
        } else {
            var buyVal = $('#{{$prefix}}contract_sysno').val();
            if (buyVal == '') {
                BJUI.alertmsg('info', '受让方合同编号不能为空');
                return false;
            }
        }
        //不能是同一个人
        var sale_c_sysno = $('#{{$prefix}}sale_customer_sysno').val();
        var buy_c_sysno = $('#{{$prefix}}buy_customer_sysno').val();
        if (sale_c_sysno == buy_c_sysno) {
            BJUI.alertmsg('info', '请选择不同的客户');
            return false;
        }
        //提交验证附件必须上传
        if ($('.filelist > li').length < 1) {
            BJUI.alertmsg('info', '请先上传附件再提交表单！');
            return;
        }
        BJUI.ajax('ajaxform', {
            url: '/stocktrans/editJson',
            form: $.CurrentNavtab.find('#{{$prefix}}form'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                console.log('返回内容1：\n' + JSON.stringify(json))
                BJUI.navtab('reloadFlag', 'navab283');
                BJUI.navtab('reloadFlag', 'navab285');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function isnull() {
        var val = $('#{{$prefix}}stockmarks').val();
        if (val == '') {
            BJUI.alertmsg('info', "请填写审核意见");
            return false;
        } else {
            return true;
        }
    }
    //添加免仓期天数
    $('#buystartdate').change(function () {
        ischange();
    });
    $('#stocktransdate').change(function () {
        ischange();
    });
    function ischange() {
        var buystartdate = $('#buystartdate').val();
        var a = Date.parse(new Date(buystartdate)) / 1000;
        var stocktransdate = $('#stocktransdate').val();
        var b = Date.parse(new Date(stocktransdate)) / 1000;
        var date = (a - b) / (60 * 60 * 24);
        $('#freedate').val(parseInt(date));
    }

</script>