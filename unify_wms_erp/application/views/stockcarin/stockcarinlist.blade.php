<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockcarinlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="2" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">

                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结束时间">
                </div>

                <label class="row-label">入库单号</label>
                <div class="row-input">
                    <input id="bar_no" type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="入库单号">
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

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select id="bar_stockinstatus" data-toggle="selectpicker" data-width="100%" name="bar_stockinstatus">
                        <option value="-100" selected="">不限</option>
                        <option value="2">暂存</option>
                        <option value="3">入库中</option>
                        <option value="4">已完成</option>
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
    <table class="table table-bordered" id="stockcarinlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarItem: 'edit,|,del',
            toolbarCustom:$.CurrentNavtab.find('#stockcarinlist_toolbar'),
            addLocation: 'last',
            dataUrl: 'stockcarin/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: {navtab:{title:'车入库单信息',id:'navab263'}},
            editUrl: '/stockcarin/edit/mode/edit/id/{sysno}',
            delUrl:'/stockcarin/deljson',
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
            <th data-options="{name:'stockinno',align:'center',width:150}">入库单号</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'qualityname',align:'center'}">规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                货物性质
            </th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'contractno',align:'center'}">合同编号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">卸货单号</th>
            <th data-options="{name:'tobeqty',align:'center'}">通知数量</th>
            <th data-options="{name:'beqty',align:'center',render:function(value){if(!value) return 0; }}">已入库数量</th>
            <th data-options="{name:'waitbeqty',align:'center',render:function(value,data){if(data.beqty==null){ return data.tobeqty; }else if(data.stockinstatus==4){return 0 ;} else { return (data.tobeqty-data.beqty).toFixed(3) ;}}}">待入库数量</th>
            <th data-options="{name:'stockinstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '入库中'} else if(value=='4') {return '已完成'}else  {return '新建'}}}">
                单据状态
            </th>
        </tr>
        </thead>
    </table>
</div>

<div id="stockcarinlist_toolbar">
    <button type="button" class="btn btn-green" data-icon="eye" onclick="seestockcarin()">查看</button>
    <button type="button" class="btn btn-green"data-icon="filter" onclick="addattachment()">附件</button>
    <button type="button" class="btn btn-green" data-icon="stack-overflow" onclick="finishstockcarin()">完成入库</button>
    <button type="button" class="btn btn-green" data-icon="car"  onclick="stockcarinlist_addcar()">增加车辆</button>
    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="signoutstockcarin()">EXCEL导出</button>
</div>

<script>
    function seestockcarin(){
        var checkdata = $('#stockcarinlist-table').data('selectedDatas');
        if(checkdata==''||checkdata==null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据查看');
        }else{
            BJUI.navtab({
                id: 'navab263',
                url: '/stockcarin/see/mode/eye/id/' + checkdata[0].sysno,
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看车入库订单'
            });
        }
    }

    function addattachment(){
        var data = $.CurrentNavtab.find("#stockcarinlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockinstatus']!=4){
            BJUI.alertmsg('warn', '只有已完成的车入库单才能添加附件');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据添加附件');
        }else{
            BJUI.navtab({
                id:'navab263',
                url:'/stockcarin/edit/mode/addattach/id/'+data[0]['sysno'],
                title:'添加附件',
            })
            return;
        }
    }

    function finishstockcarin(){
        var data = $.CurrentNavtab.find("#stockcarinlist-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(data[0]['stockinstatus']!=3){
            BJUI.alertmsg('warn', '只有入库中的车入库单才能完成入库完成操作');
        }else if(data[0]['beqty']==0){
            BJUI.alertmsg('warn', '入库数量为0的订单不能完成入库完成操作');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据完成入库');
        }else{
            BJUI.navtab({
                id:'navab263',
                url:'/stockcarin/edit/mode/confirm/id/'+data[0]['sysno'],
                title:'入库完成',
            })
            return;
        }
    }

    function stockcarinlist_addcar() {
        var checkdata = $('#stockcarinlist-table').data('selectedDatas');
        if(checkdata == ''||checkdata == null){
            BJUI.alertmsg('warn', '<h4>未选择数据！</h4>');
        }else if(checkdata.length>=2){
            BJUI.alertmsg('warn', '只能选择一条单据添加车辆');
        }else if(checkdata[0].stockinstatus!=3){
                BJUI.alertmsg('warn', '只有入库中的入库单才能添加车辆');
        }else{
            BJUI.navtab({
                id: 'navab263',
                url: '/stockcarin/edit/mode/addcar/id/'+checkdata[0].sysno,
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '增加车辆'
            });
        }
    }

    function signoutstockcarin() {
        var bar_no = $("#bar_no").val();
        var bar_name = $("#bar_name").val();
        var bar_stockinstatus = $("#bar_stockinstatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/stockcarin/excel/',
            type:'POST',
            data:{bar_no: bar_no,bar_name:bar_name,bar_stockinstatus:bar_stockinstatus},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }
</script>