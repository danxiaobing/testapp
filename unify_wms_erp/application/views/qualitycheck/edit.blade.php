<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="qualitycheck-editfrom" action="{{$action}}"  method="POST" class="datagrid-edit-form" data-data-type="json" >
            <input type="hidden" name="id" value="{{ $sysno }}">
            <input type="hidden" name="qualitycheck_detail" value="" id="qualitycheck_detail">
            <input type="hidden" name="apply_employeename"  value="" id="qualitycheck_apply_employeename">
            <input type="hidden" name="orderstatus" value="">
            <input type="hidden" id="qualitycheck_goods_sysno" name="goods_sysno" value="{{ $goods_sysno }}">
            <input type="hidden" id="qualitycheck_goodsname" name="goodsname" value="{{ $goodsname }}">
            <!--base message start-->
            <fieldset>
                <legend>基本信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">品质检查单号</label>
                    <div class="row-input">
                            <input type="text" name="qualitycheckno" id="qualitycheckno" value="{{$qualitycheckno}}" readonly>
                        </div>
                    <label class="row-label">预约日期</label>
                    <div class="row-input">
                        <input type="text" name="versionname" value="{{$bookingdate}}" readonly ></div>

                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <select name="" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%"  disabled="" >
                            <option value="1" @if($orderstatus == '1') selected @endif>新建</option>
                            <option value="2" @if($orderstatus == '2') selected @endif>暂存</option>
                            <option value="3" @if($orderstatus == '3') selected @endif>提交</option>
                            <option value="4" @if($orderstatus == '4') selected @endif>已审核</option>
                            <option value="5" @if($orderstatus == '5') selected @endif>退回</option>
                            <option value="6" @if($orderstatus == '6') selected @endif>让步待审核</option>
                            <option value="7" @if($orderstatus == '7') selected @endif>让步审核通过</option>
                            <option value="8" @if($orderstatus == '8') selected @endif>终止</option>
                        </select>
                    </div>

                    <label class="row-label">业务单号</label>
                    <div class="row-input">
						<input type="text" name="" value="@if($stockno) {{$stockno}} @else {{$bookingno}} @endif" readonly>
                    </div>

                    <label class="row-label">客户名称</label>
                    <div class="row-input">
                        <input type="text" name="customername" value="{{$customername}}" readonly>
                        <input type="hidden" name="customer_sysno" value="{{$customer_sysno}}">
                    </div>

                    <label class="row-label">业务单据类型</label>
                    <div class="row-input">
                        <input type="text" name="businesstype" value="{{$businesstype}}" readonly="readonly">
                    </div>

                    <label class="row-label">船/车名</label>
                    <div class="row-input">
                        <input type="text" name="carshipname" value="{{$carshipname}}" readonly="readonly">
                    </div>

                    <label class="row-label">申请时间</label>
                    <div class="row-input">
                        <input type="text" name="applydate" value="{{$applydate}}" readonly="readonly">
                    </div>

                    <label class="row-label">申请人</label>
                    <div class="row-input">
                        <select name="apply_user_sysno"  id="qualitycheck_apply_user_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true"  data-width="100%"  @if($orderstatus == 3 || $orderstatus == 6 || $tempType == 'view') disabled @endif>
                        <option value="">请选择</option>
						@foreach($employeelist as $item)
						<option value="{{$item['sysno']}}" @if($item['sysno'] == $apply_user_sysno) selected @endif >{{$item['employeename']}}</option>
						@endforeach
                        </select>
                    </div>
                </div>
                <br></fieldset>
            <!--base message end-->
            @if($businesstypenum==1 || $businesstypenum==4 || $businesstypenum==6 || $businesstypenum==8)
            <br>
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>预约明细</legend>
                    <div class="table-edit">
                    
                        <table class="table table-bordered" id="qualitycheck-booking-detail-table" data-toggle="datagrid"
                               data-options="{
                                    height:'100%',
                                    filterThead:false,
                                    showToolbar: false,
                                    local: 'local',
                                    dataUrl: '{{$detailUrl}}',
                                    dataType: 'json',
                                    jsonPrefix: 'obj',
                                    paging: false,
                                    fullGrid:true,
                                    showTfoot:true,
                                    linenumberAll: true
                               }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                                <th data-options="{name:'goods_quality_name',align:'center',render:function(value,data){if(!value){return data.qualityname}}}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                                    货物性质
                                </th>
                                <th data-options="{name:'bookinginqty',calc:'sum',align:'center',render:function(value,data){if(!value){return data.beqty}}}">数量(吨)</th>
                                <th data-options="{name:'shipname',align:'center'}">船名</th>
                                <th data-options="{name:'bookingindate',align:'center'}">预计到港日期</th>
                                <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品ID</th>
                            </tr>
                            </thead>
                        </table>
                       
                    </div>
                </fieldset>
            </div>
             @endif
            <br><br><br>
            <!--project start-->
            <div class="remarks">
				<fieldset>
				    <legend>品质检查明细</legend>
				<div class="table-edit">
				<table class="table table-bordered" id="qualitycheck-detail-table" data-toggle="datagrid" data-options="{
				            width:'100%',
				            tableWidth:'99%',
				            filterThead:false,
                            @if($tempType !='view' && $orderstatus != 3 && $orderstatus != 6)
				            showToolbar:true,
				            toolbarCustom:$.CurrentNavtab.find('#qualitycheckedit_btn'),
				            @endif
                            @if($orderstatus == 3 && $tempType !='view')
                            showToolbar:true,
                            toolbarCustom:$.CurrentNavtab.find('#qualityedit_audit_btn'),
                            @endif
				            local: 'local',
				            dataUrl: 'qualitycheck/qualitycheckdetail/pid/{{$sysno}}',
				            dataType: 'json',
				            jsonPrefix: 'obj',
				            paging: false,
				            fullGrid:true,
				            linenumberAll: true
				        }">
				    <thead>
				        <tr data-options="{name:'sysno'}">
				            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
				            <th data-options="{name:'checktime',align:'center'}">品质检查时间</th>
				            <th data-options="{name:'ischecked',align:'center',render:function(value){if(value==1){return '合格';}else{return '不合格';}}}">是否合格</th>
                            @if($orderstatus != 2 && $orderstatus != 5)
				            <th data-options="{name:'isskip',align:'center',render:function(value){if(value == 1){return '让步';}else if(value==2){return '不让步';}else{return '--'}}}">是否让步</th>
                            @endif
				            <th data-options="{name:'created_employeename',align:'center'}">记录人</th>
				            <th data-options="{name:'memo',align:'center'}">备注</th>
				            <th data-options="{name:'goods_sysno',align:'center',hide:true}">货品信息ID</th>
				            <th data-options="{name:'created_user_sysno',align:'center',hide:true}">记录人ID</th>
                            {{--<th data-options="{name:'u_upload',align:'center'}">查看</th>--}}
                            {{--<th data-options="{name:'u_upload',align:'center',render:qualitychecklist_operation}">查看</th>--}}

				        </tr>
				    </thead>
				</table>

				</div>
				</fieldset>
			</div>
            </form>
            <!--project end-->
			<br>
			<br>
            <br>
            @if($orderstatus == 3 || $orderstatus == 6)
            <div class="remarks">
            <fieldset style="clear: both;">
                <legend>审核意见</legend>
                <form id="qualitycheck-audit-form" action="/qualitycheck/examJson" method="POST" class="datagrid-edit-form" data-toggle="ajaxform">
                    <input type="hidden" name="id" value="{{$sysno}}">
                    <input type="hidden" name="examstep" id="qualitycheck_edit_examstep" value="">
                    <input type="hidden" name="examdetail" id="qualitycheck_edit_examdetail" value="">
                    <textarea id="qualitycheck_edit_auditreason" name="auditreason" data-toggle="autoheight" rows="3" placeholder="请在此处填写审核意见" ></textarea>
                </form>
            </fieldset>
            </div>
            <br><br><br>
            @endif
            <div class="text-center btns-user">
            @if($orderstatus == 2 && $tempType != 'view')
                <button type="button" class="btn btn-green btn-lg" onclick="savequalitycheck(2)">保存单据</button>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <button type="button" class="btn btn-green btn-lg" onclick="savequalitycheck(3)">提交单据</button>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            @endif
            @if($orderstatus == 3 && $tempType != 'view')
                <button type="button" class="btn btn-green btn-lg" onclick="examqualitycheck(6)">让步提交</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-green btn-lg" onclick="examqualitycheck(4)">审核通过</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-red btn-lg" onclick="examqualitycheck(5)">审核不通过</button>
                &nbsp; &nbsp;
            @endif
            @if($orderstatus == 6 && $tempType != 'view')
                <button type="button" class="btn btn-green btn-lg" onclick="examqualitycheck(7)">审核通过</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-red btn-lg" onclick="examqualitycheck(5)">审核不通过</button>
                &nbsp; &nbsp;
            @endif
                <button type="button" class="btn btn-lg" onclick="showRecords()">查看操作记录</button>
            </div>
            <br><br><br>


            <div class="remarks hideshow" style="display: none;">
                <fieldset>
                    <legend>操作记录明细</legend>
                    <div class="addTable">

                    </div>
                </fieldset>
            </div>
    </div>
