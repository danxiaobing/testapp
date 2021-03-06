<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json" enctype="multipart/form-data">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">

                <label class="row-label">片区编号</label>
                <div class="row-input required">
                    <input type="text" name="areaid" value="{{$areaid}}" data-rule="required; areaname"  data-rule-areaname="[/^[a-zA-Z0-9]+$/, '编号无效! 仅支持字母与数字。']"  ></div>

                <label class="row-label">片区名称</label>
                <div class="row-input required">
                    <input type="text" name="areaname" value="{{$areaname}}" data-rule="required; "></div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <input type="text" name="areamarks" value="{{$areamarks}}" data-rule=""></div>

                <label class="row-label">状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($status ==2) checked @endif></div>
            </div>
        </form>
    </div>
</div> 
<div class="bjui-pageFooter">
    <ul>
       
        <li>
            <button type="button" id="treesubmit" class="btn-green" data-icon="save">保存</button>
        </li>
         <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
    </ul>
</div>