<div class="bjui-pageContent">
    <div style="padding:10px 0;width: 90%;margin: 0 auto;">
    <br><br>
        <form id="outcars-void-form" action="/Bookoutcars/poundsoutVoid" method="POST" data-toggle="validate"  data-data-type="json">
        <input type="hidden" id="poundsout_sysno" name="id" value="{{$sysno}}">
        <input type="hidden" id="stockout_sysno" name="stockout_sysno" value="{{$stockout_sysno}}">
        <input type="hidden" name="detailData" value="{{$detailData}}">
            <fieldset>
                <legend>基本信息</legend>
                <div class="bjui-row col-3">
                    <label class="row-label">磅码单号</label>
                    <div class="row-input">
                        <input type="text" name="poundsoutno" value="@if($poundsoutno) {{$poundsoutno}} @else {{系统自动生成}} @endif" readonly="readonly">
                    </div>

                    <label class="row-label">选择地磅</label>
                    <div class="row-input ">
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="50T" data-label="50T" @if($loadometer=='50T') checked @endif disabled>
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="80T" data-label="80T" @if($loadometer=='80T') checked @endif disabled>
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="100T" data-label="100T" @if($loadometer=='100T') checked @endif disabled>
                    </div>

                    <label class="row-label">单据状态</label>
                    <input type="hidden" name="status" value="{{$poundsoutstatus}}">
                    <div class="row-input ">
                        <select name="contractstatus" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled>
                            <option value="1" @if($poundsoutstatus == 1) selected @endif>新增</option>
                            <option value="2" @if($poundsoutstatus == 2) selected @endif>核单完成</option>
                            <option value="3" @if($poundsoutstatus == 3) selected @endif>空车过磅</option>
                            <option value="4" @if($poundsoutstatus == 4) selected @endif>重车过磅</option>
                            <option value="5" @if($poundsoutstatus == 5) selected @endif >作废</option>
                        </select>
                    </div>

                    <label class="row-label">发货罐号</label>
                    <div class="row-input required">
                        <input type="hidden" id="storagetank_sysno_detail" name="storagetank_sysno_detail" value="{{$storagetank_sysno}}">
                        <input  type="text"  value="{{$storagetankname}}" disabled>
                    </div>
                    <label class="row-label">鹤位号</label>
                    <div class="row-input">
                        <input type="text" value="{{$cranename}}" disabled>
                    </div>



