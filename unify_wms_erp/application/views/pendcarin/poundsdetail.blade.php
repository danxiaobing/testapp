<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="pendcarin_edit"  method="POST" class="pendcarin-edit-form"   data-data-type="json" >
            <input type="hidden" id="pounds_sysno" name="id" value="{{$id}}">
            <input type="hidden" id="stockin_sysno" name="stockin_sysno"  value="{{ $stockin_sysno }}"> 
            <input type="hidden" id="stockindetail_sysno" name="stockindetail_sysno" value="{{ $stockindetail_sysno }}"> 
            <!--base message start-->
            <fieldset>
                <legend>基本信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">磅码单号</label>

                    <div class="row-input">
                        <input type="text" name="poundsinno" id="poundsinno"
                               value="@if($poundsinno) {{$poundsinno}} @else {{系统自动生成}} @endif" readonly>
                    </div>
                    <label class="row-label">地磅编码</label>

                    <div class="row-input required">
                        <input type="radio" name="loadometer" value="{{$loadometer}}" data-toggle="icheck"  data-label="50T" @if($loadometer=='50T') checked @endif disabled>
                        <input type="radio" name="loadometer" value="{{$loadometer}}" data-toggle="icheck"  data-label="80T" @if($loadometer=='80T') checked @endif disabled>
                        <input type="radio" name="loadometer" value="{{$loadometer}}" data-toggle="icheck"  data-label="100T" @if($loadometer=='100T') checked @endif disabled>
                    </div>
                    <label class="row-label">单据状态</label>

                    <div class="row-input ">
                        <select name="stockinstatus" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled="disabled">
                         	<option value="1"  >新增</option>
                            <option value="2" @if($poundsinstatus == 2) selected @endif>核单完成</option>
                            <option value="3" @if($poundsinstatus == 3) selected @endif>重车过磅</option>
                            <option value="4" @if($poundsinstatus == 4) selected @endif>空车过磅</option>
                            <option value="5" @if($poundsinstatus == 5) selected @endif >作废</option>
                        </select>
                    </div>
                    <label class="row-label">进货罐号</label>
                        <input type="hidden" name="storagetankname" value="{{ $storagetankname }}">
                    	<input type="hidden" name="storagetank_sysno"  value="{{ $storagetank_sysno }}">
                    <div class="row-input required">
                    	<select name="storagetank_sysno" id="storagetank_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10" disabled="disabled">
                    		<option value="{{ $storagetank_sysno }}">{{ $storagetankname }}</option>
                    	</select>
                    </div>

                    <label class="row-label">鹤位号</label>
                    
                    <div class="row-input">
                        <input type="text" name="" value="{{$cranename}}" readonly="readonly">
                    </div>
                        
                    <label class="row-label">车牌号</label>

                    <div class="row-input ">
                    	<input type="text" name="carid" value="{{ $carid }}" readonly="readonly" >
                    </div>

                    <label class="row-label">司机名称</label>

                    <div class="row-input">
                    	<input type="text" name="carname" value="{{ $carname }}" readonly="readonly">
                    </div>

                    <label class="row-label">手机号码</label>

                    <div class="row-input ">
                        <input type="text" name="mobilephone" value="{{$mobilephone}}" readonly="readonly">
                    </div>

                    <label class="row-label">身份证号</label>

                    <div class="row-input ">
                        <input type="text" name="idcard"  value="{{ $idcard }}" readonly="readonly">
                    </div>


                    <label class="row-label">是否排队</label>
                    <div class="row-input ">
                        <input type="radio" name="isqueue" value="1" data-toggle="icheck"  data-label="是" @if($isqueue==1) checked @endif disabled>
                        <input type="radio" name="isqueue" value="2" data-toggle="icheck"  data-label="否" @if($isqueue==2) checked @endif disabled>
                    </div>
                    <label class="row-label">司磅员</label>
                    <div class="row-input ">
                        <input type="text" name="create_username"  value="{{ $create_username }}" readonly="readonly">
                    </div>
                    <label class="row-label">备注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3" readonly="readonly">{{$memo or ''}}</textarea>
                    </div>

            	</div>
            </fieldset>


            <fieldset>
            <legend>综合信息</legend>
                <div class="bjui-row col-3">

                    <label class="row-label">客户</label>

                    <div class="row-input ">
                        <input type="text" name="customername" value="{{ $customername}}" readonly="readonly">
                    </div>

                    <label class="row-label ">送货公司</label>

                    <div class="row-input required">
                        <input type="text" name="deliverycompany" value="{{ $deliverycompany }}"  readonly>
                    </div>

                    <label class="row-label ">预卸数量(kg)</label>

                    <div class="row-input required">
                        <input type="text" name="unloadnumber" id="unloadnumber" value="{{ intval($unloadnumber) }}" data-rule="required,number" readonly>
                    </div>

                    <label class="row-label">品名</label>

                    <div class="row-input ">
                        <input type="text" name="goodsname" value="{{ $goodsname }}" readonly="readonly">
                    </div>          
                        
                    <label class="row-label">入库订单号</label>
                        
                    <div class="row-input ">
                        <input type="text" id="stockinno" name="stockinno" value="{{ $stockinno }}" readonly="readonly">
                    </div>   
                    
                        
                    <label class="row-label">卸货单号</label>
                        
                    <div class="row-input ">
                        <input type="text" name="takegoodsno" value="{{ $takegoodsno }}" readonly="readonly">
                    </div> 

                    <label class="row-label">重车重量</label>
    
                    <div class="row-input ">
                        <input type="text" name="fullcarqty" value="{{ intval($fullcarqty) }}" readonly="readonly">
                    </div> 


                    <label class="row-label">重车时间</label>

                    <div class="row-input required">
                        <input type="text" value="{{ $fullcartime }}" data-toggle="datepicker" data-rule="date" disabled="">
                    </div>

                    <label class="row-label">重车地磅</label>

                    <div class="row-input ">

                        <input type="text" name="fullloadometer" value="{{$fullloadometer}}" disabled>
                    </div>

                    <label class="row-label">空车重量(kg)</label>
    
                    <div class="row-input ">
                        <input type="text" name="emptycarqty" value="{{ intval($emptycarqty) }}" readonly="readonly">
                    </div> 

                    <label class="row-label">空车时间</label>

                    <div class="row-input ">
                        <input type="text" value="{{$emptycartime}}" data-toggle="datepicker" data-rule="date" disabled="">
                    </div>


                    <label class="row-label">空车地磅</label>

                    <div class="row-input ">
                        <input type="text" name="emptyloadometer" value="{{$emptyloadometer}}" disabled>
                    </div>

                    <br>

                    <label class="row-label">实际数量(kg)</label>

                    <div class="row-input ">
                        <input type="text" id="beqty" name="beqty" value="{{intval($beqty)}}" readonly>
                    </div>                    

                </div>

            </fieldset>


            @if($isqualitycheck==1)
            <!-- 品质检查单start -->

            <div class="remarks">
                <fieldset>
                    <legend>品质检查明细</legend>
                    <table class="table table-bordered" id="stockshipin-qualitycheck-table" height="40+50*{{count($qualitycheck)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar:false,
                        local: 'local',
                        data: {{$qualitycheck}},
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

        @if($void || $poundsinstatus==5)
        <div class="remarks">
            <fieldset>
                <legend>作废意见</legend>
                    <textarea name="memo" id="poundscarin_memo" rows="3" value="{{$abandonreason}}" @if($poundsinstatus==5) disabled @endif placeholder="请在此处输入作废意见">{{$abandonreason or ''}}</textarea>
            </fieldset>
         </div>
        @endif
        <br><br>
    @if($uploaded1)
            <div class="comuser-add-center" id='pendcarin_release'>
                 <fieldset class="customerfieldset" >
                   <legend>上传出库磅码单</legend>
                     <input type="file"  data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'pendcarin',action:'pendcarin_release',doc_sysno:'{{$id}}'},
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
            </div> <br>
