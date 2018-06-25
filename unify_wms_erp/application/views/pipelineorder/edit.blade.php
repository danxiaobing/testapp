<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="pipelineorder-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="pipedetaildata" name="pipedetaildata" value="">
            <input type="hidden" id="pipestatus" name="pipestatus" value="">

            <!--base message start-->
            <fieldset>
                <legend>管线单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">管线分配单号:</label>
                    <div class="row-input">
                        <input type="text" name="pipelineorderno" value="@if($list['pipelineorderno']){{$list['pipelineorderno']}} @else {{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">预计到港时间</label>
                    <div class="row-input required">
                        <input type="text" class="buyfree" id="bookingdate" name="bookingdate"  value="{{date('Y-m-d',strtotime($list['bookingdate']))}}"  data-rule="required"  @if($mode=='eye') readonly @else data-toggle="datepicker" @endif>
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                        <input type="hidden" id="orderstatus" name="orderstatus" value="{{$list['orderstatus']}}" readonly>
                        @if($list['orderstatus'] == 2)
                            <input name="statusname" value="暂存" readonly>
                        @elseif($list['orderstatus'] == 3)
                            <input name="statusname" value="提交" readonly>
                        @else
                            <input name="statusname" value="新建" readonly>
                        @endif
                    </div>


                    <label class="row-label">业务单号：</label>
                    <div class="row-input">
                        <input type="text" name="orderno" id="orderno" value="{{$list['orderno']}}" readonly>
                    </div>


                    <label class="row-label">业务单据类型</label>
                    <div class="row-input">
                        <select name="businesstype2" id="businesstype2" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10"   disabled >
                            @foreach($businesstype as $key=>$type)
                                <option value="{{$key}}" @if($key == $list['businesstype']) selected @endif>{{$type}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="businesstype" id="businesstype" value="{{$list['businesstype']}}" >
                    </div>

                    <label class="row-label">申请时间：</label>
                    <div class="row-input">
                        <input type="text" name="applydate" id="applydate" value="@if($list['applydate']) {{date('Y-m-d',strtotime($list['applydate']))}} @else {{date('Y-m-d')}} @endif"  @if($mode=='eye') readonly @else data-toggle="datepicker" @endif>
                    </div>

                    <label class="row-label">申请人</label>
                    <div class="row-input">
                        <select name="apply_user_sysno"  id="apply_user_sysno" data-toggle="selectpicker" data-rule="" data-width="100%" data-live-search="true" data-size="10"  @if($mode=='eye') disabled @endif >
                            <option value="">请选择</option>
                            @foreach($employeelist as $type)
                                <option value="{{$type['sysno']}}" @if($type['sysno'] == $list['apply_user_sysno']) selected @endif>{{$type['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="apply_employeename" name="apply_employeename" value="{{$list['apply_employeename']}}">
                    </div>

                    <label class="row-label">操作人</label>
                    <div class="row-input">
                        <select name="created_user_sysno" id="created_user_sysno" data-toggle="selectpicker" data-rule="" data-width="100%" data-live-search="true" data-size="10"  @if($mode=='eye') disabled @endif >
                            <option value="">请选择</option>
                            @foreach($employeelist as $type)
                                <option value="{{$type['sysno']}}" @if($type['sysno'] == $list['created_user_sysno']) selected @endif>{{$type['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="created_employeename" name="created_employeename" value="{{$list['created_employeename']}}">
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            @if(!in_array($list['businesstype'],[3,4,9,10]))
            <div class="remarks">
                <fieldset>
                    <legend>预约明细</legend>
                    <table class="table table-bordered" id="stockpipein-detail-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        local: 'local',
                        dataUrl: '/Pipelineorder/getbookJson/id/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                货物性质
                            </th>
                            <th data-options="{name:'unit',align:'center',render:function(value){ return '吨'}}">计量单位</th>
                            <th data-options="{name:'tobeqty',align:'center'}">数量</th>
                            <th data-options="{name:'shipbookingdate',align:'center'}">预计到货日期</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            @if($list['businesstype']==17 || $list['businesstype']==18)
                            <th data-options="{name:'stockretank_out_no',align:'center'}">倒出罐号</th>
                            <th data-options="{name:'stockretank_in_no',align:'center'}">倒入罐号</th>
                            @else
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            @endif
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">goodsid</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>
            @endif
            <div class="remarks">
                <fieldset>
                    <legend>管线分配单明细</legend>

                    <table class="table table-bordered" id="pipelineorder-detail-table" data-toggle="datagrid" data-options="{

                            height:'100%',
                            filterThead:false,
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#pipelineorder_tutton'),
                            local: 'local',
                            addLocation: 'last',
                            dataUrl: '/Pipelineorder/detailJson/id/{{$id}}',
                            dataType: 'json',
                            jsonPrefix: 'obj',
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot:true,
                            }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'wharf_pipelineno',align:'center'}">码头管线号编号</th>
                            <th data-options="{name:'area_pipelineno',align:'center'}">库区管线号</th>
                          {{--  <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'captain',align:'center'}">船长</th>--}}
                            <th data-options="{name:'goodsname',align:'center'}">品种</th>
                            <th data-options="{name:'estimateqty',align:'center'}">预计吨数</th>
                            <th data-options="{name:'estimatedate',align:'center'}">预约时间</th>
                            <th data-options="{name:'beforeqty',align:'center'}">前期储罐余量</th>
                            <th data-options="{name:'afterqty',align:'center'}">后期储罐余量</th>
                            <th data-options="{name:'beqty',align:'center'}">实际流量</th>
                            <th data-options="{name:'startpumptime',align:'center',render:function(value){if(value=='0000-00-00 00:00:00') {return ''} }}">启泵时间</th>
                            <th data-options="{name:'stoppumptime',align:'center',render:function(value){if(value=='0000-00-00 00:00:00') {return ''}}}">停泵时间</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:'true'}">罐号id</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">商品id</th>
                            <th data-options="{name:'wharf_pipeline_sysno',align:'center',hide:'true'}">码头管线号id</th>
                            <th data-options="{name:'area_pipeline_sysno',align:'center',hide:'true'}">库区管线号id</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>
            </div>
            <!--project end-->

            <br>
            <br>

            <div class="text-center btns-user">
                @if($mode && $mode !='eye')
                            <button type="button" onclick="pipelineordersubmit(2)" class="btn btn-green btn-lg">保存
                            </button>&nbsp;&nbsp;&nbsp;
                            <button type="button" onclick="pipelineordersubmit(3)" class="btn btn-green btn-lg">提交
                            </button>&nbsp;&nbsp;&nbsp;
                @endif
                <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录
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
@if($mode && $mode !='eye')
    <div id="pipelineorder_tutton">
        <button type="button" class="btn btn-blue" onclick="addpipelineorder()" data-icon="plus">添加</button>
        <button type="button" class="btn btn-red" onclick="delpipelineorder()" data-icon="fa-close">删除</button>
        <button type="button" class="btn btn-green" onclick="editpipelineorder()" data-icon="edit">修改</button>
    </div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
    //操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '20');
</script>

<script type="text/javascript">
    //明细添加功能
    function addpipelineorder(){
       var goodsList = getgoods();
        console.log(JSON.stringify(goodsList));
        var businesstype = $('#businesstype').val();
        BJUI.dialog({
            url:'/Pipelineorder/Addedit/type/add/',
            type:'POST',
            data: {goodsList:JSON.stringify(goodsList),businesstype:businesstype},
            title: '增加管线明细',
            mask:true,
            width: 1000,
            height: 600
        });
    }

    //删除
    function delpipelineorder(){
        var selectdata = $.CurrentNavtab.find('#pipelineorder-detail-table').data('selectedDatas');
          console.log(selectdata);
        if (selectdata == undefined || selectdata == '' || selectdata == null ) {
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        } else {
            var allData = $("#pipelineorder-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#pipelineorder-detail-table').datagrid('reload', {data: allData});
        }
    }

    //修改
    function editpipelineorder(){
        var selectedDatas  =  $.CurrentNavtab.find("#pipelineorder-detail-table").data('selectedDatas');
        if ( typeof(selectedDatas) != 'undefined' && selectedDatas.length == 1  &&　selectedDatas != '') {
            var goodsList = getgoods();
            var businesstype = $('#businesstype').val();
            BJUI.dialog({
                url:'/Pipelineorder/Addedit/type/edit/',
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0],goodsList:JSON.stringify(goodsList),businesstype:businesstype},
                mask:true,
                title:'修改管线分配单明细',
                width:1000,
                height:500
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选中一行进行修改!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

//保存提交
    function pipelineordersubmit(step) {
        $("#pipestatus").val(step);
        //申请人姓名
        var apply_user_sysno = $('#apply_user_sysno option:selected').text();
        if( $('#apply_user_sysno option:selected').val() !=''){
            $('#apply_employeename').val(apply_user_sysno);
        }else {
            $('#apply_employeename').val('');
        }

        //操作人姓名
        var created_user_sysno = $('#created_user_sysno option:selected').text();
        if($('#created_user_sysno option:selected').val() !=''){
            $('#created_employeename').val(created_user_sysno);
        }else {
            $('#created_employeename').val('');
        }



        //业务类型
        var businesstype2 = $('#businesstype2 option:selected').val();
        $('#businesstype').val(businesstype2);

        var Obj = $.CurrentNavtab.find("#pipelineorder-detail-table").data('allData');
        $("#pipedetaildata").val(JSON.stringify(Obj));

        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#pipelineorder-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab501');
                BJUI.navtab('closeCurrentTab','navab502');
            }
        });
    }
function getgoods(){
    var bookingData = $.CurrentNavtab.find('#stockpipein-detail-table').data('allData');
    var goodsList = new Array();
    var goodsdata = {};
    for (var i = bookingData.length - 1; i >= 0; i--) {
        if($.inArray(bookingData[i]['goods_sysno'],goodsList)==-1)
        {
            goodsdata['goods_sysno'] = bookingData[i]['goods_sysno'];
            goodsdata['goodsname'] = bookingData[i]['goodsname'];
            goodsList.push(goodsdata);
        }
    }
    return goodsList;
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