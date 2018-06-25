<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="{{$prefix}}form" action="/stocktrans/editJson" method="POST" class="datagrid-edit-form"
              data-data-type="json">
            <input type="hidden" id="{{$prefix}}detaildata" name="detaildata" value="">
            <input type="hidden" id="{{$prefix}}stocktransstatus" name="stocktransstatus" value="">
            <input type="hidden" id="{{$prefix}}sysno" name="sysno" value="{{$sysno}}">
            <input type="hidden" name="booktrans_sysno" value="{{$booktrans_sysno}}">
            <input type="hidden" name="bookingtransno" value="{{$bookingtransno}}">
            <input type="hidden" id="ca_address" name="ca_address" value="{{$ca_address}}">
            <input type="hidden" id="ca_no" name="ca_no" value="{{$ca_no}}">
            <!--base message start-->
            <fieldset>
                <legend>货权转移信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">货权转移单号</label>
                    <div class="row-input required">
                        <input type="text" name="stocktransno"
                               value="@if($stocktransno) {{$stocktransno}}  @endif"
                               @if(($stocktransstatus>=3 && $stocktransstatus!=6) || $look=='look' ) readonly @endif data-rule="required">
                    </div>
                    <label class="row-label">转移日期</label>
                    <div class="row-input required">
                        <input type="text" class="buyfree" id="stocktransdate" name="stocktransdate"
                               value="@if($stocktransdate){{$stocktransdate}}@else{{date('Y-m-d')}}@endif"
                               @if(($stocktransstatus<3 || $stocktransstatus==6) && $look!='look') data-toggle="datepicker"
                               @endif data-rule="required"
                               @if(($stocktransstatus>=3 && $stocktransstatus!=6) || $look=='look') readonly @endif>
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        @if($stocktransstatus==2 ) <input type="text" value="暂存" readonly>
                        @elseif($stocktransstatus==3 ) <input type="text" value="待审核" readonly>
                        @elseif($stocktransstatus==4 ) <input type="text" value="已审核" readonly>
                        @elseif($stocktransstatus==5 ) <input type="text" value="已完成" readonly>
                        @elseif($stocktransstatus==6 ) <input type="text" value="退回" readonly>
                        @elseif($stocktransstatus==8 ) <input type="text" value="驳回" readonly>
                        @else     <input type="text" value="新建" readonly>
                        @endif
                        <input type="hidden" id="ststatus" name="ststatus" value="{{$stocktransstatus}}">
                    </div>
                    <label class="row-label">转让方</label>
                    <div class="row-input required">
                        <select name="sale_customer_sysno" id="{{$prefix}}sale_customer_sysno" data-size="8"
                                data-nextselect="#{{$prefix}}sale_contract_sysno"
                                data-refurl="/stocktrans/customercontractJson/id/{value}" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" data-rule="required"
                                onchange="getReload(this.value)"
                                @if(($stocktransstatus>=3 && $stocktransstatus!=6) || $look=='look' ) disabled="disabled" @endif >
                            <option value="0">请选择</option>
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

                        <select name="buy_customer_sysno" id="{{$prefix}}buy_customer_sysno" data-size="8"
                                data-toggle="selectpicker" data-live-search="true" data-width="100%"
                                data-rule="required" data-nextselect="#{{$prefix}}contract_sysno"
                                data-refurl="/stocktrans/customercontractJson/id/{value}"
                                @if( ($stocktransstatus>=3 && $stocktransstatus!=6) ||$look=='look') disabled="disabled" @endif>
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
                    <label class="row-label">选择合同方</label>
                    <div class="row-input">
                        <input type="radio" name="cost_contract_type" value="1" data-toggle="icheck" data-rule="checked"
                               @if(!$cost_contract_type || $cost_contract_type== 1) checked @endif data-label="转让方合同"
                               @if(($stocktransstatus>=3 && $stocktransstatus!=6) || $look=='look') disabled @endif >
                        <input type="radio" name="cost_contract_type" value="2" data-toggle="icheck"
                               @if($cost_contract_type== 2) checked @endif data-label="受让方合同"
                               @if($stocktransstatus>=3 && $stocktransstatus!=6) disabled @endif>
                        <input type="hidden" id="cost_contract_type" name="edit_cost_contract_type"
                               value="{{$cost_contract_type}}" readonly>
                    </div>
                    <span id="sale_contract_sysno1">
                    <label class="row-label">转让方合同编号</label>
                    <div class="row-input required">
                        <select name="sale_contract_sysno" id="{{$prefix}}sale_contract_sysno"
                                class="stocktrank_sale_contrank_id" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-rule="" data-width="100%"
                                @if(($stocktransstatus>=3 && $stocktransstatus!=6)||$look=='look') disabled="disabled" @endif>
                         {{--   <option value="">请选择</option>--}}
                            <option value="{{$sale_contract_sysno or ''}}">{{$sale_contractno or '请选择'}}</option>
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
                                @if(($stocktransstatus>=3 && $stocktransstatus!=6) || $look=='look') disabled="disabled" @endif>
                            <option value="{{$contract_sysno or ''}}">{{$contractno or '请选择'}}</option>
                            {{--  <option value=""> 请选择</option>--}}
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
                               @if(($stocktransstatus<3 || $stocktransstatus==6) && $look!='look' ) data-toggle="datepicker"
                               @endif data-rule="required"
                               @if(($stocktransstatus>=3 && $stocktransstatus!=6) || $look=='look') readonly @endif>
                    </div>
                    <label class="row-label">免仓期天数</label>
                    <div class="row-input required">
                        <input type="text" name="freecostdate" id="freedate" data-rule=""
                               value="@if($freecostdate) {{$freecostdate}} @else {{'0'}} @endif" readonly>
                    </div>
                    <label class="row-label">单据来源</label>
                    <div class="row-input required">
                        @if($docsource==1 || $docsource=='') <input type="text" value="手工创建" readonly>
                        @elseif($docsource==2 ) <input type="text" value="国烨云仓" readonly>
                        @elseif($docsource==3 ) <input type="text" value="初始化导入" readonly>
                        @endif
                        <input type="hidden" id="docsource" name="docsource" value="{{$docsource}}">
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
                        height:'100%',
                        filterThead:false,
                       @if((!$stocktransstatus ||$stocktransstatus<3 || $stocktransstatus==6) && $look!='look' )
                                showToolbar: true,
                              toolbarCustom:$.CurrentNavtab.find('#edit_stocktrank_tb'),
                              toolbarItem:'',