<!--                     <label class="row-label">提货数量(kg)</label>
                    <div class="row-input required">
                        <input type="text" name="takeqty" value="{{$takeqty}}" placeholder="kg" data-rule="required" disabled>
                    </div> -->
                    <label class="row-label">品名</label>
                    <div class="row-input">
                        <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                    </div>

                    <label class="row-label">提货总数</label>
                    <div class="row-input required">
                        <input type="text" name="takeqty" value="{{intval($takeqty)}}" placeholder="kg" disabled>
                    </div>

                    <!--  
                    <label class="row-label">出库订单号</label>
                        <input type="hidden" id="stockoutdetail_sysno" name="stockoutdetail_sysno" value="{{$stockoutdetail_sysno}}">
                    <div class="row-input required">
                        <input type="text" name="stockoutno" value="{{$stockoutno}}" readonly >
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input ">
                        <input type="text" name="customername" value="{{$customername}}" data-rule="required" readonly="readonly">
                    </div>

                    <label class="row-label">提货公司</label>
                    <div class="row-input ">
                        <input type="text" name="deliverycompany" value="{{$deliverycompany}}" data-rule="required" readonly="readonly">
                    </div> 

                    <label class="row-label">提货单号</label>
                    <div class="row-input ">
                        <input type="text" name="takegoodsno" value="{{$takegoodsno}}" data-rule="required" readonly="readonly">
                    </div> 
                    -->

                    <label class="row-label">车牌号</label>
                    <div class="row-input ">
                        <input type="text" name="carid" value="{{$carid}}" data-rule="required" readonly>
                    </div>

                    <label class="row-label">司机名称</label>
                    <div class="row-input">
                        <input type="text" name="carname" value="{{$carname}}" disabled>
                    </div>

                    <label class="row-label">手机号码</label>
                    <div class="row-input ">
                        <input type="text" name="mobilephone" value="{{$mobilephone}}" disabled>
                    </div>

                    <label class="row-label">身份证号</label>
                    <div class="row-input ">
                        <input type="text" name="idcard"  value="{{$idcard}}" disabled>
                    </div>

                    <label class="row-label required">核定载重</label>
                    <div class="row-input ">
                        <input type="text"  name="loadqty" value="{{intval($loadqty)}}" placeholder="kg" disabled>
                    </div>

                    {{--<label class="row-label required">车辆轴数</label>--}}
                    {{--<div class="row-input required">--}}
                        {{--<select  name="car_axle_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%"--}}
                                {{--data-live-search="true" data-size="10" disabled>--}}
                                {{--<option>{{$axlenum}}</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}

                    {{--<label class="row-label ">轴限</label>--}}
                    {{--<div class="row-input required">--}}
                        {{--<input  type="text" name="carloadweight" value="{{$carloadweight}}" readonly="readonly">--}}
                    {{--</div>--}}

                    <label class="row-label">通知装货量</label>
                    <div class="row-input required ">
                        <input type="text"  name="noticenumber" value="{{intval($noticenumber)}}" placeholder="kg" disabled>
                    </div>

                    <label class="row-label">车辆类型</label>
                    <div class="row-input ">
                        <input type="radio" name="cartype_detial" data-toggle="icheck" value="1" data-label="槽车" @if($cartype==1) checked @endif disabled>
                        <input type="radio" name="cartype_detial" data-toggle="icheck" value="2" data-label="隔舱车" @if($cartype==2) checked @endif disabled>
                        <input type="radio" name="cartype_detial" data-toggle="icheck" value="3" data-label="桶车"  @if($cartype==3) checked @endif disabled>
                    </div>

                    <label class="row-label">是否排队</label>
                    <div class="row-input ">
                        <input type="radio" name="isqueue" value="1" data-toggle="icheck"  data-label="是" @if($isqueue==1) checked @endif  disabled>
                        <input type="radio" name="isqueue" value="2" data-toggle="icheck"  data-label="否" @if($isqueue==2) checked @endif  disabled>
                    </div>
                    <label class="row-label">司磅员</label>
                    <div class="row-input">
                        <input type="text" name="create_username" value="{{$create_username or ''}}" readonly="readonly">
                    </div>
                    <br/>
                    <label class="row-label">备 注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3" disabled>{{$memo or ''}}</textarea>
                    </div>
                </div>
            </fieldset>
            <fieldset id="cartype_field1"  style="display:none;">
                <div  id='cabin_div1' class="bjui-row col-3" style="display:none;">
                    <label class="row-label">确定前后仓</label>
                    <div class="row-input required">
                        <input type="radio" name="cabin" data-toggle="icheck" value="1" data-label="前舱" @if($frontcabin) checked @endif disabled>
                        <input type="radio" name="cabin" data-toggle="icheck" value="2" data-label="后舱" @if($behindcabin) checked @endif disabled>
                    </div>

                    <label class="row-label">单舱核载量</label>
                    <div class="row-input required ">
                        <input type="text" id="frontcabin_num1" name="frontcabin_num" value="{{intval($frontcabin) ? intval($frontcabin) : intval($behindcabin)}}" placeholder="kg" disabled>
                    </div>
                </div>

                <div id="loadtype_div1" class="bjui-row col-3" style="display:none;">
                    <label class="row-label required">罐装方式</label>
                    <div class="row-input">
                        <input type="radio" name="loadtype_detial" data-toggle="icheck" value="1" data-label="定量" @if($loadtype==1) checked @endif disabled>
                        <input type="radio" name="loadtype_detial" data-toggle="icheck" value="2" data-label="不定量" @if($loadtype==2) checked @endif disabled>
                    </div>
                    <label class="row-label">是否带桶</label>
                    <div class="row-input ">
                        <input type="radio" name="isbucket_detial" data-toggle="icheck" value="1" data-label="是" @if($isbucket==1) checked @endif disabled>
                        <input type="radio" name="isbucket_detial" data-toggle="icheck" value="2" data-label="否" @if($isbucket==2) checked @endif disabled>
                    </div>
                    <span id="bucket_num1" style="display:none;">
                        <label class="row-label">桶数</label>
                        <div class="row-input ">
                            <input type="text" id="bucketnumber1" name="bucketnumber" value="{{$bucketnumber}}" disabled>
                        </div>
                    </span>
                    <div id ="bucket_div1" style="display:none;">
                        <label class="row-label">单桶定量</label>
                        <div class="row-input">
                            <input type="text" id="singlebucketweight1" name="singlebucketweight" value="{{intval($singlebucketweight)}}" placeholder="kg" disabled>
                        </div>
                        <label class="row-label">定量总重</label>
                        <div class="row-input ">
                            <input type="text" id="totalunchanged1" name="totalunchanged" value="{{intval($totalunchanged)}}"  readonly="readonly" placeholder="kg" disabled>
                        </div>
                    </div>
                    <div id ="is_bucket_div1" style="display:none;">
                        <label class="row-label">空桶重</label>
                        <div class="row-input">
                            <input type="text" id="emptybucketweight1" name="emptybucketweight" value="{{intval($emptybucketweight)}}" placeholder="kg" disabled>
                        </div>
                        <label class="row-label">空桶总重</label>
                        <div class="row-input ">
                            <input type="text" id="totalemptybucketweight1" name="totalemptybucketweight" value="{{intval($totalemptybucketweight)}}"  readonly="readonly" placeholder="kg" disabled>
                        </div>
                    </div>

                </div>
            </fieldset> 



            <fieldset>
                <div class="bjui-row col-3">
                    
                    <label class="row-label">空车重量(kg)</label>
    
                    <div class="row-input ">
                        <input type="text" name="emptycarqty" value="{{ intval($emptycarqty) }}" readonly="readonly">
                    </div> 

                    <label class="row-label">空车时间</label>

                    <div class="row-input ">
                        <input type="text" name="emptycartime" value="{{$emptycartime}}" data-toggle="datepicker" data-rule="date" disabled="">
                    </div>


                    <label class="row-label">空车地磅</label>

                    <div class="row-input ">
<!--                         <input type="radio" name="emptyloadometer" value="1" data-toggle="icheck"  data-label="50T" @if($emptyloadometer=='50T') checked @endif disabled>
                        <input type="radio" name="emptyloadometer" value="2" data-toggle="icheck"  data-label="80T" @if($emptyloadometer=='80T') checked @endif disabled>
                        <input type="radio" name="emptyloadometer" value="3" data-toggle="icheck"  data-label="100T" @if($emptyloadometer=='100T') checked @endif disabled> -->
                        <input type="text" name="fullloadometer" value="{{$emptyloadometer}}" disabled>
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

                    <label class="row-label">空重车磅差</label>

                    <div class="row-input ">
                        <input type="text"   value="{{$weightdifference or $beqty}}"   readonly>
                    </div>  
                    @if($cartype==3)
                    <label class="row-label">大小磅磅差</label>

                    <div class="row-input ">
                        <input type="text"  value="{{$sizedifference}}" readonly>
                    </div>  
                    @endif
                    <label class="row-label">实际数量</label>

                    <div class="row-input ">
                        <input type="text" id="beqty"  name="beqty" value="{{intval($beqty)}}" readonly>
                    </div>          
                </div>
            </fieldset>


            <!--明细 start-->
            <div class="remarks">
                <fieldset>
                    <legend>磅码信息明细</legend>
                    <table class="table table-bordered" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar: true,
                        editMode:false,
                        data: '{{$detailData}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true,
                        local: 'local',
                        fieldSortable: false
                    }">
                        <thead>
                        <tr data-options="{name:'stockout_sysno'}">
                            <th data-options="{name:'stockoutno',align:'center'}">出库单号</th>
                            <th data-options="{name:'customername',align:'center',}">客户名称</th>
                            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
                            <th data-options="{name:'takegoodscompany',align:'center'}">提货公司</th>
                            <th data-options="{name:'inshipname',align:'center'}">入库船名</th>
                            <th data-options="{name:'cartakeqty',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">车预提数量</th>
                            <th data-options="{name:'cartakeqtyed',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">车已提数量</th>
                            <th data-options="{name:'tobeqty',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">预提数量</th>
                            <th data-options="{name:'takeqty',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">待提数量</th>
                            <th data-options="{name:'unitname',align:'center',render:function(value){return 'KG'; }}">计量单位</th>
                            <th data-options="{name:'realnumber',align:'center', render:function(value){ return parseInt(value)}}">提货数量</th>
                            <th data-options="{name:'bucketnumber',align:'center' }">提货桶数</th>
                            <th data-options="{name:'customer_sysno',align:'center',hide:'true' }">客户ID</th>
                            <th data-options="{name:'stockoutdetail_sysno',align:'center',hide:'true' }">出库详情ID</th>
                            <th data-options="{name:'goodsname',align:'center',hide:'true' }">商品ID</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:'true' }">商品名称</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>

            </div>
            <!--明细 end-->


                @if($void || $poundsoutstatus==5)
                <div class="remarks">
                    <fieldset>
                        <legend>作废意见</legend>
                          <textarea name="memo" id="abandonreason" rows="5" value="{{$abandonreason}}" @if($poundsoutstatus==5) disabled @endif placeholder="请将作废意见填写在此处">{{$abandonreason or ''}}</textarea> 
                    </fieldset>
                </div> 
                @endif
                <br><br>
                <div class="text-center btns-user">
                @if($void)
                
                    <button id="poundsout_void" type="button" class="btn btn-red btn-lg" >作废</button>
                &nbsp;&nbsp;&nbsp;
                @endif    
                    <button  type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
                @if($viewtype=='look')
                    <button id="" type="button" onclick="Design(printfun_poundout)"
                            class="btn btn-green btn-lg">打印设计
                    </button>
                    <button id="" type="button" onclick="Setup(printfun_poundout)"
                            class="btn btn-green btn-lg">打印
                    </button>
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

