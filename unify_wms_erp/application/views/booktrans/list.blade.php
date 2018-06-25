<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;padding: 0;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#booktrans-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">货权转移预约单号</label>
                <div class="row-input">
                    <input type="text" name="bookingtransno" value="{{$bookingtransno}}" placeholder="货权转移预约单号"></div>
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
    <table class="table table-bordered" id="booktrans-table" data-toggle="datagrid" data-options="{
        tableWidth:'99%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarCustom:$.CurrentNavtab.find('#booktrans_tb'),
        toolbarItem: 'refresh',
        dataUrl: 'booktrans/detail',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1000',height:'300',title:'收费管理',mask:true}},
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookingtransno',align:'center'}">货权转移预约单号</th>
            <th data-options="{name:'bookingtransdate',align:'center'}">预约日期</th>
            <th data-options="{name:'salename',align:'center'}">转让方名称</th>
            <th data-options="{name:'buyname',align:'center'}">受让方名称</th>
            <th data-options="{name:'buystartdate',align:'center'}">受让方计费起始日</th>
            <th data-options="{name:'bookingtransstatus',align:'center',render:function(value){
                    if(value==1){return '新建';}
                    else if(value==2){return '暂存';}
                    else if(value==3){return '已提交';}
                    else if(value==4){return '已审核';}
                    else if(value==5){return '已完成';}
                    else if(value==6){return '作废';}}}">预约单状态</th>
            <th data-options="{name:'cocno',align:'center'}">合同编号</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="booktrans_tb">
    <button type="button" class="btn btn-blue" onclick="changethis()"><i class="fa fa-plus"></i> 货权转移单</button>
</div>

<script type="text/javascript">
    function changethis() {
        var tabledata = $("#booktrans-table").data('selectedDatas');
        console.log(tabledata[0]);
        //return false;
        var sysno = tabledata[0].sysno;
        var contract_sysno = tabledata[0].contract_sysno; //合同编号
        var bookingtransdate = tabledata[0].bookingtransdate; //预约日期
        var sale_customer_sysno = tabledata[0].sale_customer_sysno; //转让方
        var buy_customer_sysno = tabledata[0].buy_customer_sysno; //受让方
        var buystartdate = tabledata[0].buystartdate; //受让方计费起始日
        var bookingtransno = tabledata[0].bookingtransno;//货权转移预约单号
        BJUI.navtab({
            id:'addbooktrans'+sysno,
            url:'/stocktrans/edit',
            data:{
                 'booktrans_sysno':sysno,
                 'contract_sysno':contract_sysno,
                 'bookingtransno':bookingtransno,
                 'stocktransdate':bookingtransdate,
                 'sale_customer_sysno':sale_customer_sysno,
                 'buy_customer_sysno':buy_customer_sysno,
                 'buymoneydate':buystartdate
            },
            title:'生成货权转移单'
        })

    }
</script>