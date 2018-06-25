<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="carcheck-edit-form" class="datagrid-edit-form"
              data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="business_sysno" value="{{$business_sysno}}">
            <input type="hidden" name="businesstype" value="{{$businesstype}}">
            <fieldset>
                <legend>车辆检查编辑</legend>
                <div class="bjui-row col-2">
                    <label class="row-label">单据编号：</label>
                    <div class="row-input required">
                        <input type="text" name="carcheckno" value="{{$carcheckno}}" placeholder="单据编号" readonly>
                    </div>

                    <label class="row-label">业务单据编号：</label>
                    <div class="row-input required">
                        <input type="text" name="businessno" value="{{$businessno}}" placeholder="业务单据编号" readonly>
                    </div>

                    <label class="row-label">单据状态：</label>
                    <div class="row-input required">
                        <input type="hidden" id="carcheckstatus" name="carcheckstatus" value="{{$carcheckstatus}}" placeholder="业务单据编号" readonly>
                        <input type="text"  value="@if($carcheckstatus == 1) 新建 @elseif($carcheckstatus == 2) 暂存 @elseif($carcheckstatus == 3) 待审核 @elseif($carcheckstatus == 4) 审核通过 @elseif($carcheckstatus == 5) 车辆退回 @elseif($carcheckstatus == 6) 作废 @elseif($carcheckstatus == 7) 终止 @else 未知类型 @endif" disabled>
                    </div>

                    <label class="row-label">作业类型：</label>
                    <div class="row-input">
                        <input type="text" name="businesstypename" value="@if($businesstype == 1) 船入库预约 @elseif($businesstype == 2) 船入库订单 @elseif($businesstype == 3) 车入库预约 @elseif($businesstype == 4) 车入库 @elseif($businesstype == 10) 车出库@elseif($businesstype == 16) 车退货 @else 未知类型 @endif" readonly>
                    </div>

                    <label class="row-label">司机姓名：</label>
                    <div class="row-input">
                        <input type="text" name="carname" value="{{$carname}}" placeholder="司机姓名" readonly>
                    </div>

                    <label class="row-label">手机号码：</label>
                    <div class="row-input ">
                        <input type="text" name="mobilephone" value="{{$mobilephone}}" placeholder="手机号码" readonly>
                    </div>

                    <label class="row-label">车牌号：</label>
                    <div class="row-input ">
                        <input type="text" name="carid" value="{{$carid}}" placeholder="车牌号" readonly>
                    </div>

                    <label class="row-label">身份证号：</label>
                    <div class="row-input">
                        <input type="text" name="idcard" value="{{$idcard}}" placeholder="身份证号" readonly>
                    </div>

                    <label class="row-label">预提/预卸数量：</label>
                    <div class="row-input required">
                        <input type="text" name="takegoodsnum" value="{{$takegoodsnum}}" placeholder="预提/预卸数量" readonly>
                    </div>

                    <label class="row-label">操作人员：</label>
                    <div class="row-input required">
                        <select name="created_employeename" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                            @foreach($customerlist as $v)
                                <option value="{{$v['realname']}}" @if($v['realname'] == $created_employeename) selected @endif>{{$v['realname']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">审核时间：</label>
                    <div class="row-input required">
                        <input type="text" data-rule="required" name="audittime" value="@if($audittime && $audittime != '0000-00-00 00:00:00'){{date('Y-m-d H:i:s', strtotime($audittime))}}@else{{date('Y-m-d H:i:s')}}@endif" data-toggle="datepicker" data-rule="required;datetime" data-pattern="yyyy-MM-dd HH:mm:ss">
                    </div>
                    <label class="row-label">审核意见：</label>
                    <div class="row-input ">
                        <textarea name="auditreason" data-toggle="autoheight" cols="auto" rows="3" >{{$auditreason or ''}}</textarea>
                    </div>
                </div>
            </fieldset>
            <fieldset class="customerfieldset">
                <legend>上传附件@if($mode == 'addattach')<span style="color: red">*</span>@endif</legend>
                <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                {
                    pick: {label: '点击选择图片'},
                    server: '/attachment/uploadjson',
                    fileNumLimit: 10,
                    formData: {module:'carCheck',action:'aduit'},
                    @if($mode == 'addattach')
                        required: true,
                    @else
                        required: false,
                    @endif
                    uploaded: '{{ $uploaded }}',
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
            <div class="text-center ">
                <button type="button" class="btn-green btn btn-lg" data-icon="save" onclick="saveReceipe(4)">审核通过</button>
                <button type="button" class="btn-green btn btn-lg" data-icon="save" onclick="saveReceipe(5)">审核不通过</button>
                <button type="button" onclick="showRecords()" class="btn btn-gray btn-lg">操作记录</button>
            </div>
        </form>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable">
                </div>
            </fieldset>
        </div>
    </div>
</div>
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '34');

    function saveReceipe(status)
    {
        var carcheckstatus = $("#carcheckstatus").val();
        if(carcheckstatus != 3){
            BJUI.alertmsg('warn','<h4>该单据不是审核状态！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.ajax('ajaxform', {
            url: "{{$action}}"+'/status/'+status,
            form: $.CurrentNavtab.find('#carcheck-edit-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('closeCurrentTab','carCheck-edit');
                BJUI.navtab('reloadFlag', 'navab576');
            }
        });
    }

</script>