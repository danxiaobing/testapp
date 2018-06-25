<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reviewlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select name="customer_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($customerlist as $value)
                        	<option value="{{$value['sysno']}}">{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($goodslist as $value)
                            <option value="{{$value['sysno']}}">{{$value['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">入库单号</label>
                <div class="row-input">
                    <input type="text" name="stockinno" value="{{$stockinno or ''}}" placeholder="入库单号">
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
    <table class="table table-bordered" id="reviewlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: false,
            toolbarCustom:'#',
            addLocation: 'last',
            dataUrl: '/introduce/reviewListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:13},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockinno',align:'center',width:150}">入库单号</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'stockindate',align:'center'}">进货日期</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'shipname',align:'center',render:function(value){if(!value) {return '槽车'}}}">船名</th>
            <th data-options="{name:'qualityname',align:'center'}">规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';}}}">货品性质</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'beqty',align:'center'}">入库数量</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单数量</th>
            <th data-options="{name:'takegoodsqty',align:'center'}">实提数量</th>
            <th data-options="{name:'untakegoodsnum',align:'center'}">结存量</th>
            <th data-options="{name:'sysno',align:'center',render:reviewlist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">
    function reviewlist_operation(val,data){
        return '<button type="button" class="btn-green" onclick="see_reviewDetail('+val+')">查看明细</button>';
    }

    function see_reviewDetail(val){
        BJUI.navtab({
            id:'introduce002',
            url:'/introduce/reviewDetailList/id/'+val,
            title:'提单追溯明细'
        });
    }
</script>