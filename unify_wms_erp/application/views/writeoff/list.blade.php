<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="stock-excel"
          data-options="{searchDatagrid:$.CurrentNavtab.find('#writeoff-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">单据期间</label>
                <div class="row-input">
                    <input type="text" name="begin_time" id="writeoff_begin_time" value="" data-toggle="datepicker"
                           placeholder="单据开始日期"></div>
                <div class="row-input">
                    <input type="text" name="end_time" id="writeoff_end_time" value="" data-toggle="datepicker"
                           placeholder="单据结束日期"></div>

                <label class="row-label">客户</label>
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

                <button type="submit" class="btn-green" data-icon="search">开始搜索</button>

            </div>
        </fieldset>
    </form>

</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="writeoff-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'del',
        toolbarCustom: '#wirteoff_div_btn',
        addLocation: 'last',
        dataUrl: '/writeoff/listjson',
        dataType: 'json',
        jsonPrefix: 'obj',
        delUrl:'/writeoff/delJson',
        delPK:'sysno',
        paging: {pageSize:14},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        showTfoot:false,
        editMode:false,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'writeoffno',align:'center'}">核销单号</th>
            <th data-options="{name:'writeoffdate',align:'center',render:function(value,data){if(value){return value;}else{return data.stocktransdate;}}}">
                核销日期
            </th>
            <th data-options="{name:'customername',align:'center'}">客户名称</th>
            <th data-options="{name:'writeoffcost',align:'center'}">核销金额</th>
            <th data-options="{name:'hx_employeename',align:'center'}">核销人</th>
        </tr>
        </thead>
    </table>
</div>
<div id="wirteoff_div_btn">
    <button id="show_writeoff_btn" class="btn btn-blue" data-icon="eye">查看</button>
    <button id="excel_writeoff_btn" class="btn btn-green" data-icon="fa-file-excel-o">Excel导出</button>
</div>

<script>

    $("#show_writeoff_btn").click(function () {
        var checkdata = $('#writeoff-table').data('selectedDatas');
        //console.log(checkdata);return;
        var chks = $.CurrentNavtab.find("#writeoff-table");
        if (chks.length < 1) {
            BJUI.alertmsg('warn', '未选中任何行', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return;
        }
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        BJUI.navtab({
            id: 'writeoffview' + checkdata[0].sysno,
            url: '/writeoff/show/',
            type: 'post',
            data: {'id': checkdata[0].sysno},
            title: '查看核销单'
        });

    });

    $("#excel_writeoff_btn").click(function () {
        var begin_time = $("#writeoff_begin_time").val();
        var end_time = $("#writeoff_end_time").val();
        var stock_customer_sysno = $("#stock_customer_sysno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url: '/writeoff/excel/',
            type: 'POST',
            data: {begin_time: begin_time, end_time: end_time, stock_customer_sysno: stock_customer_sysno},
            successCallback: function (json, options) {
                console.log(success);
            }
        });
    });
</script>
