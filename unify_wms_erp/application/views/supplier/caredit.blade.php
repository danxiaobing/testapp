<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">

                <label class="row-label">车牌号</label>
                <div class="row-input required">
                    <input type="text" name="carid" value="{{$carid}}" data-rule="required"></div>

                <label class="row-label">司机姓名</label>
                <div class="row-input">
                    <input type="text" name="carname" value="{{$carname}}"></div>

                <label class="row-label">手机</label>
                <div class="row-input">
                    <input type="text" name="mobilephone" value="{{$mobilephone}}" data-rule="mobile" ></div>

                <label class="row-label">身份证号</label>
                <div class="row-input">
                    <input type="text" name="idcard" value="{{$idcard}}" data-rule="IDcard" ></div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <input type="text" name="carmarks" value="{{$carmarks}}" ></div>

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