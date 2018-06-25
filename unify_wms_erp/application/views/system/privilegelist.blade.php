<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;padding: 0;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#privilegelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">权限名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="权限名称"></div>

                <label class="row-label">所属模块</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_parentid">
                        <option value="-100" selected="">不限</option>
                        @foreach($rootlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['privilegename']}}</option>
                        @endforeach
                    </select>
                </div>


                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
                        <option value="0">已禁用</option>
                        <option value="1" >已启用</option>
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
    <table class="table table-bordered" id="privilegelist-table" data-toggle="datagrid" data-options="{
        tableWidth:'99%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        dataUrl: 'system/privilegelistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1000',height:'500',title:'权限管理',mask:true}},
        editUrl: '/system/privilegeedit/id/{sysno}',
        delUrl:'/system/privilegedeljson',
        delPK:'sysno',
        paging: {pageSize:10},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'privilegeno',align:'center',width:100}">权限编码</th>
                <th data-options="{name:'privilegename',align:'center',width:100}">权限名称</th>
                <th  data-options="{name:'privilegeresource',align:'center',width:250}">权限url</th>
                <th data-options="{name:'parent_privilegename',align:'center',width:100}">所属父权限</th>
                <th data-options="{name:'parentsysnotype',align:'center',width:120,render:function(value){  switch(value){ case '1' : return '菜单'; case '2': return '显示权限'; case '3': return '操作权限';} }}">权限类型</th>
                <th data-options="{name:'parentsysnoicon',align:'center',width:120}">权限图标</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
                <th data-options="{name:'status',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
                <th  data-options="{name:'privilegedesc',align:'center'}">权限说明</th>
            </tr>
        </thead>
    </table>
</div>
