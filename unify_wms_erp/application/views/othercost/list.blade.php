<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#othercostlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">收费名称</label>
                <div class="row-input">
                    <input type="text" name="othercostname" value="{{$othercostname}}" placeholder="收费名称">
                </div>
                <label class="row-label">收费名称状态</label>
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
    <table class="table table-bordered" id="othercostlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom:'#othercost-button',
        dataUrl: 'othercost/datail',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1000',height:'360',title:'收费管理',mask:true}},
        editUrl: '/othercost/othercostaddedit/id/{sysno}',
        delUrl:'/othercost/deleteothercost',
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
            <th data-options="{name:'othercostname',align:'center'}">收费名称</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'othercostprice',align:'center'}">价格</th>
            <!-- <th data-options="{name:'othercostmarks',align:'center'}">备注</th> -->
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="othercost-button">
    <button type="button" id="othercost_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="othercost_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
</div>
<script type="text/javascript">
    $('#othercost_start').click(function(){
        var arr = $('#othercostlist-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }

        BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                        okCall: function() {
                            BJUI.ajax('doajax', {
                                url: '/othercost/othercostChange/',
                                type: 'POST',
                                data: {data:data,state:'start'},
                                okcallback:function(option){

                                }
                            });                    
                        }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });


        $('#othercost_stop').click(function(){
        var arr = $('#othercostlist-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }

        BJUI.alertmsg('confirm', '确定要批量禁用吗！', {
                        okCall: function() {
                            BJUI.ajax('doajax', {
                                url: '/othercost/othercostChange/',
                                type: 'POST',
                                data: {data:data,state:'stop'},
                                okcallback:function(option){

                                }
                            });                    
                        }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });
</script>
