<div class="bjui-pageContent">

    <h4><strong>基本信息</strong></h4>
    <div style="border-bottom: 0px solid #ddd;">
    <table class="table table-bordered" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            filterThead:false,
            dataUrl: '/report_customerintraday/detailJson/',
            postData:{data:{{$data}}},
            paging: false,
            linenumberAll: true,
            }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'date',align:'center'}">日期</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'}else if(value=='4') {return '内贸内销'}}}">货物性质</th>
            <th data-options="{name:'endingstocks',align:'center',render:function(value){if(!value) {return 0}}}">昨日结存量</th>
            <th data-options="{name:'inqty',align:'center',render:function(value){if(!value) {return 0}}}">今日入库量</th>
            <th data-options="{name:'outqty',align:'center',render:function(value){if(!value) {return 0}}}">今日出库量</th>
            <th data-options="{name:'ullage',align:'center',render:function(value){if(!value) {return 0}}}">今日损耗量</th>
            <th data-options="{name:'endstock',align:'center',render:function(value){if(!value) {return 0}}}">今日结存量</th>
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
            dataUrl: '/report_customerintraday/inAndOutDetailJson/',
            postData:{data:{{$data}}},
            paging: false,
            filterThead:false,
            addLocation:'first'
        }">
        <thead>
        <!-- 类型:1船入库2车入库3船出库4车出库5货转入（正）6货转出（负）7倒罐入（正）8倒罐出（负）9盘点(储罐)10盘点(客户) 11管线入库 12 管线出库 13提单入 14提单出 15超期损耗(批量脚本) 16提单撤销入 17 提单撤销出 18清库损耗 19补单入 20补单扣-->
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'doc_type',align:'center',render:function(value){if(value=='1') {return '船入库'} else if(value=='2') {return '车入库'} else if(value=='3') {return '船出库'}else if(value=='4') {return '车出库'}else if(value=='5') {return '货转入'}else if(value=='6') {return '货转出'}else if(value=='7') {return '倒罐入'}else if(value=='8') {return '倒罐出'}else if(value=='11') {return '管入库'}else if(value=='12') {return '管出库'}else if(value=='13') {return '提单入'}else if(value=='14') {return '提单出'}else if(value=='15') {return '超期损耗'}else if(value=='16') {return '提单撤销入'}else if(value=='17') {return '提单撤销出'}else if(value=='18') {return '清库损耗'}else if(value=='19') {return '补单入'}else if(value=='20') {return '补单出'}else if(value=='26') {return '退货入库'}}}">类型</th>
            <th data-options="{name:'docno',align:'center'}">单号</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提单号</th>
            <th data-options="{name:'shipname',align:'center'}">车船号</th>
            <th data-options="{name:'outqty',align:'center'}">提货量</th>
            <th data-options="{name:'inqty',align:'center'}">入库量</th>
            <th data-options="{name:'tranqty',align:'center'}">转出量</th>
            <th data-options="{name:'ullage',align:'center'}">损耗量</th>
            <th data-options="{name:'rebackqty',align:'center'}">退货量</th>
            <th data-options="{name:'takegoodscompany',align:'center'}">提货单位</th>
            <th data-options="{name:'endingstock',align:'center'}">结存量</th>
        </tr>
        </thead>
    </table>
</div>