@endif
                                local: 'local',
                               addLocation: 'last',
                               dataUrl: '/stocktrans/detailListJson/id/{{$sysno}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        @if((!$stocktransstatus||$stocktransstatus<3 || $stocktransstatus==6)&& $look!='look' )
                        {{-- editMode: {dialog:{width:'700',height:'500',title:'添加入库明细',mask:true}},--}}
                                editMode :false,
                              editUrl: '/stocktrans/editDetail/id/{{$sysno or '0'}}/prefix/{{$prefix}}',
                        @endif
                                paging: false,
                                linenumberAll: true,
                                showTfoot:true,
                                fullGrid:true
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'stockin_no',align:'center'}">来源单号</th>
                                {{--<th data-options="{name:'stockno',align:'center'}">库存单号</th>--}}
                                {{--<th data-options="{name:'instockdate',align:'center',hide:'true'}">入库日期</th>--}}
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">品名id</th>
                                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                    货物性质
                                </th>
                                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                                <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库数量</th>
                                {{--  <th data-options="{name:'stockqty',align:'center',calc:'sum'}">可用数量</th>--}}
                                <th data-options="{name:'transqty',align:'center',calc:'sum'}">转移数量</th>
                                <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                @if(!$booktrans_sysno)
                                    <th data-options="{name:'out_stock_sysno',align:'center',hide:'true'}">入库单ID</th>
                                @else
                                    <th data-options="{name:'stock_sysno',align:'center',hide:'true'}">入库单ID</th>
                                @endif
                                <th data-options="{name:'stockno',align:'center',hide:'true'}">库存单编号</th>
                                <th data-options="{name:'firstfrom_sysno',align:'center',hide:'true'}">第一次入库单id</th>
                                <th data-options="{name:'contract_sysno',align:'center',hide:'true'}">合同id</th>
                                <th data-options="{name:'storagetank_sysno',align:'center',hide:'true'}">罐号id</th>
                                <th data-options="{name:'release_num',align:'center',hide:'true'}">报关数量</th>
                                <th data-options="{name:'unrelease_num',align:'center',hide:'true'}">未报关数量</th>
                                <th data-options="{name:'goodsnature',align:'center',hide:'true'}">货物性质</th>
                                <th data-options="{name:'tank_stockqty',align:'center',hide:'true'}">储罐容量</th>
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
                    <fieldset class="transfieldset" id="transfieldset">
                        {{--  <legend>上传货权转移单<span class='red'>*</span></legend>--}}
                        <legend>上传货权转移单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 30,
                            formData: {module:'stocktrans',action:'attach-1', doc_sysno:'{{$sysno}}'},
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
                        <legend>获取提货单</legend>
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
                @if($docsource==2 && ($stocktransstatus==2 ||$stocktransstatus==8) )
                    <fieldset>
                        <legend>驳回意见</legend>
                        <textarea name="rejectreason" id="rejectreason" data-toggle="autoheight" cols="auto"
                                  rows="3" placeholder="请在此处填写驳回意见">{{$rejectreason}}</textarea>
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
            </div>
            <br><br>
            <div class="text-center btns-user">
                @if($look !='look')
                    @if($docsource==2 && ($stocktransstatus==2 || $stocktransstatus==6))
                        <button type="button" onclick="{{$prefix}}submit(3)" class="btn btn-green btn-lg">提交</button>
                        &nbsp;&nbsp;&nbsp;
                          @if($stocktransstatus !=6)
                        <button type="button" onclick="{{$prefix}}audit(8)" class="btn btn-red btn-lg">驳回</button>&nbsp;
                        &nbsp;&nbsp;
                           @endif
                    @else
                        @if(!$stocktransstatus||$stocktransstatus<3 || $stocktransstatus==6 )
                            <button type="button" onclick="{{$prefix}}submit(2)" class="btn btn-green btn-lg">保存
                            </button>&nbsp;&nbsp;&nbsp;
                            <button type="button" onclick="{{$prefix}}submit(3)" class="btn btn-green btn-lg">提交
                            </button>&nbsp;&nbsp;&nbsp;
                        @endif
                    @endif


                    @if($stocktransstatus==3)
                        <button type="button" onclick="{{$prefix}}audit(4)" class="btn btn-green btn-lg">审核通过</button>
                        &nbsp;&nbsp;&nbsp;
                        <button type="button" onclick="{{$prefix}}audit(6)" class="btn btn-red btn-lg">审核不通过</button>
                        &nbsp;&nbsp;&nbsp;
                    @endif
                    @if($stocktransstatus==4)
                        <button id="stockshipinsubmit6" type="button" onclick="Design(printfun_trans)"
                                class="btn btn-green btn-lg">打印设计
                        </button>
                        <button id="stockshipinsubmit6" type="button" onclick="Setup(printfun_trans)"
                                class="btn btn-green btn-lg">打印
                        </button>
                    @endif
                @endif
                <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录
                </button>&nbsp;
                   @if($ca_address !='' && $docsource==2 && $look=='look' )
                                 <a class="btn btn-blue" href="{{$ca_address}}" target="_blank" style="height: 50px;line-height: 38px;" >查看CA合同</a>
                                    <dd>&nbsp;</dd>
                    @endif
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

