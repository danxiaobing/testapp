<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#customerfinancelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围:</label>
                <div class="row-input datawidth">
                    <input type="text" id="customerfinanceliststartdate" name="startdate" value="{{date('Y-m-d',strtotime('-1 months'))}}" data-toggle="datepicker" >
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="customerfinancelistenddate" name="enddate" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" >
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="customer_name" id="customerfinancelist_customer_name" value="{{$customer_name or ''}}" placeholder="客户名称"></div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <input type="text" name="goods_name" id="customerfinancelist_goods_name" value="{{$goods_name or ''}}" placeholder="货品名称"></div>

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
    <table class="table table-bordered" id="customerfinancelist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            hScrollbar:true,
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#customerfinancelist',
            addLocation: 'last',
            dataUrl: '/Report_Customerfinance/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
            fieldSortable: false,
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customer_name',align:'center'}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'totalsprice',align:'center',calc:'sum'}">金额</th>
            <th data-options="{name:'sysno',align:'center',render:customerfinancelist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="customerfinancelist">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="customerfinanceexcelout()">EXCEL导出</button>
</div>
<script type="text/javascript">
    function customerfinancelist_operation(val,data){
        var customer_name = "'"+data.customer_name+"'";
        var goods_name = "'"+data.goodsname+"'";
        return '<button type="button" class="btn-green" onclick="see_customerfinancedetail('+val+','+customer_name+','+goods_name+')">联明查询</button>';
    }

    function see_customerfinancedetail(val,customer_name,goods_name){
        var begin_time = $("#customerfinanceliststartdate").val();
        var end_time = $("#customerfinancelistenddate").val();

        BJUI.navtab({
            id:'menu476',
            url:'/Report_Customerfinance/detaillist/sid/'+val,
            type: 'post',
            data:{customer_name:customer_name,goods_name:goods_name,begin_time:begin_time,end_time:end_time},
            title:'客户费用明细表'
        });
    }

    function customerfinanceexcelout(){
        var customer_name = $("#customerfinancelist_customer_name").val();
        var goods_name = $("#customerfinancelist_goods_name").val();
        var begin_time = $("#customerfinanceliststartdate").val();
        var end_time = $("#customerfinancelistenddate").val();

        var data=$('#customerfinancelist-table').data('allData');
        if(data=='' || data==null)
        {
            BJUI.alertmsg('warn','空数据无法导出',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxdownload', {
            url:'/Report_Customerfinance/listexcel/',
            type:'POST',
            data:{customer_name:customer_name,goods_name:goods_name,begin_time:begin_time,end_time:end_time},
            successCallback: function(json, options) {
                console.log('ok');
            }
        });
    }
</script>
