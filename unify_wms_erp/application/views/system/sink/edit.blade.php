<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <input type="hidden" name="id" value="{{$sysno}}">
        <input type="hidden" id="treedata" name="treedata">
        <input type="hidden" name="parentId" id="parentId" value="{{$department_sysno}}">
        <div class="bjui-row col-1">

            <label class="row-label">选择部门:</label>
            <div class="row-input required">
                <input type="text" name="departmentname" id="j_ztree_menus2" data-toggle="selectztree" size="18" data-tree="#j_select_tree2" readonly value="{{$departmentname}}" data-rule="required">
                <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true" data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck" data-on-click="S_NodeClick">
                    @foreach($departmentsname as $dename)
                        <li data-id="{{$dename['sysno']}}" data-pid="{{$dename['parent_sysno']}}" @if($dename['sysno'] == $department_sysno ) data-checked='true' @endif >{{$dename['departmentname']}}</li>
                    @endforeach
                </ul>
            </div>

            <label class="row-label">评审类别：</label>

            <div class="row-input required">
                <input type="radio" name="reviewtype" data-toggle="icheck" value="1" data-rule="checked"
                       data-label="合同评审&nbsp;&nbsp;" @if( !$reviewtype || $reviewtype ==1) checked @endif>
                <input type="radio" name="reviewtype" data-toggle="icheck" value="2" data-label="其他"
                       @if($reviewtype ==2) checked @endif>
            </div>

            <label class="row-label">所属版本：</label>

            <div class="row-input required">
                <select data-toggle="selectpicker" data-width="65%" name="version_sysno">
                        <option value="0"  >请选择版本</option>
                    @foreach($version as $item)
                        <option value="{{ $item['sysno'] }}" @if($version_sysno==$item['sysno']) selected @endif > {{ $item['versionname'] }} </option>
                    @endforeach
                </select>
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
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
        <li>
            <button type="button" id="treesubmit" class="btn-green" data-icon="save">保存</button>
        </li>
    </ul>
</div>