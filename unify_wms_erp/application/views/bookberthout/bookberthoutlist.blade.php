<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bookberthoutlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="2" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">

                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input id="begin_time" type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="预约开始时间">
                </div>
                <div class="row-input datawidth">
                    <input id="end_time" type="text" name="end_time" value="" data-toggle="datepicker" placeholder="预约结束时间">
                </div>

                <label class="row-label">预约单号</label>
                <div class="row-input">
                    <input id="bar_no" type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="预约单号">
                </div>

                <label class="row-label">单据来源</label>
                <div class="row-input">
                    <select name="docsource" data-toggle="selectpicker" data-width="100%">
                        <option value="">请选择</option>
                        <option value="1">手工创建</option>
                        <option value="2">国烨云仓</option>
                    </select>
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

                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text" name="inshipname" value="">
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select name="bar_bookberthoutstatus" data-toggle="selectpicker" data-width="100%" >
                        <option value="-100">全部</option>
                        <option value="2">暂存</option>
                        <option value="3">待确认</option>
                        <option value="4">待审核</option>
                        <option value="5">已审核</option>
                        <option value="6">已完成</option>
                        <option value="7">退回</option>
                        <option value="8">已驳回</option>
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
    <table class="table table-bordered" id="bookberthoutlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height:'100%',
            showToolbar: true,
            toolbarCustom: '#show_bookberthout_div',
            toolbarItem: 'edit,|,del',
            addLocation: 'last',
            dataUrl: 'bookberthout/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: {navtab:{title:'靠泊卸货预约单信息',id:'navab491'}},
            editUrl: '/bookberthout/edit/mode/edit/id/{sysno}',
            delUrl:'/bookberthout/deljson',
            delPK:'sysno',
            paging: {pageSize:11},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookingoutno',align:'center',width:150}">入库预约单号</th>
            <th data-options="{name:'customer_name',align:'center',width:280}">客户</th>
            <th data-options="{name:'contractno',align:'center'}">合同编号</th>
            <th data-options="{name:'goodsname',align:'center'}">货品</th>
            <th data-options="{name:'bookingoutdate',align:'center'}">预约日期</th>
            <th data-options="{name:'inshipname',align:'center'}">船名</th>
            <th data-options="{name:'bookingoutqty',align:'center'}">预计数量（吨）</th>
            <th data-options="{name:'cs_employeename',align:'center',
                render:function(value){if(value=='请选择'){return '--'}else{ return value}}}">客服</th>
            <th data-options="{name:'bookingoutstatus',align:'center',
                render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'}
                else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6')
                {return '已完成'} else if(value=='7'){return '退回'}else if(value=='8'){return '已驳回'} else {return '新建'}}}">
                单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>

<div id="show_bookberthout_div">
    <button class="btn btn-green" data-icon="eye" onclick="showbookberthout()">查看</button>
    <button type="button" class="btn btn-green" data-icon="gavel" onclick="auditbookberthout()">审核</button>
    <button class="btn btn-green" data-icon="filter" onclick="bookberthoutaddattachment()" >附件</button>
    <button class="btn btn-green" data-icon="sign-out" onclick="bookberthoutsignout()">EXCEL导出</button>
</div>

<script>
    function showbookberthout() {
        var checkdata = $('#bookberthoutlist-table').data('selectedDatas');
        if (checkdata== ''||checkdata == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条预约单查看');
        }else {
            BJUI.navtab({
                id: 'navab491',
                url: '/bookberthout/edit/mode/eye/id/' + checkdata[0].sysno,
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看靠泊卸货预约单'
            });
        }
    }

    function auditbookberthout() {
        var data = $('#bookberthoutlist-table').data('selectedDatas');

        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选中任何行！</h4>');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条预约单审核');
        }else{
            var bookingoutstatus = data[0].bookingoutstatus;
            if(bookingoutstatus!=4){
                BJUI.alertmsg('warn', '只能选择待审核的单据');
                return false;
            }
            BJUI.navtab({
                id: 'navab491',
                url: '/bookberthout/edit/mode/audit/id/' + data[0].sysno,
                type: 'post',
                data: {'id': data[0].sysno},
                title: '审核靠泊装货预约单'
            });
        }
    };

    function bookberthoutaddattachment(){
        var data = $('#bookberthoutlist-table').data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选中任何行！</h4>');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条预约单上传附件');
        }else{
            BJUI.navtab({
                id:'navab491',
                url: '/bookberthout/edit/mode/addattach/id/' + data[0].sysno,
                title: '靠泊装货预约上传附件'
            });
        }
    }

    function bookberthoutsignout(){
        var begin_time = $("#begin_time").val();
        var end_time = $("#end_time").val();
        var bar_no = $("#bar_no").val();
        var bar_name = $("#bar_name").val();
        var bar_bookingoutstatus = $("#bar_bookingoutstatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/bookberthout/excel/',
            type:'POST',
            data:{begin_time: begin_time,end_time:end_time,bar_no:bar_no,bar_name:bar_name,bar_bookingoutstatus:bar_bookingoutstatus},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }
</script>