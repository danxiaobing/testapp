<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" name="id" value="{{$sysno}}">
            <input type="hidden" name="parentId" id="parentId" value="{{$parent_sysno}}">
            <div class="bjui-row col-2">
                <label class="row-label">货品编号</label>
                <div class="row-input required">
                    <input type="text" name="goodsno" value="{{$goodsno}}"  data-rule="required;" >
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input required ">
                    <input type="text" name="goodsname" value="{{$goodsname}}">
                </div>

                <label class="row-label">货品分类</label>
                <div class="row-input">

                    <input type="text" name="parent_sysno" id="j_ztree_menus2" data-toggle="selectztree" size="28" data-tree="#j_select_tree2" readonly value="{{$bgp_name}}" >
                    <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true" data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck" data-on-click="S_NodeClick">
                        @foreach($goodsinfo as $info)
                            <li data-id="{{$info['sysno']}}" data-pid="{{$info['parent_sysno']}}" @if($info['sysno'] == $parent_sysno ) data-checked='true' @endif >{{$info['goodsname']}}</li>
                        @endforeach
                    </ul>
                </div>

                <label class="row-label">CA编号</label>
                <div class="row-input required">
                    <input type="text" name="displayno" value="{{$displayno}}"  data-rule="required;" @if(isset($id)) @endif>
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
