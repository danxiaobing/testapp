<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#booking{{$navid}}list-table')}">
        <fieldset>
            <input type="hidden" id='out_bar_type' name="bar_type" value="{{$bar_type}}" placeholder="出库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">预约单编号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="出库单号">
                </div>
                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称">
                </div>

                <label class="row-label">提货单号</label>
                <div class="row-input">
                    <input type="text" name="bar_receivenumber" value="{{$bar_receivenumber or ''}}" placeholder="提货单号">
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="bar_goodsname" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['goodsname']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
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
        tableWidth:'100%',
        height: '100%',   
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#custom_ship_tb{{$navid}}',
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
                <th  data-options="{name:'shipname',align:'center'}">船名</th>
                <th data-options="{name:'bookingoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6') {return '已完成'} else if(value=='7'){return '退回'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<input type="hidden" name="list" value="{{$list}}">
<div id="custom_ship_tb{{$navid}}">
    <button type="button" class="btn btn-green" data-icon="plus" id="generate{{$navid}}_btn">{{$navtitle}}</button>
    <button type="button" class="btn btn-red" data-icon="reply" id="bookoutno_return{{$navid}}">退回</button>
</div>
<script type="text/javascript">
$('#generate{{$navid}}_btn').click(function(){
	var checkdata=$('#booking{{$navid}}list-table').data('selectedDatas');
	if(checkdata){
		BJUI.navtab({
		    id:'navtab0123',
		    url:'{{$inaction}}',
		    type:'post',
		    data:{'booking_sysno':checkdata[0].sysno},
		    title:'{{$navtitle}}'
		});
	}else{
		BJUI.alertmsg('warn','请选择一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
	}
});
$("#bookoutno_return{{$navid}}").click(function(){
    var data = $('#booking{{$navid}}list-table').data('selectedDatas');
    if(data){
        BJUI.navtab({
            id:'navab224',
            url: '/bookout/shipedit/type/sendback/id/'+data[0].sysno,
            title: "船预约单退回",
        });
    }else{
        BJUI.alertmsg('warn','请选择一行数据!',{displayPosition:'middlecenter',displayMode:'fade'});
    }
});
</script>