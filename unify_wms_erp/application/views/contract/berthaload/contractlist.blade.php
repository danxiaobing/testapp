<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="contractformberth" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="goodsdetaildata_berthaload" name="goodsdetaildata" value="">
            <input type="hidden" id="othercostdetaildata" name="othercostdetaildata" value="">
            <!--base message start-->
            <fieldset>
                <legend>基本信息</legend>
                <div class="bjui-row col-3">

                    <label class="row-label">合同编号</label>
                    <div class="row-input required">
                        <input type="text" name="contractnodisplay" value="@if($list['contractnodisplay']){{$list['contractnodisplay']}}@endif" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif data-rule="required"  placeholder="请输入合同编号">
                    </div>

                    <label class="row-label">合同日期</label>
                    <div class="row-input required">
                        @if($list['contractstatus'] >= 2)
                            <input type="text" name="contractdate" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif
                            value="@if($list["contractdate"]){{date('Y-m-d',strtotime($list["contractdate"]))}}@else{{date('Y-m-d')}}@endif"
                                   data-rule="required;date">
                        @else
                            <input type="text" name="contractdate"
                                   value="@if($list["contractdate"]){{date('Y-m-d',strtotime($list["contractdate"]))}}@else{{date('Y-m-d')}}@endif";
                                   data-rule="required;date" data-toggle="datepicker">
                        @endif
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <input type="hidden" id="contractstatus" name="contractstatus" value="">
                        @if($list['contractstatus'] == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($list['contractstatus'] == 3)
                            <input name="statusname" value="评审中" readonly>
                        @elseif($list['contractstatus'] == 4)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($list['contractstatus'] == 5)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($list['contractstatus'] == 6)
                            <input name="statusname" value="退回" readonly>
                        @elseif($list['contractstatus'] == 7)
                            <input name="statusname" value="作废" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <input type="hidden" name="obj.customer_id" value="{{$list['customer_id']}}">
                        <input type="text" name="obj.customername" readonly value="{{$list['customername']}}"
                               data-rule="required" data-toggle="findgrid" data-options="{
                                group:'obj',
                                include:'customer_id:sysno,customername:customername,customerclass:customerclass,customerrelation:customerrelation,customercredit:customercredit,customerterm:customerterm,business_user_sysno:business_user_sysno',
                                dialogOptions: {width:'800',height:'500',title:'客户资料',maxable:true,resizable:true,mask:true},
                                gridOptions: {
                                    width:'100%',
                                    height:'100%',
                                    tableWidth:'99.8%',
                                    local: 'local',
                                    paging: {pageSize:20},
                                    dataUrl: '/customer/listAllJson',
                                    columns: [
                                        {name:'sysno', label:'id',hide:true},
                                        {name:'customername', label:'客户名称'},
                                        {name:'customerabbreviation', label:'客户简称'},
                                        {name:'customerclass',label:'客户性质',render:function(value){if(value=='1') {return '重要'} else{return '一般'} }},
                                        {name:'customerrelation',label:'是否关联方',render:function(value){if(value=='1') {return '是'} else{return '否'} }},
                                        {name:'customercredit',label:'授信额度'},
                                        {name:'customerterm',label:'授信期限'},
                                        {name:'employeename',label:'分管业务员'},
                                    ],
                                    showLinenumber:false
                                },
                                afterSelect:function(data) {
                                   var customerrelation = $('#customerrelation').val();
                                   if(customerrelation == 0){
                                        $('#customerrelation').val('否');
                                   }else if(customerrelation == 1){
                                        $('#customerrelation').val('是');
                                   }
                                }
                            }" @if($list['contractstatus'] == 3) readonly @endif onclick="changethis()" placeholder="点放大镜按钮查找">
                    </div>

                    <label class="row-label">客户性质</label>
                    <div class="row-input required">
                        <select name="obj.customerclass" data-toggle="selectpicker" data-rule="required"
                                data-width="100%" disabled id="customerclass"
                                data-live-search="true" data-size="10">
                            <option value="">请选择</option>
                            <option value="1" @if($list['customerclass'] == 1) selected @endif>重要</option>
                            <option value="2" @if($list['customerclass'] == 2) selected @endif>一般</option>
                            <option value="3" @if($list['customerclass'] == 3) selected @endif>战略</option>
                        </select>
                    </div>

                    <label class="row-label">关联方</label>

                    <div class="row-input">
                        @if($list['isbrother'] == 1)
                            <input name="obj.customerrelation" id="customerrelation" type="text" value="是" readonly>
                        @elseif($list['isbrother'] == "0")
                            <input name="obj.customerrelation" id="customerrelation" type="text" value="否" readonly>
                        @else
                            <input name="obj.customerrelation" id="customerrelation" type="text" value="" readonly>
                        @endif
                    </div>

                    <label class="row-label">授信额度</label>
                    <div class="row-input">
                        <input type="text" name="obj.customercredit" value="{{$list['customercredit']}}" readonly>
                    </div>

                    <label class="row-label">授信期限（月）</label>
                    <div class="row-input">
                        <input type="text" name="obj.customerterm"  value="{{$list['customerterm']}}" readonly>
                    </div>

                    <label class="row-label">业务员</label>
                    <div class="row-input required">
                        <select name="obj.business_user_sysno" disabled id="business_user_sysno" data-toggle="selectpicker"
                                data-width="100%" data-live-search="true" data-size="10" data-rule="required">
                            <option value="">请选择</option>
                            @foreach($saleemployee['list'] as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $list['saleemployee_sysno']) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">客服专员</label>
                    <div class="row-input required">
                        <select name="csemployee_sysno" data-toggle="selectpicker" @if($mode=='eye'||$mode =='audit'||$mode =='back') disabled @endif
                        data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                            <option value="">请选择</option>
                            @foreach($csemployee['list'] as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $list['csemployee_sysno']||$item['sysno'] == $employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">付款方式</label>
                    <div class="row-input required">
                        <select name="settlement_sysno" data-toggle="selectpicker" @if($mode=='eye'||$mode =='audit'||$mode =='back') disabled @endif
                        data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                            <option value="">请选择</option>
                            @foreach($settlementlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $list['settlement_sysno']) selected @endif>{{$item['settlementname']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <br>

                    <label class="row-label">检验要求</label>
                    <div class="row-input">
                        <input type="text" name="testrequire" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif
                        value="@if($list['testrequire']){{$list['testrequire']}}@endif"
                        >
                    </div>

                    <label class="row-label">船舶代理</label>
                    <div class="row-input">
                        <input type="text" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif
                        name="shipproxy" value="@if($list['shipproxy']){{$list['shipproxy']}}@endif"
                        >
                    </div>

                    <label class="row-label">商检</label>
                    <div class="row-input">
                        <input type="text" name="testrequirebusiness" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif
                        value="@if($list['testrequirebusiness']){{$list['testrequirebusiness']}}@endif"
                        >
                    </div>

                    <label class="row-label">合同期限</label>
                    <div class="row-input  required">
                        <div class="input-group input-daterange">
                            <input type="text" id="contractstartdate" name="contractstartdate" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif
                            value="@if($list["contractstartdate"]){{date('Y-m-d',strtotime($list["contractstartdate"]))}}@else{{date('Y-m-d',time())}}@endif"
                                   data-rule="required;date" data-toggle="datepicker">
                            <div class="input-group-addon">至</div>
                            <input type="text" id="contractenddate" name="contractenddate" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif
                            value="@if($list["contractenddate"]){{date('Y-m-d',strtotime($list["contractenddate"]))}}@endif"
                                   data-rule="required;date" data-toggle="datepicker">
                        </div>
                    </div>
                    <br>

                    <label class="row-label">合同备注</label>
                    <div class="row-input">
                        <textarea cols="80" name="contractmemo" @if($mode=='eye'||$mode =='audit'||$mode =='back') readonly @endif rows="3">{{$list["contractmemo"]}}</textarea>
                    </div>

                </div>
                <br><br>
            </fieldset>
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>合约明细</legend>
                    <table class="table table-bordered" id="goods-detail-table" height="{{40+40*(count($list)/19)}}" data-toggle="datagrid" data-options="{
                                height:'100%',
                                filterThead:false,
                                @if($mode!='eye'&&$mode !='audit'&&$mode !='addattach'&&$mode !='back')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#contractberth_goods_tb'),
                            @endif
                            local: 'local',
                            addLocation: 'last',
                            dataUrl: '/contract/goodsdatail/id/{{$id}}',
                                dataType: 'json',
                                jsonPrefix: 'obj',
                                paging: false,
                                linenumberAll: true,
                                fullGrid:true,
                                showTfoot:true,
                            }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'isladder',align:'center',render:function(value){if(value=='0'){return '否'}else{return '是'}}}">是否启用阶梯价</th>
                            <th data-options="{name:'ladderstart',align:'center',render:function(value,data){ if(data.isladder == 0 || !value || value==0){return '--';}}}">阶梯内最小吨数</th>
                            <th data-options="{name:'ladderend',align:'center',render:function(value,data){ if(data.isladder == 0 || !value || value==0){return '--';}}}">阶梯内最大吨数</th>
                            <th data-options="{name:'berthcostforeign',align:'center',render:function(value){if(!value || value==0){return '--';}}}">外贸卸船（元/吨）</th>
                            <th data-options="{name:'berthcostdomestic',align:'center',render:function(value){if(!value || value==0){return '--';}}}">内贸卸船（元/吨）</th>
                            <th data-options="{name:'berthcost',align:'center',render:function(value){if(!value || value==0){return '--';}}}">装船（元/吨）</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">货品id</th>
                            <th data-options="{name:'berthcosttype',align:'center',hide:'true',render:function(value){if(value=='1'){return '装货'}else{return '卸货'}}}">靠泊装卸收费类型</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>
            <br>

            @if($mode =='addattach')
                <div class="remarks">
                    <fieldset class="customerfieldset" id='ship_declaration' style="clear: both;">
                        <legend>合同附件<span style="color: red;">*</span></legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                            {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'contract',action:'addconattach',doc_sysno:'{{$id}}'},
                                required: true,
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
            @endif

            @if($list['contractstatus'] >=2)
                <div class="remarks">
                    <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="auditopinion" name="auditopinion"  data-toggle="autoheight" cols="auto" rows="3">{{$list['auditopinion']}}</textarea>
                        <br>
                    </fieldset>
                </div>
            @endif
            @if($list['contractstatus'] >=5)
                <div class="remarks">
                    <fieldset>
                        <legend>作废原因</legend>
                        <textarea id="abandonreason" name="abandonreason" data-toggle="autoheight" cols="auto" rows="3">{{$list['abandonreason']}}</textarea>
                        <br>
                    </fieldset>
                </div>
            @endif

            <div class="text-center btns-user">
                @if(($list['contractstatus'] < 3||$list['contractstatus'] == 6)&&$mode !='eye')
                    <button type="button" class="btn btn-success btn-lg" onclick="contractformsubmit(1)">暂存</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-success btn-lg" onclick="contractformsubmit(2)">提交</button>&nbsp;&nbsp;&nbsp;
                @elseif($list['contractstatus']==4&&$mode!='eye')
                    <button type="button" class="btn btn-info btn-lg" onclick="contractformsubmit(3)">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-danger btn-lg" onclick="contractformsubmit(4)">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @elseif($list['contractstatus']==5&&$mode !='eye'&&$mode !='addattach')
                    <button type="button"  class="btn btn-danger btn-lg" onclick="contractformsubmit(5)">作废 </button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode =='addattach')
                    <button type="button" class="btn btn-success btn-lg" onclick="addconattach()">上传附件</button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode == 'eye')
                    <button type="button" class="btn btn-success btn-lg" onclick="contract_list2_downloadSeal_pack()">打印</button>&nbsp;
                @endif
                <button type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>&nbsp;
            </div>

        </form>

        <br>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable">

                </div>
            </fieldset>
        </div>
    </div>
