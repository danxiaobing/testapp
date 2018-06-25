<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
     <br><br>
        <form id="ship_stockoutedit_form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" id="ship_stockoutedit_id" name="id" value="{{$id}}">
            <!-- <input type="hidden" id='ship_stockout_view' name="view" value="{{$view or ''}}"> -->
            <!-- 给小兵用的 -->
            <input type="hidden" name="takebetween" value="{{$takebetween}}">
            <input type="hidden" id="ship_stockoutedit_detaildata" name="stockoutdetaildata">
            <input type="hidden" id="ship_stockoutedit_type" name="stockouttype" value="{{$stockouttype}}">
            <input type="hidden" id="ship_stockoutedit_status" name="stockoutstatus" value="@if($stockoutstatus) {{$stockoutstatus}} @else {{2}} @endif">

            <fieldset>
                <legend>出库单信息</legend>
                <br><br>

                <div class="bjui-row col-3">
                    <label class="row-label">出库单号</label>
                    <div class="row-input">
                        <input type="text" id='ship_stockoutedit_stockoutno' name="stockoutno" value="{{$stockoutno or ''}}"
                               readonly>
                    </div>
                    <label class="row-label">出库日期</label>
                    <div class="row-input">
                        <input type="text" id='shipout_stockoutdate' name="stockoutdate" data-toggle="datepicker"
                               value="@if($stockoutdate) {{ $stockoutdate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <input type="text" id='shipout_status' name="status" value="{{ $status }}" readonly>
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <select name="customer_sysno" id="ship_stockout_customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customername" id="ship_stockout_customername" value="{{$customername}}">
                    </div>

                    <input type="hidden" name="booking_out_sysno" value="{{$booking_out_sysno}}"/>

                    <label class="row-label">预约编号</label>
                    <div class="row-input">
                        <input type="text" id='ship_stockout_bookingoutno' name="bookingoutno" value="{{$bookingoutno}}"
                               readonly/>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <select name="cs_employee_sysno" id="ship_stockout_cs_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-rule=""
                                data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="ship_stockout_cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <label class="row-label">质计</label>
                    <div class="row-input">
                        <select name="zj_employee_sysno" id="ship_stockout_zj_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $zj_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="zj_employeename" id="ship_stockout_zj_employeename" value="{{$zj_employeename}}">
                    </div>

                    <label class="row-label">仓储</label>
                    <div class="row-input">
                        <select name="cc_employee_sysno" id="ship_stockout_cc_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cc_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cc_employeename" id="ship_stockout_cc_employeename" value="{{$cc_employeename}}">
                    </div>


                    <label class="row-label">提货单号</label>
                    <div class="row-input">
                        <input type="text" id='ship_stockout_takegoodsno' name="takegoodsno" value="{{$takegoodsno}}" readonly>
                    </div>

                    <label class="row-label">提货公司</label>
                    <div class="row-input">
                        <input type="text" id='ship_stockout_takegoodscompany' name="takegoodscompany" value="{{$takegoodscompany}}" readonly>
                    </div>

                    <label class="row-label">商检单位</label>
                    <div class="row-input">
                        <input type="text" name="businesscheckunitname" value="{{$businesscheckunitname}}" readonly>
                    </div>

                    <label class="row-label">船舶代理</label>
                    <div class="row-input">
                        <input type="text" name="shipproxyname" value="{{$shipproxyname}}" readonly>
                    </div>

                    <label class="row-label">靠岸码头</label>
                    <div class="row-input">
                        <select name="wharf_sysno" id="ship_stockout_wharf_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($wharflist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $wharf_sysno) selected @endif>{{$item['wharfname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="wharfname" id="ship_stockout_wharfname" value="">
                    </div>

                    <label class="row-label">装货时间</label>
                    <div class="row-input">
                        <input type="text" name="wharf_date" data-toggle="datepicker" value="{{ $wharf_date }}" readonly>
                    </div>

                    <label class="row-label">管线分配</label>
                    <div class="row-input">
                        <select name="ispipelineorder" id="ship_stockout_ispipelineorder" data-size="5" data-toggle="selectpicker" data-width="100%" disabled>
                            <option value="1" @if($ispipelineorder == 1) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">泊位分配</label>
                    <div class="row-input">
                        <select name="isberthorder" id="ship_stockout_isberthorder" data-size="5" data-toggle="selectpicker" data-width="100%" disabled>
                            <option value="1"  @if($isberthorder == 1) selected @endif>是</option>
                            <option value="2" @if($isberthorder == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">品质检查</label>
                    <div class="row-input">
                        <select name="isqualitycheck" id="ship_stockout_isqualitycheck" data-size="5" data-toggle="selectpicker" data-width="100%" disabled>
                            <option value="1" @if($isqualitycheck == 1) selected @endif>是</option>
                            <option value="2" @if($isqualitycheck == 2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">司磅员</label>
                    <div class="row-input">
                        <select name="sby_employee_sysno" id="ship_stockout_sby_employee_sysno" data-size="5" data-toggle="selectpicker"
                                data-live-search="true" data-width="100%" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $sby_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="sby_employeename" id="ship_stockout_sby_employeename" value="{{$sby_employeename}}">
                    </div>

                    <label class="row-label">备注</label>
                    <div class="row-input">
                        <textarea  name="memo"  data-toggle="autoheight" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print' || $type == 'execute') readonly @endif>{{$memo}}</textarea>
                    </div>
                    @if($stockoutstatus == 8 || $stockoutstatus == 3 || $stockoutstatus == 4)
                    <label class="row-label">船检量</label>
                    <div class="row-input">
                        <input type="text" name="shipchecknum" value="{{$shipchecknum}}" data-rule="range[0~];number" @if($type == 'view' || $type == 'audit' || $type == 'cancel' || $type == 'print') readonly @endif>
                    </div>
                    @endif
                </div>
                <br><br>
            </fieldset>
        <div class="remarks">
            <fieldset>
                <legend>出库单信息</legend>
                <div class="table-edit">

                    <table class="table table-bordered" id="ship-stockout-receipt-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                           height:'100%',
                            filterThead:false,
                            @if($type != 'view' && $type != 'audit' && $type != 'cancel' && $type != 'print')
                            showToolbar:true,
                            toolbarCustom:$.CurrentNavtab.find('#ship_custom_stockout_tb'),
                            @endif
                            data:{{$detaillist}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot: true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr data-options="{name:'stock_sysno'}">
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'stockin_no',align:'center'}">入库单号</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';  }}}">
                                货物性质
                            </th>
                            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库数量</th>
                            <th data-options="{name:'introduceqty',align:'center',calc:'sum'}">提单总数</th>
                            <th data-options="{name:'takeqty',align:'center',calc:'sum'}">预约提货数量</th>
                            <th data-options="{name:'tobeqty',align:'center',calc:'sum'}">通知提货数量</th>
                            @if($stockoutstatus == 8 || $stockoutstatus == 3 || $stockoutstatus == 4)
                            <th data-options="{name:'bussinesscheckqty',align:'center',calc:'sum'}">罐检数量</th>
                            @endif
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'bookout_detail_sysno',align:'center',hide:true}">预约单号id</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>
                            <th data-options="{name:'stockno',align:'center',hide:true}">库存单号</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">出货罐号id</th>
                            <th data-options="{name:'stockinshipname',align:'center',hide:true}">进货船名</th>
                            <th data-options="{name:'stocktype',align:'center',hide:true}">库存来源</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <div class="remarks">
            <fieldset>
                <legend>管线明细</legend>
                <div class="table-edit">

                    <table class="table table-bordered" height="40+50*{{count($pipelineorder)}}" data-toggle="datagrid" data-options="{
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
        <div class="remarks">
            <fieldset>
                <legend>品质检查明细</legend>
                <div class="table-edit">

                    <table class="table table-bordered" height="40+50*{{count($qualitycheck)}}" data-toggle="datagrid" data-options="{
                           height:'100%',
                            filterThead:false,
                            data:{{$qualitycheck}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr>
                            <th data-options="{name:'goodsname',align:'center'}">品种</th>
                            <th data-options="{name:'checktime',align:'center'}">品质检查时间</th>
                            <th data-options="{name:'ischecked',align:'center',render:function(value){switch(value) { case '1': return '是'; case '2':return '否'; default: return '';  }}}">是否合格</th>
                            <th data-options="{name:'isskip',align:'center',render:function(value){switch(value) { case '0': return '不用让步'; case '1': return '是'; case '2':return '否'; default: return '--';  }}}">是否让步</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
        <div class="remarks">
            <fieldset>
                <legend>泊位明细</legend>
                <div class="table-edit">

                    <table class="table table-bordered" height="40+50*{{count($berthorder)}}" data-toggle="datagrid" data-options="{
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
            <div class="comuser-add-left ">
                <fieldset class="customerfieldset">
                    <legend>上传商检报告</legend>
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'stockout',action:'receipt'},
                            required: false,
                            uploaded: '{{ $uploaded }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf,txt',
                                mimeTypes: '.jpg,.png,.pdf,.txt'
                            }
                        }"
                    >
                </fieldset>
            </div>
            <div class="comuser-add-right ">
                <fieldset class="customerfieldset">
                    <legend>上传提货单</legend>
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'stockout',action:'takegoods'},
                            required: false,
                            uploaded: '{{ $uploaded1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath:'/attachment/deljson/',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,pdf,txt',
                                mimeTypes: '.jpg,.png,.pdf,.txt'
                            }
                        }"
                    >
                </fieldset>
            </div>
        </form>
        <div class="remarks">
        @if($type == 'cancel' || $type == 'audit')
            <fieldset>
                    <legend>@if($type == 'cancel')作废@elseif($type == 'audit')审核@endif意见</legend>
                    <form id="ship-stockout-exam-form" action="/stockout/examJson" method="POST" class="datagrid-edit-form" data-toggle="validate" data-data-type="json" data-validator-option="{stopOnError:false,timely:false}">
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="examstep" id="shipout_edit_examstep" value="">
                        <textarea id="ship_stockout_marks" name="stockoutmarks"  data-toggle="autoheight" rows="3" placeholder="请在此处填写@if($type == 'cancel')作废@elseif($type == 'audit')审核@endif意见"></textarea>
                        </div>
                    </form>

            </fieldset>
        @endif
            <br><br>
        <div class="text-center ">
            @if($stockoutstatus < 3 && $type != 'view')
                <button type="button" onclick="stockoutSubmit(2)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="stockoutSubmit(8)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($stockoutstatus == 6 && $type != 'view')
                <button type="button" onclick="stockoutSubmit(6)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="stockoutSubmit(8)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($stockoutstatus == 8 && $type != 'view')
                <button type="button" onclick="executeBack()" class="btn btn-red btn-lg">退回</button>&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="stockoutSubmit(3)" class="btn btn-success btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
            @endif
            @if($type == 'print')
                <button id="stockshipinsubmit6" type="button" onclick="Design(printfun_shipout_edit)"
                        class="btn btn-success btn-lg">打印设计</button>
                <button id="stockshipinsubmit6" type="button" onclick="Setup(printfun_shipout_edit)"
                        class="btn btn-success btn-lg">打印 </button>
            @endif
            @if($type == 'cancel')
                <button type="button" onclick="stockoutExam(5)" class="btn btn-red btn-lg">作废</button>
            @endif
            @if($type == 'audit')
                <button type="button" onclick="stockoutExam(4)" class="btn btn-green btn-lg">审核通过</button>
                &nbsp;&nbsp;&nbsp;
                <button type="button" onclick="stockoutExam(6)" class="btn btn-red btn-lg">审核不通过</button>
                &nbsp;&nbsp;&nbsp;
            @endif
            <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>
        </div>
        <br><br>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable"></div>
            </fieldset>
        </div>
        <br><br>
        </div>
    </div>
