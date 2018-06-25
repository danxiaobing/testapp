<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookcarinlistsure-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="2" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input id="begin_time" type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="预约开始时间"></div>
                <div class="row-input datawidth">
                    <input id="end_time" type="text" name="end_time" value="" data-toggle="datepicker" placeholder="预约结束时间"></div>

                <label class="row-label">入库预约单号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="入库预约单号">
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
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
    <table class="table table-bordered" id="bookcarinlistsure-table" height="40+50*{{count($list)}}" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom: '#sure_bookcarin_btn',
            addLocation: 'last',
            dataUrl: 'bookcarin/sureJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            delUrl:'/bookcarin/deljson',
            delPK:'sysno',
            paging: {pageSize:10},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookinginno',align:'center',width:150}">入库预约单号</th>
            <th data-options="{name:'customer_name',align:'center',width:280}">客户</th>
            <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">卸货单号</th>
            <th data-options="{name:'cs_employeename',align:'center',render:function(value){if(value=='请选择'){return '--'}else{return value}}}">客服专员</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货物性质</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){return '吨'}}">计量单位</th>
            <th data-options="{name:'bookinginqty',align:'center'}">数量</th>
            <th data-options="{name:'bookinginstatus',align:'center',
                render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'}
                else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6')
                {return '已完成'} else if(value=='7'){return '退回'} else {return '新建'}}}">
                单据状态
            </th>
            <th data-options="{name:'storagetankname',align:'center',hide:'true'}">罐号</th>
        </tr>
        </thead>
    </table>
</div>

<div id="sure_bookcarin_btn">
    <button type="button" class="btn btn-green" onclick="surebookcarin()" data-icon="gavel">确认</button>
</div>

<script>
     function surebookcarin(){
        var data = $('#bookcarinlistsure-table').data('selectedDatas');
         if(data == ''||data == null){
             BJUI.alertmsg('warn', '<h4>未选中任何行！</h4>');
         }else if(data.length>=2){
             BJUI.alertmsg('warn', '只能选择一条预约单确认');
         }else{
             BJUI.navtab({
                 id: 'navab218',
                 url: '/bookcarin/edit/mode/sure/id/'+data[0].sysno,
                 title: '确认车入库预约单'
             });
         }
    };
</script>