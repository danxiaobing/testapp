<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reporttrack-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">查询期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="bar_startdate" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="bar_enddate" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称"></div>

                <label class="row-label">单据类型</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_doctype">
                        <option value="-100" selected="">请选择</option>
                        <option value="1">船入库</option>
                        <option value="2">车入库</option>
                        <option value="3">货权转移</option>
                    </select>
                </div>

                <label class="row-label">单号</label>
                <div class="row-input">
                    <input type="text" id="rp_bar_no" name="bar_no" value="{{$bar_no or ''}}" placeholder="单号"></div>


                <!-- <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="bar_stockoutstatus" data-toggle="selectpicker" data-width="100%" name="bar_stockinstatus">
                        <option value="-100" selected="">不限</option>
                        <option value="1">新建</option>
                        <option value="2">暂存</option>
                        <option value="3">已提交</option>
                        <option value="4">已审核</option>
                        <option value="5">已完成</option>
                        <option value="6">作废</option>
                    </select>
                </div> -->
                 <label class="row-label"></label>
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
    <table class="table table-bordered" id="reporttrack-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        showToolbar: false,
        dataUrl: '/reporttrack/listJson',
        afterSave:function(str, datas){ this.refresh();},
        afterDelete:function(str, datas){ this.refresh();},
        paging: false,
        filterThead:false,
        addLocation:'first',
        isTree: 'stockin_no',
        treeOptions: {
            expandAll: false,
            add: false,
            simpleData: true,
            keys: {
                key:'sysno',
                parentKey: 'parent_sysno'
            }
        },
        hScrollbar:true,
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'stockin_no',align:'center'}">单号</th>
                <th data-options="{name:'doctype',align:'center',render:function(value,data){  switch(value){ case '1' : var str='船入库';break; case '2': var str='车入库';break; case '3': var str='货权转移';break; case '4': var str='管入库';break; case 5: var str='船出库';break; case 6: var str='车出库';break; case 7: var str='管出库';break;} return str;}}">类型</th>
                <th data-options="{name:'customername',align:'center'}">客户</th>
                <!-- <th data-options="{name:'sale_customername',align:'center'}">转让方</th>
                <th data-options="{name:'buy_customername',align:'center'}">受让方</th> -->
                <th data-options="{name:'goodsname',align:'center'}">货品</th>
                <th data-options="{name:'goodsqualityname',align:'center'}">规格</th>
                <!-- <th data-options="{name:'unitname',align:'center'}">计量单位</th> -->
                <th data-options="{name:'instockqty',align:'center'}">数量</th>
                <th data-options="{name:'ableqty',align:'center'}">余量</th>
                <th data-options="{hide:true,name:'flag',align:'center',render:function(value,data){ if(data.flag==true){return '<span style=color:red;font-weight:600>'+value+'</span>';}else{return value;} }}">标记</th>
            </tr>
        </thead>
    </table>
</div>

<!-- <div id="reporttrack_tb">
    <button type="button" class="btn btn-blue" data-icon="edit" onclick="view()"><i class="fa fa-plus"></i> 查看</button>
</div> -->

<script type="text/javascript">
    $.CurrentNavtab.find('#reporttrack-table').on('afterLoad.bjui.datagrid', function() {

        var selectNo = $.CurrentNavtab.find('#rp_bar_no').val();
        
        $(this).find("tr").each(function(){

            if($(this).text().indexOf(selectNo) >0){

                $(this).css({'color':'red','font-weight':'600'});
  
            }
        });
    });

</script>