<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">

            <div class="bjui-row col-2">
                <label class="row-label">收费名称：</label>

                <div class="row-input required">
                    <input type="text" name="othercostname" value="{{$othercostname}}" data-rule="required"></div>

                <label class="row-label">计量单位</label>

                <div class="row-input required">
                    {{--
                    <input type="text" name="unit_sysno" value="{{$unit_sysno}}" data-rule="required;number">
                    --}}
                    <select name="unit_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($unitlist as $item)
                        <option value="{{$item['sysno']}}"
                                @if($item['sysno'] == $unit_sysno) selected @endif>{{$item['unitname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">价格：</label>

                <div class="row-input required">
                    <input type="text" name="othercostprice" value="{{$othercostprice}}" data-rule="required;number"></div>

<!--                 <label class="row-label">备注：</label>

                <div class="row-input">
                    <input type="text" name="othercostmarks" value="{{$othercostmarks}}"></div> -->

                <label class="row-label">状态：</label>

                <div class="row-input required">
                    <input type="radio" name="status" data-toggle="icheck" value="1" data-rule="checked"
                       data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                    <input type="radio" name="status" data-toggle="icheck" value="2" data-label="停用"
                       @if($status ==2) checked @endif></div>
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