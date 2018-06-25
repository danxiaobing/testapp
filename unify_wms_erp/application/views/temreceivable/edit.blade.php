<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="temreceivable-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="temdetaildata" name="temdetaildata" value="">
            <input type="hidden" id="temreceivablestatus" name="temreceivablestatus" value="">

            <!--base message start-->
            <fieldset>
                <legend>基本信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">通知单号</label>
                    <div class="row-input">
                        <input type="text" name="receivableno" value="@if($list['receivableno']){{$list['receivableno']}} @else {{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">日期</label>
                    <div class="row-input required">
                        <input type="text" class="buyfree" id="receivabledate" name="receivabledate"  value="@if($list['receivabledate']) {{$list['receivabledate']}} @else {{date('Y-m-d')}} @endif"  data-rule="required"  @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  readonly @else data-toggle="datepicker"  @endif>
                    </div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="receivablestatus" name="receivablestatus" value="{{$list['receivablestatus']}}" readonly>
                        @if($list['receivablestatus'] == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($list['receivablestatus'] == 3)
                            <input name="statusname" value="待审核" readonly>
                        @elseif($list['receivablestatus'] == 4)
                            <input name="statusname" value="已审核" readonly>
                        @elseif($list['receivablestatus'] == 5)
                            <input name="statusname" value="作废" readonly>
                        @elseif($list['receivablestatus'] == 6)
                            <input name="statusname" value="退回" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>

                    <label class="row-label">客户:</label>
                    <div class="row-input  required">
                        <select name="customer_sysno" id="customer_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  disabled  @endif>
                          <option value="">请选择</option>
                           @foreach($customerlist as $key=>$value)
                                <option value="{{$value['sysno']}}" @if($value['sysno'] == $list['customer_sysno']) selected @endif>{{$value['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customername" id="customername" value="{{$list['customername']}}" >
                    </div>

                    <label class="row-label">开票单位</label>
                    <div class="row-input">
                        <select name="base_company_sysno" id="base_company_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  disabled  @endif>
                            @foreach($company as $key=>$value)
                                <option value="{{$value['sysno']}}"  @if($list['base_company_sysno'] && $value['sysno'] == $list['base_company_sysno']) selected  @elseif(!$list['base_company_sysno'] && $value['isdefault']==1) selected  @endif>{{$value['companyname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="base_companyname" id="base_companyname" value="{{$list['base_companyname']}}" >

                    </div>

                    <label class="row-label">开票抬头</label>
                    <div class="row-input required">
                        {{--<select name="invoice_company_sysno" id="invoice_company_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10" >--}}
                            {{--@foreach($customerlist as $key=>$value)--}}
                                {{--<option value="{{$value['sysno']}}" @if($value['sysno'] == $list['invoice_company_sysno']) selected @endif>{{$value['customername']}}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                        <input type="text" name="invoice_companyname" id="invoice_companyname" data-rule="required"  value="{{$list['invoice_companyname']}}" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  readonly   @endif>
                    </div>

                    <label class="row-label">开票品名</label>
                    <div class="row-input required">
                        <input type="text" id="goodsname" name="goodsname" value="{{$list['goodsname']}}"  data-rule="required" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  readonly  @endif>
                    </div>

                    <label class="row-label">结算期间</label>
                    <div class="row-input required datawidth">
                        <input type="text" name="invoice_startdate" id="invoice_startdate" value="{{$list['invoice_startdate'] or date('Y-m-d') }}" placeholder="开始时间"   data-rule="required" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  readonly @else data-toggle="datepicker"  @endif>
                    </div>
                    <div class="row-input required datawidth">
                        <input type="text" name="invoice_enddate" id="invoice_enddate" value="{{$list['invoice_enddate'] or date('Y-m-d')}}" placeholder="结束时间"  data-rule="required" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  readonly @else data-toggle="datepicker"  @endif>
                    </div>

                    <label class="row-label">开票金额</label>
                    <div class="row-input">
                        <input type="text" name="costreceivable" id="costreceivable" data-rule="required;number;range[0~]" value="{{$list['costreceivable']}}" @if($mode=='eye' || ($list['receivablestatus']>=3 && $list['receivablestatus']<=5))  readonly  @endif>
                    </div>
                </div>
                <br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>临时收款单明细</legend>

                    <table class="table table-bordered" id="temreceivable-detail-table" data-toggle="datagrid" data-options="{

                            height:'100%',
                            filterThead:false,
                            @if($mode ==''||$mode =='edit')
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#temreceivable_tutton'),
                            @endif
                            local: 'local',
                            addLocation: 'last',
                            dataUrl: '/Temreceivable/detailJson/id/{{$id}}',
                            dataType: 'json',
                            jsonPrefix: 'obj',
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot:true,
                            }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'costname',align:'center'}">收费名称</th>
                            <th data-options="{name:'totalprice',align:'center',calc:'sum'}">费用金额</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>
            <!--project end-->

            <br>
            <br>

            @if($mode =='audit')
                <div class="remarks">
                    <fieldset>
                        <legend>审核意见</legend>
                        <textarea id="auditreason" name="auditreason" data-toggle="autoheight" rows="3" placeholder="请在此处填写审核意见">{{$auditreason}}</textarea>
                    </fieldset>
                </div>
           @endif
            @if($mode =='back' )
                <div class="remarks">
                    <fieldset>
                        <legend>作废意见</legend>
                        <textarea id="abandonreason" name="abandonreason" data-toggle="autoheight" rows="3" placeholder="请在此处填写作废意见">{{$abandonreason}}</textarea>
                    </fieldset>
                </div>
            @endif
            <div class="text-center btns-user">

                    @if($mode ==''||$mode =='edit' || !$mode)
                    <button type="button" onclick="temreceivablesubmit(2)" class="btn btn-green btn-lg">保存
                    </button>&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="temreceivablesubmit(3)" class="btn btn-green btn-lg">提交
                    </button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($mode =='audit')
                        <button type="button" onclick="temreceivablesubmit(4)" class="btn btn-green btn-lg">审核通过</button>&nbsp;&nbsp;&nbsp;
                        <button type="button" onclick="temreceivablesubmit(6)" class="btn btn-red btn-lg">审核不通过</button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($mode =='back')
                        <button type="button" onclick="temreceivablesubmit(5)" class="btn btn-red btn-lg">作废</button>&nbsp;&nbsp;&nbsp;
                    @endif
                    <button type="button" onclick="showRecords()"class="btn btn-lg">查看操作记录</button>&nbsp;

                </button>&nbsp;

            </div>
            <br><br>
            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">

                    </div>
                </fieldset>
            </div>
            <div style="height: 200px;"><p>&nbsp;</p></div>

        </form>
    </div>
</div>
@if($mode ==''||$mode =='edit')
<div id="temreceivable_tutton">
    <button type="button" class="btn btn-blue" onclick="addTemreceivable()" data-icon="plus">添加</button>
    <button type="button" class="btn btn-red" onclick="delTemreceivable()" data-icon="fa-close">删除</button>
    <button type="button" class="btn btn-green" onclick="editTemreceivable()" data-icon="edit">修改</button>
</div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
    //操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '15');
</script>

<script type="text/javascript">
    //明细添加功能
    function addTemreceivable(){
        BJUI.dialog({
            url:'/Temreceivable/Addedit/type/add/',
            title: '临时费用明细',
            mask:true,
            width: 800,
            height: 500
        });
    }

    //修改
    function editTemreceivable(){
        var selectedDatas  =  $.CurrentNavtab.find("#temreceivable-detail-table").data('selectedDatas');
        if ( typeof(selectedDatas) != 'undefined' && selectedDatas.length == 1) {
            BJUI.dialog({
                url:'/Temreceivable/Addedit/type/edit/',
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                mask:true,
                title:'修改临时费用单明细',
                width:800,
                height:400
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选中一行进行修改!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    //保存提交
    function temreceivablesubmit(step) {
        if(step==6){
            $("#auditreason").attr("data-rule","required");
        }
        if(step==5){
            $("#abandonreason").attr("data-rule","required");
        }
        //明细
        var Obj = $.CurrentNavtab.find("#temreceivable-detail-table").data('allData');
        $("#temdetaildata").val(JSON.stringify(Obj));
        //得到客户的姓名
        var customer_sysno = $('#customer_sysno option:selected').text();
        $('#customername').val(customer_sysno);

        //得到开票公司
        var base_company_sysno = $('#base_company_sysno option:selected').text();
        $('#base_companyname').val(base_company_sysno);


        //当前提交的状态
        $('#temreceivablestatus').val(step);

        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#temreceivable-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab510');
                BJUI.navtab('closeCurrentTab','navab509');
            }
        });
    }

    function delTemreceivable(){
        var selectdata = $.CurrentNavtab.find('#temreceivable-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        } else {
            var allData = $("#temreceivable-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#temreceivable-detail-table').datagrid('reload', {data: allData});
        }
    }


    function saveaddattach(){

        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#stockretankform'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab303');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

</script>