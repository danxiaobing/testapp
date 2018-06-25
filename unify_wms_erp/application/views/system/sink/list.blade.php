<div class="bjui-pageHeader " style="background-color:#fff; border-bottom:none;padding: 0;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#systemsinklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">汇签部门</label>
                <div class="row-input">
                    <input type="text" name="departmentname" value="{{$departmentname}}" placeholder="汇签部门">
                </div>
                <label class="row-label">汇签部门状态</label>
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
    <table class="table table-bordered" id="systemsinklist-table" data-toggle="datagrid" data-options="{
        height:'100%',
        gridTitle : '汇签部门',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        dataUrl: 'sendversion/sinklistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'500',height:'300',title:'汇签部门',mask:true,id:'navab11'}},
        editUrl: '/sink/sinkedit/id/{sysno}',
        delUrl:'/sink/delete',
        delPK:'sysno',
        paging: {pageSize:10},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'sortnum',align:'center'}">排序号</th>
            <th data-options="{name:'department_sysno',align:'center'}">所属部门表主键</th>
            <th data-options="{name:'departmentname',align:'center'}">冗余部门名称</th>
            <th data-options="{name:'reviewtype',align:'center',render:function(value){return value == '1'?'合同评审':'其他'}}">评审类别</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">最后更新时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
