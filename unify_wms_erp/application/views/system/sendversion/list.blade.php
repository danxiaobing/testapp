<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#systemversionlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">方案名称</label>
                <div class="row-input">
                    <input type="text" name="versionname" value="{{$departmentname}}" placeholder="汇签部门">
                </div>
                <label class="row-label">方案状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="status">
                        <option value="" selected="">不限</option>
                        <option value="1" >发布</option>
                        <option value="2">停用</option>
                        <option value="3">未发布</option>
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
    <table class="table table-bordered" id="systemversionlist-table" data-toggle="datagrid" data-options="{
        height:'100%',
        showToolbar: true,
        toolbarCustom : '#version-button',
        dataUrl: 'sendversion/versoionlistJson',
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
            <th data-options="{name:'versionname',align:'center',render:function(value,data){ if(data.status==1){return '<span style=color:red;font-weight:600>'+value+'</span>';}else{return value;}} }">方案名称</th>
            <th data-options="{name:'versionno',align:'center'}">编号</th>
            <th data-options="{name:'versiontype',align:'center',render:function(value){if(value == '1'){return '仓储合同'}else if(value == '2'){return '靠泊装卸合同'}else{return '其他'}}}">适用单据</th>
            <!-- <th data-options="{name:'versionshow',align:'center'}">版本</th> -->
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd'}">创建时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){ if(value =='1') {return '发布';} else if(value==2){return '停用'} else if(value=='3'){return '未发布'} }}">状态</th>
            <th data-options="{align:'center',hide:true}">哈哈哈</th>
        </tr>
        </thead>
    </table>
</div>
<div id="version-button">
    <button type="button" id="version_add" class="btn btn-blue" data-icon="plus" >添加</button>
    <button type="button" id="version_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" id="version_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="version_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
</div>
<script type="text/javascript">
    /**
    *  发布版本
    */
    $('#version_start').click(function(){
        var data = $('#systemversionlist-table').data('selectedDatas');
        if(data && data.length > 0){
            var sysno = data[0]['sysno'];
            var status = data[0]['status'];
            var versiontype = data[0]['versiontype'];

            BJUI.alertmsg('confirm', '确定要发布吗,这样其它版本会关闭!', {
                okCall: function() {
                    $.ajax({
                        type:'get',
                        url:'/sendversion/versionchange/id/'+sysno+'/state/start/versiontype/'+versiontype,
                        success:function(option){
                            if(option){
                                BJUI.alertmsg('ok','发布成功');
                            }else{
                                BJUI.alertmsg('error','发布失败');
                            }
                            BJUI.navtab('refresh','menu430');
                        }
                    });
                }
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });

    /**
    *  停用版本
    */
    $('#version_stop').click(function(){
        var data = $('#systemversionlist-table').data('selectedDatas');


        if(data && data.length > 0){

            var sysno = data[0]['sysno'];

            var status = data[0]['status'];

            if(status == 3){
                BJUI.alertmsg('info','<h4>未发布版本不能停用!<h4>');
                return;
            }
            BJUI.alertmsg('confirm', '确定要停用吗!', {
                okCall: function() {
                    $.ajax({
                        type:'get',
                        url:'/sendversion/versionchange/id/'+sysno+'/state/stop',
                        success:function(option){
                            if(option){
                                BJUI.alertmsg('ok','停用成功');
                            }else{
                                BJUI.alertmsg('error','停用失败');
                            }
                            BJUI.navtab('refresh','menu430');
                        }
                    });
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });

    $('#version_add').click(function(){
        BJUI.navtab({
            id:'version-add',
            url:'/sendversion/versionedit/',
            title:'添加汇签版本配置',
        });
    });

    $('#version_edit').click(function(){
     var data = $('#systemversionlist-table').data('selectedDatas');


        if(data && data.length > 0){

            var sysno = data[0]['sysno'];
            // console.log(sysno);return;
            BJUI.navtab({
                id:'version-edit',
                url:'/sendversion/versionedit/id/'+sysno,
                data:{id:sysno},
                type:'POST',
                title:'添加汇签版本配置',
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }

    });


$.CurrentNavtab.find('#systemversionlist-table').on('afterLoad.bjui.datagrid', function() {
        var data = $.CurrentNavtab.find('#systemversionlist-table').data('allData');

        for (var i = data.length - 1; i >= 0; i--) {
            if(data[i].status==1) {
               
            $(this).datagrid('selectedRows', i, function () {
                $(this).css({'color':'red','font-weight':'600'});
            });
            $(this).datagrid('selectedRows', i, false);
                 
            }
        }
});

</script>