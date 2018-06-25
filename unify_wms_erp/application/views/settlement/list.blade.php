<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#settlementlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">结算方式</label>
                <div class="row-input">
                    <input type="text" name="settlementname" value="{{$settlementname}}" placeholder="结算方式">
                </div>
                <label class="row-label">结算方式状态</label>
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
                        <!-- <button type="reset" class="btn-orange" data-icon="times">重置</button> -->
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="settlementlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,refresh',
        toolbarCustom:'#settlement-button',
        dataUrl: 'settlement/datail',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'440',height:'250',title:'结算方式管理',mask:true}},
        editUrl: '/settlement/settlementaddedit/id/{sysno}',
        delUrl:'/settlement/deletesettlement',
        delPK:'sysno',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'settlementname',align:'center'}">结算方式</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="settlement-button">
    <button type="button" id="settlemendel" class="btn btn-red" data-icon="times" >删除</button>
    <button type="button" id="settlement_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="settlement_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
</div>
<script type="text/javascript">
    //批量删除
    $('#settlemendel').click(function(){
        var checkdata=$('#settlementlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量删除吗！', {
                okCall: function() {
                    //回调操作

                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Settlement/deletesettlement/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu182');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }
    });

//批量添加
    $('#settlement_start').click(function(){
        var checkdata=$('#settlementlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    //回调操作
                        var date = [];
                        for(i=0;i<checkdata.length;i++){
                            date[i] = checkdata[i].sysno;
                        }
                        BJUI.ajax('doajax',{
                            type : 'POST',
                            url:'/Settlement/statuschange/',
                            data:{date : date },
                            okCallback: function(json, options) {
                                BJUI.navtab('refresh','menu182');
                            }
                        });
                        BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }



    });
    //批量停用
    $('#settlement_stop').click(function(){
        var checkdata=$('#settlementlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    //回调操作
                        var date = [];
                        for(i=0;i<checkdata.length;i++){
                            date[i] = checkdata[i].sysno;
                        }
                        BJUI.ajax('doajax',{
                            type : 'POST',
                            url:'/Settlement/statusover/',
                            data:{date : date },
                            okCallback: function(json, options) {
                                BJUI.navtab('refresh','menu182');
                            }
                        });
                        BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }

    });

</script>