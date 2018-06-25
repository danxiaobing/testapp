<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentDialog.find('#customergoodslist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-2">
                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <input type="text" name="goodsname" value="{{$goodsname or ''}}" placeholder="货品名称"></div>
                <div class="row-input">
                    
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                        <button type="button" class="btn btn-blue" data-icon="plus" onclick="subGood()">确认添加</button>
                   
                </div>
            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <form id="customergoodslist-form" action="customer/goodslistwhereJson/" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <input type="hidden" id="inwhere" name="inwhere" value="">
    <table class="table table-bordered" id="customergoodslist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: false,
        dataUrl: 'customer/basislistJson/id/{{$id}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        showCheckboxcol:true,
        paging: false,
        filterThead:false,
        addLocation:'first',
        isTree: 'goodsname',
        fullGrid:true,
        treeOptions: {
            expandAll: false,
            add: false,
            simpleData: true,
            keys: {
                key:'sysno',
                parentKey: 'parent_sysno'
            }
        },
        onload:function(){
             hasGoods();
        }
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'goodsname',align:'center',width:100}">货品名称</th>
            <th  data-options="{name:'goodsno',align:'center',width:250}">货品编号</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
        </tr>
        </thead>
    </table>
    </form>
</div>
<div id="custom_goods_submit">
    
</div>

<script type="text/javascript">

    function subGood() {

        var data  = $("#customergoodslist-table").data('selectedDatas');

        if (data == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        }else{
            $('#customergoods-selected-table').datagrid('reload',  {data:data});
            BJUI.dialog('closeCurrent');
        }

    }

    function hasGoods(){

        var data =  $("#customergoods-selected-table").data('allData');
        var sysnos = [];

        for(i=0; i < data.length ; i ++){
            sysnos[i] = data[i].sysno;
        }
        if(sysnos.length == 0)
            return;

        var arr = $('#customergoodslist-table').data('allData');//.datagrid('selectedRows', 1,  true);
        for( j = 0; j< arr.length; j++){

            if($.inArray(arr[j].sysno, sysnos) > -1 ){

                $('#customergoodslist-table').datagrid('selectedRows', j,  true);
            }
        }
    }



</script>
