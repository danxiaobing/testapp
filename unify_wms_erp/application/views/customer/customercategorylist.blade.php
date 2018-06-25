<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#customercategorylist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">客户分类名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户分类名称"></div>



                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
                        <option value="2">已禁用</option>
                        <option value="1" >已启用</option>
                    </select>
                </div>
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
    <table class="table table-bordered" id="customercategorylist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom : '#customercategory-button',
        addLocation: 'last',
        dataUrl: 'customer/categorylistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'600',height:'350',title:'客户分类',mask:true}},
        editUrl: '/customer/categoryedit/id/{sysno}',
        delUrl:'/customer/categorydeljson',
        delPK:'sysno',
        columnResize:false,
        paging: {pageSize:10},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showCheckboxcol:true,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'categoryname',align:'center',width:100}">客户分类名称</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
                <th data-options="{name:'status',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="customercategory-button">
    <button type="button" id="customercategory_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="customercategory_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
</div>
<script type="text/javascript">
    $('#customercategory_start').click(function(){
        var checkdata=$('#customercategorylist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            var date = [];
            for(i=0;i<checkdata.length;i++){
                date[i] = checkdata[i].sysno;
            }
            BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/customer/categorystatuschange/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','navab331');
                        }
                    });
                }
            })
            
        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });
    $('#customercategory_stop').click(function(){
        var checkdata=$('#customercategorylist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            var date = [];
            for(i=0;i<checkdata.length;i++){
                date[i] = checkdata[i].sysno;
            }
            BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/customer/categorystatusover/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','navab331');
                        }
                    });
                }
            })
        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });

</script>