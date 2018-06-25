<div class="bjui-pageContent">
        <form id="clearstock-form-add" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentDialog.find('#clearstock-add-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-2">
                <label class="row-label">单号</label>
                <div class="row-input">
                    <input type="text" name="stockinno" value="{{$stockinno or ''}}" placeholder="单号"></div>
                <label class="row-label">货品</label>
                <div class="row-input">
                    <input type="text" name="goodsname" value="{{$goodsname or ''}}" placeholder="货品"></div>
                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text" name="shipname" value="{{$shipname or ''}}" placeholder="船名"></div>
                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" id="clear_search" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
        <input type="hidden" name="id" value="{{$id}}">
        <table class="table table-bordered" id="clearstock-add-table" data-toggle="datagrid" data-options="{
                height: '100%',
                showToolbar: false,
                dataUrl: '/clearstock/addselect/customer_sysno/'+{{$customer_sysno}},
                dataType: 'json',
                jsonPrefix: 'obj',
                showCheckboxcol:false,
                paging: {pageSize:12},
                filterThead:false,
                addLocation:'first',
                fullGrid:true,
                showLinenumber: false,
            }">
            <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'stockinno',align:'center',width:200,render:function(value, data){if(value){return value;}else{return data.stocktransno;}}}">单号</th>
                <th  data-options="{name:'goodsname',align:'center'}">货品</th>
                <th  data-options="{name:'goodsqualityname',align:'center'}">规格</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){if(value==1){return '保税';}else if(value==2){return '外贸';}else if(value==3){return '内贸转出口';}else if(value==4){return '内贸内销';}}}">货物性质</th>
                <th  data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'doctype',align:'center',render:function(value,data) {if(value==1){return data.shipname;} else { return '--';} }}">船名</th>
                <th  data-options="{name:'instockqty',align:'center'}">入库数量</th>
                <th  data-options="{name:'outstockqty',align:'center'}">实发数量</th>
                <th  data-options="{name:'stockqty',align:'center'}">余量</th>
                <th  data-options="{name:'clockqty',align:'center'}">锁定数量</th>
                <th  data-options="{name:'availableqty',align:'center',render:function(value,data) {if(!value) {return data.stockqty-data.clockqty }}}">可用数量</th>
                <th  data-options="{name:'doctype',align:'center',hide:'true'}">单据类型</th>
                <th  data-options="{name:'customername',align:'center',hide:'true'}">客户</th>
            </tr>
            </thead>
        </table>
        <br>
        <div class="remarks">
            <fieldset>
                <legend>备注信息</legend>
                 <textarea name="memo" id="memo" data-toggle="autoheight" rows="3" placeholder="请在此处填写备注信息">{{$memo}}</textarea>
            </fieldset>
        </div>
       
    </form>
</div>
<!-- <div id="add_clearstock">
        <button type="button" class="btn btn-green" data-icon="plus" onclick="addclearstockdata()">确认添加</button>
</div> -->
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn btn-green" onclick="addclearstockdata()">确认添加</button></li>
    </ul>
</div>
<script type="text/javascript">
   //确认添加
    function addclearstockdata() {
        var data  = $("#clearstock-add-table").data('selectedDatas');//获取页面选中数据
        console.log(data);
        if (data == undefined) { //未选择提示
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }else{ //获取选中的入库单主键或货权转移单主键
               var stock_sysno = data[0].sysno;
               var customer_sysno = data[0].customer_sysno;
               var memo =$('#memo').val();

            BJUI.ajax('doajax',{
                url:'/clearstock/submitdetail/',
                data:{stock_sysno:stock_sysno,memo:memo,customer_sysno:customer_sysno},
                loadingmask:true,
                okCallback:function(json){
                    $('#clearstock-detail-table').datagrid('reload',{data:json});
                    BJUI.dialog('closeCurrent','');
                }
            });
        }
    }

</script>