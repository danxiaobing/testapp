<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockberthoutlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="4" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">

                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间">
                </div>

                <label class="row-label">靠泊卸货单号</label>
                <div class="row-input">
                    <input id="bar_no" type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="靠泊卸货单号">
                </div>

                <br>
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
                    <select name="bar_stockoutstatus" id="bar_stockoutstatus" data-toggle="selectpicker" data-width="100%">
                        <option value="-100" selected="">不限</option>
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">已审核</option>
                        <option value="5">作废</option>
                        <option value="6">退回</option>
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
    <table class="table table-bordered" id="stockberthoutlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarItem: 'edit',
            toolbarCustom:$.CurrentNavtab.find('#stockberthoutlist_toolbar'),
            addLocation: 'last',
            dataUrl: 'stockberthout/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: {navtab:{title:'靠泊卸货单信息',id:'navab999'}},
            editUrl: '/stockberthout/edit/mode/edit/id/{sysno}',
            delUrl:'/stockberthout/deljson',
            delPK:'sysno',
            paging: {pageSize:12},
            showCheckboxcol: true,
            linenumberAll: true,
            showLinenumber:true,
            filterThead:false,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockoutno',align:'center',width:150}">靠泊卸货单号</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'inshipname',align:'center'}">船名</th>
            <th data-options="{name:'tobeqty',align:'center'}">通知数量（吨）</th>
            <th data-options="{name:'beqty',align:'center',render:function(value){if(!value) return 0; }}">实际流量（吨）</th>
            <th data-options="{name:'cs_employeename',align:'center',render:function(value){if(value=='请选择'){return '--'}else{return value}}}">客服</th>
            <th data-options="{name:'stockoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'}else if(value=='5') {return '作废'}else if(value=='6') {return '退回'}else  {return '新建'}}}">
                单据状态</th>
        </tr>
        </thead>
    </table>
</div>

<div id="stockberthoutlist_toolbar">
    <button type="button" class="btn btn-green" data-icon="eye" onclick="seestockberthout()">查看</button>
    <button type="button" class="btn btn-green"data-icon="filter" onclick="addattachment()">附件</button>
    <button type="button" class="btn btn-red" data-icon="reply" onclick="backstockberthout()">退回</button>
    <button type="button" class="btn btn-green" data-icon="gavel" onclick="auditstockberthout()">审核</button>
    <button type="button" class="btn btn-red" data-icon="fa-scissors" onclick="blankstockberthout()">作废</button>
    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="signoutstockberthout()">EXCEL导出</button>
</div>

<script>
    //导出word
    function stockberthout_downloadSeal_list() {
        var data = $.CurrentNavtab.find("#stockberthoutlist-table").data('selectedDatas');
        if(data == ''||data == null) {
            BJUI.alertmsg('warn', '请先选中单据再打印');
            return false;
        }
        if(data.length > 1){
            BJUI.alertmsg('warn', '只能选择一条单据打印');
            return false;
        }
        BJUI.ajax('ajaxdownload', {
            url:'/stockberthout/export/',
            type:'POST',
            data:{id:data[0]['sysno']},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });

    }

    function seestockberthout(){
        var checkdata = $('#stockberthoutlist-table').data('selectedDatas');
        if(checkdata==''||checkdata==null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据查看');
        }else{
            BJUI.navtab({
                id: 'navab999',
                url: '/stockberthout/edit/mode/eye/id/' + checkdata[0].sysno,
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看靠泊卸货订单'
            });
        }
    }

    function addattachment(){
        var data = $.CurrentNavtab.find("#stockberthoutlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockoutstatus']!=4){
            BJUI.alertmsg('warn', '只有已审核的靠泊卸货订单才能添加附件');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据添加附件');
        }else{
            BJUI.navtab({
                id:'navab999',
                url:'/stockberthout/edit/mode/addattach/id/'+data[0]['sysno'],
                title:'添加附件',
            })
            return;
        }
    }

    function backstockberthout(){
        var data = $.CurrentNavtab.find("#stockberthoutlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockoutstatus']!=2){
            BJUI.alertmsg('warn', '只有暂存的靠泊卸货单才能完成退回操作');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据进行退回');
        }else{
            BJUI.navtab({
                id:'navab999',
                url:'/stockberthout/edit/mode/back/id/'+data[0]['sysno'],
                title:'退回靠泊卸货订单',
            })
            return;
        }
    }

    function auditstockberthout(){
        var data = $.CurrentNavtab.find("#stockberthoutlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockoutstatus']!=3){
            BJUI.alertmsg('warn', '只有待审核的入库单才能完成审核操作');
        }else if(data[0]['beqty']==0){
            BJUI.alertmsg('warn', '实际流量为0的订单不能完成审核操作');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据审核');
        }else{
            BJUI.navtab({
                id:'navab999',
                url:'/stockberthout/edit/mode/audit/id/'+data[0]['sysno'],
                title:'审核靠泊卸货订单',
            })
            return;
        }
    }

    function blankstockberthout(){
        var data = $.CurrentNavtab.find("#stockberthoutlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockoutstatus']!=4){
            BJUI.alertmsg('warn', '只有已审核的靠泊卸货单才能完成作废操作');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据进行作废');
        }else{
            BJUI.navtab({
                id:'navab999',
                url:'/stockberthout/edit/mode/blank/id/'+data[0]['sysno'],
                title:'作废靠泊卸货订单',
            })
            return;
        }
    }

    function signoutstockberthout() {
        var bar_no = $("#bar_no").val();
        var bar_name = $("#bar_name").val();
        var bar_stockoutstatus = $("#bar_stockoutstatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/stockberthout/excel/',
            type:'POST',
            data:{bar_no: bar_no,bar_name:bar_name,bar_stockoutstatus:bar_stockoutstatus},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }
</script>