</div>
@if($mode!='eye'&&$mode !='audit'&&$mode !='addattach'&&$mode !='back')
    <div id="contractberth_goods_tb">
        <button type="button" class="btn btn-blue" data-icon="plus" onclick="addbergoodsdetail()">添加</button>
        <button type="button" class="btn btn-red" data-icon="times" onclick="delbergoodsdetail()">删除</button>
        <button type="button" class="btn btn-green" data-icon="edit" onclick="editbergoodsdetail()">修改</button>
    </div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'})
</script>

<script type="text/javascript">
    //    //操作记录显示|隐藏
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '16');

    function addbergoodsdetail(){
        BJUI.dialog({
            id:'goodsdetail_add',
            url:'/contract/goodsaddoredit3/',
            title:'增加合约明细',
            width:600,
            height:550,
            mask:true
        });
        return;
    }

    function delbergoodsdetail(){
        var selectdata  =  $.CurrentNavtab.find('#goods-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            var allData  = $.CurrentNavtab.find("#goods-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#goods-detail-table').datagrid('reload',  {data:allData});
        }
    }

    function editbergoodsdetail(){
        var selectedDatas  =  $.CurrentNavtab.find("#goods-detail-table").data('selectedDatas');
        var zuguantype = $("#edit_contracttype").val();

        if (selectedDatas != undefined && selectedDatas.length == 1) {
            BJUI.dialog({
                url:'/contract/contractdetailedit/handlestatus/edit/zuguantype/'+zuguantype+'/contracttype/5',
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                title:'合同明细',
                width:600,
                height:550,
                mask:true
            });
        }else{
            BJUI.alertmsg('warn', '请选中一行进行修改',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        return;
    }

    function addothercost(){
        BJUI.dialog({
            id:'addothercost',
            url:'/contract/othercostaddoredit/',
            title:'增加杂费',
            width:700,
            height:500,
            mask:true
        });
    }

    function delothercost(){
        var selectdata  =  $.CurrentNavtab.find('#othercost-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            var allData  = $("#othercost-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#othercost-detail-table').datagrid('reload',  {data:allData});
        }
    }

    function contract_list2_downloadSeal_pack() {
        BJUI.ajax('ajaxdownload', {
            url: '/contract/export/id/{{$id}}'
        });
    }

    function addconattach(){
        BJUI.navtab('closeTab', 'navab243');

        var error = null;
        $('#contractformberth').isValid(function (v) {
            error = v ? '表单验证通过' : '表单验证不通过';
        });

        if (error == '表单验证通过') {
            BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentNavtab.find('#contractformberth'),
                validate: true,
                loadingmask: true,
                okCallback: function(json, options) {
                    BJUI.navtab('reloadFlag', 'menu244,menu437');
                    BJUI.navtab('closeCurrentTab', '');
                }
            });
        }
    }

    function contractformsubmit(val) {
        // BJUI.navtab('closeTab', 'navab242');
        // BJUI.navtab('closeTab', 'navab243');

        $.CurrentNavtab.find("#contractstatus").val(val);

        $.CurrentNavtab.find('#customerclass').removeAttr("disabled");
        $.CurrentNavtab.find('#business_user_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#edit_contracttype').removeAttr("disabled");

        if (val == 5) {
            $("#abandonreason").attr("data-rule", "required");
        } else if (val == 4) {
            $("#auditopinion").attr("data-rule", "required");
        }else if (val == 3) {
            $("#auditopinion").attr("data-rule", "a");
        }

        var o = $.CurrentNavtab.find("#goods-detail-table").data('allData');
        $("#goodsdetaildata_berthaload").val(JSON.stringify(o));
        var b = $("#storage-detail-table").data('allData');
        $("#storagedetaildata").val(JSON.stringify(b));
        var j = $("#othercost-detail-table").data('allData');
        $("#othercostdetaildata").val(JSON.stringify(j));
        var error = null;
        $('#contractformberth').isValid(function (v) {
            error = v ? '表单验证通过' : '表单验证不通过';
        });
        // console.log(error);return false;
        if (error == '表单验证通过') {
            BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentNavtab.find('#contractformberth'),
                validate: true,
                loadingmask: true,
                okCallback: function(json, options) {
                    BJUI.navtab('reloadFlag', 'menu244,menu437');
                    BJUI.navtab('closeCurrentTab', '');
                }
            });
        }
    }

    function copycontract(){
        BJUI.navtab('closeTab', 'navab243');

        $.CurrentNavtab.find('#customerclass').removeAttr("disabled");
        $.CurrentNavtab.find('#business_user_sysno').removeAttr("disabled");

        var o = $.CurrentNavtab.find("#goods-detail-table").data('allData');
        $("#goodsdetaildata_berthaload").val(JSON.stringify(o));
        var b = $("#storage-detail-table").data('allData');
        $("#storagedetaildata").val(JSON.stringify(b));
        var j = $("#othercost-detail-table").data('allData');
        $("#othercostdetaildata").val(JSON.stringify(j));
        var error = null;
        $('#contractformberth').isValid(function (v) {
            error = v ? '表单验证通过' : '表单验证不通过';
        });

        if (error == '表单验证通过') {
            //$('#contractform').submit();
            BJUI.ajax('ajaxform', {
                url: '{{$action}}',
                form: $.CurrentNavtab.find('#contractformberth'),
                validate: true,
                loadingmask: true,
                okCallback: function(json, options) {
                    BJUI.navtab('reloadFlag', 'menu244,menu437');
                    BJUI.navtab('closeCurrentTab', '');
                }
            });
        }
    }

    $.CurrentNavtab.find("input:radio[name='costtype']").on('ifChecked', function(event) {
        var costtype = $('input:radio:checked').val();
        if (costtype == 1) {
            $("#costdatediv").attr('style','display:inline');
            $("#instockdiv").attr('style','display:none');
        }else if(costtype == 2){
            $("#costdatediv").attr('style','display:none');
            $("#instockdiv").attr('style','display:inline');
        }

    });
    $('.datepicker').datetimepicker({
        //language:  'fr',
        format: 'yyyy-mm-dd',
        weekStart: 1,
        autoclose: true,
        startView: 2,
        minView: 2,
        forceParse: false,
        language: 'zh-CN'

    });
    $(".startDateText").change(function(){
        var endDate = $(this).parents(".input-daterange").find(".endDateText");
        var form_date = $(this).val();
        endDate.datetimepicker('setStartDate', form_date);
    })

    $(".endDateText").change(function(){
        var endDate = $(this).parents(".input-daterange").find(".startDateText");
        var form_date = $(this).val();
        endDate.datetimepicker('setEndDate', form_date);
    })
</script>