@endif
         <div class="text-center btns-user">
            @if($void)
                <button id="poundscarins_void" type="button" class="btn btn-red btn-lg" >作废</button>
            &nbsp;&nbsp;&nbsp;
            @endif    
            <button  type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
            @if($viewtype=='look')
                <button id="" type="button" onclick="Design(printfun_poundin)"
                        class="btn btn-green btn-lg">打印设计
                </button>
                <button id="" type="button" onclick="Setup(printfun_poundin)"
                        class="btn btn-green btn-lg">打印
                </button>
            @endif            
         </div>

        </form>
        <br><br>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable">
                </div>
            </fieldset>
        </div>
    </div>
</div>

<!-- 给打印用 -->
<div id="div_poundin_edit" style="display: none;">
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
        <caption><b><font face="黑体" size="8">{{$companyname}}</font></b><br><font face="黑体" size="4">(收货作业通知单)</font>
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
    //----------------------操作记录 
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, 7);
    //----------------------操作记录 end
    

    $('#poundscarins_void').click(function(){
        if($('#poundscarin_memo').val() == ''){
            BJUI.alertmsg('warn','请填写作废意见');
            return;
        }
        var memo = $('#poundscarin_memo').val();
        var id = $('#pounds_sysno').val();
        var stockin_sysno = $('#stockin_sysno').val();
        var beqty = $('#beqty').val();
        var storagetank_sysno = $('#storagetank_sysno').val();
        var poundsinno = $('#poundsinno').val();
        var stockinno = $('#stockinno').val();
        // console.log(stockinno);return;
        BJUI.ajax('ajaxform',{
            url: '/pendcarin/poundsVoid',
            form: $.CurrentNavtab.find('#pendcarin_edit'),
            validate: false,
            loadingmask: true,
            type: 'POST',
            okCallback: function(json){
                    BJUI.alertmsg('ok','作废成功！');
                    BJUI.navtab('reloadFlag','navab447');
                    BJUI.navtab('closeCurrentTab','');
            }
        });
    });
    

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
    var printfun_poundin = function CreateStockIn() {
        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "入库磅码单");
        // LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");
        // LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", document.getElementById("div_poundin_edit").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(110, 147, 130, 29, "{{date('Y-m-d')}}");
        LODOP.ADD_PRINT_TEXT(157, 210, 130, 27, "{{$carid}}");
        LODOP.ADD_PRINT_TEXT(157, 461, 131, 24, "{{$goodsname}}");
        LODOP.ADD_PRINT_TEXT(158, 606, 131, 26, "{{$unloadnumber}}"/1000);
        LODOP.ADD_PRINT_TEXT(205, 209, 130, 30, "{{$storagetankname}}"); 
        LODOP.ADD_PRINT_TEXT(414, 166, 130, 30, "{{$create_username}}"); 
        LODOP.ADD_PRINT_TEXT(360, 390, 120, 110, "{{$memo}}"); 
        if("{{$qrcode_queue}}"){
            LODOP.ADD_PRINT_TEXT(352, 554, 130, 30, "扫描二维码\n查看排号情况"); 
            LODOP.ADD_PRINT_IMAGE(337,641, 110, 105,"{{COMMON::createPic( 'http://'.$_SERVER['SERVER_NAME'].$qrcode_queue, TRUE, 'test_bangmaout.png', 110, 110)}}");
        }
    }

</script>