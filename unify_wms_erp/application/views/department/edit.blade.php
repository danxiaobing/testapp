<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2016-11-11 13:41:02
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-11-11 14:13:17
 */
?>
<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
<form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <input type="hidden" id="treedata" name="treedata">
    <input type="hidden" name="id" value="{{$id}}">
    <div class="bjui-row col-2">
       {{-- <input type="hidden" name="departmentno" value="{{$departmentno}}" />--}}
        <label class="row-label">部门编号：</label>
        <div class="row-input required">
            <input type="text" name="departmentno" value="{{$departmentno}}" data-rule="required">
        </div>


        <label class="row-label">部门名称：</label>
        <div class="row-input required">
            <input type="text" name="departmentname" value="{{$departmentname}}" data-rule="required">
        </div>

        <label class="row-label">所属部门：</label>
        <div class="row-input required">
            <select name="parent_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%">
                <option value="0">根权限</option>
                @foreach($rootlist as $item)
                    @if($item['sysno']!=$id)
                    <option value="{{$item['sysno']}}" @if($item['sysno'] == $parent_sysno) selected @endif>{{$item['departmentname']}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <label class="row-label">部门描述：</label>
        <div class="row-input">
            <input type="text" name="departmentdesc" value="{{$departmentdesc}}" >
        </div>

        <label class="row-label">状态：</label>
        <div class="row-input required">
            <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
            <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($status ==2) checked @endif>
        </div>
    </div>
</form>
    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" id="treesubmit" class="btn-green" data-icon="save">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>