</div>
@if($tempType !='view' && $orderstatus != 3 && $orderstatus != 6)
<div id="qualitycheckedit_btn">
    <button type="button"  class="btn btn-blue" data-icon="plus" onclick="editORadd('add')">添加</button>
    <button type="button"  class="btn btn-green" data-icon="edit" onclick="editORadd('edit')">编辑</button>
    <button type="button"  class="btn btn-red" data-icon="times" onclick="delqualitycheck()">删除</button>
</div>
@endif
@if($orderstatus == 3 && $tempType !='view')
<div id="qualityedit_audit_btn">
    <button type="button"  class="btn btn-green" data-icon="edit" onclick="auditEditORadd()">编辑</button>
</div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
   //----------------------操作记录
    addLog($.CurrentNavtab.find('.addTable'), {{$id or 0}}, 22);
    //----------------------操作记录 end

    function qualitychecklist_operation(val,data){
       return '<button type="button" class="btn-green" onclick="see_qualitycheck('+val+')">查看图片</button>';
    }

    function see_qualitycheck(val){
//       console.log(val);return;
       BJUI.dialog({
           id:'attach-stockout-show',
           url:'/qualitycheck/detailList/id/'+val,
           title:'查看图片',
           width:820,
           height:660,
           mask:true,
       });
    }

    function auditEditORadd()
    {
        var receiptdata = $.CurrentNavtab.find('#qualitycheck-detail-table').data('selectedDatas');
        if (receiptdata == undefined || receiptdata.length == 0 || receiptdata=='') {
            BJUI.alertmsg('warn', "请先选择明细", {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var data = $.CurrentNavtab.find('#qualitycheck-detail-table').data('allData');
        for (var i = 0; i < data.length; i++) {
            if(data[i].sysno > receiptdata[0].sysno){
                BJUI.alertmsg('warn', "只能编辑最后一条明细", {displayPosition: 'middlecenter', displayMode: 'fade'});
                return;
            }
        }

        if(receiptdata[0].ischecked != 2){
            BJUI.alertmsg('warn', "品检不合格的才可以编辑", {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        BJUI.dialog({
            id: 'qualitycheck-detailedit',
            type: 'POST',
            url: '/qualitycheck/qualitycheckdetailedit/handlestatus/audit',
            title: '品质检查明细',
            data:{data:receiptdata[0]},
            width: 780,
            height: 450,
            mask: true
        });
    }

    function editORadd(handlestatus)
    {
        var data = '';
        var goodsdata = {};
        // var qualitycheckno = $('#qualitycheckno').val();
        if(handlestatus=='edit'){
            var receiptdata = $.CurrentNavtab.find('#qualitycheck-detail-table').data('selectedDatas');

            if (receiptdata == undefined || receiptdata.length == 0 || receiptdata=='') {
                BJUI.alertmsg('warn', "请先选择明细", {displayPosition: 'middlecenter', displayMode: 'fade'});
                return;
            }
            data = receiptdata[0];

        }else{
            /*var bookingData = $.CurrentNavtab.find('#qualitycheck-booking-detail-table').data('allData');
            goodsdata.goodsname = bookingData[0]['goodsname'];
            goodsdata.goods_sysno = bookingData[0]['goods_sysno'];*/
            goodsdata.goodsname = $("#qualitycheck_goodsname").val();
            goodsdata.goods_sysno = $("#qualitycheck_goods_sysno").val();
        }


//         for (var i = bookingData.length - 1; i >= 0; i--) {
//             if($.inArray(bookingData[i]['goods_sysno'],goodsList)==-1)
//             {
//                 goodsdata['goods_sysno'] = bookingData[i]['goods_sysno'];
//                 goodsdata['goodsname'] = bookingData[i]['goodsname'];
// //                goodsdata['qualitycheckno'] = qualitycheckno;
//                 goodsdata['handlestatus'] = handlestatus;
//                 goodsList.push(goodsdata);
//             }
//         }

        BJUI.dialog({
            id: 'qualitycheck-detailedit',
            type: 'POST',
            url: '/qualitycheck/qualitycheckdetailedit/handlestatus/'+handlestatus,
            title: '品质检查明细',
            // data:{data:data,goodsList:JSON.stringify(goodsList)},
            data:{data:data,goodsdata:JSON.stringify(goodsdata)},
            width: 780,
            height: 450,
            mask: true
        });
    }
    function delqualitycheck()
    {
        var selectdata = $.CurrentNavtab.find('#qualitycheck-detail-table').data('selectedDatas');
        if (selectdata == undefined || selectdata=='') {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $.CurrentNavtab.find("#qualitycheck-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#qualitycheck-detail-table').datagrid('reload', {data: allData});
        }
    }


    function savequalitycheck(step)
    {

   		var allData = $.CurrentNavtab.find("#qualitycheck-detail-table").data('allData');
   		if (allData == undefined || allData=='') {
            BJUI.alertmsg('warn','请填写明细才可以保存提交!',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var detailData = JSON.stringify(allData);
        $.CurrentNavtab.find('#qualitycheck_detail').val(detailData);
        if($('#qualitycheck_apply_user_sysno option:selected').val()!=''){
            $.CurrentNavtab.find('#qualitycheck_apply_employeename').val($('#qualitycheck_apply_user_sysno option:selected').text());
        }
        $.CurrentNavtab.find(':input[name=orderstatus]').val(step);
        BJUI.ajax('ajaxform', {
		    url: '{{$action}}',
		    form: $.CurrentNavtab.find('#qualitycheck-editfrom'),
		    type: 'POST',
		    validate: true,
		    loadingmask: true,
		    okCallback: function(json, options) {
		        BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('refresh', 'navab578');
		    }
		});
    }

    function examqualitycheck(step)
    {
        var allData = $.CurrentNavtab.find("#qualitycheck-detail-table").data('allData');
        if(step == 6 && allData[allData.length-1].isskip != 1){
            BJUI.alertmsg('warn','没有让步，不能点击让步提交!',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(step == 5){
            if($("#qualitycheck_edit_auditreason").val() == ''){
                BJUI.alertmsg('warn','请填写审核意见',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
        }
        if(step == 4 && allData[allData.length-1].isskip == 1){
            BJUI.alertmsg('warn','已选择让步，请点击让步提交!',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        $("#qualitycheck_edit_examstep").val(step);
        $("#qualitycheck_edit_examdetail").val(JSON.stringify(allData[allData.length-1]));

        BJUI.ajax('ajaxform', {
            url: '/qualitycheck/examJson',
            form: $.CurrentNavtab.find('#qualitycheck-audit-form'),
            validate: false,
            loadingmask: true,
            okCallback: function(json, options) {
                if(json.code == 200){
                    BJUI.navtab('closeCurrentTab', '');
                    BJUI.navtab('refresh', 'navab578');
                }else{
                    BJUI.alertmsg('warn','操作失败',{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }

            }
        })
    }
</script>
