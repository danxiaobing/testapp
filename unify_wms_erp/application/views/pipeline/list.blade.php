<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" action="{{$action}}" data-options="{searchDatagrid:$.CurrentNavtab.find('#pipeline-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">管线名称</label>
                <div class="row-input">
                    <input type="text" name="pipelinename" value="" placeholder="管线名称">
                </div>

                <label class="row-label">管线类型</label>
                <div class="row-input">
                    <select name="pipelinetype" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        <option value="1">码头管线</option>
                        <option value="2">库区管线</option>
                    </select>
                </div>
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="bar_status" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >不限</option>
                        <option value="1">启用</option>
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
    <table class="table table-bordered" id="pipeline-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarCustom:'#pipeline_btn',
        dataUrl: 'pipeline/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:11},
        showCheckboxcol: false,
        editMode:false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'pipelinename',align:'center',width:200}">管线号</th>
                <th data-options="{name:'pipelinetype',align:'center',render:function(value){if(value==1){return '码头管线';}else{return '库区管线';}}}">管线类型</th>
                <th  data-options="{name:'pipelineflow',align:'center'}">流量/分钟（吨）</th>
                <th data-options="{name:'installtime',align:'center',type:'date',pattern:'yyyy-MM-dd'}">安装时间</th>
                <th data-options="{name:'status',align:'center',render:function(value){if(value==1){return '启用';}else{return '停用';}} }">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="pipeline_btn">
    <button type="button" id="pipeline_add" class="btn btn-blue" data-icon="plus" >添加</button>
    <button type="button" id="pipeline_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" id="pipeline_del" class="btn btn-red" data-icon="times" onclick="del()" >删除</button>
    <button type="button" id="pipeline_unlock" class="btn btn-green" data-icon="unlock-alt" onclick="setstatus(1)">启用</button>
    <button type="button" id="pipeline_lock" class="btn btn-blue"  data-icon="hand-paper-o" onclick="setstatus(2)">停用</button>
    <button type="button"  class="btn btn-green" data-icon="gavel" onclick="pipiline_history()">使用历史</button>
    <button type="button"  class="btn btn-red" data-icon="scissors" onclick="pipeline_clear()" >洗管</button>
</div>

<script>
    $('#pipeline_add').click(function(){
        BJUI.dialog({
            id:'pipeline-add',
            width:900,
            height:500,
            mask:true,
            type:'POST',
            url:'/pipeline/pipelineedit',
            title:'添加管线',
        });
    });

    $('#pipeline_edit').click(function(){

        var checkdata=$('#pipeline-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;

            BJUI.dialog({
                id:'pipeline-edit'+id,
                width:900,
                height:500,
                mask:true,
                type:'POST',
                url:'/pipeline/pipelineedit',
                data:{sysno:id},
                title:'管线管理'
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选择数据</h4>');
        }
    });


    function setstatus(step)
    {
        var data = $('#pipeline-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];

            var status = data[0]['status'];

            if(status == step){
                BJUI.alertmsg('warn','<h4>不要重复操作</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var alertmsg = '';
            if(step==1){
                alertmsg = '确定要启用吗?';
            }else{
                alertmsg = '确定要停用吗?';
            }
            BJUI.alertmsg('confirm', alertmsg, {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/pipeline/setstatus',
                        type:'POST',
                        loadingmask: true,
                        data:{id:sysno, status:step},
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu486');
                        }
                    })
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    function del()
    {
        var data = $('#pipeline-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];


            BJUI.alertmsg('confirm', '确定删除吗', {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/pipeline/delpipeline',
                        type:'POST',
                        loadingmask: true,
                        data:{id:sysno},
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu486');
                        }
                    })
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }


    function pipiline_history()
    {
        var data = $('#pipeline-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'pipeline-history'+sysno,
                mask:true,
                type:'POST',
                url:'/pipeline/pipelinehistory/id/'+sysno,
                data:{id:sysno},
                title:'管线使用历史'
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    function pipeline_clear()
    {
        var data = $('#pipeline-table').data('selectedDatas');

        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];
            var pipelinename = data[0]['pipelinename']; 
            BJUI.dialog({
                id:'pipeline-clear',
                width:1200,
                height:800,
                mask:true,
                type:'POST',
                url:'/pipeline/pipelineClear',
                data:{id:sysno,pipelineno:pipelinename},
                title:'管线清理'
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }

    }
</script>