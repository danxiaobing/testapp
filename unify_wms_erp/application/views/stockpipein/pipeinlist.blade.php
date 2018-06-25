<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookingpipelist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>

                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

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
    <table class="table table-bordered" id="bookingpipelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#custom_pipe_tb',
        dataUrl: '{{$dataurl}}',
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
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'qualityname',align:'center'}">规格</th>
            <th data-options="{name:'bookinginqty',align:'center'}">数量</th>
            <th data-options="{name:'cs_employeename',align:'center'}">客服</th>
            <th data-options="{name:'bookinginstatus',align:'center',width:100,render:function(value)
                    {if(value=='1') {return '新建'} else if(value=='2') {return '暂存'}
                    else if(value=='3') {return '评审中'} else if(value=='4') {return '待审核'}
                    else if(value=='5') {return '已审核'} else if(value=='6') {return '退回'}else if(value=='7') {return '作废'}}}">
                单据状态
            </th>

        </tr>
        </thead>
    </table>
</div>
<input type="hidden" name="list" value="{{$list}}">
<div id="custom_pipe_tb">
    <button type="button" class="btn btn-red" data-icon="reply" id="generate_pipebackid_btn">管入库退回</button>
    <!-- <button type="button" class="btn btn-green" data-icon="edit" id="login_book_shipin">登记放行信息</button> -->
    <button type="button" class="btn btn-blue" data-icon="plus" id="generatepipe_btn">生成管入库单</button>
</div>

<script type="text/javascript">
    //生成管入库单
    $('#generatepipe_btn').click(function () {
        var chks = $.CurrentNavtab.find("#bookingpipelist-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookingpipelist-table').data('selectedDatas');
        if (!checkdata) {
            BJUI.alertmsg('warn', '请勾选有效数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'navab256',
                url: '/stockpipein/edit/',
                type: 'post',
                mask: true,
                data: {'booking_sysno': checkdata[0].sysno},
                title: '生成管入库单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '只能编辑单条数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length < 1) {
            BJUI.alertmsg('warn', '请勾选有效数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });


//管入库退回
    $('#generate_pipebackid_btn').click(function () {
        var chks = $.CurrentNavtab.find("#bookingpipelist-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookingpipelist-table').data('selectedDatas');
        if (!checkdata) {
            BJUI.alertmsg('warn', '请勾选有效数据');
        } else if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'backtab335',
                url: '/bookpipelinein/edit/type/back/booking_sysno/' + checkdata[0].sysno,
                title: '管入库退回',
                mask: true,
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '只能编辑单条数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length < 1) {
            BJUI.alertmsg('warn', '请勾选有效数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });



    $('#login_book_shipin').click(function () {
        var chks = $.CurrentNavtab.find("#booking{{$navid}}list-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#booking{{$navid}}list-table').data('selectedDatas');
        if (!checkdata) {
            BJUI.alertmsg('warn', '请勾选有效数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'navab605',
                url: '/bookshipin/edit/type/register/booking_sysno/' + checkdata[0].sysno,
                mask: true,
                title: '登记放行信息'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '只能编辑单条数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length < 1) {
            BJUI.alertmsg('warn', '请勾选有效数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });
</script>