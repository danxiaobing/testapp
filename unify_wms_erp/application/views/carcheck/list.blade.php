<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#carcheck-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" name="carid" placeholder="车牌号">
                </div>
                <label class="row-label">作业类型</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="operationtype">
                        <option value="" selected="">全部</option>
                        <option value="1">入库卸货</option>
                        <option value="2">出库提货</option>
                        <option value="3">退货</option>
                    </select>
                </div>
                <label class="row-label">单据类型</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="carcheckstatus">
                        <option value="" selected="">全部</option>
                        <option value="1">新建</option>
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">审核通过</option>
                        <option value="5">车辆退回</option>
                        <option value="6">作废</option>
                        <option value="7">终止</option>
                    </select>
                </div>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                        <!-- <button type="reset" class="btn-orange" data-icon="times">重置</button> -->
                    </div>
                </div>
            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="carcheck-list-table" data-toggle="datagrid" data-options="{
        height:'100%',
        showToolbar: true,
        toolbarCustom : '#carcheck-button',
        dataUrl: 'carcheck/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: false,
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        inlineEditMult:false
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'businessno',align:'center',width:100 }">业务单据编号</th>
            <th data-options="{name:'operationtype',width:150,align:'center',render:function(value){ if(value =='1') {return '入库卸货';} else if(value==2){return '出库提货'} else if(value==3){return '退货'}}}">作业类型</th>
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'carname',align:'center'}">司机</th>
            <th data-options="{name:'mobilephone',align:'center'}">手机号</th>
            <th data-options="{name:'idcard',align:'center'}">身份证</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">预提/预卸数量(KG)</th>
            <th data-options="{name:'carcheckstatus',align:'center',render:function(value){ if(value =='1') {return '新建';} else if(value==2){return '暂存'} else if(value==3){return '待审核'} else if(value==4){return '审核通过'} else if(value==5){return '车辆退回'} else if(value==6){return '作废'}else if(value==7){return '终止'}}}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="carcheck-button">
    <button type="button" class="btn btn-blue" data-icon="eye" onclick="carcheck_look()">查看</button>
    <button type="button" id="carcheck_edit" class="btn btn-green" data-icon="gavel" >车辆审核</button>
    <button type="button" class="btn btn-red" data-icon="reply" onclick="carcheck_change()">返回上步操作</button>
</div>
<script type="text/javascript">
    function carcheck_change()
    {
        var data = $('#carcheck-list-table').data('selectedDatas');
        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>请选择单条数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            if(data[0].carcheckstatus != 4){
                BJUI.alertmsg('warn','<h4>只有审核通过的单据才能返回上步操作！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }

            var sysno = data[0]['sysno'];
            var businesstype = data[0]['businesstype'];
            var business_sysno = data[0]['business_sysno'];

            BJUI.ajax('doajax', {
                url: '/Carcheck/ajaxgetCarcheck/id/'+sysno+'/businesstype/'+businesstype+'/business_sysno/'+business_sysno,
                loadingmask: true,
                okCallback: function(json, options) {
                    BJUI.navtab('reloadFlag', 'navab576');
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }
    function carcheck_look(){
        var data = $('#carcheck-list-table').data('selectedDatas');
        if(data && data.length > 0){
            if(data.length>1)
            {
                BJUI.alertmsg('warn','<h4>请选择一行数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'carCheck-edit',
                url:"/Carcheck/look/id/"+data[0].sysno,
                title:'车辆核对查看'
            });
        }else {
            BJUI.alertmsg('warn', '<h4>未选中数据！</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
        }
    }

    $('#carcheck_edit').click(function(){
        var data = $('#carcheck-list-table').data('selectedDatas');
        if(data[0].carcheckstatus != 3) {
            BJUI.alertmsg('warn','<h4>该数据不是待审核状态！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(data && data.length > 0){
            if(data.length>1)
            {
                BJUI.alertmsg('warn','<h4>请选择一行数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'carCheck-edit',
                url:'/Carcheck/edit',
                data:{id:sysno},
                type:'POST',
                mask:true,
                fresh:true,
                title:'车辆审核',
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });
</script>