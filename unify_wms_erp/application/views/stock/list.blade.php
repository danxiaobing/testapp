<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="stock-excel"
          data-options="{searchDatagrid:$.CurrentNavtab.find('#stocklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">单据期间</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="预约开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="预约结束时间"></div>
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

                <label class="row-label">合同编号</label>
                <div class="row-input required">
                    <select name="contract_no" id="stock_contract_sysno" data-size="5" data-toggle="selectpicker"
                            data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($list as $item)
                            <option value="{{$item['sysno']}}">{{$item['contractnodisplay']}}</option>
                        @endforeach
                        <option value="{{$contract_no}}">{{$contract_no}}</option>
                    </select>
                </div>

                <label class="row-label">是否清库</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="isclearstock">
                        <option value="">不限</option>
                        <option value="1">是</option>
                        <option value="0" selected="">否</option>
                    </select>
                </div>


                <label class="row-label">品名</label>
                <div class="row-input">
                    <select name="goodsname" data-size="5" data-toggle="selectpicker" data-live-search="true"
                            data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货物性质</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="goodsnature">
                        <option value="" selected="">不限</option>
                        <option value="1">保税</option>
                        <option value="2">外贸</option>
                        <option value="3">内贸转出口</option>
                        <option value="4">内贸内销</option>
                    </select>
                </div>
                <label class="row-label"></label>
                <div class="row-input "></div>
                <label class="row-label"></label>
                <div class="row-input "></div>
                <label class="row-label"></label>
                <div class="row-input ">
                    <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                </div>
            </div>
        </fieldset>
    </form>

</div>
<div class="bjui-pageContent clearfix">

    <table class="table table-bordered" id="stocklist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'export',
        toolbarCustom:'#stock_look',
        addLocation: 'last',
        dataUrl: 'stock/stocklistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        delPK:'sysno',
        exportOption: {type:'file', options:{url:'/stock/Excel',form:$('#stock-excel')}},
        paging: {pageSize:11},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        showTfoot:true,
        editMode:false,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'firstfrom_no',align:'center',width:150,render:function(value,data){if(data.doctype==3){return data.stocktransno}}}">单号</th>
            <th data-options="{name:'stockindate',align:'center',render:function(value,data){if(data.doctype==3){return data.stocktransdate;}}}">
                单据日期
            </th>
            <th data-options="{name:'firstdate',align:'center'}">首期到期日</th>
            <th data-options="{name:'financedate',align:'center'}">仓储费结算至</th>
            <th data-options="{name:'doctype',align:'center',render:function(value){if(value==1){return '船入库';}else if(value==2){return '车入库';}else if(value==3){return '货权转移';}else if(value==4){return '管入库'}else if(value==5){return '倒罐入库'}}}">
                单据类型
            </th>
            <th data-options="{name:'isclearstock',align:'center',render:function(value){return (value==1)?'是':'否';}}">
                清库
            </th>
            <th data-options="{name:'shipname',align:'center',render:function(value){if(value==''){return '--';}}}">船名
            </th>
            <th data-options="{name:'',align:'center',render:function(value,data){if(data.doctype==3 && value!=''){return data.buy_customername;}else{return data.customername;}}}">
                客户
            </th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsqualityname',align:'center'}">规格</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'inqty',align:'center',calc:'sum',render:function(value,data){ if(data.doctype==3){ return '--'; } } }">
                入库数量
            </th>
            <th data-options="{name:'stockqty',align:'center',calc:'sum'}">余量</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value==1){return '保税';}else if(value==2){return '外贸';}else if(value==3){return '内贸转出口';}else if(value==4){return '内贸内销';}else{return value;}}}">
                货物性质
            </th>
            <th data-options="{name:'clockqty',align:'center',calc:'sum'}">锁货数量</th>
            <th data-options="{name:'overflag',align:'center',render:function(value){if(value==0){return '否';} else if(value==1){return '是';} else if(value ==''){return '--'; } }}">
                是否溢罐
            </th>
            <th data-options="{name:'overqty',align:'center',calc:'sum',render:function(value){ if(value==''){ return '--'; }else if(value==0){ return '--'; } }}">
                溢出吨数
            </th>
            <th data-options="{name:'stockin_sysno',align:'center',hide:true,render:function(value,data){if(value==''){return data.stocktrans_sysno;}}}">
                单据ID
            </th>
        </tr>
        </thead>
    </table>
</div>

<div id="stock_look">
    <button type="button" id="stock_look_btn" class="btn btn-blue"><i class="fa fa-eye"></i> 查看单据</button>
</div>
<script>
    $('#stock_look_btn').click(function () {
        var checkdata = $('#stocklist-table').data('selectedDatas');
        if (checkdata && checkdata.length > 0) {
            if (checkdata.length > 1) {
                BJUI.alertmsg('warn', '<h4>查看或者编辑时只能选择一条数据!<h4>');
                return;
            }
            var doctype = checkdata[0].doctype;
            var url = '';
            var id = 0;
            if (doctype == 2) {
                id = checkdata[0].in_sysno;
                url = '/stockcarin/see/mode/eye/id/' + id;
                title = '查看车入库单据';
            } else if (doctype == 1) {
                id = checkdata[0].in_sysno;
                url = '/stockshipin/show/id/' + id;
                title = '查看船入库单据';
            } else if (doctype == 3) {
                id = checkdata[0].stocktrans_sysno;
                url = '/stocktrans/lookstocktrank/id/' + id + '/val/1';
                title = '查看货权转移单据';
            } else if (doctype == 4) {
                id = checkdata[0].in_sysno;
                url = '/stockpipein/edit/type/eye/id/' + id + '/val/1';
                title = '查看管入库单据';
            } else if (doctype == 5) {
                id = checkdata[0].in_sysno;
                url = '/retank/lookretank/mode/eye/id/' + id + '/val/1';
                title = '查看倒罐单据';
            }

            // console.log(checkdata); return;
            if (id == 0 || id == null) {
                BJUI.alertmsg('error', '数据异常,无法查看');
                return;
            }

            BJUI.navtab({
                id: 'look_stock',
                url: url,
                type: 'post',
                data: {id: id},
                title: title,
            });


        } else {
            BJUI.alertmsg('warn', '<h4>未选中数据！</h4>');
        }
    });

</script>