</div>
    @if($type != 'view' && $type != 'audit' && $type != 'cancel' && $type != 'print')
    <div id="ship_custom_stockout_tb">
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editReceipt()">修改</button>
    </div>
    @endif
<div id="div_shipout_edit" style="display: none;">
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


    <table class='table-dy' border=0 cellSpacing=0 cellPadding=0 width='100%' height="200" bordercolor="#000000"
           style="border-collapse:collapse">
        {{--<caption><b><font face="黑体" size="8"></font></b></caption>--}}
        <!-- <tbody> -->
        <tr>
            <td style="width: 10%;"><b>装船日期</b></td>
            <td style="width: 40%;"></td>
            <td style="width: 10%;"><b>出&nbsp;库&nbsp;单&nbsp;号&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
            <td style="width: 40%;"></td>
        </tr>
        <tr>
            <td><b>进货船名</b></td>
            <td></td>
            <td><b>装&nbsp;货&nbsp;船&nbsp;名&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>品&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</b></td>
            <td></td>
            <td><b>实&nbsp;际&nbsp;装&nbsp;货&nbsp;量</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>罐&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号</b></td>
            <td></td>
            <td rowspan='2'><b>提 货 人 身 份 证 号 码</b></td>
            <td rowspan='2'></td>
        </tr>
        <tr>
            <td><b>提 单 号</b></td>
            <td></td>
        </tr>
        <!-- <tr>
            <td><b>货主</b></td>
            <td></td>
        </tr> -->
        <tr>
            <td><b>提货单位</b></td>
            <td></td>
            <td rowspan='2'><b>提&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;人签&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;字</b></td>
            <td rowspan='2'></td>
        </tr>
        <tr>
            <td><b>出库时间</b></td>
            <td></td>
        </tr>

        <!-- </tbody>
        <tfoot> -->
        <tr>
            <th><b>公司盖章:</b></th>
            <th><b>主管签字:</b></th>
            <th><b>计量员:</b></th>
            <th><b>备注:</b></th>
        </tr>
