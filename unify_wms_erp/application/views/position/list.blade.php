<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#userlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">岗位编号</label>
                <div class="row-input">
                    <input type="text" name="positionno" value="{{$positionno}}" placeholder="岗位编号">
                </div>
                <label class="row-label">所属部门</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%"  name="department_sysno">
                            <option value="">请选择</option>
                        @foreach($departments as $department)
                            <option value="{{$department['sysno']}}">{{$department['departmentname']}}</option>
                        @endforeach
                    </select>

                </div>
                <label class="row-label">岗位名称</label>
                <div class="row-input">
                    <input type="text" name="positionname" value="{{$positionname}}" placeholder="岗位名称">
                </div>
                <label class="row-label">岗位状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="status">
                        <option value="" selected="">不限</option>
                        <option value="1" >启用</option>
                        <option value="2">停用</option>
                    </select>
                </div>
                <label class="row-label"></label>
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
    <table class="table table-bordered" id="userlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'99%',
        height: '100%',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        dataUrl: 'position/datailJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1000',height:'300',title:'岗位管理',mask:true}},
        editUrl: '/position/positionaddedit/id/{sysno}',
        delUrl:'/position/deleteposition',
        delPK:'sysno',
        paging: {pageSize:10},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'positionno',align:'center'}">岗位编号</th>
            <th data-options="{name:'departmentname',align:'center'}">所属部门</th>
            <th data-options="{name:'positionname',align:'center'}">岗位名称</th>
            <th data-options="{name:'positiondesc',align:'center'}">岗位备注</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
