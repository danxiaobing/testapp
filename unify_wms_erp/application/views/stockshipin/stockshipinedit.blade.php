<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="stockshipinform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="1">
            <input type="hidden" id="stockshipindetaildata" name="stockshipindetaildata" value="">
            <input type="hidden" id="stockshipinquality" name="stockshipinquality" value="">
            <input type="hidden" name="isCA" value="{{$isCA}}">
            <!--base message start-->
            <fieldset>
                <legend>入库单信息</legend>
                <br><br>
                <div class="bjui-row col-3">
                    <label class="row-label">入库单号</label>

                    <div class="row-input">
                        <input type="text" name="stockinno" value="@if($stockinno){{$stockinno}}@else{{系统自动生成}}@endif"
                               readonly>
                    </div>
                    <label class="row-label">入库日期</label>

                    <div class="row-input required">
                        <input type="text" id="stockindate" name="stockindate"
                               value="@if($stockindate){{date('Y-m-d',strtotime($stockindate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date"
                               @if($type != 'back' && $type!='review' && $type!='register')
                               data-toggle="datepicker"
                               @else
                               disabled
                                @endif
                        >
                    </div>
                    <label class="row-label">单据状态</label>

                    <div class="row-input required">
                        <input type="hidden" id="stockinstatus" name="stockinstatus"
                               value="@if($stockinstatus){{$stockinstatus}}@else{{2}}@endif" readonly>
                        @foreach($stockinstatusnamelist as $item)
                            @if($item['id'] == $stockinstatus)
                                <input type="text" name="stockinstatusname" value="{{$item['name']}}" readonly>
                            @endif
                        @endforeach
                    </div>
                    <label class="row-label">客户</label>

                    <div class="row-input">
                        <select name="obj.customer_sysno" id="stockshipin_customer_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true"
                                data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="log_customer_sysno" value="{{$customer_sysno}}">
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
                        <input type="text" name="contractno" value="@if($contractno){{$contractno}}@else{{''}}@endif"
                               readonly>
                    </div>
                    <label class="row-label">客服专员</label>

                    <div class="row-input ">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5" data-toggle="selectpicker"
                                @if($type == 'back' || $type=='review' || $type == 'register' ) disabled
                                @endif data-live-search="true" 
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cc_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>
                    <label class="row-label">质计</label>

                    <div class="row-input">
                        <select name="zj_employee_sysno" id="zj_employee_sysno" data-size="5" data-toggle="selectpicker"
                                @if($type == 'back' || $type=='review' || $type == 'register' ) disabled
                                @endif    data-live-search="true"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $zj_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="zj_employeename" id="zj_employeename" value="{{$zj_employeename}}">
                    </div>
                    <label class="row-label">仓储</label>

                    <div class="row-input">
                        <select name="cc_employee_sysno" id="cc_employee_sysno" data-size="5" data-toggle="selectpicker"
                                @if($type == 'back' || $type=='review' || $type == 'register') disabled
                                @endif   data-live-search="true"
                                data-width="100%">
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
                                @if($type == 'back' || $type=='review' || $type == 'register') disabled
                                @endif   data-live-search="true"
                                
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
                        <input type="text"  name="wharf_date"
                               value="@if($wharf_date){{date('Y-m-d',strtotime($wharf_date))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="date"
                               @if($type != 'back' && $type!='review' && $type!='register')
                               data-toggle="datepicker"
                               @else
                               disabled
                                @endif
                        >
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
                        <select name="" data-size="5"
                                data-toggle="selectpicker"
                                disabled
                                data-rule="required" data-width="100%">
                            <option value="1" @if($isqualitycheck==1 || !$isqualitycheck) selected @endif>是</option>
                            <option value="2">否</option>
                        </select>
                        <input type="hidden" name="isqualitycheck" value="1">
                    </div>

                    <label class="row-label">提单数量(吨)</label>

                    <div class="row-input required">
                        <input type="text" name="takegoodsnum"  @if($type == 'back' || $type=='review' || $type == 'register' ) readonly @endif
                            value="{{$takegoodsnum}}" data-rule="required;number;range[0~]">
                    </div>

                    <label class="row-label">船检数量(吨)</label>

                    <div class="row-input">
                        <input type="text" name="shipcheckqty" id="shipcheckqty"  @if($type == 'back' || $type=='review' || $type == 'register' ) readonly @endif
                        value="{{$shipcheckqty}}" data-rule="number range[0~]">
                    </div>


                    <label class="row-label">送货公司</label>

                    <div class="row-input">
                        <input type="text" name="deliverycompany" id="deliverycompany"  @if($type == 'back' || $type=='review' || $type == 'register' ) readonly @endif
                        value="{{$deliverycompany}}" data-rule="">
                    </div>

                    <label class="row-label">司磅员</label>

                    <div class="row-input">
                        <select name="sby_employee_sysno" id="sby_employee_sysno" data-size="5"
                                data-toggle="selectpicker"
                                @if($type == 'back' || $type=='review' || $type == 'register' ) disabled @endif
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
                    <textarea name="memo" data-toggle="autoheight" cols="27.5" rows="3"  @if($type == 'back' || $type=='review' || $type == 'register' ) readonly @endif >{{$memo}}</textarea>
                </div>
                <br><br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>入库单明细</legend>
                    <table class="table table-bordered" id="stockshipin-detail-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        @if($type =='addattach' || $type=='sure' || $type=='review' || $type=='back') showToolbar: false, @else  showToolbar: true,@endif
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
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                货物性质
                            </th>
                            <th data-options="{name:'tobeqty',align:'center'}">通知数量</th>
                            <th data-options="{name:'takegoodsnum',align:'center',hide:true}">提单数量</th>
                            <th data-options="{name:'shipcheckqty',align:'center',hide:true}">船检数量</th>
                            <th data-options="{name:'beqty',align:'center'}">商检数量</th>
                            <th data-options="{name:'unitname',align:'center',render:function(value){if(value=='') {return '吨'} else {return value}} }">
                                计量单位
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
            <br>
            <!--project end-->
            
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
            <br>
            <!--upload start-->
            <div class="comuser-add">
                <!-- 自带bug -->
                <div style="display: none">
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
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
                            formData: {module:'stockshipin',action:'declare_release'},
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
            <div class="remarks">
                @if($type == 'back')
                    <fieldset>
                        <legend>作废原因</legend>
                        <textarea id="stockshipinmarks" name="stockmarks" data-toggle="autoheight" cols="auto" rows="3"
                                  placeholder="请在此处填写">{{$stockmarks}}</textarea>
                        <br>
                        <br>
                    </fieldset>
                @endif
                @if($type == 'review')
                    <fieldset>
                        <legend>操作</legend>
                        <textarea id="stockshipinmarks" name="stockmarks" data-toggle="autoheight" cols="auto" rows="3"
                                  placeholder="请在此处填写审核意见">{{$stockmarks}}</textarea>
                        <br>
                        <br>
                    </fieldset>
                @endif
            </div>
            <br><br>
            <div class="text-center btns-user">
                @if($type=='back')
                    <button id="stockshipinsubmit5" type="button" onclick="stockshipinsubmit(5)"
                            class="btn btn-red btn-lg">作废
                    </button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($type=='review')
                    <button id="stockshipinsubmit4" type="button" onclick="stockshipinsubmit(4)"
                            class="btn btn-green btn-lg">审核通过
                    </button>&nbsp;&nbsp;&nbsp;
                    <button id="stockshipinsubmit6" type="button" onclick="stockshipinsubmit(6)"
                            class="btn btn-red btn-lg">审核驳回
                    </button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($type =='attach')
                    <button id="stockshipinsubmit1" type="button" onclick="stockshipinsubmit(1)"
                            class="btn btn-green btn-lg">上传
                    </button>&nbsp;
                @endif
                @if($type == 'register')
                    <button id="stockshipinsubmit8" type="button" onclick="stockshipinsubmit(8)"
                            class="btn btn-green btn-lg">登记
                    </button>&nbsp;
                @endif
                @if($type == 'edit' || !isset($type))
                    @if($stockinstatus < 3 || $stockinstatus >= 5)
                        <button id="stockshipinsubmit1" type="button" onclick="stockshipinsubmit(2)"
                                class="btn btn-green btn-lg">保存
                        </button>&nbsp;&nbsp;&nbsp;
                        <button id="stockshipinsubmit2" type="button" onclick="stockshipinsubmit(3)"
                                class="btn btn-green btn-lg">提交
                        </button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($stockinstatus==4)
                        <button id="stockshipinsubmit6" type="button" onclick="Design(printfun_stockship_in)"
                                class="btn btn-green btn-lg">打印设计
                        </button>
                        <button id="stockshipinsubmit6" type="button" onclick="Setup(printfun_stockship_in)"
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

