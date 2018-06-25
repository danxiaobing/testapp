<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#booking{{$navid}}list-table')}">
        <fieldset>
            <input type="hidden" id='carout_bar_type' name="bar_type" value="{{$bar_type}}" placeholder="出库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">预约单编号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="出库单号">
                </div>
                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称">
                </div>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="booking{{$navid}}list-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#custom_carout_tb',
        dataUrl: '{{$dataurl}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: false,
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'bookingoutno',align:'center'}">预约单号</th>
                <th data-options="{name:'customer_name',align:'center'}">客户</th>
                <th  data-options="{name:'receivenumber',align:'center'}">提货单号</th>
                <th  data-options="{name:'receiveunitname',align:'center'}">提货公司</th>
                <th  data-options="{name:'receivebetween',align:'center'}">提货区间</th>
                <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';  }}}">
                    货物性质
                </th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'bookingoutqty',align:'center'}">通知提货数量</th>
                <th data-options="{name:'bookingoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'} else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6') {return '已完成'} else if(value=='7'){return '退回'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<input type="hidden" name="list" value="{{$list}}">
<div id="custom_carout_tb">
    <button type="button" class="btn btn-green" data-icon="plus" id="generate{{$navid}}_btn">{{$navtitle}}</button>
    <button type="button" class="btn btn-red" data-icon="reply" id="carout_return">退回</button>
</div>
<script type="text/javascript">
$('#generate{{$navid}}_btn').click(function(){
	var checkdata=$('#booking{{$navid}}list-table').data('selectedDatas');
	if(checkdata){
		BJUI.navtab({
		    id:'generate{{$navid}}_btn'+checkdata[0].sysno,
		    url:'{{$inaction}}',
		    type:'post',
		    data:{'booking_sysno':checkdata[0].sysno},
		    title:'{{$navtitle}}'
		});
	}else{
		BJUI.alertmsg('warn','<h4>请选数据！</h4>');
	}
});
$("#carout_return").click(function(){
    var checkdata = $('#booking{{$navid}}list-table').data('selectedDatas');
    var title = $('#carout_bar_type').val() == 1 ? '船预约单退回' : '车预约单退回';
    var url = $('#carout_bar_type').val() == 1 ? '/bookout/shipedit/id/'+checkdata[0].sysno +'/val/1' : '/bookout/caredit/id/'+checkdata[0].sysno +'/val/1' ;
    if(checkdata){
        BJUI.navtab({
            id:'bookingoutno_return'+checkdata[0].sysno,
            url: url,
            type:'post',
            data:{'booking_sysno':checkdata[0].sysno},
            title: title,
        });
    }else{
        BJUI.alertmsg('warn','请选择需要退回的数据!');
    }
});
</script>