<div id="div_stocktrans_edit" style="display: none;">
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


    <table class='table-dy' border=0 cellSpacing=0 cellPadding=0 width='100%' height="200" bordercolor="#000000"
           style="border-collapse:collapse">
        <caption><b><font face="黑体" size="8">{{$companyname}}</font></b><br><font face="黑体" size="4">(货权转让通知单)</font>
        </caption>
        <thead>
        <tr class="t_head">
            <td><b>日期:</b></td>
            <td></td>
            <td><b>编号:</b></td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="width: 20%;"><b>原货主名称</b></td>
            <td style="width: 40%;"></td>
            <td style="width: 40%;"><b>备注</b></td>
        </tr>
        <tr>
            <td style="width: 20%;"><b>转让方名称</b></td>
            <td style="width: 40%;"></td>
            <td style="width: 40%;" rowspan='6'></td>
        </tr>
        <tr>
            <td style="width: 20%;"><b>受让方名称</b></td>
            <td style="width: 40%;"></td>
        </tr>
        <tr>
            <td style="width: 20%;"><b>品名</b></td>
            <td style="width: 40%;"></td>
        </tr>
        <tr>
            <td style="width: 20%;"><b>转让数量(大写)</b></td>
            <td style="width: 40%;"></td>
        </tr>
        <tr>
            <td style="width: 20%;"><b>受让方计费期间</b></td>
            <td style="width: 40%;"></td>
        </tr>
        {{-- <tr>
             <td style="width: 20%;"><b>转让期间付费方名称</b></td>
             <td style="width: 40%;"></td>
         </tr>--}}
        </tbody>
        <tfoot>
        <tr>
            <th><b>开票室:</b></th>
            <th><b>审核:</b></th>
            <th><b>制单:</b></th>
        </tr>
        </tfoot>
    </table>

