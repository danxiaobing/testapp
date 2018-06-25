<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookpipelineinreviewlist-table')}">
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

    <table class="table table-bordered" id="bookpipelineinreviewlist-table" height="40+50*{{count($list)}}"
           data-toggle="datagrid" data-options="{
           tableWidth:'1600',
        height: '100%',
        showToolbar: true,
        toolbarCustom: '#review_bookspipelinein',
        addLocation: 'last',
        dataUrl: '/bookpipelinein/reviewJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {navtab:{title:'船入库预约单信息',id:'navab212'}},
        editUrl: '',
        delUrl:'',
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookinginno',align:'center',width:200}">入库预约单号</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){if(value==1){return '手工创建'}else if(value == 2){return '国烨云仓' } }}">
                单据来源
            </th>
            <th data-options="{name:'customer_name',align:'center',width:200}">客户</th>
            <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){return '吨'}}">计量单位</th>
            <th data-options="{name:'bookinginqty',align:'center'}">数量</th>
            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
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

<div id="review_bookspipelinein">
    <button type="button" id="review_bookpipelinein_btn" class="btn btn-green" data-icon="gavel">审核</button>
</div>

<script>
    $("#review_bookpipelinein_btn").click(function () {
        //var chks = $.CurrentNavtab.find("input:checked");
        var chks  = $("#bookpipelineinreviewlist-table").data('selectedDatas');
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookpipelineinreviewlist-table').data('selectedDatas');
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据');
            return false;
        }
        BJUI.navtab({
            id: 'reviewshipin980',
            mask: true,
            url: '/bookpipelinein/edit/type/review/booking_sysno/' + checkdata[0].sysno,
            title: '审核管入库预约单'
        });
    });
</script>