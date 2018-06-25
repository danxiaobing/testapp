<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockadjustlist-table')}">
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

                <label class="row-label">公司名称</label>
                <div class="row-input">
                    <select name="customer_sysno" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">库存调整单号</label>
                <div class="row-input">
                    <input id="bar_no" type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="库存调整单号">
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select id="bar_stockcheckstatus" data-toggle="selectpicker" data-width="100%" name="bar_stockcheckstatus">
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
    <table class="table table-bordered" id="stockadjustlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'99%',
            height: '100%',
            showToolbar: true,
            toolbarItem: 'edit,|,del',
            toolbarCustom:$.CurrentNavtab.find('#stockadjustlist_toolbar'),
            addLocation: 'last',
            dataUrl: 'stockadjust/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: {navtab:{title:'库存调整单信息',id:'navab574'}},
            editUrl: '/stockadjust/edit/mode/edit/id/{sysno}',
            delUrl:'/stockadjust/deljson',
            delPK:'sysno',
            paging: {pageSize:12},
            showCheckboxcol: true,
            linenumberAll: true,
            showLinenumber:true,
            filterThead:false
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockcheckno',align:'center',width:150}">调整单号</th>
            <th data-options="{name:'stockcheckdate',align:'center'}">调整时间</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户名称</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'beqty',align:'center'}">调整数量（吨）</th>
            <th data-options="{name:'stockcheckstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='5') {return '作废'} else if(value=='6') {return '退回'}else  {return '新建'}}}">
                单据状态</th>
        </tr>
        </thead>
    </table>
</div>

<div id="stockadjustlist_toolbar">
    <button type="button" class="btn btn-blue" data-icon="eye" onclick="seestockadjust()">查看</button>
    <button type="button" class="btn btn-green" data-icon="gavel" onclick="auditstockadjust()">审核</button>
    <button type="button" class="btn btn-red" data-icon="fa-scissors" onclick="blankstockadjust()">作废</button>
    <button type="button" class="btn btn-green"data-icon="filter" onclick="addattachment()">附件</button>
    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="signoutstockadjust()">EXCEL导出</button>
</div>

<script>
    function seestockadjust(){
        var checkdata = $('#stockadjustlist-table').data('selectedDatas');
        if(checkdata==''||checkdata==null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据查看');
        }else{
            BJUI.navtab({
                id: 'navab574',
                url: '/stockadjust/edit/mode/eye/id/' + checkdata[0].sysno,
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看库存调整订单'
            });
        }
    }

    function addattachment(){
        var data = $.CurrentNavtab.find("#stockadjustlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockcheckstatus']!=4){
            BJUI.alertmsg('warn', '只有已审核的库存调整单才能添加附件');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据添加附件');
        }else{
            BJUI.navtab({
                id:'navab574',
                url:'/stockadjust/edit/mode/addattach/id/'+data[0]['sysno'],
                title:'添加附件',
            })
            return;
        }
    }

    function auditstockadjust(){
        var data = $.CurrentNavtab.find("#stockadjustlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockcheckstatus']!=3){
            BJUI.alertmsg('warn', '只有待审核的库存调整单才能完成审核操作');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据进行审核');
        }else{
            BJUI.navtab({
                id:'navab998',
                url:'/stockadjust/edit/mode/audit/id/'+data[0]['sysno'],
                title:'审核库存调整订单',
            })
            return;
        }
    }

    function blankstockadjust(){
        var data = $.CurrentNavtab.find("#stockadjustlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockcheckstatus']!=4){
            BJUI.alertmsg('warn', '只有已审核的库存调整单才能完成作废操作');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据进行作废');
        }else{
            BJUI.navtab({
                id:'navab998',
                url:'/stockadjust/edit/mode/blank/id/'+data[0]['sysno'],
                title:'库存调整订单',
            })
            return;
        }
    }

    function signoutstockadjust() {
        var bar_no = $("#bar_no").val();
        var bar_name = $("#bar_name").val();
        var bar_stockinstatus = $("#bar_stockinstatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/stockadjust/excel/',
            type:'POST',
            data:{bar_no: bar_no,bar_name:bar_name,bar_stockinstatus:bar_stockinstatus},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }
</script>