<!--
        </tfoot> -->
    </table>

</div>

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    $(function () {
        if (!{{$id}}) {
            if({{$stockouttype}} == 1){
                var prefix = 'D2';
            }
            if({{$stockouttype}} == 2){
                var prefix = 'C2';
            }
            $.ajax({
                url: '/bookout/getbookoutno/prefix/'+prefix,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $.CurrentNavtab.find('#ship_stockoutedit_stockoutno').val(data);
                },
            });
        }

    });

    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '5');

    $('#ship_stockout_wharf_sysno').change(function(){
        $('#ship_stockout_wharfname').val($('#ship_stockout_wharf_sysno option:selected').text());
    })

    function editReceipt() {
        var receiptdata = $.CurrentNavtab.find('#ship-stockout-receipt-table').data('selectedDatas');
        var stockoutstatus = $.trim($("#ship_stockoutedit_status").val());

        if (receiptdata == '' || receiptdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.dialog({
            id: 'stockout-receipt-{{$id}}',
            data: receiptdata[0],
            type: 'POST',
            url: '/stockout/shipdetailedit/cid/' + "{{$customer_sysno}}" + '/rtype/' + "{{$stockouttype}}/stockoutstatus/" + stockoutstatus,
            title: '出库单',
            width: 850,
            height: 480,
            mask:true,

        });
    }

    function stockoutSubmit(step) {
        var Obj = $.CurrentNavtab.find('#ship-stockout-receipt-table').data('allData');
        if (typeof  Obj == 'undefined') {
            BJUI.alertmsg('warn', '请填写详细出库单',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var stockouttype = $("#ship_stockoutedit_type").val();
        var stockoutstatus = $.trim($("#ship_stockoutedit_status").val());

        for (var i = 0; i < Obj.length; i++) {
            if (Obj[i].tobeqty == null) {
                BJUI.alertmsg('warn', '通知数量不能为空',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
        }

        if (stockoutstatus == 8 && step != 6) {
            for (var i = 0; i < Obj.length; i++) {
                if (Obj[i].bussinesscheckqty == '') {
                    BJUI.alertmsg('warn', '罐检数量不能为空',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;
                }
            }
        }

        if(step == 2 || step == 6 || step ==8){

        }

        $.CurrentNavtab.find("#ship_stockout_cs_employeename").val($.CurrentNavtab.find("#ship_stockout_cs_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#ship_stockout_zj_employeename").val($.CurrentNavtab.find("#ship_stockout_zj_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#ship_stockout_cc_employeename").val($.CurrentNavtab.find("#ship_stockout_cc_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#ship_stockout_sby_employeename").val($.CurrentNavtab.find("#ship_stockout_sby_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#ship_stockout_customername").val($.CurrentNavtab.find("#ship_stockout_customer_sysno option:selected").text());
        $.CurrentNavtab.find('#ship_stockout_customer_sysno').removeAttr("disabled");

        $.CurrentNavtab.find("#ship_stockoutedit_detaildata").val(JSON.stringify(Obj));

        $.CurrentNavtab.find("#ship_stockoutedit_status").val(step);

        $.CurrentNavtab.find("#ship_stockout_ispipelineorder").removeAttr('disabled');
        $.CurrentNavtab.find("#ship_stockout_isberthorder").removeAttr('disabled');
        $.CurrentNavtab.find("#ship_stockout_isqualitycheck").removeAttr('disabled');
        BJUI.ajax('doajax',{
            url:'/stockout/checkstoragetank/step/'+step,
            data:{bookingdata : Obj},
            type:'POST',
            dataType: 'json',
            okCallback: function (json, options) {
                if(json.code == 300){
                    BJUI.alertmsg('confirm', json.msg, {okCall: function() {
                        submitForm();
                        }
                    });
                }else{
                    submitForm();
                }
            }
        });
    }

    function submitForm() {
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#ship_stockoutedit_form'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab269,navab271,navab448,navab557');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function stockoutExam(step) {
        $("#shipout_edit_examstep").val(step);

        if (!$.CurrentNavtab.find("#ship_stockout_marks").val() && step == 6) {
            BJUI.alertmsg('warn', '请先填写审核意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        if (!$.CurrentNavtab.find("#ship_stockout_marks").val() && step == 5) {
            BJUI.alertmsg('warn', '请先填写作废意见！',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxform', {
            form: $.CurrentNavtab.find('#ship-stockout-exam-form'),
            validate: false,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab269,navab276,navab278,navab271,navab448');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function executeBack() {
        var id = $.trim($("#ship_stockoutedit_id").val());
        BJUI.ajax('doajax', {
            url: "/stockout/executeBack/id/" + id,
            okCallback: function (json, options) {
                if(json.code == 200){
                    BJUI.navtab('reloadFlag', 'navab269,navab276,navab278,navab271,navab448,navab557');
                    BJUI.navtab('closeCurrentTab', '');
                }else{
                    BJUI.alertmsg('warn', json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                }

            }
        });
    }

    function downloadSeal() {
        BJUI.ajax('ajaxdownload', {
            url: '/stockout/downloadSeal'
        });
    }

    function previewSeal() {
        var data = {'id': 1, 'type': 1};

        var html = template('seal_tpl', data);


        BJUI.dialog({
            id: 'test_dialog3',
            html: html,
            title: '示例Dialog3',
            max: true
        })

    }

    function printSeal() {

        //  $("#test_dialog3").printArea();
        var height = $.CurrentDialog.find('.dialogContent ').css('height');
        var overflow = $.CurrentDialog.find('.dialogContent ').css('overflow');

        $.CurrentDialog.find('.dialogContent ').css({
            'height': 'auto', //高度自动
            'overflow': 'visible' //在打印之前把这个div的overflow改成全部显示
        }).printArea();
        $.CurrentDialog.find('.dialogContent ').css({
            'height': height, //高度自动
            'overflow': overflow //在打印之前把这个div的overflow改成全部显示
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
        var Obj = $("#ship-stockout-receipt-table").data('allData');
        var outdate = $("#shipout_stockoutdate").val();
        var takegoodscompany = $("#ship_stockout_takegoodscompany").val();
        var takegoodsno = $("#ship_stockout_takegoodsno").val();
        var stockoutno = $("#ship_stockoutedit_stockoutno").val();

        var shipname = Obj[0].shipname;
        var stockinshipname = Obj[0].stockinshipname;
        for (var i = 1; i < Obj.length; i++) {
            shipname = shipname + ',' + Obj[i].shipname;
        }
        var storagetankname = Obj[0].storagetankname;
        for (var i = 1; i < Obj.length; i++) {
            storagetankname = storagetankname + ',' + Obj[i].storagetankname;
        }

        var bussinesscheckqty = 0;
        for (var i = 0; i < Obj.length; i++) {
            bussinesscheckqty += parseFloat(Obj[i].bussinesscheckqty);
        }
        var title =  "{{$companyname}}装船计重单";

        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "船出库单");
        LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");

        LODOP.ADD_PRINT_TABLE("10%", "1%", "96%", "98%", document.getElementById("div_shipout_edit").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(10, 180, 800, 50, title);
        LODOP.SET_PRINT_STYLEA(2, "FontName", "黑体");
        LODOP.SET_PRINT_STYLEA(2, "FontSize", 30);
        LODOP.ADD_PRINT_TEXT(90, 130, 250, 24, outdate);
        LODOP.ADD_PRINT_TEXT(140, 130, 250, 24, stockinshipname);
        LODOP.ADD_PRINT_TEXT(190, 130, 250, 24, Obj[0].goodsname);
        LODOP.ADD_PRINT_TEXT(240, 130, 250, 24, storagetankname);
        LODOP.ADD_PRINT_TEXT(290, 130, 250, 24, takegoodsno);
        // LODOP.ADD_PRINT_TEXT(323, 146, 130, 30, "{{$customername}}");
        LODOP.ADD_PRINT_TEXT(338, 130, 250, 24, takegoodscompany);
        LODOP.ADD_PRINT_TEXT(389, 130, 250, 24, outdate);
        LODOP.ADD_PRINT_TEXT(88, 660, 250, 24, stockoutno);
        LODOP.ADD_PRINT_TEXT(138, 660, 250, 24, shipname);
        LODOP.ADD_PRINT_TEXT(188, 660, 250, 24, bussinesscheckqty+'吨');
        // LODOP.ADD_PRINT_TEXT(240, 647, 240, 39, "");
        // LODOP.ADD_PRINT_TEXT(343, 647, 150, 37, "");


    }


</script>