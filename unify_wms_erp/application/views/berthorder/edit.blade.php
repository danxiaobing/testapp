<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="berthorder-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="berthdetaildata" name="berthdetaildata" value="">
            <input type="hidden" id="step" name="step" value="">
            <input type="hidden" id="getbookdata" name="getbookdata" value="">
            <!--base message start-->
            <fieldset>
                <legend>泊位单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">泊位分配单号:</label>
                    <div class="row-input">
                        <input type="text" name="berthorderno" value="@if($list['berthorderno']){{$list['berthorderno']}} @else {{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">预计日期</label>
                    <div class="row-input">
                        <input type="text" class="buyfree" id="bookingdate" name="bookingdate"  value="{{$list['bookingdate']}}" readonly @if($mode=='eye') readonly @else data-toggle="datepicker" @endif>
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input">
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
                        <select name="businesstype"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10"   @if($mode=='eye') disabled @endif disabled>
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
                    <div class="row-input" >
                        <select name="apply_user_sysno"  id="apply_user_sysno" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10"  @if($mode=='eye') disabled @endif>
                            <option value="">请选择</option>
                            @foreach($employeelist as $type)
                                <option value="{{$type['sysno']}}" @if($type['sysno'] == $list['apply_user_sysno']) selected @endif>{{$type['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="apply_employeename" name="apply_employeename" value="{{$list['apply_employeename']}}">
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
            @if(!in_array($list['businesstype'],[3,4,9,10]))
            <div class="remarks">
                <fieldset>
                    <legend>预约明细</legend>

                    <table class="table table-bordered" id="getbook" data-toggle="datagrid" data-options="{

                            height:'100%',
                            filterThead:false,
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#berthorder_tutton'),
                            local: 'local',
                            addLocation: 'last',
                            dataUrl: '/Berthorder/getbookJson/id/{{$id}}',
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
                            <th data-options="{name:'qualityname',align:'center',render:function(value){if(value=='0') {return ''}}}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                货物性质
                            </th>
                            <th data-options="{name:'unit',align:'center',render:function(value){ return '吨'}}">计量单位</th>
                            <th data-options="{name:'tobeqty',align:'center'}">数量</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'bookingindate',align:'center'}">预计到港日期</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">品名id</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>
            </div>
            @endif
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>泊位分配单明细</legend>

                    <table class="table table-bordered" id="berthorder-detail-table" data-toggle="datagrid" data-options="{

                            height:'100%',
                            filterThead:false,
                            showToolbar: true,
                            toolbarCustom:$.CurrentNavtab.find('#berthorder_tutton'),
                            local: 'local',
                            addLocation: 'last',
                            dataUrl: '/Berthorder/detailJson/id/{{$id}}',
                            dataType: 'json',
                            jsonPrefix: 'obj',
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            showTfoot:true,
                            }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'berthname',align:'center'}">泊位号</th>
                            <th data-options="{name:'berthloadcapacity',align:'center'}">允许最大吃水(米)</th>
                            <th data-options="{name:'berthlength',align:'center'}">泊位长度(米)</th>
                            <th data-options="{name:'berthdeep',align:'center'}">泊位水深(米)</th>
                            <th data-options="{name:'berthtype',align:'center',render:function(value){if(value==0) {return '不限' }}}">核准停泊船型</th>
                            <th data-options="{name:'berthloadweight',align:'center'}">核准停泊能力(吨)</th>
                            <th data-options="{name:'wharfname',align:'center'}">码头</th>
                            <th data-options="{name:'shipname',align:'center'}">船名</th>
                            <th data-options="{name:'captain',align:'center'}">船长</th>
                            <th data-options="{name:'shipcontact',align:'center'}">联系方式</th>
                            <th data-options="{name:'planintime',align:'center'}">计划靠泊时间</th>
                            <th data-options="{name:'planouttime',align:'center'}">计划离泊时间</th>
                            <th data-options="{name:'beintime',align:'center'}">实际靠泊时间</th>
                            <th data-options="{name:'beouttime',align:'center'}">实际离泊时间</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'wharf_sysno',align:'center',hide:'true'}">码头id</th>
                            <th data-options="{name:'berth_sysno',align:'center',hide:'true'}">泊位id</th>
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
                <button type="button" onclick="berthordersubmit(2)" class="btn btn-green btn-lg">保存
                </button>&nbsp;&nbsp;&nbsp;
                <button type="button" onclick="berthordersubmit(3)" class="btn btn-green btn-lg">提交
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
<div id="berthorder_tutton">
    <button type="button" class="btn btn-blue" onclick="addberthorder()" data-icon="plus">添加</button>
    <button type="button" class="btn btn-red" onclick="delretank()" data-icon="fa-close">删除</button>
    <button type="button" class="btn btn-green" onclick="editberthorder()" data-icon="edit">修改</button>
</div>
@endif

<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
    //操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '21');
</script>

<script type="text/javascript">
    //明细添加功能
    function addberthorder(){
        BJUI.dialog({
            url:'/berthorder/Addedit/type/add/',
            title: '增加泊位明细',
            mask:true,
            width: 1000,
            height: 600
        });
    }

    function delretank(){
        var selectdata = $.CurrentNavtab.find('#berthorder-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        } else {
            var allData = $("#berthorder-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#berthorder-detail-table').datagrid('reload', {data: allData});
        }
    }

    //修改
    function editberthorder(){
        var selectedDatas  =  $.CurrentNavtab.find("#berthorder-detail-table").data('selectedDatas');
        if ( typeof(selectedDatas) != 'undefined' && selectedDatas.length == 1) {
            BJUI.dialog({
                url:'/berthorder/Addedit/type/edit/',
                type:'POST',
                data:{selectedDatasArray:selectedDatas[0]},
                mask:true,
                title:'修改泊位分配单明细',
                width:1000,
                height:500
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选中一行进行修改!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    //保存提交
    function berthordersubmit(step) {
        $.CurrentNavtab.find("#step").val(step);
        //申请人姓名
        var apply_user_sysno = $.CurrentNavtab.find('#apply_user_sysno option:selected').text();
        if( $.CurrentNavtab.find('#apply_user_sysno option:selected').val() !=''){
            $.CurrentNavtab.find('#apply_employeename').val(apply_user_sysno);
        }else {
            $.CurrentNavtab.find('#apply_employeename').val('');
        }
        
        var Obj = $.CurrentNavtab.find("#berthorder-detail-table").data('allData');
        $.CurrentNavtab.find("#berthdetaildata").val(JSON.stringify(Obj));

        var data = $.CurrentNavtab.find("#getbook").data('allData');
        $.CurrentNavtab.find("#getbookdata").val(JSON.stringify(data));


        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#berthorder-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.navtab('reloadFlag', 'navab502');
                BJUI.navtab('closeCurrentTab','navab502');
            }
        });
    }

</script>