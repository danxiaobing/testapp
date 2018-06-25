<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="checkform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
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
                        <input type="text" name="checkrecorddate" value="@if($checkrecorddate){{date('Y-m-d',strtotime($checkrecorddate))}}@else{{date('Y-m-d')}}@endif" @if($mode=='audit'||$mode=='abolish') readonly @endif  data-pattern="yyyy-MM-dd" data-toggle="datepicker"  data-rule="required;date"></div>

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
                                data-width="100%" data-rule="required" data-live-search="true" data-size="10" @if($mode=='audit'||$mode=='abolish') disabled @endif>
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
                        <input type="text" name="storagetanknature" id="storagetanknature" value="{{$storagetanknature}}"  readonly >
                    </div>

                    <label class="row-label">货品名称</label>
                    <div class="row-input required">
                        <input type="text" name="goodsname" id="goodsname" value="{{$goodsname}}" readonly >
                    </div>

                    <label class="row-label">温度(°):</label>
                    <div class="row-input required">
                        <input type="text" name="temperature" value="{{$temperature}}" data-rule="required"  @if($mode=='audit'||$mode=='abolish') readonly @endif >
                    </div>

                    <label class="row-label">液位(m):</label>
                    <div class="row-input required">
                        <input type="text" name="liquid" value="{{$liquid}}" data-rule="required"  @if($mode=='audit'||$mode=='abolish') readonly @endif >
                    </div>

                    <label class="row-label">打尺量(吨):</label>
                    <div class="row-input required">
                        <input type="text" name="rulerqty" value="{{$rulerqty}}" data-rule="required"  @if($mode=='audit'||$mode=='abolish') readonly @endif >
                    </div>

                    <label class="row-label">品质检查:</label>
                    <div class="row-input required">
                        <select data-toggle="selectpicker" name="ischecked" data-width="100%" data-rule="required" @if($mode=='audit'||$mode=='abolish') disabled @endif>
                            <option value="">请选择</option>
                            <option value="1" @if($ischecked == 1) selected @endif>合格</option>
                            <option value="2" @if($ischecked == '2') selected @endif>不合格</option>
                        </select>
                    </div>

                    <label class="row-label">操作人员:</label>
                    <div class="row-input required">
                        <select name="created_employee_sysno" id="created_employee_sysno" data-size="5"  @if($mode=='audit'||$mode=='abolish') disabled @endif  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
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
                        <input type="text" name="rulerdate" value="@if($rulerdate){{date('Y-m-d H:i:s',strtotime($rulerdate))}}@else{{date('Y-m-d H:i:s')}}@endif" @if($mode=='audit'||$mode=='abolish') readonly @endif data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss" data-rule="required;datetime" placeholder="打尺时间">
                    </div>
                </div>
                <br>

                <div class="remarks">
                    <fieldset>
                        <legend>备注</legend>
                        <textarea id="memo" name="memo" data-rule="" data-toggle="autoheight" cols="auto" rows="3" @if($mode=='audit'||$mode=='abolish') readonly @endif placeholder="请在此处填写备注">{{$memo}}</textarea>
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

            @if($stockcheckstatus ==3)
            <div class="remarks">
                <fieldset>
                 <legend>审核意见</legend>
                 <textarea id="auditreason" name="auditreason" data-rule="" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                </fieldset>
            </div>
            @endif
            @if($mode == 'abolish')
            <div class="remarks">
                <fieldset>
                    <legend>作废意见</legend>
                    <textarea id="abandonreason" name="abandonreason" data-rule="required" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写作废意见">{{$abandonreason}}</textarea>
                </fieldset>
            </div>
            @endif
            <br> <br>
            <div class="text-center btns-user">
                @if($stockcheckstatus < 3 || $stockcheckstatus == 6)
                    <button id="checksubmit1" type="button" onclick="checksubmit(2)" class="btn btn-green btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                    <button id="checksubmit2" type="button" onclick="checksubmit(3)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($stockcheckstatus ==3)
                    <button id="checksubmit3" type="button" onclick="checksubmit(4)" class="btn btn-green btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                    <button id="checksubmit4" type="button" onclick="checksubmit(6)" class="btn btn-red btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                @endif
                @if($mode == 'abolish')
                    <button type="button" onclick="checksubmit(5)" class="btn btn-red btn-lg">作废</button>&nbsp;&nbsp;&nbsp;
                @endif
                    <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>
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
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '10');
</script>

<script type="text/javascript">
    $("#stockchecktype").change(function(){
        var v=$("#stockchecktype").val();
        if(v==2) {
            $.CurrentNavtab.find("#storagetankno").attr("class","row-input required");
            $.CurrentNavtab.find("#stockcheckstoragetankno").attr("data-rule","required");
        }
        else
        {
            $.CurrentNavtab.find("#storagetankno").attr("class","row-input");
            $.CurrentNavtab.find("#stockcheckstoragetankno").attr("data-rule","");
        }
    });

    function checksubmit(step) {
        $("Input[name='stockcheckstatus']").val(step);
        $("#storagetank_sysno").removeAttr('disabled');

        $("#customername").val($("#customer_sysno option:selected").text());

        if(step == 6)
        {
            $.CurrentNavtab.find("#auditreason").attr("data-rule", "required");
        }

        $('#checkform').isValid(function(v){
            error=v ? '表单验证通过' : '表单验证不通过';
        });

        if(error=='表单验证通过'){
            //if(Obj.length != 0){
                $("#a3").removeAttr('disabled');
                $("#a4").removeAttr('disabled');
                $("#customer_sysno").removeAttr('disabled');
                BJUI.ajax('ajaxform', {
                    url: '{{$action}}',
                    form: $.CurrentNavtab.find('#checkform'),
                    validate: true,
                    loadingmask: true,
                    okCallback: function(json, options) {
                        BJUI.navtab('reloadFlag', 'navab562');
                        BJUI.navtab('closeCurrentTab', '');
                    }
                });
            }
        //}
    }

    $("#storagetank_sysno").change(function (){
        var storagetank_sysno = $("#storagetank_sysno").val();
        BJUI.ajax('doajax', {
            url:'/check/recordgoodsJson/id/'+storagetank_sysno,
            loadingmask: true,
            dataType:'json',
            okCallback: function(json) {
                console.log(json);
                $("#storagetanknature").val(json.storagetanknature);
                $("#goodsname").val(json.goodsname);
            }
        });
    })

    function json_array(data){
        var len=eval(data).length;
        var arr=[];
        for(var i=0;i<len;i++){
            arr[i]=data[i].storagetanknature;
            arr[i]=data[i].goodsname;
        }
        return arr;
    }

    function saveaddattach(){
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#checkform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab291');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }
</script>