<!-- 给打印用 -->
<div id="div_poundout_edit1" style="display: none;">
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
        <caption><b><font face="黑体" size="8">{{$companyname}}</font></b><br><font face="黑体" size="4">(发货作业通知单)</font>
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
<script>
    //----------------------操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$sysno or 0}}, 8);
    //----------------------操作记录 end


        if($("input:radio[name='cartype_detial']:checked").val() == 2){
            $('#cartype_field1').show();
            //开启2
            $('#cabin_div1').show();
            //设置必填项
            $("#frontcabin_num1").attr('data-rule',"required");
            //关闭3
            $('#loadtype_div1').hide();
            $('#bucket_div1').hide();
            //关闭3必填项
            $("#singlebucketweight1").removeAttr('data-rule');
            $('#bucketnumber1').removeAttr('data-rule');
            $("#emptybucketweight1").removeAttr('data-rule',"required");
        }else if($("input:radio[name='cartype_detial']:checked").val() == 3){
            //关闭2
            $('#cabin_div1').hide();
            //关闭2必填项
            $("#frontcabin_num1").removeAttr('data-rule');
            //开启3
            $('#cartype_field1').show();
            $('#loadtype_div1').show();

                //装罐方式
                if($("input:radio[name='loadtype_detial']:checked").val() == 1){
                    var count = 0;
                    $('#bucket_div1').show();
                    $('#bucket_num1').show();

                        if($("input:radio[name='isbucket_detial']:checked").val() == 1){
                            $("#is_bucket_div1").hide();
                            $('#bucket_num1').show();
                        }else if($("input:radio[name='isbucket_detial']:checked").val() == 2){
                            $("#is_bucket_div1").show();
                            $('#bucket_num1').show();
                        }

                }else {
                    //不定量
                    $('#bucket_div1').hide();
                    $('#bucket_num1').hide();
                    $("#singlebucketweight1").removeAttr('data-rule',"required");
                    $('#bucketnumber1').removeAttr('data-rule',"required");
                    $("#emptybucketweight1").removeAttr('data-rule',"required");

                        if($("input:radio[name='isbucket_detial']:checked").val() == 1){
                            $('#bucket_num1').hide();
                            $("#is_bucket_div1").hide();
                        }else if($("input:radio[name='isbucket_detial']:checked").val() == 2){
                            $("#is_bucket_div1").show();
                            $('#bucket_num1').show();
                        }
                }

        }else {
            $('#cartype_field1').hide();
            //关闭2
            $('#cabin_div1').hide();
            //关闭3
            $('#loadtype_div1').hide();
            $('#bucket_div1').hide();
            //关闭2必填项
            $("#frontcabin_num1").removeAttr('data-rule');

            //关闭3
            $("#singlebucketweight1").removeAttr('data-rule');
            $('#bucketnumber1').removeAttr('data-rule');

        }


  //作废
    $('#poundsout_void').click(function(){
            if($('#abandonreason').val() == ''){
                BJUI.alertmsg('warn','请填写作废意见');
                return;
            }
            var memo = $('#abandonreason').val();
            var id = $('#poundsout_sysno').val();
            var stockout_sysno = $('#stockout_sysno').val();
            var beqty = $('#beqty').val();
            var storagetank_sysno = $('#storagetank_sysno').val();
            var stockoutdetail_sysno = $('#stockoutdetail_sysno').val();
            var poundsoutno = $(':input[name=poundsoutno]').val();
            // console.log($.CurrentNavtab.find('#outcars-void-form'));return;
            BJUI.ajax('ajaxform',{
                url: '/Bookoutcars/poundsoutVoid',
                form: $.CurrentNavtab.find('#outcars-void-form'),
                validate: false,
                loadingmask: true,
                // type: 'POST',
                // data: {id:id,memo:memo,beqty:beqty,storagetank_sysno:storagetank_sysno,stockout_sysno:stockout_sysno,stockoutdetail_sysno:stockoutdetail_sysno,poundsoutno:poundsoutno},
                okCallback: function(json){
                        // BJUI.alertmsg('ok','作废成功！');
                        BJUI.navtab('closeCurrentTab','');
                        BJUI.navtab('refresh','navab451');
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
    {{--console.log({{$table_html}});--}}
    //打印入库单字段布局
    var printfun_poundout = function CreateStockIn() {
        // console.log("{{'http://'.$_SERVER['SERVER_NAME'].$qrcode_queue}}");
        LODOP = getLodop();
        LODOP.PRINT_INITA(0, 0, 800, 600, "出库磅码单");
        // LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");
        // LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", document.getElementById("div_poundout_edit1").innerHTML);
        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
        LODOP.ADD_PRINT_TEXT(110, 47, 130, 29, "{{date('Y-m-d')}}");
        LODOP.ADD_PRINT_TEXT(157, 110, 130, 27, "{{$carid}}");
        LODOP.ADD_PRINT_TEXT(157, 361, 131, 24, "{{$goodsname}}");
        LODOP.ADD_PRINT_TEXT(158, 550, 70, 26, "{{$takeqty}}"/1000);
        LODOP.ADD_PRINT_TEXT(205, 109, 130, 30, "{{$storagetankname}}"); 
        LODOP.ADD_PRINT_TEXT(259, 113, 130, 30, "{{$cranename}}"); 
        LODOP.ADD_PRINT_TEXT(414, 66, 130, 30, "{{$create_username}}"); 
        LODOP.ADD_PRINT_TEXT(360, 290, 120, 110, "{{$memo}}"); 
        if("{{$qrcode_queue}}"){
            LODOP.ADD_PRINT_TEXT(26, 114, 130, 30, "扫描二维码\n查看排号情况"); 
            LODOP.ADD_PRINT_IMAGE(6,-12, 110, 105,"{{COMMON::createPic( 'http://'.$_SERVER['SERVER_NAME'].$qrcode_queue, TRUE, 'test_bangmaout.png', 110, 110)}}");
        }


    }

</script>
