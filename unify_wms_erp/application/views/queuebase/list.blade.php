<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#queuebase-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">方案状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_queuetype">
                        <option value="" selected="">全部</option>
                        <option value="1">鹤位号</option>
                        <option value="2">储罐号</option>
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
    <table class="table table-bordered" id="queuebase-list-table" data-toggle="datagrid" data-options="{
        height:'100%',
        showToolbar: true,
        toolbarCustom : '#queuebase-button',
        dataUrl: 'Queuebase/listJson',
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
            <th data-options="{name:'queuetype',align:'center',width:100,render:function(value){ if(value==1){return '鹤位号';}else{return '储罐号';}} }">类别</th>
            <th data-options="{name:'queueno',width:150,align:'center'}">编号</th>
            <th data-options="{name:'goodsname',align:'center'}">货品</th>
            <th data-options="{name:'queuetime',align:'center',type:'date',render:function(value){return value+'分钟'}}">单位等候时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){ if(value =='1') {return '启用';} else if(value==2){return '停用'}}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="queuebase-button">
    <button type="button" id="queuebase_add" class="btn btn-blue" data-icon="plus" >添加</button>
    <button type="button" id="queuebase_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" class="btn btn-red" data-icon="times" onclick="queuebase_change('del')">删除</button>
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="queuebase_change('start')">启用</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="queuebase_change('stop')">停用</button>
</div>
<script type="text/javascript">
    function queuebase_change(action)
    {
        var data = $('#queuebase-list-table').data('selectedDatas');

        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>请选择单条数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var sysno = data[0]['sysno'];
            var status = data[0]['status'];
            if(status==1 && action=='start'){
                BJUI.alertmsg('warn','<h4>不要重复启用！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            if(status==2 && action=='stop'){
                BJUI.alertmsg('warn','<h4>不要重复停用！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }

            BJUI.ajax('doajax', {
                url: '/Queuebase/queuebasechange/id/'+sysno+'/action/'+action,
                loadingmask: true,
                okCallback: function(json, options) {
                    BJUI.navtab('reloadFlag', 'navab529,navab528,navab527');
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }


    $('#queuebase_add').click(function(){
        BJUI.dialog({
            id:'queuebase-add',
            width:700,
            height:500,
            mask:true,
            url:'/Queuebase/edit',
            title:'添加排号',
        });
    });

    $('#queuebase_edit').click(function(){
     var data = $('#queuebase-list-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1)
            {
                BJUI.alertmsg('warn','<h4>请选择一行数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
            var sysno = data[0]['sysno'];
            BJUI.dialog({
                id:'version-edit',
                url:'/Queuebase/edit',
                data:{id:sysno},
                type:'POST',
                width:700,
                height:500,
                mask:true,
                title:'排号设置',
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }

    });


</script>