<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="checkform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="checkdetaildata" id="checkdetaildata" value="">
            <input type="hidden" name="instockdata" id="instockdata" value="">
            <!--base message start-->
            <fieldset>
                <legend>库存盘点信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">盘点单号</label>
                    <div class="row-input required">
                        <input type="text" name="checkrecordno" value="@if($checkrecordno){{$checkrecordno}}@else {{系统自动生成}} @endif" readonly>
                    </div>
                    <label class="row-label">盘点日期</label>
                    <div class="row-input required">
                        <input type="text" name="checkrecorddate" value="@if($checkrecorddate){{date('Y-m-d',strtotime($checkrecorddate))}}@else{{date('Y-m-d')}}@endif" @if($mode=='audit'||$mode=='abolish') readonly @endif disabled data-pattern="yyyy-MM-dd" data-toggle="datepicker"  data-rule="required;date"></div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="stockcheckstatus" name="stockcheckstatus" value="{{$stockcheckstatus}}">
                        @if($stockcheckstatus == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($stockcheckstatus == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($stockcheckstatus == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($stockcheckstatus == 5)
                            <input name="statusname" value="作废" readonly>
                        @elseif($stockcheckstatus == 6)
                            <input name="statusname" value="退回" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>
                    <label class="row-label">储罐编号</label>
                    <div class="row-input required">
                        <select data-toggle="selectpicker" name="storagetank_sysno" id="storagetank_sysno"
                                data-width="100%" data-rule="required" data-live-search="true" data-size="10" disabled>
                            <option value="" selected="">请选择</option>
                            @foreach($storagetanklist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $storagetank_sysno) selected @endif>{{$item['storagetankname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="storagetankname" id="storagetankname" value="{{$storagetankname}}">
                    </div>

                    <label class="row-label">储罐性质</label>
                    <div class="row-input required">
                        <input type="text" name="storagetanknature" value="{{$storagetanknature}}"  @if($mode=='audit'||$mode=='abolish') readonly @endif  disabled>
                    </div>

                    <label class="row-label">货品名称</label>
                    <div class="row-input required">
                        <input type="text" name="goodsname" value="{{$goodsname}}"  @if($mode=='audit'||$mode=='abolish') readonly @endif disabled>
                    </div>

                    <label class="row-label">温度(°):</label>
                    <div class="row-input required">
                        <input type="text" name="temperature" value="{{$temperature}}"  @if($mode=='audit'||$mode=='abolish') readonly @endif disabled>
                    </div>

                    <label class="row-label">液位(m):</label>
                    <div class="row-input required">
                        <input type="text" name="liquid" value="{{$liquid}}"  @if($mode=='audit'||$mode=='abolish') readonly @endif disabled>
                    </div>

                    <label class="row-label">打尺量(吨):</label>
                    <div class="row-input required">
                        <input type="text" name="rulerqty" value="{{$rulerqty}}"  @if($mode=='audit'||$mode=='abolish') readonly @endif disabled>
                    </div>

                    <label class="row-label">品质检查:</label>
                    <div class="row-input required">
                        <select data-toggle="selectpicker" name="ischecked" data-width="100%" data-rule="required" disabled>
                            <option value="">请选择</option>
                            <option value="1" @if($ischecked == 1) selected @endif>合格</option>
                            <option value="2" @if($ischecked == '2') selected @endif>不合格</option>
                        </select>
                    </div>

                    <label class="row-label">操作人员:</label>
                    <div class="row-input required">
                        <select name="created_employee_sysno" id="created_employee_sysno" data-size="5"  @if($mode=='audit'||$mode=='abolish') disabled @endif  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $created_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="created_employeename" id="created_employeename" value="{{$created_employeename}}">
                    </div>

                    <label class="row-label">打尺时间:</label>
                    <div class="row-input required">
                        <input type="text" name="rulerdate" value="@if($rulerdate){{date('Y-m-d H:i:s',strtotime($rulerdate))}}@else{{date('Y-m-d H:i:s')}}@endif" @if($mode=='audit'||$mode=='abolish') readonly @endif data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss" data-rule="required;datetime" placeholder="打尺时间" disabled>
                    </div>
                </div>
                <br>

                <div class="remarks">
                    <fieldset>
                        <legend>备注</legend>
                        <textarea id="memo" name="memo" data-rule="" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写备注" disabled>{{$memo}}</textarea>
                    </fieldset>
                </div>

            </fieldset>

            <!--project end-->
            <div class="remarks">
                <fieldset>
                    <legend>上传附件</legend>
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                    {
                        pick: {label: '点击选择附件'},
                        server: '/attachment/uploadjson',
                        fileNumLimit: 10,
                        formData: {module:'check',action:'receipt'},
                        @if($mode=='attach')
                            required: true,
                        @else
                            required: false,
                        @endif
                            uploaded: '{{ $attach1 }}',
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
            <center><button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button></center>&nbsp;&nbsp;&nbsp;
    </div>
    </form>
    <br> <br>
    <div class="remarks hideshow" style="display: none;">
        <fieldset>
            <legend>操作记录明细</legend>
            <div class="addTable">
            </div>
        </fieldset>
    </div>
    <br> <br><br> <br><br> <br>
</div>
</div>



<script src="/static/common/js/custom.js"></script>
<script src="/static/common/js/common.js"></script>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'})
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '10');
</script>

<script type="text/javascript">

</script>

