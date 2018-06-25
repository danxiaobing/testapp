<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#customerlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">客户编号</label>
                <div class="row-input">
                    <input type="text" id="customer_bar_no" name="bar_no" value="{{$bar_no or ''}}" placeholder="客户编号"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" id="customer_bar_name" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称"></div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status" id="customer_bar_status">
                        <option value="-100" selected="">不限</option>
                        <option value="2">已禁用</option>
                        <option value="1" >已启用</option>
                    </select>
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
<div id="customer-button">
    <button type="button" id="customer_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="customer_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
    <button type="button"  class="btn btn-green" data-icon="filter" onclick="customer_list_signout()">EXCEL导出</button>
    <button type="button" id="customer_look" class="btn btn-blue" data-icon="eye"  onclick="customer_look()">查看</button>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="customerlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom : '#customer-button',
        addLocation: 'last',
        dataUrl: 'customer/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {navtab:{title:'客户详细信息',id:'navab326'}},
        editUrl: '/customer/edit/id/{sysno}',
        delUrl:'/customer/deljson',
        delPK:'sysno',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        hScrollbar:true,
        showTfoot:true,
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'customerno',align:'center'}">客户编号</th>
                <th data-options="{name:'customername',align:'center'}">客户名称</th>
                <!-- <th data-options="{name:'categoryname',align:'center'}">客户分类</th> -->
                <th data-options="{name:'customerdeal',align:'center',render:function(value){return value =='1' ? '是' : '否'}}">是否成交</th>
                <th  data-options="{name:'customercredit',align:'center',calc:'sum'}">信用额度</th>
                <th  data-options="{name:'customerterm',align:'center',calc:'sum'}">信用期限</th>
                <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">
    function customer_look()
    {
        var data = $('#customerlist-table').data('selectedDatas');

        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];


            BJUI.navtab({
                id:'customerlist-look'+sysno,
                mask:true,
                type:'POST',
                url:'/customer/showCustomer/id/'+sysno,
                data:{id:sysno},
                title:'查看客户'
            });



        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }
    $('#customer_start').click(function(){
        var chks=$.CurrentNavtab.find("#customerlist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
            return;
        }
        var checkdata=$('#customerlist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            var date = [];
            for(i=0;i<checkdata.length;i++){
                date[i] = checkdata[i].sysno;
            }
            BJUI.alertmsg('confirm', '确定要启用吗！', {
                okCall: function() {
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/customer/statuschange/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','navab327');
                        }
                    });
                }
            })
            
        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });
    $('#customer_stop').click(function(){
        var chks=$.CurrentNavtab.find("#customerlist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
            return;
        }
        var checkdata=$('#customerlist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            var date = [];
            for(i=0;i<checkdata.length;i++){
                date[i] = checkdata[i].sysno;
            }
            BJUI.alertmsg('confirm', '确定要停用吗！', {
                okCall: function() {
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/customer/statusover/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','navab327');
                        }
                    });
                }
            })
        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });

    function customer_list_signout() {
        var bar_name = $("#customer_bar_name").val();
        var bar_no = $("#customer_bar_no").val();
        var bar_status = $("#customer_bar_status option:selected").val();

        var data=$('#customerlist-table').data('allData');
        if(data=='' || data==null)
        {
            BJUI.alertmsg('warn','空数据无法导出',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }

        BJUI.ajax('ajaxdownload', {
            url:'/customer/excel/',
            type:'POST',
            data: {bar_name: bar_name, bar_no: bar_no, bar_status: bar_status},
            successCallback: function(json, options) {
                console.log(123);
            }
        });
    }
</script>