<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#StockcarinWaitlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">

                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间">
                </div>

                <label class="row-label">预约单编号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="入库单号">
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
    <table class="table table-bordered" id="StockcarinWaitlist-table" data-toggle="datagrid" data-options="{
            height: '100%',
            showToolbar: true,
            toolbarItem: '',
            toolbarCustom:$.CurrentNavtab.find('#custom_cart_tb'),
            dataUrl: '/booking/carInListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: false,
            paging: {pageSize:20},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            fullGrid:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookinginno',align:'center'}">预约单号</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
            <th data-options="{name:'cs_employeename',align:'center'}">客服专员</th>
            <th data-options="{name:'goodsname',align:'center'}">货名</th>
            <th data-options="{name:'qualityname',align:'center'}">规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'bookinginqty',align:'center'}">数量</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){if(value=='') {return '吨'} else {return value}} }">
                计量单位
            </th>
            <th data-options="{name:'bookinginstatus',align:'center',render:function(value){if(value==5) {return '已审核'}}}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="custom_cart_tb">
    <button type="button" class="btn btn-green" data-icon="registered" onclick="addstockcar()">登记车辆信息</button>
    <button type="button" class="btn btn-green" data-icon="plus" onclick="addstockcarin()">生成入库订单</button>
    <button type="button" class="btn btn-red" data-icon="reply" onclick="backstockcarin()">退回</button>
</div>

<script type="text/javascript">
    function addstockcar() {
        var checkdata = $('#StockcarinWaitlist-table').data('selectedDatas');
        if(checkdata==''||checkdata==null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据登记车辆');
        }else if (checkdata) {
            BJUI.navtab({
                id: 'navab218',
                url: '/bookcarin/edit/mode/addcar/id/'+checkdata[0].sysno,
                title: '登记车辆'
            });
        }
    }

    function addstockcarin() {
        var checkdata = $('#StockcarinWaitlist-table').data('selectedDatas');
        if(checkdata==''||checkdata==null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据生成入库订单');
        }else if (checkdata) {
            BJUI.navtab({
                id: 'navab263',
                url: '/stockcarin/edit/',
                type: 'post',
                data: {'booking_sysno': checkdata[0].sysno},
                title: '生成入库订单'
            });
        }
    }

    function backstockcarin() {
        var checkdata = $('#StockcarinWaitlist-table').data('selectedDatas');
        if(checkdata==''||checkdata==null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据退回');
        }else if (checkdata) {
            BJUI.navtab({
                id: 'navab218',
                url: '/bookcarin/edit/mode/back/id/'+checkdata[0].sysno,
                type: 'post',
                data: {'booking_sysno': checkdata[0].sysno},
                title: '退回'
            });
        }
    }
</script>