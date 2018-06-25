<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportstocklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">入库日期:</label>
                <div class="row-input datawidth">
                    <input type="text" name="start_time" value="" placeholder="开始时间"  data-toggle="datepicker" >
                </div>
                <div class="row-input datawidth">
                	<input type="text" name="end_time" value="{{date('Y-m-d',time())}}" placeholder="结束时间"  data-toggle="datepicker">
                </div>
                <label class="row-label">客户:</label>
                <div class="row-input">
                    <select name="customer_sysno" data-toggle="selectpicker" data-live-search="true"  data-size='10' data-width="100%">
                        <option value="0">不限</option>
                        @foreach($customerlist as $key=>$value)
                        	<option value="{{$value['sysno']}}">{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">显示已清库:</label>
                <div class="row-input">
                    <select name="clearstockstatus" data-toggle="selectpicker" data-width="100%" data-live-search="true" >
                        <option value="0">不限</option>
                        <option value="1">未清库</option>
                        <option value="2">已清库</option>
                    </select>
                </div>
                <br/>
                <label class="row-label">入库单号:</label>
                <div class="row-input datawidth">
                    <input type="text" name="stockinno" value="" placeholder="入库单号" >
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
    <table class="table table-bordered" id="reportstocklist-table" data-toggle="datagrid" data-options="{
    	fullGrid:true,
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#reportstock_btn_div',
        addLocation: 'last',
        dataUrl: '/Reportstock/stockListJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:8},
        showCheckboxcol: true,
        filterThead:false,
        linenumberAll: true,
        fieldSortable:false,
        showLinenumber:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'firstfrom_no',align:'center'}">入库单号</th>
            <th data-options="{name:'instockdate',align:'center'}"> 入库日期</th>
            <th data-options="{name:'doctype',align:'center',render:function(value){if(value=='1') {return '船入库'} else if(value=='2') {return '车入库'} else if(value=='3') {return '货权转移'}}}">入库单类型</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'customername',align:'center'}">客户名称</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'instockqty',align:'center'}">数量</th>
            <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
            <th data-options="{name:'stockqty',align:'center'}">余额</th>
            <th data-options="{name:'beyondqty',align:'center'}">超发量</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货物性质</th>
            <th data-options="{name:'changetype',align:'center',render:function(value){if(value=='1') {return '船入库'} else if(value=='2') {return '车入库'} else if(value=='3') {return '船出库'} else if(value=='4') {return '车出库'}else if(value=='5') {return '货权转移'}else if(value=='6') {return '清库'}else {return ' '}}}">最近变动</th>
        </tr>
        </thead>
    </table>
</div>
<div id="reportstock_btn_div">
    <button type="button" class="btn btn-green" data-icon="eye" id="reportstock_btn">查看变动详情</button>
</div>
<script type="text/javascript">
    $('#reportstock_btn').click(function(){
        var chks = $.CurrentNavtab.find("#reportstocklist-table");
        if(chks.length < 1)
        {
            BJUI.alertmsg('warn','未选中任何行');
            return false;
        }
		var checkdata=$('#reportstocklist-table').data('selectedDatas');

//        console.log(checkdata[0].firstfrom_sysno);return false;
		if(checkdata && checkdata != ''){
            if(checkdata.length >1){
                BJUI.alertmsg('warn','只能选择一条数据！');
                return false;
            }
			BJUI.dialog({
			    id:'stock_detail_01',
			    url:'/Reportstock/detail/sysno/'+checkdata[0].sysno+"/firstfrom_sysno/"+checkdata[0].firstfrom_sysno,
                width:1200,
                height:700,
			    title:'库存变动详情',
                mask:true
			});
		}else{
			BJUI.alertmsg('warn','未选择数据！');
            return false;
		}
		
	});
</script>