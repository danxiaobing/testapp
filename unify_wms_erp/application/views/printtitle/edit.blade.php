<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-data-type="json" enctype="multipart/form-data">
        <input type="hidden" id="printData" name="printData">
        <input type="hidden" name="id" value="{{$id}}">
        <div class="bjui-row col-5">
            <label class="row-label">票据抬头名称:</label>
            <div class="row-input required">
                <input type="text" name="titlename" value="{{$titlename}}" data-rule="required">
            </div>
        </div>
        <div class="bjui-row col-5">
            <label class="row-label">备注:</label>
            <div class="row-input required">
                <input type="text" name="titlemarks" value="{{$titlemarks}}" data-rule="required">
            </div>
        </div>
        <div class="bjui-row col-5">
            <label class="row-label">状态:</label>
            <div class="row-input required">
                <input type="radio" name="isdefault"  data-toggle="icheck" value="0" data-label="停用" @if( !$isdefault || $isdefault == 0 ) checked @endif>
                <input type="radio" name="isdefault"  data-toggle="icheck" value="1" data-label="启用" @if( $isdefault == 1 ) checked @endif>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" onclick="submit()"class="btn-success" >保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>
<script type="text/javascript">
    function submit()
    {
        BJUI.ajax('ajaxform',{
            url: "{{$action}}",
            form: $.CurrentDialog.find('#treeform'),
            type: 'POST',
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.dialog('closeCurrent','');
                BJUI.navtab('refresh', 'menu525');
            }
        });
    }
</script>
