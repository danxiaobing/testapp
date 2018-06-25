<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#printtitleindex-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">公司编号</label>
                <div class="row-input">
                    <input type="text" name="titlename" value="{{$titlename}}" placeholder="抬头名称">
                </div>
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="isdefault">
                        <option value="" selected="">不限</option>
                        <option value="1" >启用</option>
                        <option value="0">停用</option>
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

    <table class="table table-bordered" id="printtitleindex-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add',
        toolbarCustom:'#printtitle-button',
        dataUrl: '/printtitle/getlistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'600',height:'400',title:'票据抬头管理',mask:true}},
        editUrl: '/Printtitle/PrintTitleEdit/id/{sysno}',
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
            {{--<th data-options="{name:'titlename',align:'center',width:80}">序号</th>--}}
            <th data-options="{name:'titlename',align:'center',width:80}">票据抬头名称</th>
            <th data-options="{name:'created_at',align:'center',width:150}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',width:150}">修改时间</th>
            <th data-options="{name:'titlemarks',align:'center',width:150}">备注</th>
            <th data-options="{name:'isdefault',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="printtitle-button">
    <button type="button" id="printtitle_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" id="del_print" class="btn btn-red" data-icon="times" >删除</button>
    {{--<button type="button" id="printtitle_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>--}}
    {{--<button type="button" id="printtitle_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>--}}
</div>
<script type="text/javascript">
    //编辑按钮
    $('#printtitle_edit').click(function(){
        var checkdata = $('#printtitleindex-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;

            BJUI.dialog({
                id:'print-title-edit',
                width:800,
                height:400,
                mask:true,
                type:'POST',
                url:'/Printtitle/PrintTitleEdit/id/'+id,
//                data:{sysno:id},
                title:'票据抬头管理-编辑'
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选择数据</h4>');
        }
    });

    //批量删除
    $('#del_print').click(function(){
        var checkdata=$('#printtitleindex-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm','确认批量删除吗！',{
                okCall : function(){
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Printtitle/printTitleDelJson/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh', 'menu525');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            });
        }
    });

    //批量启用
    $('#printtitle_start').click(function(){
        var checkdata=$('#printtitle-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm','确定批量启用吗？',{
                okCall :function(){
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Company/statuschange/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu185');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }
    });

    //批量停止
    $('#printtitle_stop').click(function(){
        var checkdata=$('#printtitle-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    var checkdata=$('#printtitle-table').data('selectedDatas');
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Company/statusover/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu185');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }
    });

</script>