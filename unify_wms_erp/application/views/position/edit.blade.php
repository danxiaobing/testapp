<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" id="treedata" name="treedata">
        <input type="hidden" name="parentId" id="parentId">

        <div class="bjui-row col-2">
            <label class="row-label">岗位编号</label>

            <div class="row-input required">
                <input type="text" name="positionno" value="{{$positionno}}" data-rule="required;number">
            </div>

            <label class="row-label">岗位名称</label>

            <div class="row-input required">
                <input type="text" name="positionname" value="{{$positionname}}" data-rule="required">
            </div>

            <label class="row-label">所属部门</label>

            <div class="row-input required">
                <input type="text" name="department_sysno" id="j_ztree_menus2" data-toggle="selectztree" 
                       data-tree="#j_select_tree2" readonly value="{{$departmentname}}" data-rule="required">
                <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true"
                    data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck"
                    data-on-click="S_NodeClick" >
                    @foreach($departmentlist as $info)
                        <li data-id="{{$info['sysno']}}" data-pid="{{$info['parent_sysno']}}"
                            @if($info['sysno'] == $department_sysno ) data-checked='true' @endif >{{$info['departmentname']}}
                        </li>
                    @endforeach
                </ul>

            </div>

            <label class="row-label">职位描述</label>

            <div class="row-input">
                <input type="text" name="positiondesc" value="{{$positiondesc}}">
            </div>

            <label class="row-label">状态：</label>

            <div class="row-input required">
                <input type="radio" name="status" data-toggle="icheck" value="1" data-rule="checked"
                       data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                <input type="radio" name="status" data-toggle="icheck" value="2" data-label="停用"
                       @if($status ==2) checked @endif>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" id="treesubmit" class="btn-success" data-icon="save">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>