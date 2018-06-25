<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="outcars-edit-form" action="/Bookoutcars/insertFromDataAjax" method="POST"   data-data-type="json">

            <input type="hidden" id="outcardetaildata" name="outcardetaildata" value="">
            <input type="hidden" name="poundout_id" value="{{$id}}">
            <input type="hidden" name="goods_sysno" value="{{$tiDanList['goods_sysno']}}">
            <fieldset>
                <legend>基本信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">磅码单号</label>
                    <div class="row-input">
                        <input type="text" name="poundsoutno" value="@if($poundsoutno) {{$poundsoutno}} @else {{系统自动生成}} @endif" readonly="readonly">
                    </div>

                    <label class="row-label required">选择地磅</label>
                    <div class="row-input ">
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="50T" data-label="50T" @if($tiDanList['loadometer']=='50T') checked @endif>
                        <input type="radio" name="loadometer"  data-toggle="icheck" checked value="80T" data-label="80T" @if($tiDanList['loadometer']=='80T') checked @endif>
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="100T" data-label="100T" @if($tiDanList['loadometer']=='100T') checked @endif>
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input ">
                        <select name="contractstatus" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled="">
                            <option value="1" @if($contractstatus == 1) selected @endif>新增</option>
                            <option value="2" @if($contractstatus == 2) selected @endif>核单完成</option>
                            <option value="3" @if($contractstatus == 3) selected @endif>空车过磅</option>
                            <option value="4" @if($contractstatus == 4) selected @endif>重车过磅</option>
                            <option value="5" @if($contractstatus == 5) selected @endif >作废</option>
                        </select>
                    </div>

                    <label class="row-label">发货罐号</label>
                    <div class="row-input required">
                        <input id="storagetankname_outcar" type="hidden" name="storagetankname_outcar" value="">
                        <select id="storagetank_sysno_outcar" name="storagetank_sysno_outcar" data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                            @foreach($storagetankList as $val)
                                <option id="storagetoption" value="{{$val['storagetank_sysno'] }}" @if($val['storagetank_sysno'] == $sourcestoragetank_sysno) selected @endif>{{ $val['storagetankname'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">鹤位号</label>
                    <div class="row-input required">
                        <input id="bookoutcars_carnename" type="hidden" name="cranename" value="">
                        <select id="bookoutcars_crane_sysno" name="crane_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                            {{--<option value="">请选择</option>--}}
                            @foreach($craneList as $val)
                                <option value="{{$val['sysno'] }}" @if($val['cranename'] == $tiDanList['cranename']) selected @endif>{{ $val['cranename'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">品名</label>
                    <div class="row-input">
                        <input type="text" name="goodsname" value="{{$tiDanList['goodsname']}}" readonly>
                    </div>

                    <label class="row-label">提货数量(kg)</label>
                    <div class="row-input required">
                        <input type="text" name="takeqty" value="{{$tiDanList['realnumber'] ? intval($tiDanList['realnumber']) : 0}}" id="takeqty" readonly  data-rule="required;range[0~];">
                    </div>

                    <!--
                    <label class="row-label">出库订单号</label>
                    <input type="hidden" name="stockout_sysno" value="{{$tiDanList['stockout_sysno']}}">
                    <input type="hidden" name="stockoutdetail_sysno" value="{{$tiDanList['out_detail_sysno']}}">
                    <div class="row-input required">
                        <input type="text" name="stockoutno" value="{{$tiDanList['stockoutno']}}" readonly >
                    </div>
                
                    
                    <label class="row-label">客户</label>
                    <div class="row-input ">
                        <input type="text" name="customername" value="{{$tiDanList['customername'] or ''}}" data-rule="required" readonly="readonly">
                    </div>
                    
                    <label class="row-label">提货公司</label>
                    <div class="row-input ">
                        <input type="text" name="deliverycompany" value="{{$tiDanList['takegoodscompany'] or ''}}" data-rule="required" readonly="readonly">
                    </div>


                    <label class="row-label">提货单号</label>
                    <div class="row-input ">
                        <input type="text" name="takegoodsno" value="{{$tiDanList['takegoodsno'] or ''}}" data-rule="required" readonly="readonly">
                    </div>
                    -->
                    <label class="row-label">车牌号</label>
                    <div class="row-input ">
                        <input type="text" name="carid" value="{{$tiDanList['carid'] or ''}}" data-rule="required" readonly="readonly">
                    </div>

                    <label class="row-label">司机名称</label>
                    <div class="row-input">
                        <input type="text" name="carname" value="{{$tiDanList['carname'] or ''}}" readonly="readonly">
                    </div>

                    <label class="row-label">手机号码</label>
                    <div class="row-input ">
                        <input type="text" name="mobilephone" value="{{$tiDanList['mobilephone'] or ''}}" readonly="readonly">
                    </div>

                    <label class="row-label">身份证号</label>
                    <div class="row-input ">
                        <input type="text" name="idcard"  value="{{$tiDanList['idcard'] or ''}}" readonly="readonly">
                    </div>

                    <label class="row-label required">核定载重(kg)</label>
                    <div class="row-input required">
                        <input type="text" id="loadqty" name="loadqty" value="{{$tiDanList['loadqty'] ? intval($tiDanList['loadqty']) : ''}}" placeholder="kg" data-rule="required;digits">
                    </div>

                    {{--<label class="row-label required">车辆轴数</label>--}}
                    {{--<div class="row-input required">--}}
                        {{--<select id="axlenum" name="car_axle_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%"--}}
                                {{--data-live-search="true" data-size="10">--}}
                            {{--<option value="">请选择</option>--}}
                            {{--@foreach($carInfolist as $key => $value)--}}
                                {{--<option value="{{$value['sysno']}}" @if($tiDanList['car_axle_sysno']==$value['sysno']) selected @endif>{{$value['axlenum']}}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}

                    {{--<label class="row-label ">轴限(kg)</label>--}}
                    {{--<div class="row-input required">--}}
                        {{--<input id="carloadweight" type="text" name="carloadweight" value="{{intval($tiDanList['carloadweight'])}}" readonly="readonly">--}}
                    {{--</div>--}}

                    <label class="row-label">通知装货量</label>
                    <div class="row-input">
                        <input type="text" id="noticenumber" name="noticenumber" value="{{$tiDanList['reallynumber'] ? intval($tiDanList['reallynumber']) : ''}}" readonly="readonly" placeholder="kg">
                    </div>

                    <label class="row-label">车辆类型</label>
                    <div class="row-input ">
                        <input type="radio" name="cartype" data-toggle="icheck" value="1" @if($tiDanList['cartype']==1) checked @endif data-label="槽车">
                        <input type="radio" name="cartype" data-toggle="icheck" value="2" @if($tiDanList['cartype']==2) checked @endif data-label="隔舱车">
                        <input type="radio" name="cartype" data-toggle="icheck" value="3" @if($tiDanList['cartype']==3) checked @endif data-label="桶车">
                    </div>

                    <label class="row-label">是否排队</label>
                    <div class="row-input ">
                        <input type="radio" name="isqueue" value="1" data-toggle="icheck"  data-label="是" checked>
                        <input type="radio" name="isqueue" value="2" data-toggle="icheck"  data-label="否">
                    </div>
                    <label class="row-label">司磅员</label>
                    <div class="row-input required">
                        <select name="create_username" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            @foreach($customerlist as $v)
                                <option value="{{$v['realname']}}" @if($v['realname'] == $tiDanList['create_username']) selected @endif>{{$v['realname']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <br/>
                    <label class="row-label">备 注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$tiDanList['memo'] or ''}}</textarea>
                    </div>
                </div>
                <br/>
            </fieldset>
            <div id="cartype_html">

            </div>
            <div  id='cabin_div' style="display: none;">
                <fieldset>
                    <legend>隔舱车</legend>
                    <div class="bjui-row col-3">
                        <label class="row-label">确定前后仓</label>
                        <div class="row-input required">
                            <input type="radio" name="cabin" data-toggle="icheck" value="1" data-label="前舱" @if($tiDanList['frontcabin']) checked @endif>
                            <input type="radio" name="cabin" data-toggle="icheck" value="2" data-label="后舱" @if($tiDanList['behindcabin']) checked @endif>
                        </div>

                        <label class="row-label">单舱核载量(kg)</label>
                        <div class="row-input required ">
                            <input type="text" id="frontcabin_num" name="frontcabin_num" data-rule="range[0~]" value="{{$tiDanList['frontcabin']  ? intval($tiDanList['frontcabin']) : intval($tiDanList['behindcabin'])}}" placeholder="kg">
                        </div>
                    </div>
                </fieldset>
            </div>

            <div id="cartype_field" style="display:none;">
                <fieldset>
                    <legend>桶车信息</legend>
                    <div id="loadtype_div" class="bjui-row col-3" >
                    <span id="fillingType">
                        <label class="row-label required">罐装方式</label>
                        <div class="row-input required">
                            <input type="radio" name="loadtype" data-toggle="icheck" value="1" data-label="定量" @if($tiDanList['loadtype']==1) checked @endif>
                            <input type="radio" name="loadtype" data-toggle="icheck" value="2" data-label="不定量" @if($tiDanList['loadtype']==2) checked @endif>
                        </div>
                    </span>
                    <span id="fillingY">
                        <label class="row-label">是否带桶</label>
                        <div class="row-input required">
                            <input type="radio" name="isbucket" data-toggle="icheck" value="1" data-label="是" @if($tiDanList['isbucket']==1) checked @endif>
                            <input type="radio" name="isbucket" data-toggle="icheck" value="2" data-label="否" @if($tiDanList['isbucket']==2) checked @endif>
                        </div>
                    </span>
                    <span id="bucket_num" >
                        <label class="row-label">桶数</label>
                        <div class="row-input required">
                            <input type="text" id="bucketnumber" name="bucketnumber" data-rule="digits;" value="{{$tiDanList['bucketnumber']}}">
                        </div>
                    </span>
                        <div id ="bucket_div" >
                            <label class="row-label">单桶定量</label>
                            <div class="row-input required">
                                <input type="text" id="singlebucketweight" name="singlebucketweight" data-rule="digits;" value="{{intval($tiDanList['singlebucketweight'])}}" placeholder="kg">
                            </div>
                            <label class="row-label required">定量总重</label>
                            <div class="row-input ">
                                <input type="text" id="totalunchanged" name="totalunchanged" value="{{$tiDanList['totalunchanged']}}"  readonly="readonly" placeholder="kg">
                            </div>
                        </div>
                        <div id ="is_bucket_div" >
                            <label class="row-label">空桶重</label>
                            <div class="row-input required">
                                <input type="text" id="emptybucketweight" name="emptybucketweight" data-rule="digits;" value="{{intval($tiDanList['emptybucketweight'])}}" placeholder="kg">
                            </div>
                            <label class="row-label">空桶总重</label>
                            <div class="row-input required">
                                <input type="text" id="totalemptybucketweight" name="totalemptybucketweight" value="{{$tiDanList['totalemptybucketweight']}}"  readonly="readonly" placeholder="kg">
                            </div>
                        </div>

                    </div>
                </fieldset>
            </div>
            <br><br>

            <!--明细 start-->
            <div class="remarks">
                <fieldset>
                    <legend>磅码信息明细</legend>
                    <table class="table table-bordered" id="Bookoutcars-detail-pound-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar: true,
                        toolbarCustom: $.CurrentNavtab.find('#stockcarout_edit_btn1'),
                        editMode:false,
                        data: {{$detailData}},
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
                            <th data-options="{name:'unitname',align:'center',render:function(value){return 'KG'; }}">计量单位</th>
                            <th data-options="{name:'cartakeqty',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">车预提数量</th>
                            <th data-options="{name:'cartakeqtyed',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">车已提数量</th>
                            <th data-options="{name:'tobeqty',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">预提数量</th>
                            <th data-options="{name:'takeqty',align:'center',render:function(value){if(!isNaN(value)){return parseFloat(value)}else{return value}}}">待提数量</th>
                            <th data-options="{name:'realnumber',align:'center', render:function(value){ if(value){return parseInt(value)}}}">提货数量</th>
                            <th data-options="{name:'bucketnumber',align:'center' }">提货桶数</th>
                            <th data-options="{name:'customer_sysno',align:'center',hide:'true' }">客户ID</th>
                            <th data-options="{name:'stockoutdetail_sysno',align:'center',hide:'true' }">出库详情ID</th>
                            <th data-options="{name:'goodsname',align:'center',hide:'true' }">商品ID</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:'true' }">商品名称</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:'true'}">储罐ID</th>
                            <th data-options="{name:'storagetankname',align:'center',hide:'true' }">储罐</th>
                            <th data-options="{name:'sysno',align:'center',hide:'true' }">Detai ID</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>

            </div>
            <!--明细 end-->

            <br> <br> <br> <br> <br>

        <div class="text-center btns-user">
            @if($edit)
            <button id="out_car_edit" type="button" class="btn btn-green btn-lg">保存</button>
            @else
            <button id="out_car_success" type="button" class="btn btn-green btn-lg">核单完成</button>
            @endif
            <button id="out_car_look" type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
        </div>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable"></div>
            </fieldset>
        </div>
        </form>
    </div>
</div>
<div id="stockcarout_edit_btn1">
    <button type="button" class="btn btn-green" data-icon='edit' onclick="stockcarout_edit()">编辑</button>
    <button type="button" class="btn btn-red" data-icon='times ' onclick="stockcarout_del()">删除</button>
</div>
<script src="/static/common/js/common.js"></script>

<script type="text/javascript">
 
    addLog($.CurrentNavtab.find('.addTable'), {{$id or 0}}, 8);

</script>

<script type="text/javascript">
    $(function(){
        //获取默认罐号
        var  yuanStorageName= $("select[name=storagetank_sysno_outcar]").find("option:selected").text();
        $("#storagetankname_outcar").val(yuanStorageName);
        $("#storagetank_sysno_outcar").change(function(){
            $("#storagetankname_outcar").val($("select[name=storagetank_sysno_outcar]").find("option:selected").text());
        });
        //可以出库的罐号
        var sure_storagetamk_sysno = "{{$sure_storagetamk_sysno}}";
        //获取轴限
//        $("#axlenum").change(function(){
//            $.ajax({
//                url:'/Bookoutcars/getCarloadWeightJson',
//                type: 'POST',
//                dataType: 'json',
//                data: {'sysno': $(this).val()},
//                success:function(data){
//                    if(data.statusCode == 200){
//                        $('#carloadweight').val(data.carloadweight);
//                    }else {
//                        BJUI.alertmsg('warn', data.message);
//                    }
//                }
//            });
//        })

        /*-------------------切换车辆类型----------------------*/
        $("input:radio[name='cartype']").on('ifChecked', function(event){
            $("#cartype_field ").hide();
            $("#cabin_div").hide();
            var carType = 0;
            carType = event.target.defaultValue;
            if(carType == 2){
                $("#cartype_field").hide();
                $("#cabin_div").show();
            } else if(carType == 3){
                $("#cartype_field ").show();
                $("#cabin_div").hide();
                bucketInit();
            }
        });


        /*-------------------初始化隔舱车-------------------*/

        /*-------------------初始化桶车-------------------*/
        function bucketInit(){
            //初始化
            $("#cartype_field ").find('input[type="text"]').val('');
            $("input:radio[name='loadtype']").iCheck('uncheck');
            $("input:radio[name='isbucket']").iCheck('uncheck');

            //相关完全隐藏(是否带桶,桶数,单桶定量 定量总重,空桶重 空桶总重)
            $("#fillingY").hide();
            $("#bucket_num").hide();
            $("#bucket_div").hide();
            $("#is_bucket_div").hide();
        }

        /*-------------------选择罐装方式--------------------*/

        var loadtype_num,isbucket_num;
        $("input:radio[name='loadtype']").on('ifChecked', function(event){
            $("#fillingY").show();
            $("input:radio[name='isbucket']").iCheck('uncheck');
            loadtype_num = event.target.defaultValue;
            hideAll();
        });

        /*-------------------是否带桶--------------------*/

        $("input:radio[name='isbucket']").on('ifChecked', function(event){
            isbucket_num = event.target.defaultValue;
            //console.log("罐装方式 "+loadtype_num+'<br>'+'是否'+isbucket_num);
            if(loadtype_num == 1 && isbucket_num == 1){
                showHarr([1,2]);
            } else if(loadtype_num == 1 && isbucket_num == 2){
                showHarr([1,2,3]);
            } else if(loadtype_num == 2 && isbucket_num == 1){
                showHarr();
            } else if(loadtype_num == 2 && isbucket_num == 2){
                showHarr([1,3]);
            } else {
                BJUI.alertmsg('warn','请选择罐装方式！');
            }

        });


        /*------------------选择显示------------------*/
        function showHarr(arr){
            hideAll();
            if(arr){
                for (var i = 0; i < arr.length; i++) {
                    if (arr[i] == 1) {
                        $("#bucket_num").show();
                    }
                    else if (arr[i] == 2) {
                        $("#bucket_div").show();
                    }
                    else if (arr[i] == 3) {
                        $("#is_bucket_div").show();
                    }
                }
            }
        }
        /*-------------------隐藏所有---------------------*/
        function hideAll(){
            $("#bucket_num").hide();
            $("#bucket_div").hide();
            $("#is_bucket_div").hide();
            $("#cartype_field ").find('input[type="text"]').val('');

        }

        /*----------------------获取桶车信息------------------------*/
        function allMessage(arrObj){
            if(arrObj) {
                var htmM = "";
                for (var i = 0; i < arrObj.length; i++) {
                    if ($(arrObj[i]).val()=="") {
                        var messgae = '';
                        htmM += "空"+",";
                        if(arrObj[i] == "#bucketnumber"){
                            messgae = '桶数不能为空';
                        }else if( arrObj[i] == "#singlebucketweight"){
                            messgae = '单通数量不能为空';
                        }else if( arrObj[i] == "#emptybucketweight" ){
                            messgae = '空桶重量不能为空';
                        }
                        BJUI.alertmsg('warn', messgae);
                        return false;
                    }
                    else{
                        htmM += $(arrObj[i]).val() + ",";
                    }
                }
                return true;
            }
            return true;
        }
        function typeRadio(obj,objs){
            var as= false;
            if(obj == 1 && objs == 1){
                as = allMessage(["#bucketnumber","#singlebucketweight"]);
            } else if (obj == 1 && objs == 2){
                as = allMessage(["#bucketnumber","#singlebucketweight","#emptybucketweight"]);
            } else if (obj == 2 && objs == 1){
                as = allMessage();
            } else if (loadtype_num == 2 && objs == 2){
                as = allMessage(["#bucketnumber","#emptybucketweight"]);
            }
            return as;
        }

        /*-----------------计算空桶总重--------------------*/
        getEmptyCount();
        function getEmptyCount(){
            var count_empty= 0;
            var bucketnumber = 0;
            var emptybucketweight = 0;
            $('#emptybucketweight').blur(function(){
                emptybucketweight = parseFloat($('#emptybucketweight').val());
                bucketnumber = parseFloat($('#bucketnumber').val());
                if( isNaN(bucketnumber)) {
                    bucketnumber = 0;
                }
                if(isNaN(emptybucketweight)) {
                    emptybucketweight = 0;
                }
                count_empty = emptybucketweight * bucketnumber;
                $("#totalemptybucketweight").val(Math.round(1000*(count_empty))/1000.0
                );
            });
            $('#bucketnumber').blur(function(){
                emptybucketweight = parseFloat($('#emptybucketweight').val());
                bucketnumber = parseFloat($('#bucketnumber').val());
                if( isNaN(bucketnumber)) {
                    bucketnumber = 0;
                }
                if(isNaN(emptybucketweight)) {
                    emptybucketweight = 0;
                }
                count_empty = emptybucketweight * bucketnumber;
                $("#totalemptybucketweight").val(Math.round(1000*(count_empty))/1000.0);
            });
        }

        /*-----------------计算定量总重--------------------*/
        getWeightCount();
        function getWeightCount() {
            var bucketnumber = 0;
            var singlebucketweight = 0;
            $('#singlebucketweight').blur(function () {
                bucketnumber = parseFloat($('#bucketnumber').val());
                singlebucketweight = parseFloat($('#singlebucketweight').val());
                if (isNaN(bucketnumber)) {
                    bucketnumber = 0;
                }
                if (isNaN(singlebucketweight)) {
                    singlebucketweight = 0;
                }
                count = singlebucketweight * bucketnumber;
                $("#totalunchanged").val(Math.round(1000*(count))/1000.0);
            });
            $('#bucketnumber').blur(function () {
                bucketnumber = parseFloat($('#bucketnumber').val());
                singlebucketweight = parseFloat($('#singlebucketweight').val());
                if (isNaN(bucketnumber)) {
                    bucketnumber = 0;
                }
                if (isNaN(singlebucketweight)) {
                    singlebucketweight = 0;
                }
                count = singlebucketweight * bucketnumber;
                $("#totalunchanged").val(Math.round(1000*(count))/1000.0);
            });
        }

        $("#out_car_success").click(function(){
            /*        var Obj = $("#out-cars-edit-table").data('allData');
             $('#outcardetaildata').val(JSON.stringify(Obj));*/
            if($("input:radio[name='loadometer']:checked").size()<1){
                BJUI.alertmsg('warn','请选择地磅!');
                return false;
            }

            if($("input:radio[name='cartype']:checked").size()<1){
                BJUI.alertmsg('warn','车辆类型!');
                return false;
            }else {
                if($("input:radio[name='cartype']:checked").val() == 3){
                    if($("input:radio[name='loadtype']:checked").size()<1){
                        BJUI.alertmsg('warn','请选择装罐方式!');
                        return false;
                    }
                    if($("input:radio[name='isbucket']:checked").size()<1){
                        BJUI.alertmsg('warn','请选择是否带桶!');
                        return false;
                    }
                    /*-----------------------桶车判断------------------------*/
                    if(loadtype_num && isbucket_num) {
                        var boolV = typeRadio(loadtype_num, isbucket_num);
                        if(!boolV){
                            return false;
                        }
                    }
                } else if($("input:radio[name='cartype']:checked").val() == 2){
                    if($("#frontcabin_num").val() == ''){
                        BJUI.alertmsg('warn', '单舱核载量必填');
                        return false;
                    }
                }
            }


            var carloadweight = parseFloat($("#carloadweight").val());
            var loadqty = parseFloat($("#loadqty").val());
            var noticenumber = parseFloat($("#noticenumber").val());
            var totalcount = parseFloat($("#takeqty").val());

            /*        if($("#loadqty").val() == '' ) {
             BJUI.alertmsg('warn', '核定载量必填');
             return false;
             }
             if( $("#carloadweight").val() == '') {
             BJUI.alertmsg('warn', '必须选择车辆轴数');
             return false;
             }
             if( $("#noticenumber").val() == '') {
             BJUI.alertmsg('warn', '通知装货量必填');
             return false;
             }
             if( $("#takeqty").val() == '') {
             BJUI.alertmsg('warn', '提货数量必填');
             return false;
             }

             if( loadqty < noticenumber){
             BJUI.alertmsg('warn', '通知装货量不能大于车辆核定载重');
             return false;
             }
             if(carloadweight < noticenumber){
             BJUI.alertmsg('warn', '通知装货量不能大于车辆轴限');
             return false;
             }
             var totalcount = $('#takeqty').val();
             */
             if(totalcount > loadqty){
                 BJUI.alertmsg('warn', '提货数量不能大于核定载重');
                 return false;
             }

            var detaildata = $.CurrentNavtab.find('#Bookoutcars-detail-pound-table').data('allData');

            var tonum = 0;
            var tidanAlert = false;
            for (var i = 0; i < detaildata.length;i++){
                if(detaildata[i].realnumber == 0 ){
                    BJUI.alertmsg('warn','<h4>请完善磅码明细！<h4>');
                    return false;
                }
                if((detaildata[i].takeqty)*1000 < detaildata[i].realnumber){
                    tidanAlert = true;
                }
                tonum += detaildata[i].realnumber
            }

//        if(tonum < noticenumber){
//            BJUI.alertmsg('warn', '通知装货量不能大于提货总数');
//            return false;
//        }

            // if($("input:radio[name='cartype']:checked").val() == 3 && $("input:radio[name='loadtype']:checked").val() == 1)
            // {
            //     if(noticenumber!=parseFloat($('#totalunchanged').val()))
            //     {
            //         BJUI.alertmsg('warn','提货数量不等于定量总重，请更改提单量或者定量总重');
            //         return false;
            //     }
            // }

            // if($("input:radio[name='cartype']:checked").val() == 3 && $("input:radio[name='loadtype']:checked").val() == 2 && $("input:radio[name='isbucket']:checked").val() == 2)
            // {
            //     if(noticenumber!=parseFloat($('#totalunchanged').val()))
            //     {
            //          BJUI.alertmsg('warn','提货数量不等于空桶总重，请更改提单量或者空桶总重');
            //          return false;
            //     }
            // }

            var detailData = JSON.stringify(detaildata);
            $('#outcardetaildata').val(detailData);
            // return;
            $('#storagetankname_outcar').val($('#storagetank_sysno_outcar option:selected').text());
            var storagetank_sysno_outcar = $('#storagetank_sysno_outcar').val();

            $('#bookoutcars_carnename').val($('#bookoutcars_crane_sysno option:selected').text());


            var tank_stockqty = 0;
            $.ajax({
                url:'/Bookoutcars/ajaxgetTank_stockqty',
                type:'POST',
                async:false,
                data:{storagetank_sysno:storagetank_sysno_outcar},
                success:function(data){
                    tank_stockqty = parseFloat(data);
                }
            });
            if(parseFloat(tank_stockqty) < (totalcount/1000)){//原来是根据noticenumber来比对
                BJUI.alertmsg('warn', $('#storagetank_sysno_outcar option:selected').text()+'罐号库存不够，不允许审核通过');
                return false;
            }
            var storagetank_sysno_array = new Array();
            storagetank_sysno_array = sure_storagetamk_sysno.split(",");
            if(tidanAlert){
                BJUI.alertmsg('confirm', '提货数量不能超过待提数量', {
                    cancelCall:function () {
                        return ;
                    },
                    okCall:function () {
                        if($.inArray($("select[name=storagetank_sysno_outcar]").find("option:selected").val(), storagetank_sysno_array) != -1){
                            BJUI.ajax('ajaxform', {
                                url: '/Bookoutcars/insertFromDataAjax',
                                form: $.CurrentNavtab.find('#outcars-edit-form'),
                                validate: true,
                                loadingmask: true,
                                okCallback: function (json, options) {
                                    console.log(111);
                                    BJUI.navtab('reloadFlag','navab443,navab451');
                                    BJUI.navtab('closeCurrentTab', '')
                                    isbucket_num  = 0;
                                    loadtype_num = 0;
                                }
                            });
                        }else {
                            BJUI.alertmsg('confirm', '发货罐号跟预约出库罐号不一致,请确认是否继续?', {
                                okCall: function() {
                                    BJUI.ajax('ajaxform', {
                                        url: '/Bookoutcars/insertFromDataAjax',
                                        form: $.CurrentNavtab.find('#outcars-edit-form'),
                                        validate: true,
                                        loadingmask: true,
                                        okCallback: function (json, options) {
                                            BJUI.navtab('reloadFlag','navab443,navab451');
                                            BJUI.navtab('closeCurrentTab', '');
                                            isbucket_num  = 0;
                                            loadtype_num = 0;
                                        }
                                    });
                                }
                            });
                        }
                    }
                })
            }else {
                if($.inArray($("select[name=storagetank_sysno_outcar]").find("option:selected").val(), storagetank_sysno_array) != -1){
                    BJUI.ajax('ajaxform', {
                        url: '/Bookoutcars/insertFromDataAjax',
                        form: $.CurrentNavtab.find('#outcars-edit-form'),
                        validate: true,
                        loadingmask: true,
                        okCallback: function (json, options) {
                            console.log(111);
                            BJUI.navtab('reloadFlag','navab443,navab451');
                            BJUI.navtab('closeCurrentTab', '')
                            isbucket_num  = 0;
                            loadtype_num = 0;
                        }
                    });
                }else {
                    BJUI.alertmsg('confirm', '发货罐号跟预约出库罐号不一致,请确认是否继续?', {
                        okCall: function() {
                            BJUI.ajax('ajaxform', {
                                url: '/Bookoutcars/insertFromDataAjax',
                                form: $.CurrentNavtab.find('#outcars-edit-form'),
                                validate: true,
                                loadingmask: true,
                                okCallback: function (json, options) {
                                    BJUI.navtab('reloadFlag','navab443,navab451');
                                    BJUI.navtab('closeCurrentTab', '');
                                    isbucket_num  = 0;
                                    loadtype_num = 0;
                                }
                            });
                        }
                    });
                }
            }
        });

        if("{{$edit}}")
        {

            $("#cartype_field ").hide();
            $("#cabin_div").hide();
            var carType = 0;
            carType = $("input:radio[name='cartype']:checked").val();
            if(carType == 2){
                $("#cartype_field").hide();
                $("#cabin_div").show();
            } else if(carType == 3){
                $("#cartype_field ").show();
                $("#cabin_div").hide();
                $("#fillingY").show();
                // bucketInit();
            }

        }

        $('#out_car_edit').click(function(){
            if($("input:radio[name='loadometer']:checked").size()<1){
                BJUI.alertmsg('warn','请选择地磅!');
                return false;
            }

            if($("input:radio[name='cartype']:checked").size()<1){
                BJUI.alertmsg('warn','车辆类型!');
                return false;
            }else {
                if($("input:radio[name='cartype']:checked").val() == 3){
                    if($("input:radio[name='loadtype']:checked").size()<1){
                        BJUI.alertmsg('warn','请选择装罐方式!');
                        return false;
                    }
                    if($("input:radio[name='isbucket']:checked").size()<1){
                        BJUI.alertmsg('warn','请选择是否带桶!');
                        return false;
                    }
                    /*-----------------------桶车判断------------------------*/
                    if(loadtype_num && isbucket_num) {
                        var boolV = typeRadio(loadtype_num, isbucket_num);
                        if(!boolV){
                            return false;
                        }
                    }
                } else if($("input:radio[name='cartype']:checked").val() == 2){
                    if($("#frontcabin_num").val() == ''){
                        BJUI.alertmsg('warn', '单舱核载量必填');
                        return false;
                    }
                }
            }


            var carloadweight = parseFloat($("#carloadweight").val());
            var loadqty = parseFloat($("#loadqty").val());
            var noticenumber = parseFloat($("#noticenumber").val());

            /*        if($("#loadqty").val() == '' ) {
             BJUI.alertmsg('warn', '核定载量必填');
             return false;
             }
             if( $("#carloadweight").val() == '') {
             BJUI.alertmsg('warn', '必须选择车辆轴数');
             return false;
             }
             if( $("#noticenumber").val() == '') {
             BJUI.alertmsg('warn', '通知装货量必填');
             return false;
             }
             if( $("#takeqty").val() == '') {
             BJUI.alertmsg('warn', '提货数量必填');
             return false;
             }*/

            if( loadqty < noticenumber){
                BJUI.alertmsg('warn', '通知装货量不能大于车辆核定载重');
                return false;
            }
//            if(carloadweight < noticenumber){
//                BJUI.alertmsg('warn', '通知装货量不能大于车辆轴限');
//                return false;
//            }
            var totalcount = $('#takeqty').val();

//            if(totalcount < noticenumber){
//                BJUI.alertmsg('warn', '通知装货量不能大于提货总数');
//                return false;
//            }
            if(totalcount > loadqty){
                BJUI.alertmsg('warn', '提货数量不能大于核定载重');
                return false;
            }

            var detaildata = $.CurrentNavtab.find('#Bookoutcars-detail-pound-table').data('allData');

            var tonum = 0;
            // console.log(detaildata); return;
            var tidanAlert = false;
            for (var i = 0; i < detaildata.length;i++){
                if(detaildata[i].realnumber == 0 ){
                    BJUI.alertmsg('warn','<h4>请完善磅码明细！<h4>');
                    return false;
                }
                if((detaildata[i].takeqty)*1000 < detaildata[i].realnumber){
                    tidanAlert = true;
                }
                tonum += detaildata[i].realnumber
            }
//            if(tonum < noticenumber){
//                BJUI.alertmsg('warn', '通知装货量不能大于提货总数');
//                return false;
//            }
            // return;
            var detailData = JSON.stringify(detaildata);
            $('#outcardetaildata').val(detailData);
            // return;
            $('#bookoutcars_carnename').val($('#bookoutcars_crane_sysno option:selected').text());
            //console.log($('#bookoutcars_carnename').val());
            $('#storagetankname_outcar').val($('#storagetank_sysno_outcar option:selected').text());
            var storagetank_sysno_outcar = $('#storagetank_sysno_outcar').val();
            var tank_stockqty = 0;
            $.ajax({
                url:'/Bookoutcars/ajaxgetTank_stockqty',
                type:'POST',
                async:false,
                data:{storagetank_sysno:storagetank_sysno_outcar},
                success:function(data){
                    tank_stockqty = data;
                }
            });

            if(parseFloat(tank_stockqty) < (noticenumber/1000)){
                BJUI.alertmsg('warn', $('#storagetank_sysno_outcar option:selected').text()+'罐号库存不够，不允许审核通过');
                return false;
            }
            if(tidanAlert){
                BJUI.alertmsg('confirm', '提货数量不能超过待提数量', {
                    cancelCall:function () {
                        return ;
                    },
                    okCall:function () {
                        BJUI.ajax('ajaxform', {
                            url: '/Bookoutcars/Ajaxpoundoutedit',
                            form: $.CurrentNavtab.find('#outcars-edit-form'),
                            validate: true,
                            loadingmask: true,
                            okCallback: function (json, options) {
                                BJUI.navtab('reloadFlag','navab443,navab451');
                                BJUI.navtab('closeCurrentTab', '');
                                isbucket_num  = 0;
                                loadtype_num = 0;
                            }
                        });
                    }
                })
            }else {
                BJUI.ajax('ajaxform', {
                    url: '/Bookoutcars/Ajaxpoundoutedit',
                    form: $.CurrentNavtab.find('#outcars-edit-form'),
                    validate: true,
                    loadingmask: true,
                    okCallback: function (json, options) {
                        BJUI.navtab('reloadFlag','navab443,navab451');
                        BJUI.navtab('closeCurrentTab', '');
                        isbucket_num  = 0;
                        loadtype_num = 0;
                    }
                });
            }

        });
    })

    function stockcarout_edit()
    {
        var receiptdata = $.CurrentNavtab.find('#Bookoutcars-detail-pound-table').data('selectedDatas');

        if (receiptdata == undefined || receiptdata.length == 0 || receiptdata=='') {
            BJUI.alertmsg('warn', "请先选择明细", {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else {
            BJUI.dialog({
                id: 'Bookoutcars-detailedit',
                data:receiptdata[0],
                type: 'POST',
                url: '/Bookoutcars/bookoutcarsdetailedit/',
                title: '磅码信息明细',
                width: 700,
                height: 600,
                mask: true
            });
        }
        return;
    }
    function stockcarout_del()
    {
        BJUI.alertmsg('confirm', '你不需要从此入库单中出货吗?', {
            okCall: function() {
                var selectdata  =  $.CurrentNavtab.find('#Bookoutcars-detail-pound-table').data('selectedDatas');
                if (selectdata == ''||selectdata == null) {
                    BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }

                var allData  = $("#Bookoutcars-detail-pound-table").data('allData');
                if(allData.length==1){
                    BJUI.alertmsg('warn','至少留有一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
                    return ;
                }
                for (var i = selectdata.length - 1; i >= 0; i--) {
                    allData = allData.remove(selectdata[i].gridIndex);
                }
                $.CurrentNavtab.find('#Bookoutcars-detail-pound-table').datagrid('reload',  {data:allData});
            }
        })
    }
</script>