<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id='{{$prefix}}from' class="stocktrans_export"
          data-options="{searchDatagrid:$.CurrentNavtab.find('#customercategorylist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">转移日期</label>
                <div class="row-input datawidth">
                    <input type="text" name="stocktransdate_start" value="{{$created_at or ''}}"
                           data-toggle="datepicker" placeholder="转移开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="stocktransdate_end" value="{{$updated_at or ''}}" data-toggle="datepicker"
                           placeholder="转移结束时间"></div>
                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="stocktransstatus">
                        <option value="" selected="">全部</option>
                        {{--<option value="1">新建</option>--}}
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">已审核</option>
                        <option value="6">退回</option>
                        <option value="8">驳回</option>
                    </select>
                </div>
                <label class="row-label">单据来源</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="docsource">
                        <option value="" selected="">全部</option>
                        <option value="1">手工创建</option>
                        <option value="2">国烨云仓</option>
                    </select>
                </div>
                <label class="row-label">转让方</label>
                <div class="row-input">
                    <select name="sale_customer_sysno" id="{{$prefix}}sale_customer_sysno" data-size="5"
                            data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}"
                                    @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">受让方</label>
                <div class="row-input">
                    <select name="buy_customer_sysno" id="{{$prefix}}sale_customer_sysno" data-size="5"
                            data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}"
                                    @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                </div>

            </div>
        </fieldset>
    </form>

</div>
<div class="bjui-pageContent clearfix">

    <table class="table table-bordered" id="customercategorylist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        tableWidth : '100%',
        showToolbar: true,
        toolbarCustom : '#stocktrank-button',
        toolbarItem: 'del,|,export',
        addLocation: 'last',
        dataUrl: 'Stocktrans/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: false,
       {{-- editUrl: '/stocktrans/edit/id/{sysno}/type/edit',--}}
            delUrl:'/stocktrans/delJson',
            delPK:'sysno',
            exportOption: {type:'file', options:{url:'/stocktrans/export1',form:$('.stocktrans_export')}},
            paging: {pageSize:12},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
             linenumberAll: true,
             hScrollbar:true

        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stocktransno',align:'center',width:'200'}">单据编号</th>
            <th data-options="{name:'docsource',align:'center',render:function(value){if(value==1) {return '手工创建'} else if(value==2){return '国烨云仓'} else {return '初始化导入' } }}">
                单据来源
            </th>
            <th data-options="{name:'sale_customername',align:'center',width:'280'}">转让方名称</th>
            <th data-options="{name:'buy_customername',align:'center',width:'280'}">受让方名称</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){ return '吨' }}">计量单位</th>
            <th data-options="{name:'qty',align:'center'}">数量</th>
            <th data-options="{name:'buystartdate',align:'center'}">受让方计费起始日</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value==1) {return '保税';} else if(value==2){return '外贸';} else if(value==3) {return '内贸转出口'; } else {return '内贸内销'; } }}">
                货物性质
            </th>
            <th data-options="{name:'stocktransstatus',align:'center',render:function(value){
            if(value==1){return '新建';}
            else if(value==2){return '暂存';}
            else if(value==3){return '待审核';}
            else if(value==4){return '已审核';}
            else if(value==5){return '已完成';}
            else if(value==6){return '退回';}
             else if(value==8){return '驳回';}}}">单据状态
            </th>
            <th data-options="{name:'created_at',align:'center',hide:'true'}">创建时间</th>
            <th data-options="{name:'stocktransdate',align:'center',hide:'true'}">转移日期</th>
        </tr>
        </thead>
    </table>
</div>
<div id="stocktrank-button">
    <button type="button" id="stocktrank_edit" class="btn btn-green" data-icon="edit">编辑</button>
    <button type="button" id="stocktrank_provide" class="btn btn-green" data-icon="gavel">审核</button>
    <button type="button" id="look_stocktrank_data" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" id="look_attachment" class="btn btn-green" data-icon="filter">附件</button>
    <button type="button" id="stocktrank_Print" class="btn btn-green" data-icon="print">打印</button>
    {{-- <button type="button" id="stocktrank_handle" class="btn btn-red" data-icon="file-text">处理</button>--}}
