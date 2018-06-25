<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookshipinsurelist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">入库预约单号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="入库预约单号"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称"></div>
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

    <table class="table table-bordered" id="bookshipinsurelist-table" height="40+50*{{count($list)}}"
           data-toggle="datagrid" data-options="{
           tableWidth:'1800',
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom: '#sure_bookshipin',
        addLocation: 'last',
        dataUrl: 'bookshipin/sureJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {navtab:{title:'船入库预约单信息',id:'navab212'}},
        editUrl: '',
        delUrl:'',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookinginno',align:'center',width:100}">入库预约单号</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){if(value==1){return '手工创建'}else if(value == 2){return '国烨云仓' } }}">
                单据来源
            </th>
            <th data-options="{name:'customer_name',align:'center',width:260}">客户</th>
            <th data-options="{name:'businesscheckunitname',align:'center',render:function(value){if(!value){return '--';}}}">商检单位</th>
            <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){return '吨'}}">计量单位</th>
            <th data-options="{name:'bookinginqty',align:'center'}">数量</th>
            <th data-options="{name:'shipname',align:'center',width:260}">船名</th>
            <th data-options="{name:'storagetankname',align:'center',width:260}">罐号</th>
            <th data-options="{name:'cs_employeename',align:'center',render:function(value){if(!value){return '--';}}}">客服</th>
            <th data-options="{name:'bookinginstatus',align:'center',
                render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'}
                else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6')
                {return '已完成'} else if(value=='7'){return '退回'} else {return '新建'}}}">
                单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>

<div id="sure_bookshipin">
    <button type="button" id="sure_bookshipin_btn" class="btn btn-green" data-icon="gavel">确认</button>
</div>

<script>
    $("#sure_bookshipin_btn").click(function () {
        var chks = $.CurrentNavtab.find("#bookshipinsurelist-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookshipinsurelist-table').data('selectedDatas');
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '未选中任何行！');
            return false;
        }
        BJUI.navtab({
            id: 'sureshipin981',
            mask: true,
            url: '/bookshipin/edit/type/sure/booking_sysno/' + checkdata[0].sysno,
            title: '确认船入库预约单'
        });
    });
</script>