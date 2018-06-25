<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookshipinlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">业务期间:</label>

                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="预约开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="预约结束时间"></div>

                <label class="row-label">入库预约单号</label>

                <div class="row-input">
                    <input type="text" name="bar_no" id="bar_no" value="{{$bar_no or ''}}" placeholder="入库预约单号"></div>

                <label class="row-label">数据来源</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="docsource">
                        <option value="">请选择</option>
                        <option value="1">手工创建</option>
                        <option value="2">国烨云仓</option>
                    </select>
                </div>

                <label class="row-label">客户名称</label>

                <div class="row-input required">
                    <select name="customer_sysno" id="stock_customer_sysno" data-nextselect="#stock_contract_sysno"
                            data-refurl="/customer/customercontractJson2/id/{value}" data-size="5"
                            data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">操作状态</label>

                <div class="row-input">
                    <select data-toggle="selectpicker" id="bar_bookinginstatus" data-width="100%"
                            name="bar_bookinginstatus">
                        <option value="-100" selected="">不限</option>
                        <option value="2">暂存</option>
                        <option value="3">待确认</option>
                        <option value="4">待审核</option>
                        <option value="5">已审核</option>
                        <option value="6">已完成</option>
                        <option value="7">退回</option>
                        <option value="8">驳回</option>
                    </select>
                </div>
                <label class="row-label"></label>
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
    <table class="table table-bordered" id="bookshipinlist-table" height="40+50*{{count($list)}}" data-toggle="datagrid"
           data-options="{
        tableWidth:'100%',
        height:'100%',
        showToolbar: true,
        toolbarItem: 'edit,|,del',
        toolbarCustom: '#show_bookshipin_div',
        addLocation: 'last',
        dataUrl: 'bookshipin/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {navtab:{title:'船入库预约单信息',id:'navab212'}},
        editUrl: '/bookshipin/edit/type/edit/booking_sysno/{sysno}',
        delUrl:'/bookshipin/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        hScrollbar:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookinginno',width:200,align:'center'}">入库预约单号</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){if(value==1){return '手工创建'}else if(value == 2){return '国烨云仓' } }}">
                单据来源
            </th>
            <th data-options="{name:'customer_name',width:280,align:'center'}">客户</th>
            <th data-options="{name:'businesscheckunitname',align:'center',render:function(value){if(!value){return '--';}}}">商检单位</th>
            <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){return '吨'}}">计量单位</th>
            <th data-options="{name:'bookinginqty',align:'center'}">数量</th>
            <th data-options="{name:'shipname',width:280,align:'center'}">船名</th>
            <th data-options="{name:'storagetankname',width:280,align:'center'}">罐号</th>
            <th data-options="{name:'cs_employeename',align:'center',render:function(value){if(!value){return '--';}}}">客服</th>
            <th data-options="{name:'bookinginstatus',align:'center',
                render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'}
                else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6')
                {return '已完成'} else if(value=='7'){return '退回'} else if(value=='8') {return '驳回'} else {return '新建'}}}">
                单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>

<div id="show_bookshipin_div">
    <button id="show_bookshipin_btn" class="btn btn-blue" data-icon="eye">查看</button>
    <button id="fujian_bookshipin_btn" class="btn btn-green" data-icon="filter">附件</button>
    <button id="dbexcel_bookshipin_btn" class="btn btn-green" data-icon="sign-out">Excel导出</button>
</div>

<script>
    $("#show_bookshipin_btn").click(function () {
        var chks = $.CurrentNavtab.find("#bookshipinlist-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookshipinlist-table').data('selectedDatas');
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'navab212',
                url: '/bookshipin/show/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'id': checkdata[0].sysno},
                title: '查看船入库预约单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能查看多条记录', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }

    });

    $("#dbexcel_bookshipin_btn").click(function () {
        var bar_no = $("#bar_no").val();
        var bar_name = $("#bar_name").val();
        var bar_bookinginstatus = $("#bar_bookinginstatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url: '/bookshipin/excel/',
            type: 'POST',
            data: {bar_no: bar_no, bar_name: bar_name, bar_bookinginstatus: bar_bookinginstatus},
            successCallback: function (json, options) {
                console.log(Success);
            }
        });
    });

    $("#fujian_bookshipin_btn").click(function () {
        var chks = $.CurrentNavtab.find("#bookshipinlist-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookshipinlist-table').data('selectedDatas');
        //console.log(checkdata[0].sysno);return;
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'navab212',
                mask: true,
                url: '/bookshipin/edit/type/addattach/booking_sysno/' + checkdata[0].sysno,
                title: '船入库预约上传附件'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能上传多条附件', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });
</script>