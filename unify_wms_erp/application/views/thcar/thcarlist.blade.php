<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#thcarlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="{{$begin_time or ''}}" data-toggle="datepicker" placeholder="预约开始时间">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="{{$end_time or ''}}" data-toggle="datepicker" placeholder="预约结束时间">
                </div>

                <label class="row-label">客户名称:</label>
                <div class="row-input">
                    <input type="hidden" id="outcarcustomername" name="customername" value="">
                    <select id="outcarcusid" name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">车牌号:</label>
                <div class="row-input">
                    <input type="text" name="carid" value="{{$carid or ''}}" placeholder="车牌号">
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
    <table class="table table-bordered" id="thcarlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'99%',
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#custom_thcarlist_tb',
        addLocation: 'last',
        dataUrl: '/thcar/getListJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'stockoutno',align:'center',width:200}">出库订单号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'qualityname',align:'center'}">货品规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货品性质</th>
        </tr>
        </thead>
    </table>
</div>
<div id="custom_thcarlist_tb">
    <button type="button" class="btn btn-green"  id="custom_thcar_view_btn" data-icon="plus">生成退货磅码单</button>
</div>

<script type="text/javascript">
    $("#outcarcusid").change(function (){
        $("#outcarcustomername").val($("#outcarcusid option:selected").text())
    });

    $("#custom_thcar_view_btn").click(function() {
        var data  = $("#thcarlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '未选中任何行');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一辆车核单');
        }else{
            BJUI.navtab({
                id : 'navab585',
                url: '/thcar/Edit',
                type:'POST',
                data:{data:data[0]},
                title: '退货单核单',
                width: 1300,
                height: 800
            });
        }
    });
</script>
