<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="stockretankform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="retankdetaildata" name="retankdetaildata" value="">
            <!--base message start-->
            <fieldset>
                <legend>倒罐单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">倒罐单编号</label>
                    <div class="row-input">
                        <input type="text" name="stockretankno" value="@if($stockretankno){{$stockretankno}} @else {{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">倒罐日期</label>
                    <div class="row-input required">
                        <input type="text" name="stockretankdate" value="@if($stockretankdate){{$stockretankdate}}@else{{date('Y-m-d')}}@endif"  data-toggle="datepicker" data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="stockretankstatus" name="stockretankstatus" value="{{$stockretankstatus}}" readonly>
                        @if($stockretankstatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($stockretankstatus == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($stockretankstatus == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($stockretankstatus == 5)
                            <input name="statusname" value="作废" readonly>
                        @elseif($stockretankstatus == 6)
                            <input name="statusname" value="退回" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">倒罐申请单编号</label>
                    <div class="row-input">
                        <input type="hidden" name="bookingretank_sysno" value="@if($mode ==""){{$id}}@else{{$bookingretank_sysno}}@endif" readonly>
                        <input type="text" name="bookingretankno" value="{{$bookingretankno}}" readonly>
                    </div>

                    <label class="row-label">倒罐类型</label>
                    <div class="row-input">
                        <select id="retankstocktype" name="stocktype" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" @if($mode =='audit'||$mode == 'eye')disabled @endif>
                            <option value="1" @if($stocktype==1) selected @endif>库存</option>
                            <option value="2" @if($stocktype==2) selected @endif>介绍信</option>
                        </select>
                    </div>

                    <label class="row-label">货品品名</label>
                    <div class="row-input required">
                        <select id="goods_sysno" name="goods_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" @if($mode =='audit'||$mode == 'eye')disabled @endif>
                            <option value="">请选择</option>
                            @foreach($goodslist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $goods_sysno) selected @endif>{{$item['goodsname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="goodsname" id="goodsname" value="">
                    </div>

                    <label class="row-label">管线分配</label>
                    <div class="row-input">
                        <select id="ispipelineorder" name="ispipelineorder" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" @if($mode =='audit'||$mode == 'eye')disabled @endif>
                            <option value="1" @if($ispipelineorder==1) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder==2) selected @endif>否</option>
                        </select>
                    </div>

                    <label class="row-label">创建人</label>
                    <div class="row-input required">
                        <select id="zj_employee_sysno" name="zj_employee_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $zj_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                            <input type="hidden" id="zj_employeename" name="zj_employeename">
                        </select>
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
            <fieldset>
                <legend>倒罐单明细</legend>
               
                    <table class="table table-bordered" id="retank-detail-table" data-toggle="datagrid" data-options="{
                                height:'100%',
                                filterThead:false,
                                @if($mode ==''||$mode =='edit')
                                showToolbar: true,
                                toolbarCustom:$.CurrentNavtab.find('#edit_retank_tb'),
                                @endif
                                local: 'local',
                                addLocation: 'last',
                                @if($mode =='')
                                dataUrl: '/retank/addappdetailJson/id/{{$id}}',
                                @else
                                dataUrl: '/retank/adddetailjson/id/{{$id}}',
                                @endif
                                dataType: 'json',
                                jsonPrefix: 'obj',
                                paging: false,
                                linenumberAll: true,
                                fullGrid:true,
                                showTfoot:true,
                            }">
                        <thead>
                            <tr data-options="{name:'sysno'}" id="retank_move">
                                <th data-options="{name:'customername',align:'center'}">客户</th>
                                <th data-options="{name:'stockin_no',align:'center'}">库存单号</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'stockretank_out_no',align:'center'}">倒出罐号</th>
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',hide:'true',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货物性质</th>
                                <th data-options="{name:'unitname',align:'center',render:function(){return '吨'}}">计量单位</th>
                                <th data-options="{name:'tank_stockqty',align:'center',calc:'sum'}">储罐结存量</th>
                                <th data-options="{name:'stockretank_in_no',align:'center'}">倒入罐号</th>
                                <th data-options="{name:'bookingretankqty',align:'center',calc:'sum'}">申请倒入数量</th>
                                <th data-options="{name:'stockretankqty',align:'center',calc:'sum'}">实际倒入数量</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'stockretank_out_sysno',align:'center',hide:'true'}">移出罐号id</th>
                                <th data-options="{name:'stockretank_in_sysno',align:'center',hide:'true'}">移入罐号id</th>
                                <th data-options="{name:'stock_sysno',align:'center',hide:'true'}">库存id</th>
                                <th data-options="{name:'actualcapacity',align:'center',hide:'true'}">剩余库存</th>
                                <th data-options="{name:'customer_sysno',align:'center',hide:'true'}">客户ID</th>
                                <th data-options="{name:'stockin_sysno',align:'center',hide:'true'}">库存ID</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">货品ID</th>
                                <th data-options="{name:'out_stock_sysno',align:'center',hide:'true'}">移出罐库存记录ID</th>
                                <th data-options="{name:'release_num',align:'center',hide:'true'}">报关数量</th>
                            </tr>
                        </thead>
                    </table>
                
            </fieldset>
            </div>
            <!--project end-->
            {{--配图上传--}}
                <div class="remarks">
                    <fieldset>
                        <legend>上传倒罐单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookretank',action:'bookretank-edit'},
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

            
                    @if($mode =='audit')
                    <div class="remarks">
                        <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="auditreason" name="auditreason" data-toggle="autoheight" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                        </fieldset>
                    </div>
                    @endif
                    @if($mode =='back' )
                     <div class="remarks">
                        <fieldset>
                        <legend>作废意见</legend>
                           <textarea id="abandonreason" name="abandonreason" data-toggle="autoheight" rows="3" placeholder="请在此处填写作废意见">{{$abandonreason}}</textarea>
                        </fieldset>
                    </div>
                    @endif
                    <br>
                    <br>
                    <div class="text-center btns-user">
                        @if($mode ==''||$mode =='edit')
                            <button type="button" onclick="retanksubmit(2)" class="btn btn-green btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                            <button type="button" onclick="retanksubmit(3)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($mode =='audit')
                            <button type="button" onclick="retanksubmit(4)" class="btn btn-green btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                            <button type="button" onclick="retanksubmit(6)" class="btn btn-red btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($mode =='back')
                            <button type="button" onclick="retanksubmit(5)" class="btn btn-red btn-lg">作废</button>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($mode =='eye')
                            <button type="button" onclick="Design(printfun_poundout)"class="btn btn-green btn-lg">打印预览</button>
                            <button type="button" onclick="Setup(printfun_poundout)"class="btn btn-green btn-lg">打印</button>
                        @endif
                        @if($mode =='addattach')
                            <button type="button" class="btn btn-blue btn-lg" onclick="saveaddattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                        @endif
                        <button type="button" onclick="showRecords()"class="btn btn-lg">查看操作记录</button>&nbsp;
                    </div>
                    <br><br>
                    <div class="remarks hideshow" style="display: none;">
                        <fieldset>
                            <legend>操作记录明细</legend>
                            <div class="addTable">

                            </div>
                        </fieldset>
                    </div>
                    <div style="height: 200px;"><p>&nbsp;</p></div>
                    
        </form>
    </div>
</div>
@if($mode ==''||$mode =='edit')
<div id="edit_retank_tb">
    {{--<button type="button" class="btn btn-blue" onclick="addretank()" data-icon="plus">添加</button>--}}
    <button type="button" class="btn btn-red" onclick="delretank()" data-icon="fa-close">删除</button>
    <button type="button" class="btn btn-green" onclick="editretank()" data-icon="edit">修改</button>
</div>
@endif

<!-- 给打印用 -->
<div id="div_retank_edit1" style="display: none;">
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
        <caption><b><font face="黑体" size="5">KB 建涛（常州）石化码头有限公司</font></b><br><font face="黑体" size="4">(发货作业通知单)</font>
        </caption>
        <thead>
        <tr class="t_head">
            <td><b>签发日期:</b></td>
            <td></td>
            <td></td>
            <td ><b>作业类型:</b></td>
            <td colspan="2" width="35%">卸车<input type="checkbox">装船<input type="checkbox">装桶<input type="checkbox">管输<input type="checkbox"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="width: 16%;"><b>车（船）号</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>品名</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>发货重量</b></td>
            <td style="width: 16%;"></td>
        </tr>
        <tr>
            <td style="width: 16%;"><b>罐号</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>发货前流量累计</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>毛重（前尺）</b></td>
            <td style="width: 16%;"></td>
        </tr>
        <tr>
            <td style="width: 16%;"><b>鹤管号</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>发货后流量累计</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>皮重（后尺）</b></td>
            <td style="width: 16%;"></td>
        </tr>
        <tr>
            <td style="width: 16%;"><b>提货人安全检查确认签名</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>流量计实发数</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;"><b>净重</b></td>
            <td style="width: 16%;"></td>
        </tr>
        <tr>
            <td style="width: 16%;"><b>作业开始时间</b></td>
            <td style="width: 16%;"></td>
            <td style="width: 16%;" rowspan="2"><b>备注（注意事项）</b></td>
            <td style="width: 16%;" rowspan="2" colspan="3"></td>
        </tr>
        <tr>
            <td style="width: 16%;"><b>作业结束时间</b></td>
            <td style="width: 16%;"></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th ><b>结算中心:</b></th>
            <th><b>接单确认:</b></th>
            <th colspan="2"><b>作业确认:</b></th>
            <th colspan="2"><b>完工确认:</b></th>
        </tr>
        </tfoot>
    </table>

</div>

<!-- 给打印用end -->


<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
//操作记录
addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '12');
</script>

<script type="text/javascript">
    $('select[name=goods_sysno]').on('change',function () {
        $.CurrentNavtab.find('#retank-detail-table').datagrid('reload',  {data:[]});
    })

    $.CurrentNavtab.find("#retankstocktype").change(function (){
        $.CurrentNavtab.find('#retank-detail-table').datagrid('reload',  {data:[]});
    })

    //明细修改功能
    function addretank(){
        var goods_sysno = $.CurrentNavtab.find("#goods_sysno option:selected").val();
        var goods_name = $.CurrentNavtab.find("#goods_sysno option:selected").text();
        if(goods_name.length >0 ){
            BJUI.dialog({
                url:'/retank/retankdetailedit/handlestatus/edit/goods_sysno/'+ goods_sysno,
                title: '增加倒罐明细',
                mask:true,
                width: 800,
                height: 400
            });
            return;
        }else {
            BJUI.alertmsg('warn','<h4>请选择货品品名！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

    }

    function delretank(){
        var selectdata = $.CurrentNavtab.find('#retank-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        } else {
            var allData = $("#retank-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#retank-detail-table').datagrid('reload', {data: allData});
        }
    }

    function editretank(){
        var selectedDatas  =  $.CurrentNavtab.find("#retank-detail-table").data('selectedDatas');
        if ( typeof(selectedDatas) != 'undefined' && selectedDatas.length == 1) {
            BJUI.dialog({
                url:'/retank/generatedetailedit/handlestatus/edit/',
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                mask:true,
                title:'修改倒罐明细',
                width:800,
                height:400
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选中一行进行修改!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    function retanksubmit(step) {
        $.CurrentNavtab.find("#goodsname").val($("#goods_sysno option:selected").text());
        $.CurrentNavtab.find("#zj_employeename").val($("#zj_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#retankstocktype").removeAttr('disabled');
        $.CurrentNavtab.find("#goods_sysno").removeAttr('disabled');
        $.CurrentNavtab.find("#ispipelineorder").removeAttr('disabled');

        $.CurrentNavtab.find("#stockretankstatus").val(step);

        if(step==6){
            $("#auditreason").attr("data-rule","required");
        }
        if(step==7){
            $("#abandonreason").attr("data-rule","required");
        }

        var Obj = $.CurrentNavtab.find("#retank-detail-table").data('allData');
        $("#retankdetaildata").val(JSON.stringify(Obj));

        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#stockretankform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab303,navab556');
                BJUI.navtab('closeCurrentTab','navab302');
            }
        });
    }

    function saveaddattach(){
        var goodsname=$('#goods_name').val();
//        console.log(goodsname);
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#stockretankform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab303');
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
    console.log({{$table_html}});
    //打印入库单字段布局
    var printfun_poundout = function CreateStockIn() {
        // console.log("{{'http://'.$_SERVER['SERVER_NAME'].$qrcode_queue}}");
        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "出库磅码单");
//         LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");
         LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", document.getElementById("div_retank_edit1").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(90, 116, 130, 29, "{{date('Y-m-d')}}");//签发日期
        LODOP.ADD_PRINT_TEXT(141, 171, 130, 27, "{{$shipname}}");//车船号
        LODOP.ADD_PRINT_TEXT(141, 440, 131, 24, "{{$goodsname}}");//品名
        LODOP.ADD_PRINT_TEXT(141, 690, 131, 26, "{{$stockretankqty}}");//发货重量
        LODOP.ADD_PRINT_TEXT(190, 156, 130, 30, "{{$stockretank_no}}");//罐号
        LODOP.ADD_PRINT_TEXT(341, 408, 265, 100, "{{$memo}}");//备注
        if("{{$qrcode_queue}}"){
            LODOP.ADD_PRINT_TEXT(352, 554, 130, 30, "扫描二维码\n查看排号情况");
            LODOP.ADD_PRINT_IMAGE(337,641, 110, 105,"{{COMMON::createPic( 'http://'.$_SERVER['SERVER_NAME'].$qrcode_queue, TRUE, 'test_bangmaout.png', 110, 110)}}");
        }


    }

</script>