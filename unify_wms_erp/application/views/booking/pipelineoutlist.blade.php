<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#pipelining-list-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="{{$bar_type}}" placeholder="出库单类型">
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
    <table class="table table-bordered" id="pipelining-list-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',   
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#pipelining_list_tb',
        dataUrl: '{{$dataurl}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: false,
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        hScrollbar:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'bookingoutno',align:'center',width:200}">预约单号</th>
                <th data-options="{name:'customername',align:'center',width:280}">客户</th>
                <th  data-options="{name:'receivenumber',align:'center'}">提货单号</th>
                <th  data-options="{name:'goodsname',align:'center'}">品名</th>
                <th  data-options="{name:'qualityname',align:'center'}">规格</th>
                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货物性质</th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'bookingoutqty',align:'center'}">提货数量</th>
                <th  data-options="{name:'cs_employeename',align:'center'}">客服</th>
                <th data-options="{name:'bookingoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6') {return '已完成'} else if(value=='7'){return '退回'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<input type="hidden" name="list" value="{{$list}}">
<div id="pipelining_list_tb">
    <button type="button" class="btn btn-green" data-icon="plus" id="pipelining_create_btn">{{$navtitle}}</button>
    <button type="button" class="btn btn-red" data-icon="reply" id="pipelining_back_btn">退回</button>
</div>
<script type="text/javascript">
$('#pipelining_create_btn').click(function(){
	var data=$('#pipelining-list-table').data('selectedDatas');
	if(data){
		BJUI.navtab({
		    id:'navab1024',
		    url:'/stockout/pipelineEdit/bookout_sysno/'+data[0].sysno,
		    title:'{{$navtitle}}'
		});
	}else{
		BJUI.alertmsg('warn','<h4>请选数据！</h4>');
	}
});
$("#pipelining_back_btn").click(function(){
    var data = $('#pipelining-list-table').data('selectedDatas');
    if(data){
        BJUI.navtab({
            id:'navab505',
            url: '/bookout/pipelineEdit/type/sendback/id/'+data[0].sysno,
            title: "管预约单退回",
        });
    }else{
        BJUI.alertmsg('warn','请选择需要退回的数据!');
    }
});
</script>