</div>
@if(($stocktransstatus <= 2 || $stocktransstatus==6) && $look !='look')
    <div id="edit_stocktrank_tb">

        <button type="button" class="btn btn-blue" data-icon="plus" onclick="addstocktrank()"> 添加</button>
        <button type="button" class="btn btn-red" data-icon="close" onclick="removeStockrank()"> 删除</button>
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editStockrank()"> 修改</button>

    </div>
@endif
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

            {{--$('#{{$prefix}}sale_contract_sysno').empty();--}}
            {{--$('#{{$prefix}}sale_contract_sysno').selectpicker('render');--}}
            {{--$('#{{$prefix}}sale_contract_sysno').selectpicker('refresh');--}}

        } else {
            $.CurrentNavtab.find("#sale_contract_sysno1").hide();
            $.CurrentNavtab.find("#buy_contract_sysno2").show();

            {{--$('#{{$prefix}}contract_sysno').empty();--}}
            {{--$('#{{$prefix}}contract_sysno').selectpicker('render');--}}
            {{--$('#{{$prefix}}contract_sysno').selectpicker('refresh');--}}
        }
    })


    //明细添加功能
    function addstocktrank() {
        BJUI.dialog({
            url: '/stocktrans/editDetail/id/{{$sysno or '0'}}/prefix/{{$prefix}}',
            title: '增加货品明细',
            mask: true,
            width: 800,
            height: 400
        });
        return;
    }
    //明细删除
    function removeStockrank() {
        var selectdata = $.CurrentNavtab.find('.prefix_retankdetail').data('selectedDatas');

        if (selectdata == undefined || selectdata == '' || selectdata == null) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $(".prefix_retankdetail").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('.prefix_retankdetail').datagrid('reload', {data: allData});
        }
    }

    //明细修改功能
    function editStockrank() {
        var selectedDatas = $.CurrentNavtab.find("#{{$prefix}}detail-table").data('selectedDatas');
        var prefix_sale_c_sysno = $('#prefix_sale_customer_sysno').val();
        if (prefix_sale_c_sysno == '') {
            BJUI.alertmsg('warn', '<h4>请选择转让方!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        console.log(selectedDatas);
        if (typeof(selectedDatas) != 'undefined' && selectedDatas.length == 1 && selectedDatas != null && selectedDatas != '') {
            console.log(selectedDatas);
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
            BJUI.alertmsg('warn', '请选中一行进行修改', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }

    }

    function {{$prefix}}audit(status) {
        if (status == 6) {
            var result = isnull();
            // console.log(result);
            if (!result) {
                return false;
            }
        }
        if( status==8 ){
            var val = $('#rejectreason').val();
            if (val == '') {
                BJUI.alertmsg('warn', '请填写驳回意见', {displayPosition: 'middlecenter', displayMode: 'fade'});
                return false;
            }
            BJUI.alertmsg('confirm', '确认驳回吗？', {
                okCall: function () {
                    audit_stocktrank(status);
                }
            })
            return;
        }
        audit_stocktrank(status);
    }
    function  audit_stocktrank(status){
        var cost_contract_type = $('#cost_contract_type').val();
        if (cost_contract_type == 1) {
            var contract_sysno = $('#{{$prefix}}sale_contract_sysno').val();
            var contract_no = $('#{{$prefix}}sale_contract_sysno option:selected').text();

        } else {
            var contract_sysno = $('#{{$prefix}}contract_sysno').val();
            var contract_no = $('#{{$prefix}}contract_sysno option:selected').text();
        }

        var data = $.CurrentNavtab.find('#{{$prefix}}detail-table').data('allData');
        var num  = 0;
        for (var i = data.length - 1; i >= 0; i--) {
            num+=parseFloat(data[i]['transqty']);
        }
        var params = {}; 
        params['contract_sysno'] = contract_sysno;
        params['contract_no'] = contract_no;
        params['stockin_sysno'] = data[0]['firstfrom_sysno'];
        params['stockin_no'] = data[0]['stockin_no'];
        params['stockqty'] = num;
        params['goods_sysno'] = data[0]['goods_sysno'];
        params['goodsname'] = data[0]['goodsname'];
        params['qualityname'] = data[0]['qualityname'];
        params['customer_sysno'] = $('#{{$prefix}}sale_customer_sysno').val();
        params['customer_name'] = $('#{{$prefix}}sale_customer_sysno option:selected').text();
        params['stockindate'] = data[0]['instockdate'];
        params['buystartdate'] = $('#buystartdate').val();
        params['buy_customer_sysno'] = $('#{{$prefix}}buy_customer_sysno').val();
        params['buy_customer_name'] = $('#{{$prefix}}buy_customer_sysno option:selected').text();
        params['freedate'] = $('#freedate').val();
        params['last_stock_sysno'] = data[0]['stock_sysno'];
        // console.log(params); return;
        //国烨云仓CA显示
        var docsource = $('#docsource').val();
        var ca_address = $('#ca_address').val();
        var ca_no = $('#ca_no').val();
        params['docsource'] = docsource;
        params['ca_address'] = ca_address;
        params['ca_no'] = ca_no;
        if(docsource==2 && ca_address !='' && ca_no!='' ){
            BJUI.setRegional('progressmsg', 'CA读取中……');
        }
        BJUI.ajax('doajax', {
            url: '/stocktrans/auditJson/',
            data: {sysno: $("#{{$prefix}}sysno").val(), status: status, stockmarks: $("#{{$prefix}}stockmarks").val(),rejectreason : $('#rejectreason').val() , costdata:params},
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
                BJUI.alertmsg('warn', '转让方合同编号不能为空', {displayPosition: 'middlecenter', displayMode: 'fade'});
                return false;
            }
        } else {
            var buyVal = $('#{{$prefix}}contract_sysno').val();
            if (buyVal == '') {
                BJUI.alertmsg('warn', '受让方合同编号不能为空', {displayPosition: 'middlecenter', displayMode: 'fade'});
                return false;
            }
        }
        //不能是同一个人
        var sale_c_sysno = $('#{{$prefix}}sale_customer_sysno').val();
        var buy_c_sysno = $('#{{$prefix}}buy_customer_sysno').val();
        if (sale_c_sysno == buy_c_sysno) {
            BJUI.alertmsg('warn', '请选择不同的客户', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        //提交验证附件必须上传
//       var attachnum = 0;
//        var docsource = $('#docsource').val();
//        console.log(docsource);
//        $.CurrentNavtab.find('.transfieldset').each(function(){
//            if($(this).find(".filelist > li").length > 0)
//            {
//                attachnum = attachnum +1;
//            }
//        });
//        if(docsource==1 || docsource==''){
//            if(attachnum<1  )
//            {
//                BJUI.alertmsg('warn', '请先上传附件再提交表单',{displayPosition:'middlecenter',displayMode:'fade'});
//                return false;
//            }
//        }

        //提交时控货弱控
        if (step == 3) {
            $.ajax({
                url: '/stocktrans/controltype',
                type: 'POST',
                dataType: 'json',
                data: {detail: Obj, data: $('#{{$prefix}}form').serializeJson()},
                success: function (data) {
                    if (data.contracttype == 3) {
                        BJUI.alertmsg('confirm', '计费合同为包罐合同。', {
                            okCall: function () {
                                submit_control_stocktrank();
                            }
                        })
                    } else if (data.contracttype == 4) {
                        BJUI.alertmsg('confirm', '计费合同为包罐容合同。', {
                            okCall: function () {
                                submit_control_stocktrank();
                            }
                        })
                    } else if (data.contracttype == 0) {
                        BJUI.alertmsg('confirm', '参数错误', {
                            okCall: function () {
                                submit_control_stocktrank();
                            }
                        })
                    } else {
                        submit_control_stocktrank();
                    }
                }
            });

        } else {
            submit_stocktrank();
        }
    }
    //提交
    function submit_stocktrank() {
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
    //控货提教
    function submit_control_stocktrank() {
        var Obj = $("#{{$prefix}}detail-table").data('allData');
        $.ajax({
            url: '/stocktrans/controlgoods',
            type: 'POST',
            dataType: 'json',
            data: {detail: Obj, data: $('#{{$prefix}}form').serializeJson()},
            success: function (data) {
                console.log(data);
                if (data.code == 200) {
                    var info = '';
                    if (data.message[1] != 0 || data.message[2] != 0 || data.message[3] != 0) {
                        if (data.message[1] != 0) {
                            info += ' 欠费超信用期限' + data.message[1] + '天;';
                        }
                        if (data.message[2] != 0) {
                            info += ' 欠费超信用额度' + data.message[2] + ';';
                        }
                        if (data.message[3] != 0) {
                            info += ' 欠费超控货比重' + data.message[3] + ';';
                        }
                        BJUI.alertmsg('confirm', info, {
                            okCall: function () {
                                submit_stocktrank();
                            }
                        });
                    } else {
                        submit_stocktrank();
                    }
                } else {
                    if (data.code == 400) {
                        BJUI.alertmsg('info', data.message);
                        BJUI.alertmsg('warn', data.message + '！!', {
                            displayPosition: 'middlecenter',
                            displayMode: 'fade'
                        })
                        return false;
                    } else if (data.code == 300) {
                        BJUI.alertmsg('confirm', '控货异常', {
                            okCall: function () {
                                submit_stocktrank();
                            }
                        })
                    } else {
                        submit_stocktrank();
                    }
                }
            },
        });
    }


    function isnull() {
        var val = $('#{{$prefix}}stockmarks').val();
        if (val == '') {
            BJUI.alertmsg('warn', '请填写审核意见', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        } else {
            return true;
        }
    }
    //获取提货样单
    function getReload(cid) {
        if (cid == '' || cid == 0) {
            $.CurrentNavtab.find('#botans_sample_div').empty();
        } else {
            BJUI.ajax('doajax', {
                url: '/stocktrans/customerSampleJson/',
                data: {cid: cid},
                loadingmask: false,
                okCallback: function (json, options) {
                    $.CurrentNavtab.find('#botans_sample_div').empty();
                    for (i = 0; i < json.length; i++) {
                        var obj = json[i];
                        var uploadedAttr = ' style="cursor:pointer;" data-toggle="dialog" data-options="{id:\'bjui-dialog-view-upload-image\', image:\'' + '/attachment/preview/id/' + obj.sysno + '\', width:800, height:500, mask:true, title:\'查看图片\'}"',
                            li = $('<li class="uploaded" >' +
                                '<p class="imgWrap" ' + uploadedAttr + '>' +
                                '<img src="/attachment/preview/id/' + obj.sysno + '">' +
                                '</p>' +
                                '</li>');

                        $.CurrentNavtab.find('#botans_sample_div').append(li);
                    }
                }
            });
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


<!--云打印-->
<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<script language="javascript" type="text/javascript">
    var LODOP; //声明为全局打印变量
    console.log({{$table_html}});
    //打印入库单字段布局
    var printfun_trans = function CreateStockIn() {
        var Obj = $("#{{$prefix}}detail-table").data('allData');
        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "货权转让单");
        LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");
        LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", document.getElementById("div_stocktrans_edit").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(142, 235, 134, 25, "{{$parentShipper}}");
        LODOP.ADD_PRINT_TEXT(191, 238, 131, 26, "{{$sale_customername}}");
        LODOP.ADD_PRINT_TEXT(240, 238, 129, 27, "{{$buy_customername}}");
        LODOP.ADD_PRINT_TEXT(293, 239, 128, 26, Obj[0].goodsname);
        LODOP.ADD_PRINT_TEXT(339, 239, 126, 29, changeNumMoneyToChinese(Obj[0].transqty, '吨', '', true));
        LODOP.ADD_PRINT_TEXT(391, 242, 125, 28, "{{$buystartdate}}");
        LODOP.ADD_PRINT_TEXT(443, 242, 125, 27, "");
        LODOP.ADD_PRINT_TEXT(98, 153, 162, 25, "{{$stocktransdate}}");
        LODOP.ADD_PRINT_TEXT(98, 876, 186, 29, "{{$stocktransno}}");
        LODOP.ADD_PRINT_TEXT(178, 641, 410, 266,Obj[0].memo);
//        LODOP.PREVIEW();
    }

</script>