</div>
<script type="text/javascript">
    //编辑
    $('#stocktrank_edit').click(function () {
        var checkdata = $('#customercategorylist-table').data('selectedDatas');
        if (typeof(checkdata) == 'undefined' || checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        var stocktransstatus = checkdata[0].stocktransstatus;
        if (checkdata) {
            if (stocktransstatus == 2 || stocktransstatus == 6) {
                BJUI.navtab({
                    id: 'navab444',
                    url: '/stocktrans/edit/id/' + checkdata[0].sysno + '/type/edit',
                    type: 'post',
                    data: {'sysno': checkdata[0].sysno}, title: '编辑货权转移',
                })
            } else {
                BJUI.alertmsg('warn', '<h4>只能选择暂存或退回的数据!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            }
        } else {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });


    //审核
    $('#stocktrank_provide').click(function () {
        var checkdata = $('#customercategorylist-table').data('selectedDatas');
        if (typeof(checkdata) == 'undefined' || checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        var stocktransstatus = checkdata[0].stocktransstatus;
        if (checkdata) {
            if (stocktransstatus == 3) {
                BJUI.navtab({
                    id: 'navab444',
                    url: '/stocktrans/edit/id/' + checkdata[0].sysno,
                    type: 'post',
                    data: {'sysno': checkdata[0].sysno}, title: '审核货权转移',
                })
            } else {
                BJUI.alertmsg('warn', '<h4>只能选择待审核的数据!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            }
        } else {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });


    //处理
    $('#stocktrank_handle').click(function () {
        var checkdata = $('#customercategorylist-table').data('selectedDatas');
        if (typeof(checkdata) == 'undefined' || checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        console.log(checkdata);
        var stocktransstatus = checkdata[0].stocktransstatus;
        var docsource = checkdata[0].docsource;
        if (stocktransstatus == 2 && docsource == 2) {
            BJUI.navtab({
                id: 'stocktrank_provide' + checkdata[0].sysno,
                url: '/stocktrans/edit/id/' + checkdata[0].sysno,
                type: 'post',
                data: {'sysno': checkdata[0].sysno, 'handle': 'handle'},
                title: '处理货权转移',
            })
        } else {
            BJUI.alertmsg('warn', '<h4>只能选择暂存且来自国烨云仓的数据!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }

    });

    //查看功能
    $('#look_stocktrank_data').click(function () {
        var checkdata = $('#customercategorylist-table').data('selectedDatas');
        if (checkdata == '' || typeof(checkdata) == 'undefined' || checkdata == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        } else {
            BJUI.navtab({
                id: 'navab444',
                url: '/stocktrans/edit/id/' + checkdata[0].sysno + '/val/1',
                type: 'post',
                data: {'id': checkdata[0].sysno, 'look': 'look'},
                title: '查看货权转移单'
            });
        }
    });
    //查看附件
    $("#look_attachment").click(function () {

        var data = $("#customercategorylist-table").data('selectedDatas');
        if (data == '' || typeof(data) == 'undefined' || data == null) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return false;
        } else {
            var obj = data[0];
            if (obj.sysno != '') {
                BJUI.dialog({
                    url: '/attachment/view/stocktrans/attach-1/' + obj.sysno,
                    title: '查看' + obj.stocktransno + "附件",
                    width: 900,
                    height: 600,
                    mask: true,
                });
            }
        }
    });
    //打印详细单据
    $('#stocktrank_Print').click(function () {
        var checkdata = $('#customercategorylist-table').data('selectedDatas');
        if (typeof(checkdata) == 'undefined' || checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        var stocktransstatus = checkdata[0].stocktransstatus;
        if (checkdata) {
            if (stocktransstatus == 4) {
                BJUI.navtab({
                    id: 'stocktrank_print',
                    url: '/stocktrans/edit/id/' + checkdata[0].sysno,
                    type: 'post',
                    data: {'sysno': checkdata[0].sysno}, title: '打印货权转移单',
                })
            } else {
                BJUI.alertmsg('warn', '<h4>只能选择已审核的数据!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
            }
        } else {
            BJUI.alertmsg('warn', '<h4>未选中任何行!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    });


</script>
