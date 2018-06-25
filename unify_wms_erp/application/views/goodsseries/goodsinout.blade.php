<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" id="goodsoutinlist-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#goodsoutinlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" class="goodsinout_datepicker" value="{{$begin_time or ''}}"  ></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="customername" value="" placeholder="客户名称"></div>

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
    <table class="table table-bordered" id="goodsoutinlist-table" data-toggle="datagrid" data-options="{
        fullGrid:true,
        height: '100%',
        showToolbar: true,
        toolbarItem: 'export',
        dataUrl: 'Report_countgoods/GoodsinoutJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:10},
        exportOption: {type:'file', options:{url:'/Report_Countgoods/Excelinoutlist',form:$('#goodsoutinlist-excel')}},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
        showNoDataTip:true,
        editMode:false,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'lastqty',align:'center'}">上期结存</th>
            <th data-options="{name:'instock',align:'center'}">本期进货</th>
            <th data-options="{name:'outstock',align:'center'}">本期出库</th>
            <th data-options="{name:'traninstock',align:'center'}">本期货转入</th>
            <th data-options="{name:'tranoutstock',align:'center'}">本期货转出</th>
            <th data-options="{name:'customer_beqty',align:'center' }">客户结存量</th>
            <th data-options="{name:'storagetank_qty',align:'center' }">储罐结存量</th>
            <th data-options="{name:'9',align:'center',render:goodsoutin_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<script>
$('.goodsinout_datepicker').datetimepicker({
        //language:  'fr',
       format: 'yyyy-mm',  
         weekStart: 1,  
         autoclose: true,  
         startView: 3,  
         minView: 3,
         forceParse: false,
         language: 'zh-CN'

    });

    function goodsoutin_operation(val,data){
        return '<button type="button" class="btn-green" onclick="see_inoutdetail('+data.customer_sysno+','+data.storagetank_sysno+','+data.goods_sysno+')">查看明细</button>';
    }

    function see_inoutdetail(customer_sysno='',storagetank_sysno='',goods_sysno='')
    { 
        var time = $("input[name='begin_time']").val();
        
        BJUI.dialog({
            id:'see_inoutdetail',
            url:'/Report_countgoods/see_inoutdetail',
            data:{customer_sysno:customer_sysno,storagetank_sysno:storagetank_sysno,goods_sysno:goods_sysno,time:time},
            type:'POST',
            title:'查看明细',
            width:1300,
            height:700,
            mask:true,
        });
    }
</script>