<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" name="id" value="{{$sysno}}">
            <div class="bjui-row col-2">


                <label class="row-label">计量单位名称</label>
                <div class="row-input required">
                    <input type="text" name="unitname" value="{{$unitname}}"  data-rule="required">
                </div>
                
                <label class="row-label">计量单位类型</label>
                <div class="row-input required">
                    <input type="radio" name="unittype"  data-toggle="icheck" value="1" data-rule="checked" data-label="重量&nbsp;&nbsp;" @if( !$unittype || $unittype ==1) checked @endif>
                    <input type="radio" name="unittype"  data-toggle="icheck" value="2" data-label="数量" @if($unittype ==2) checked @endif>
                </div>

                <label class="row-label">精度保留几位小数点</label>
                <div class="row-input required">
                    <input type="text" name="decimalpoint" value="{{$decimalpoint}}" data-toggle="spinner" data-min="1" data-rule="required;number">
                </div>

                <label class="row-label">状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($status ==2) checked @endif>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="submit" class="btn-green" data-icon="save">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        
    </ul>
</div>