<div id="stockshipin_tb">
    @if($type == 'edit' || !isset($type) || $type == 'register')
        @if(!$booking_in_sysno)
            <button type="button" class="btn btn-green" onclick="addStockshipin()" data-icon="plus">添加</button>
        @else
            <button type="button" class="btn btn-green" data-icon="edit" onclick="editStockshipin()">修改</button>
        @if(!$type)
            <button type="button" class="btn btn-green" data-icon="sitemap" onclick="splitStockshipin()">拆单</button>
            <button type="button" class="btn btn-red" data-icon="times" onclick="delStockshipin()">删除</button>
        @endif
    @endif
@endif
<!-- <button type="button" class="btn btn-red" onclick="subStockshipin()"><i class="fa fa-minus"></i> 移除</button> -->
</div>

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

    // $.CurrentNavtab.find('.hideshow').slideUp();

    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '3');

    // function showH() {

    //     $.CurrentNavtab.find('.hideshow').toggle(500);

    // }

    //----------------------操作记录 end
</script>

<script type="text/javascript">
    function addStockshipin() {
        var customer_sysno = $.CurrentNavtab.find('#stockshipin_customer_sysno').val();
        if (customer_sysno.length > 0) {
            BJUI.dialog({
                id: 'sotckshipin-detail-{{$id}}',
                url: '/stockshipin/adddetail/cid/' + customer_sysno,
                title: '增加入库单明细',
                width: 700,
                height: 550,
                mask: true
            });
        } else {
            BJUI.alertmsg('warn', '请先选中客户再添加明细单', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
        return;
    }

    function editStockshipin() {

        var receiptdata = $.CurrentNavtab.find('#stockshipin-detail-table').data('selectedDatas')
        if (receiptdata == undefined || receiptdata.length == 0) {
            BJUI.alertmsg('warn', "请先选择入库明细");
        } else {
            BJUI.dialog({
                id: 'stockin-receipt-{{$id}}',
                data: receiptdata[0],
                type: 'POST',
                url: '/stockshipin/detailedit/status/' + '{{$stockinstatus}}',
                title: '入库单明细',
                width: 700,
                height: 600,
                mask: true
            });

        }
        return;
    }

    function splitStockshipin()
    {
        var receiptdata = $.CurrentNavtab.find('#stockshipin-detail-table').data('selectedDatas')
        var stockinstatus = '{{$stockinstatus}}';
        if(stockinstatus==''){stockinstatus=0};
        if (receiptdata == undefined || receiptdata.length == 0) {
            BJUI.alertmsg('warn', "请先选择入库明细");
        } else {
            BJUI.dialog({
                id: 'stockin-receipt-{{$id}}',
                data: receiptdata[0],
                type: 'POST',
                url: '/stockshipin/detailedit/status/' + stockinstatus+'/split/'+1,
                title: '入库单明细',
                width: 700,
                height: 600,
                mask: true
            });

        }
        return;        
    }


    function delStockshipin()
    {
        var selectdata  =  $.CurrentNavtab.find('#stockshipin-detail-table').data('selectedDatas');
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var allData  = $("#stockshipin-detail-table").data('allData');
        if(allData.length==1){
            BJUI.alertmsg('warn','至少留有一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return ;
        }
        for (var i = selectdata.length - 1; i >= 0; i--) {
            allData = allData.remove(selectdata[i].gridIndex);
        }
        $.CurrentNavtab.find('#stockshipin-detail-table').datagrid('reload',  {data:allData});
    }

    function stockshipinsubmit(step) {
        if (step == 5 || step == 6) {
            $.CurrentNavtab.find("#stockshipinmarks").attr("data-rule", "required");
        } else {
            $.CurrentNavtab.find('#stockshipinform').data('validator').options.ignore = '#stockshipinmarks';
        }
        var Obj = $.CurrentNavtab.find("#stockshipin-detail-table").data('allData');
        var Quobj = $.CurrentNavtab.find("#stockshipin-qualitycheck-table").data('allData');
        var num = 0;
        for (var i = Obj.length - 1; i >= 0; i--) {

            if (!Obj[i].beqty) {
                BJUI.alertmsg('warn', '请填写实际数量');
                return;
            }
        }


        var error4Uploader = false;
        $.CurrentNavtab.find('.customerfieldset').each(function () {
            if (!$(this).find('.uploadBtn').hasClass('disabled') && $(this).find(".filelist > li").length > 0) {
                error4Uploader = true;
            }
        });
        if (error4Uploader) {
            BJUI.alertmsg('warn', '请先提交图片再提交表单！');
            return;
        }

        //如果是审核状态判断是否生成其它三张预约单据
        if(step==4){
            var res = qualitycheckBill();//品质检查单判断
            if(res['code']!=200)
            {
                BJUI.alertmsg('warn', res['msg']);
                return;
            }

            if("{{$isberthorder}}"==1){
                var berthorderdata = $.CurrentNavtab.find('#stockshipin-berthorder-table').data('allData');
                if(berthorderdata.length<1){
                    BJUI.alertmsg('warn', '该订单需要进行泊位分配!');
                    return;
                }
            }

            if("{{$ispipelineorder}}"==1){
                var pipelineorderdata = $.CurrentNavtab.find('#stockshipin-pipelineorder-table').data('allData');
                if(pipelineorderdata.length<1){
                    BJUI.alertmsg('warn', '该订单需要进行管线分配!');
                    return;
                }
            }
            
        }

        $.CurrentNavtab.find("#stockshipindetaildata").val(JSON.stringify(Obj));
        $.CurrentNavtab.find("#stockshipinquality").val(JSON.stringify(Quobj));
        $.CurrentNavtab.find("#stockinstatus").val(step);

        if($.CurrentNavtab.find("#cs_employee_sysno option:selected").val()!=''){
            $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        }

        if($.CurrentNavtab.find("#sby_employee_sysno option:selected").val()!=''){
            $.CurrentNavtab.find("#sby_employeename").val($.CurrentNavtab.find("#sby_employee_sysno option:selected").text());
        }
        if($.CurrentNavtab.find("#zj_employee_sysno option:selected").val()!='')
        {
            $.CurrentNavtab.find("#zj_employeename").val($.CurrentNavtab.find("#zj_employee_sysno option:selected").text());
        }
        if($.CurrentNavtab.find("#cc_employee_sysno option:selected").val()!='')
        {
            $.CurrentNavtab.find("#cc_employeename").val($.CurrentNavtab.find("#cc_employee_sysno option:selected").text());
        }

        $.CurrentNavtab.find("#stockshipin_customername").val($.CurrentNavtab.find("#stockshipin_customer_sysno option:selected").text());

        $.CurrentNavtab.find('#stockshipin_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#zj_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#cc_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#stockindate').removeAttr("disabled");
        // console.log('{{$isCA}}');return;
        if("{{$isCA}}"!=''){
            // console.log(1111);
            BJUI.setRegional('progressmsg', 'CA读取中……');
        }
        // return;
        BJUI.ajax('ajaxform', {
            url: '{{$atcion}}',
            form: $.CurrentNavtab.find('#stockshipinform'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('refresh', 'navab255');
                BJUI.navtab('refresh', 'navab257');
            }
        });
    }


    $('#wharf_sysno').change(function()
        {
            $('#wharfname').val($('#wharf_sysno option:selected').text());
        });


    /**
     * 判断品质检查单据的情况 来确定是否给生成订单
     * @return {[type]} [description]
     */
    function qualitycheckBill()
    {
        var data = $.CurrentNavtab.find('#stockshipin-qualitycheck-table').data('allData');

        var result = new Array();
        if(data.length<1){
            result['code'] = 300;
            result['msg'] = '请完成品质检查才可入库!';
            return  result;
        }

        for (var i = data.length - 1; i >= 0; i--) {

            if(data[i].ischecked==2 && data[i].isskip==2){
                result['code'] = 300;
                result['msg'] = '品质不符合标准无法入库!';
                return result;
                break;
            }
        }
        result['code'] = 200;
        result['msg'] = '质量合格!';
        return result;

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