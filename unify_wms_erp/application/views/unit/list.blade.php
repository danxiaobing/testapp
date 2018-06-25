<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#userlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">计量单位名称</label>
                <div class="row-input">
                    <input type="text" name="unitname" value="{{$unitname or ''}}" placeholder="计量单位名称">
                </div>
                <label class="row-label">计量单位状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="" selected="">不限</option>
                        <option value="1" >启用</option>
                        <option value="2">停用</option>
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
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="userlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,refresh',
        toolbarCustom : '#unit-button',
        dataUrl: 'unit/unitlistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1150',height:'320',title:'计量单位',mask:true}},
        editUrl: '/unit/unitedit/id/{sysno}',
        delUrl:'/unit/unitdeljson',
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'unitname',align:'center'}">计量单位名称</th>
            <th  data-options="{name:'unittype',align:'center'}">计量单位类型</th>
            <th  data-options="{name:'decimalpoint',align:'center'}">精度保留位数   </th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="unit-button">
    <button type="button" id="dels" class="btn btn-red" data-icon="times" >删除</button>
    <button type="button" id="unit_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="unit_stop" class="btn btn-green" data-icon="hand-paper-o">停用</button>
</div>
<script type="text/javascript">
    //批量删除
    $('#dels').click(function(){
        var checkdata=$('#userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量删除吗！', {
                okCall: function() {
                    //回调操作
                    var checkdata=$('#userlist-table').data('selectedDatas');

                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Unit/unitdeljson/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu181');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！')
                }
            })
        }

    });

    //批量启用
    $('#unit_start').click(function(){
        var checkdata=$('#userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选择数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    //回调操作
                    var checkdata=$('#userlist-table').data('selectedDatas');

                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Unit/statuschange/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu181');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }


    });
    //批量停用
    $('#unit_stop').click(function(){
        var checkdata=$('#userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选择数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    //回调操作
                    var checkdata=$('#userlist-table').data('selectedDatas');
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Unit/statusover/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu181');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }

    });

</script>