<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookberthoutlist-table')}">
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
    <table class="table table-bordered" id="bookberthoutlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#custom_berthout_tb',
        dataUrl: '/stockberthout/waitberthOutListJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: false,
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookingoutno',align:'center'}">预约单号</th>
            <th data-options="{name:'customer_name',align:'center'}">客户</th>
            <th data-options="{name:'contractno',align:'center'}">合同编号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'bookingoutqty',align:'center'}">数量</th>
            <th data-options="{name:'cs_employeename',align:'center'}">客服</th>
            <th data-options="{name:'bookingoutstatus',align:'center',width:100,render:function(value)
                    {if(value=='1') {return '新建'} else if(value=='2') {return '暂存'}
                    else if(value=='3') {return '待确认'} else if(value=='4') {return '待审核'}
                    else if(value=='5') {return '已审核'} else if(value=='6') {return '退回'}else if(value=='7') {return '作废'}}}">
                单据状态
            </th>

        </tr>
        </thead>
    </table>
</div>
<input type="hidden" name="list" value="{{$list}}">
<div id="custom_berthout_tb">
    <button type="button" class="btn btn-red" data-icon="reply" id="generate_berthbackid_btn">靠泊卸货退回</button>
    <button type="button" class="btn btn-blue" data-icon="plus" id="generateberth_btn">生成靠泊卸货单</button>
</div>

<script type="text/javascript">
    //生成管入库单
    $('#generateberth_btn').click(function () {
        var chks = $.CurrentNavtab.find("#bookberthoutlist-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        var checkdata = $('#bookberthoutlist-table').data('selectedDatas');

        console.log(checkdata);
        if (!checkdata || checkdata[0].bookingoutstatus !=5 ) {
            BJUI.alertmsg('warn', '请勾选有效数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'navab256',
                url: '/stockberthout/edit/mode/waitedit',
                type: 'post',
                mask: true,
                data: {'bookout_sysno': checkdata[0].sysno},
                title: '生成管入库单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '只能编辑单条数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else if (checkdata.length < 1) {
            BJUI.alertmsg('warn', '请勾选有效数据！', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });


    //管入库退回
    $('#generate_berthbackid_btn').click(function () {
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


</script>