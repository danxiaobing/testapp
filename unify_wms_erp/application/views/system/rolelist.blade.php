<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#rolelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">角色编码</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no or ''}}" placeholder="角色编码">
                </div>
                <label class="row-label">角色名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="角色名称">
                </div>
                <label class="row-label">角色状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
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
    <table class="table table-bordered" id="rolelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        addLocation: 'last',
        dataUrl: 'system/rolelistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1200',height:'600',title:'角色管理',mask:true}},
        editUrl: '/system/roleedit/id/{sysno}',
        delUrl:'/system/roledeljson',
        delPK:'sysno',
        fullGrid:true,
        paging: {pageSize:20},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'roleno',align:'center',width:200}">角色编码</th>
                <th data-options="{name:'rolename',align:'center',width:100}">角色名称</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
                <th data-options="{name:'status',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
                <th  data-options="{name:'roledesc',align:'center'}">角色说明</th>
            </tr>
        </thead>
    </table>
</div>