<div class="bjui-pageContent clearfix">
    <h4><strong>基本信息</strong></h4>
    <div>
        <table class="table table-bordered" id="goodstrace-detail-table" data-toggle="datagrid" data-options="{
            fullGrid:true,
            height: '100%',
            tableWidth : '100%',
            showToolbar: true,
            toolbarItem: 'export',
            addLocation: 'last',
            dataUrl: '/Report_Goodstrace/detailJson/sysno/{{$sysno}}',
            exportOption: {type:'file', options:{url:'/Report_Goodstrace/exportDetail/sysno/{{$sysno}}'}},
            dataType: 'json',
            jsonPrefix: 'obj',
            paging:false,
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            {{--showTfoot:true,--}}
            hScrollbar:true
        }">
            <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'customername',align:'center'}">货主</th>
                <th data-options="{name:'shipname',align:'center',}">进货船名</th>
                <th data-options="{name:'goodsname',align:'center'}">货物名称</th>
                <th data-options="{name:'stockindate',align:'center'}">进货日期</th>
                <th data-options="{name:'storagetankname',align:'center'}">进货罐号</th>
                {{--<th data-options="{name:'takegoodsnum',align:'center'}">提单量</th>--}}
                <th data-options="{name:'instockqty',align:'center'}">商检岸罐量</th>
                <th data-options="{name:'ullage',align:'center'}">损耗量</th>
                <th data-options="{name:'stockqty',align:'center'}">结存量</th>
            </tr>
            </thead>
        </table>
    </div>

    <h4><strong>出入库信息</strong></h4>
    <div class="bjui-pageHeader">
        <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#goodstrace_search_table')}">
            <fieldset>
                <legend style="font-weight:normal;">高级搜索</legend>
                <div class="bjui-row col-4">
                    <input type="hidden" name="sysno" value="{{$sysno}}" />
                    <label class="row-label">提单号</label>
                    <div class="row-input">
                        <input type="text" name="takegoodsno" id="instocktakegoodsno" value="" placeholder="提单号">
                    </div>

                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <input type="text" name="customername" id="instockcustomername" value="" placeholder="客户">
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
    <div>
        <table class="table table-bordered" id="goodstrace_search_table" data-toggle="datagrid" data-options="{
            fullGrid:true,
            height: '100%',
            tableWidth : '100%',
            showToolbar: false,
            toolbarItem: '',
            addLocation: 'last',
            dataUrl: '/Report_Goodstrace/getOutJson/sysno/{{$sysno}}',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: false,
            {{--paging: {pageSize:12},--}}
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            {{--showTfoot:true,--}}
             hScrollbar:true
        }">
            <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'docno',align:'center'}">单号</th>
                {{-- 1船入库2车入库3船出库4车出库5货转入（正）6货转出（负）7倒罐入（正）8倒罐出（负）9盘点(储罐)10盘点(客户) 11管线入库 12 管线出库 13提单入 14提单出 15超期损耗(批量脚本) 16提单撤销入 17 提单撤销出--}}
                <th data-options="{name:'doc_type',align:'center',render:function(value){if(value=='1'){return '船入库';}else if(value=='2'){return '车入库';} else if(value==3){ return '船出库'; } else if(value==4) {return '车出库'} else if (value==5) {return '货转入'}else if (value==6) {return '货转出'}else if (value==7) {return '倒罐入'}else if (value==8) {return '倒罐出'}else if (value==9) {return '盘点(储罐)'}else if (value==10) {return '盘点(客户)'}else if (value==11) {return '管线入库'}else if (value==12) {return '管线出库'}else if (value==13) {return '提单入'}else if (value==14) {return '提单出'}else if (value==15) {return '超期损耗'}else if (value==16) {return '提单撤销入'}else if (value==17) {return '提单撤销出'}else if (value==18) {return '清库损耗'}else if (value==19) {return '补单入'}else if (value==20) {return '补单入扣'}else if (value==21) {return '提单倒罐入'}else if (value==22) {return '提单倒罐出'}else if (value==23) {return '提单作废出'}else if (value==24) {return '提单作废入'}else if (value==25) {return '库存调整'}else if (value==26) {return '退货'}}}">类型</th>
                <th data-options="{name:'customername',align:'center'}">客户</th>
                <th data-options="{name:'created_at',align:'center'}">日期</th>
                <th data-options="{name:'takegoodsno',align:'center'}">提单号</th>
                <th data-options="{name:'shipname',align:'center'}">车船类型</th>
                <th data-options="{name:'carid',align:'center'}">车号</th>
                <th data-options="{name:'instockqty',align:'center',calc:'sum'}">入库量/货转量</th>
                <th data-options="{name:'beqty',align:'center',calc:'sum'}">实提数量</th>
                <th data-options="{name:'tuihuo',align:'center',calc:'sum'}">退货数量(吨)</th>
                <th data-options="{name:'stockqty',align:'center',calc:'sum'}">结存量（吨）</th>
                <th data-options="{name:'ullage',align:'center',calc:'sum'}">损耗量（吨）</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script>

</script>











