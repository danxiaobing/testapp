<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="thcar-edit-form" action="{{$action}}" method="POST" data-data-type="json">
            <input type="hidden" id="outcardetaildata" name="outcardetaildata" value="">
            <input type="hidden" name="poundsreback_id" value="{{$id}}">
            <input type="hidden" name="takegoodscompany" value="{{$data['takegoodscompany']}}">
            <input type="hidden" name="stockreback_sysno" value="{{$data['stockreback_sysno']}}">
            <input type="hidden" name="stockrebackno" value="{{$data['stockrebackno']}}">
            <input type="hidden" name="poundsout_sysno" value="{{$data['sysno']}}">
            <fieldset>
                <legend>基本信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">磅码单号</label>
                    <div class="row-input">
                        <input type="text" name="poundsinno" value="@if($data['poundsinno']) {{$data['poundsinno']}} @else {{系统自动生成}} @endif" readonly="readonly">
                    </div>

                    <label class="row-label required">选择地磅</label>
                    <div class="row-input ">
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="50T" data-label="50T" @if($data['loadometer']=='50T') checked @endif>
                        <input type="radio" checked name="loadometer"  data-toggle="icheck" value="80T" data-label="80T" @if($data['loadometer']=='80T') checked @endif>
                        <input type="radio" name="loadometer"  data-toggle="icheck" value="100T" data-label="100T" @if($data['loadometer']=='100T') checked @endif>
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input ">
                        <select id="poundsinstatus" name="poundsinstatus" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" disabled>
                            <option value="1" @if($data['poundsinstatus'] == 1) selected @endif>新增</option>
                            <option value="2" @if($data['poundsinstatus'] == 2) selected @endif>核单完成</option>
                            <option value="3" @if($data['poundsinstatus'] == 3) selected @endif>空车过磅</option>
                            <option value="4" @if($data['poundsinstatus'] == 4) selected @endif>重车过磅</option>
                            <option value="5" @if($data['poundsinstatus'] == 5) selected @endif >作废</option>
                        </select>
                    </div>

                    <label class="row-label">退货罐号</label>
                    <div class="row-input required">
                        <input id="storagetankname" type="hidden" name="storagetankname" value="{{$data['storagetankname']}}">
                        <select id="storagetank_sysno" name="storagetank_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                                <option id="storagetoption" value="{{$data['storagetank_sysno'] }}"  selected >{{ $data['storagetankname'] }}</option>
                        </select>
                    </div>

                    <label class="row-label">鹤位号</label>
                    <div class="row-input required">
                        <input id="cranename" type="hidden" name="cranename" value="{{$data['cranename']}}">
                        <select id="crane_sysno" name="crane_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                            @foreach($craneList as $val)
                                <option value="{{$val['sysno'] }}" @if($val['cranename'] == $data['cranename']) selected @endif>{{ $val['cranename'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">车牌号</label>
                    <div class="row-input ">
                        <input type="text" name="carid" value="{{$data['carid'] or ''}}" data-rule="required" readonly="readonly">
                    </div>

                    <label class="row-label">司机姓名</label>
                    <div class="row-input">
                        <input type="text" name="carname" value="{{$data['carname'] or ''}}" readonly="readonly">
                    </div>

                    <label class="row-label">手机号码</label>
                    <div class="row-input ">
                        <input type="text" name="mobilephone" value="{{$data['mobilephone'] or ''}}" readonly="readonly">
                    </div>

                    <label class="row-label">身份证号</label>
                    <div class="row-input ">
                        <input type="text" name="idcard"  value="{{$data['idcard'] or ''}}" readonly="readonly">
                    </div>
                    <label class="row-label">货品名称</label>
                    <div class="row-input">
                        <input type="hidden" name="goods_sysno" value="{{$data['goods_sysno']}}">
                        <input type="text" name="goodsname" value="{{$data['goodsname']}}" readonly>
                    </div>
                    <label class="row-label">预退数量</label>
                    <div class="row-input">
                        <input type="text" id="unloadnumber" name="unloadnumber" value="{{$data['unloadnumber']}}" placeholder="kg">
                    </div>

                    <label class="row-label">是否排队</label>
                    <div class="row-input ">
                        <input type="radio" name="isqueue" value="1" data-toggle="icheck"  data-label="是">
                        <input type="radio" name="isqueue" value="2" data-toggle="icheck"  data-label="否"checked>
                    </div>
                    <label class="row-label">司磅员</label>
                    <div class="row-input required">
                        <select name="create_username" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            @foreach($customerlist as $v)
                                <option value="{{$v['realname']}}" @if($v['realname'] == $data['create_username']) selected @endif>{{$v['realname']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <br/>
                    <label class="row-label">备 注</label>
                    <div class="row-input">
                        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$data['memo'] or ''}}</textarea>
                    </div>
                </div>
                <br/>
            </fieldset>

            @if($mode!='new')
            <fieldset>
                <legend>综合信息</legend>
                <div class="bjui-row col-3">
                    <label class="row-label">退货单号</label>
                    <div class="row-input ">
                        <input type="text" name="{{$data['takegoodscompany']}}" value="" readonly="readonly">
                    </div>

                    <label class="row-label">重车重量</label>
                    <div class="row-input ">
                        <input type="text" name="fullcarqty" value="{{$data['fullcarqty']}}" readonly="readonly">
                    </div>

                    <label class="row-label">重车时间</label>
                    <div class="row-input required">
                        <input type="text" value="{{$data['fullcartime']}}" data-toggle="datepicker" data-rule="date" disabled="">
                    </div>

                    <label class="row-label">重车地磅</label>
                    <div class="row-input ">
                        <input type="text" name="fullloadometer" value="{{$data['fullloadometer']}}" disabled>
                    </div>

                    <label class="row-label">空车重量(kg)</label>
                    <div class="row-input ">
                        <input type="text" name="emptycarqty" value="{{$data['emptycarqty']}}" readonly="readonly">
                    </div>

                    <label class="row-label">空车时间</label>
                    <div class="row-input ">
                        <input type="text" name="emptycartime" value="{{$data['emptycartime']}}" data-toggle="datepicker" data-rule="date" disabled="">
                    </div>

                    <label class="row-label">空车地磅</label>
                    <div class="row-input ">
                        <input type="text" name="fullloadometer" value="{{$data['emptyloadometer']}}" disabled>
                    </div>

                    <label class="row-label">实际数量</label>
                    <div class="row-input ">
                        <input type="text" id="beqty"  name="beqty" value="{{$data['beqty']}}" readonly>
                    </div>
                </div>
            </fieldset>
            @endif

            <!--明细 start-->
            <div class="remarks">
                <fieldset>
                    <legend>磅码信息明细</legend>
                    <table class="table table-bordered" id="Thcar-detail-pound-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        @if($mode=='new')
                        dataUrl: '/thcar/getstockbackdetailjson/id/{{$data['stockreback_sysno']}}',
                        @else
                        dataUrl: '/thcar/getpoundrebackdetailjson/id/{{$id}}',
                        @endif
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
                            <th data-options="{name:'customername',align:'center',}">客户</th>
                            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
                            <th data-options="{name:'takegoodscompany',align:'center'}">提货公司</th>
                            <th data-options="{name:'unitname',align:'center',render:function(value){return 'KG'; }}">计量单位</th>
                            <th data-options="{name:'inshipname',align:'center'}">入库船名</th>
                            <th data-options="{name:'takegoodsnum',align:'center', render:function(value){ if(value){return parseInt(value)}}}">提货数量</th>
                            <th data-options="{name:'realnumber',align:'center' }">退货数量</th>
                            <th data-options="{name:'stockreback_sysno',align:'center',hide:true }">退货订单id</th>
                            <th data-options="{name:'sysno',align:'center',hide:true }">退货明细id</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>

            </div>
            <!--明细 end-->

            @if($mode!='new')
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

        <div class="comuser-add-center" id='pendcarin_release'>
            <fieldset class="customerfieldset" >
                <legend>上传附件</legend>
                <input type="file"  data-name="attachment[]" data-toggle="webuploader" data-options="
                {
                    pick: {label: '点击选择图片'},
                    server: '/attachment/uploadjson',
                    fileNumLimit: 10,
                    formData: {module:'thcar',action:'edit',doc_sysno:'{{$id}}'},
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
        @if($mode=='ablish'||$mode=='eye')
            <div class="remarks">
                <fieldset>
                    <legend >作废原因</legend>
                    <textarea id="abandonreason" name="abandonreason" data-toggle="autoheight" cols="auto" rows="3" @if($mode=='eye') readonly @endif>{{$data['abandonreason']}}</textarea>
                    <br>
                </fieldset>
            </div>
        @endif

        <div class="text-center btns-user">
            @if($mode=='edit')
            <button type="button" class="btn btn-green btn-lg" onclick="poundrebacksubmit(2)">提交</button>
            @elseif($mode=='new')
            <button type="button" class="btn btn-green btn-lg" onclick="poundrebacksubmit(2)">核单完成</button>
            @elseif($mode=='ablish')
                <button type="button" class="btn btn-red btn-lg" onclick="poundrebacksubmit(5)">作废</button>
            @endif
            <button id="th_car_look" type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
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
<script src="/static/common/js/common.js"></script>

<script type="text/javascript">
    
    addLog($.CurrentNavtab.find('.addTable'), {{$id or 0}}, 35);

</script>

<script type="text/javascript">

    $.CurrentNavtab.find("#storagetank_sysno").change(function (){
        $.CurrentNavtab.find("#storagetankname").val($.CurrentNavtab.find("#storagetank_sysno option:selected").text());
    })

    $.CurrentNavtab.find("#crane_sysno").change(function (){
        $.CurrentNavtab.find("#cranename").val($.CurrentNavtab.find("#crane_sysno option:selected").text());
    })

    function poundrebacksubmit(val) {
        if (val == 5) {
            $.CurrentNavtab.find("#abandonreason").attr("data-rule", "required");
        }

        $.CurrentNavtab.find("#poundsinstatus").removeAttr('disabled');
        var o = $("#Thcar-detail-pound-table").data('allData');
        $("#outcardetaildata").val(JSON.stringify(o));

        var error = null;
        $('#thcar-edit-form').isValid(function (v) {
            error = v ? '表单验证通过' : '表单验证不通过';
        });

        if (error == '表单验证通过') {
            BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentNavtab.find('#thcar-edit-form'),
                validate: true,
                loadingmask: true,
                okCallback: function(json, options) {
                    BJUI.navtab('refresh', 'navab584');
                    BJUI.navtab('closeCurrentTab', '');
                }
            });
        }
    }

</script>