<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#pipeline-stockoutexecutelist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">出库单号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" id='pipeline_execute_bar_no' value="" placeholder="出库单号"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" id='pipeline_execute_bar_name' value="" placeholder="客户名称"></div>
                
                <label class="row-label">提货单号</label>
                <div class="row-input">
                    <input type="text" id='pipeline_execute_bar_receivenumber' name="bar_receivenumber" placeholder="提货单号">
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="bar_goodsname" id='pipeline_execute_bar_goodsname' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['goodsname']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div> 
                
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="" data-toggle="selectpicker" data-width="100%" disabled="true">
                        <option value="8">待执行</option>

                    </select>
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
                <input type="hidden" name="stockouttype" value="{{$stockouttype}}">
            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="pipeline-stockoutexecutelist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#pipeline_stockout_execute_tb',
        addLocation: 'last',
        dataUrl: '/stockout/pipelineExecuteListJson/bar_stockoutstatus/8/',
        dataType: 'json',
        paging: {pageSize:12},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
        hScrollbar:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'stockoutno',align:'center',width:200}">出库单号</th>
                <th data-options="{name:'customername',align:'center',width:280}">客户</th>
                <th  data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
                <th  data-options="{name:'goodsname',align:'center'}">品名</th>
                <th  data-options="{name:'qualityname',align:'center'}">规格</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'} else  {return ''}}}">货物性质</th>
                <th  data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'takeqty',align:'center'}">预约提货数量</th>
                <th  data-options="{name:'tobeqty',align:'center'}">通知提货数量</th>
                <th  data-options="{name:'shipname',align:'center'}">船名</th>
                <th data-options="{name:'stockoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='5') {return '作废'}else if(value=='8') {return '待执行'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>

   




</div>
<div id="pipeline_stockout_execute_tb">
    <button type="button" class="btn btn-green" data-icon="gavel"  id="pipeline_stockout_execute_edit_btn">编辑</button>
</div>

<script type="text/javascript">
        $("#pipeline_stockout_execute_edit_btn").click(function() {

            var data  = $("#pipeline-stockoutexecutelist-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id: 'navab1024',
                url: '/stockout/pipelineedit/type/execute/id/'+data[0].sysno,
                title: '管出库编辑'
            });    
        });
</script>