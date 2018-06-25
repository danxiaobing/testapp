<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#userlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">用户账号</label>
                <div class="row-input">
                    <input type="text" name="username" value="{{$username or ''}}" placeholder="用户账号">
                </div>
                <label class="row-label">真实姓名</label>
                <div class="row-input">
                    <input type="text" name="realname" value="{{$realname or ''}}" placeholder="真实姓名">
                </div>
                <label class="row-label">账号状态</label>
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
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom:$.CurrentNavtab.find('#user_list_tb'),
        dataUrl: 'user/userlistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'600',height:'600',title:'账号管理',mask:true}},
        editUrl: '/user/useredit/id/{sysno}',
        delUrl:'/user/userdeljson',
        delPK:'sysno',
        fullGrid:true,
        paging: {pageSize:10},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'username',align:'center'}">用户账号</th>
                <th  data-options="{name:'realname',align:'center'}">真实名称</th>
                {{--<th data-options="{name:'employee_sysno',align:'center'}">信息表ID</th>--}}
                <th data-options="{name:'lastlogintime',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">最后登录时间</th>
                <th data-options="{name:'lastloginip',align:'center'}">最后登陆IP</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
                <th data-options="{name:'lockstatus',align:'center',render:function(value){return value =='0' ? '否' : '是'}}">锁定</th>
                <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="user_list_tb">
    <button type="button" class="btn btn-green" onclick="deblocking()">解锁</button>
</div>
<script type="text/javascript">
        function deblocking() {
            var data  =  $.CurrentNavtab.find('#userlist-table').data('selectedDatas');
            if (data == '' || data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
            BJUI.ajax('doajax', {
            url: '/user/deblocking/id/'+data[0]['sysno'],
            loadingmask: true,
            okCallback: function(json, options) {
                if (json.code == 200) {
                    BJUI.alertmsg('info','解锁成功',{displayPosition:'middlecenter',displayMode:'fade'});
                    BJUI.navtab('reload','menu192');
                }else{
                    BJUI.alertmsg('info','解锁失败',{displayPosition:'middlecenter',displayMode:'fade'});
                }
                
            }
        })
        }
</script>