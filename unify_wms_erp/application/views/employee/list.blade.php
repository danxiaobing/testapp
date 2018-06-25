<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#employeelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">员工编号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" value="{{$bar_no}}" placeholder="请输入员工编号">
                </div>
                <label class="row-label">员工姓名</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="请输入员工姓名"></div>
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
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
    <table class="table table-bordered" id="employeelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        dataUrl: 'employee/employeelistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1200',height:'800',title:'员工管理',mask:true}},
        editUrl: '/employee/employeeedit/id/{sysno}',
        delUrl:'/employee/employeedeljson',
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'employeeno',align:'center'}">员工编号</th>
            <th data-options="{name:'sysno',align:'center'}">员工工号</th>
            <th data-options="{name:'employeename',align:'center'}">员工姓名</th>
            <th data-options="{name:'positionname',align:'center'}">所属岗位</th>
            <th data-options="{name:'employeeemail',align:'center'}">电子邮箱</th>
            <th data-options="{name:'employeecontacttel',align:'center'}">联系电话</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>