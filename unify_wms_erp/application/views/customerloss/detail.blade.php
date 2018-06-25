<div class="bjui-pageContent">
    <div class="row-input">
        <div class="btn-group">
            <button type="button" class="btn btn-green" data-icon="sign-out" onclick="lossdetail({{$data}})"></i>EXCEL导出</button>
        </div>
    </div>
    <h4><strong>基本信息</strong></h4>
    <div style="border-bottom: 0px solid #ddd;">
    <table class="table table-bordered" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            filterThead:false,
            dataUrl: '/report_customerloss/detailJson/',
            postData:{data:{{$data}}},
            paging: false,
            linenumberAll: true,
            }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">货品</th>
            <th data-options="{name:'created_at',align:'center'}">入库时间</th>
            <th data-options="{name:'shipname',align:'center',render:function(value){if(!value) {return '--'}}}">车/船名</th>
            <th data-options="{name:'storagestock',align:'center',render:function(value){if(!value) {return 0}}}">商检量</th>
            <th data-options="{name:'outqty',align:'center',render:function(value){if(!value) {return 0}}}">出库量</th>
            <th data-options="{name:'inqty',align:'center',render:function(value){if(!value) {return 0}}}">货转量</th>
            <th data-options="{name:'ullage',align:'center',render:function(value){if(!value) {return 0}}}">损耗量</th>
            <th data-options="{name:'endstock',align:'center',render:function(value){if(!value) {return 0}}}">结存量</th>
        </tr>
        </thead>
    </table>
    </div>
    <br><br><br>

    <h4><strong>详细信息</strong></h4>
    <table class="table table-bordered" data-toggle="datagrid" data-options="{
            tableWidth:'99%',
            height: '100%',
            showToolbar: false,
            dataUrl: '/report_customerloss/inAndOutDetailJson/',
            postData:{data:{{$data}}},
            paging: false,
            filterThead:false,
            addLocation:'first'
        }">
        <thead>
        <!-- 类型:1船入库2车入库3船出库4车出库5货转入（正）6货转出（负）7倒罐入（正）8倒罐出（负）9盘点(储罐)10盘点(客户) 11管线入库 12 管线出库 13提单入 14提单出 -->
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'created_at',align:'center'}">出/入库时间</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            {{--<th data-options="{name:'doc_type',align:'center',render:function(value){if(value=='1') {return '船入库'} else if(value=='2') {return '车入库'} else if(value=='3') {return '船出库'}else if(value=='4') {return '车出库'}else if(value=='5') {return '货转入'}else if(value=='6') {return '货转出'}else if(value=='11') {return '管入库'}else if(value=='12') {return '管出库'}else if(value=='13') {return '提单入'}else if(value=='14') {return '提单出'}else if(value=='16') {return '提单撤销入'}else if(value=='17') {return '提单撤销出'}}}">类型</th>--}}
            {{--<th data-options="{name:'docno',align:'center'}">单号</th>--}}
            {{--<th data-options="{name:'takegoodsno',align:'center'}">提单号</th>--}}
            {{--<th data-options="{name:'shipname',align:'center'}">车船号</th>--}}
            {{--<th data-options="{name:'outqty',align:'center'}">提货量</th>--}}
            <th data-options="{name:'inqty',align:'center'}">入库量</th>
            <th data-options="{name:'outqty',align:'center'}">发货量</th>
            <th data-options="{name:'tranqty',align:'center'}">货转量</th>
            <th data-options="{name:'stock',align:'center'}">库存量</th>
            <th data-options="{name:'ullage',align:'center'}">损耗量</th>
            <th data-options="{name:'percent',align:'center'}">损耗标准</th>
        </tr>
        </thead>
    </table>
</div>
<script>
    function lossdetail(val){
        BJUI.ajax('ajaxdownload', {
            url:'/report_customerloss/exceldetail/',
            type:'POST',
            data:{data:val},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>



