<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="treeform" action="{{$action}}"  class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="goodsname" value="">

            <div class="bjui-row col-2">

                <label class="row-label">鹤位编号</label>
                <div class="row-input required">
                    <input type="text" name="cranename" value="{{$cranename}}" data-rule="required; "></div>

                <label class="row-label">单据状态</label>
                <div class="row-input required">
                    <input type="hidden" id="cranestatus" name="cranestatus" value="{{$cranestatus}}">
                    @if($cranestatus == 2)
                        <input name="statusname" value="暂存" readonly>
                    @elseif($cranestatus == 3)
                        <input name="statusname" value="待审核" readonly>
                    @elseif($cranestatus == 4)
                        <input name="statusname" value="已审核" readonly>
                    @elseif($cranestatus == 6)
                        <input name="statusname" value="退回" readonly>
                    @elseif($cranestatus == 7)
                        <input name="statusname" value="作废" readonly>
                    @else
                        <input name="statusname" value="新建" readonly>
                    @endif
                </div>

                <label class="row-label">品种</label>
                <div class="row-input required">
                    <select name="goods_sysno" id="goods_sysno" data-rule="required" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($goodslist as $item)
                            @if($goods_sysno == $item['sysno'])
                            <option value="{{$item['sysno']}}" selected>{{$item['goodsname']}}</option>
                                @else
                                <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                            @endif
                        @endforeach
                    </select>
                    <input type="hidden" name="goodsname" id="goodsname" value="{{$goodsname}}">
                </div>

                <label class="row-label">客服专员</label>
                <div class="row-input required">
                    <select name="csemployee_sysno" id="csemployee_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($employeelist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $csemployee_sysno) selected @endif>{{$item['employeename']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="csemployeename" id="csemployeename" value="{{$csemployeename}}">
                </div>

                <label class="row-label">储罐</label>
                <div class="row-input required">
                    <select data-toggle="selectpicker" name="storagetank_sysno" id="storagetank_sysno" data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($storagetanklist as $item)
                            <option value="{{$item['sysno']}}"
                                    @if($item['sysno'] == $storagetank_sysno) selected @endif>{{$item['storagetankname']}}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="storagetankname" id="storagetankname" value="{{$storagetankname}}">
                </div>


                <label class="row-label">安装时间</label>
                <div class="row-input required">
                    <input type="text" name="installtime" value="{{$installtime}}" data-toggle="datepicker" placeholder="请选择安装时间">
                </div>

                <label class="row-label">鹤位状态</label>
                @if($cranestatus != 0 && $cranestatus != 1 && $cranestatus != 2 && $cranestatus !=6)
                <div class="row-input required">
                    <input type="radio" value="1" name="status" data-toggle="icheck"  data-label="启用" @if($status==1) checked @else checked @endif disabled>
                    <input type="radio" value="2" name="status" data-toggle="icheck"  data-label="停用" @if($status==2) checked @endif disabled>
                    <input type="hidden" name="status" id="status" value="{{$status}}">
                </div>
                @else
                    <div class="row-input required">
                        <input type="radio" value="1" name="status" data-toggle="icheck"  data-label="启用"  @if($status==1) checked @else checked @endif>
                        <input type="radio" value="2" name="status" data-toggle="icheck"  data-label="停用"  @if($status==2) checked @endif>
                    </div>
                @endif

                @if($cranestatus !=3)
                <div class="remarks">
                    <fieldset>
                        <legend>备注</legend>
                        <textarea  name="memo" data-rule="" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写备注">{{$memo}}</textarea>
                    </fieldset>
                </div>
                @else
                    <div class="remarks">
                        <fieldset>
                            <legend>审核意见</legend>
                            <textarea id="auditreason" name="auditreason" data-rule="" data-toggle="autoheight" cols="auto" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                        </fieldset>
                    </div>
                @endif

                <div class="text-center btns-user">
                    @if($cranestatus < 3 || $cranestatus == 6)
                        <button id="checksubmit1" type="button" onclick="submit1(2)" class="btn btn-green btn-lg">保存</button>&nbsp;&nbsp;&nbsp;
                        <button id="checksubmit2" type="button" onclick="submit1(3)" class="btn btn-green btn-lg">提交</button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($cranestatus ==3)
                        <button id="checksubmit3" type="button" onclick="submit1(4)" class="btn btn-green btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                        <button id="checksubmit4" type="button" onclick="submit1(6)" class="btn btn-red btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                    @endif
                        <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>&nbsp;&nbsp;&nbsp;
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
        </form>
</div>
{{--<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
    //操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '30');
</script>--}}

<script type="text/javascript">

    $(function(){

        $('#goods_sysno').on('change', function () {
            var goods_name_operation = $(this).find("option:selected").text();
           console.log(goods_name_operation);
            if(goods_name_operation){
                $('input[name=goodsname]').val(goods_name_operation);
            }
        });
    })

    function submit1(step)
    {
        $("Input[name='cranestatus']").val(step);

        if(step == 6)
        {
            $("#auditreason").attr("data-rule", "required");
        }

        $('#storagetankname').val($('#storagetank_sysno option:selected').text());
        $('#csemployeename').val($('#csemployee_sysno option:selected').text());
        BJUI.ajax('ajaxform',{
            url: "{{$action}}",
            form: $.CurrentDialog.find('#treeform'),
            type: 'POST',
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                console.log(json);
                BJUI.navtab('refresh', 'menu481');
                BJUI.dialog('closeCurrent','');
            }
        });
    }


</script>