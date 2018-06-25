<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="stockadjustform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="4">
            <input type="hidden" id="stockadjustdetaildata" name="stockadjustdetaildata" value="">
            <fieldset>
                <legend>基本信息</legend>
                <br><br>
                <div class="bjui-row col-3">

                    <label class="row-label">调整单号</label>
                    <div class="row-input">
                        <input type="text" name="stockcheckno" value="@if($stockcheckno){{$stockcheckno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">调整日期</label>
                    <div class="row-input required">
                        <input type="text" name="stockcheckdate" value="@if($stockcheckdate){{date('Y-m-d',strtotime($stockcheckdate))}}@else{{date('Y-m-d')}}@endif" readonly data-toggle="datepicker" data-rule="required;date">
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="stockcheckstatus" name="stockcheckstatus" value="{{$stockcheckstatus}}" readonly>
                        @if($stockinstatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($stockinstatus == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($stockinstatus == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($stockinstatus == 5)
                            <input name="statusname" value="作废" readonly>
                        @elseif($stockinstatus == 6)
                            <input name="statusname" value="退回" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">公司名称</label>
                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="stockadjust_customer_sysno" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-nextselect="#stockadjust_goods_sysno" data-refurl="/stockadjust/customergoodsJson/id/{value}" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customer_name" id="stockadjust_customername" value="{{$customer_name}}">
                    </div>

                    <label class="row-label">货品名称</label>
                    <div class="row-input required">
                        <select name="goods_sysno" id="stockadjust_goods_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%" @if($mode !=''&&$mode !='edit') disabled @endif>
                            <option value="{{$goods_sysno}}" @if($goods_sysno) selected @endif>{{$goodsname}}</option>
                        </select>
                        <input type="hidden" name="goodsname" id="stockadjust_goodsname" value="{{$goodsname}}">
                    </div>

                    <label class="row-label">制单人</label>
                    <div class="row-input">
                        <select name="zj_employee_sysno" data-size="5" data-toggle="selectpicker" @if($mode !=''&&$mode !='edit') disabled @endif
                                data-live-search="true"  data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}" @if($item['sysno'] == $zj_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="stockadjust_zj_employeename" name="zj_employeename" value="{{$zj_employeename}}">
                    </div>

                </div>
                <br>
                <br>
            </fieldset>

            <div class="remarks">
                <fieldset>
                    <legend>调整明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="stockadjust-detail-table" data-toggle="datagrid" data-options="{
                                height:'100%',
                                filterThead:false,
                                @if($mode == ''||$mode=='edit'||$mode=='sure')
                                showToolbar: true,
                                toolbarCustom:$.CurrentNavtab.find('#stockadjust_tb'),
                                @endif
                                local: 'local',
                                addLocation: 'last',
                                dataUrl: '/stockadjust/adddetailJson/id/{{$id}}',
                                dataType: 'json',
                                jsonPrefix: 'obj',
                                paging: false,
                                fullGrid:true,
                                linenumberAll: true,
                                showTfoot:true,
                            }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'stockinno',align:'center'}">单号</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货物性质</th>
                                <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库数量(吨)</th>
                                <th data-options="{name:'stockqty',align:'center',calc:'sum'}">结存量（吨）</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>

            <fieldset class="customerfieldset">
                <legend>上传附件@if($mode == 'addattach')<span style="color: red">*</span>@endif</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                    {
                        pick: {label: '点击选择图片'},
                        server: '/attachment/uploadjson',
                        fileNumLimit: 10,
                        formData: {module:'stockadjust',action:'stockadjustatt'},
                        @if($mode == 'addattach')
                        required: true,
                        @else
                        required: false,
                        @endif
                        uploaded: '{{ $uploaded1 }}',
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

            @if($mode == 'eye'||$mode == 'audit')
                <div class="remarks">
                    <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="auditreason" name="auditreason" data-toggle="autoheight" @if($mode=='eye') readonly @endif cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            @if($mode == 'eye'||$mode == 'blank')
                <div class="remarks">
                    <fieldset>
                        <legend>作废意见<span style="color: red">*</span></legend>
                        <textarea id="abandonreason" name="abandonreason" data-toggle="autoheight" @if($mode=='eye') readonly @elseif($mode == 'back') data-rule="required" @endif cols="auto" rows="3" placeholder="请在此处填写退回意见">{{$abandonreason}}</textarea>
                    </fieldset>
                </div>
            @endif

            <br>
            <br>
            <div class="text-center btns-user">
                @if($mode == ''||$mode =='edit')
                    <button type="button" class="btn btn-green btn-lg" onclick="stockadjustsubmit(2)">暂存</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-green btn-lg" onclick="stockadjustsubmit(3)">提交</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'audit')
                    <button type="button" class="btn btn-green btn-lg" onclick="stockadjustsubmit(4)">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-red btn-lg" onclick="stockadjustsubmit(6)">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'addattach')
                    <button type="button" class="btn btn-green btn-lg" onclick="saveaddattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                @elseif($mode == 'blank')
                    <button type="button" class="btn btn-red btn-lg" onclick="stockadjustsubmit(5)">作废</button>&nbsp;&nbsp;&nbsp;
                @endif
                <button type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>&nbsp;
                @if($mode == 'eye' && $ca_no)
                    <a href="{{$ca_address}}" target="_blank" class="btn btn-orange" style="height: 50px;line-height: 38px;">查看CA合同</a> @endif
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

@if($mode == ''||$mode=='edit'||$mode=='sure')
    <div id="stockadjust_tb">
        <button type="button" class="btn btn-green" data-icon="add" onclick="addstockadjustdetail()">添加</button>
        {{--<button type="button" class="btn btn-green" data-icon="edit" onclick="editstockadjustdetail()">修改</button>--}}
        <button type="button" class="btn btn-red" data-icon="del" onclick="delstockadjustdetail()">删除</button>
        <button type="button" id="stockadjustdetailmode" style="display: none">{{$mode}}</button>
    </div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'});

    //操作记录
    addLog($.CurrentNavtab.find('.addTable'),{{$id}}, '33');

</script>

<script type="text/javascript">

    $("#stockadjust_customer_sysno").change(function (){
        $('#stockadjust-detail-table').datagrid('reload',  {data:[]});
    });

    function addstockadjustdetail(){
        var customer_sysno = $.CurrentNavtab.find('#stockadjust_customer_sysno').val();
        var goods_sysno = $.CurrentNavtab.find('#stockadjust_goods_sysno').val();
        var mode = $("#stockadjustdetailmode").html();

        if (customer_sysno.length > 0 && goods_sysno.length > 0) {
            BJUI.dialog({
                url:'/stockadjust/stockadjustdetailedit/handlestatus/add/cid/' + customer_sysno + '/goodid/' + goods_sysno,
                type:'POST',
                data:{mode:mode},
                mask:true,
                title:'调整明细',
                width:800,
                height:400
            });
        }else{
            console.log('no');
        }
        return;
    }

    function delstockadjustdetail(){
        var selectdata  =  $.CurrentNavtab.find('#stockadjust-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            var allData  = $("#stockadjust-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#stockadjust-detail-table').datagrid('reload',  {data:allData});
        }
    }

    function stockadjustsubmit(step) {

        $.CurrentNavtab.find("#stockcheckstatus").val(step);

        if (step == 6) {
            $.CurrentNavtab.find("#backreason").attr("data-rule", "required");
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "required");
        } else if (step == 4) {
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "a");
        } else if (step == 8) {
            $.CurrentNavtab.find("#rejectreason").attr("data-rule", "required");
        } else if (step == 3) {
            $.CurrentNavtab.find("#rejectreason").attr("data-rule", "a");
        }

        var detailObj = $.CurrentNavtab.find("#stockadjust-detail-table").data('allData');
        $.CurrentNavtab.find("#stockadjustdetaildata").val(JSON.stringify(detailObj));

        $.CurrentNavtab.find("#zj_employeename").val($.CurrentNavtab.find("#zj_employee_sysno option:selected").text());
        $.CurrentNavtab.find("#stockadjust_customername").val($.CurrentNavtab.find("#stockadjust_customer_sysno option:selected").text());
        $.CurrentNavtab.find("#stockadjust_goodsname").val($.CurrentNavtab.find("#stockadjust_goods_sysno option:selected").text());
        $.CurrentNavtab.find("#stockadjust_zj_employeename").val($.CurrentNavtab.find("#zj_employee_sysno option:selected").text());

        $.CurrentNavtab.find('#stockadjust_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#stockadjust_goods_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#zj_employee_sysno').removeAttr("disabled");

        $('#stockadjustform').isValid(function (v) {
            if (v) {
                if (step == 8) {
                    BJUI.alertmsg('confirm', "是否驳回此预约单？", {okCall:function() {
                        submit();
                    }})
                } else {
                    BJUI.ajax('ajaxform', {
                        url: $.CurrentNavtab.find('#stockadjustform').attr('action'),
                        form: $.CurrentNavtab.find('#stockadjustform'),
                        validate: true,
                        loadingmask: true,
                        okCallback: function (json, options) {
                            BJUI.navtab('reloadFlag', 'navab573');
                            BJUI.navtab('closeCurrentTab', '');
                        }
                    });
                }
            }
        })
    }

    function saveaddattach(){
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#stockadjustform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab219');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

</script>