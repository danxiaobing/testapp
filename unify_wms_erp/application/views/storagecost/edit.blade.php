<div class="bjui-pageContent">
    <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <input type="hidden" id="treedata" name="treedata">
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="parentId" id="parentId" value="{{$goods_sysno}}">
        <div class="bjui-row col-3">

            <label class="row-label">管理编号：</label>

            <div class="row-input">
                <input type="text" name="storagecostno" value="@if($storagecostno) {{$storagecostno}} @else {{系统自动生成}} @endif" readonly>
            </div>

            <label class="row-label">标准名称：</label>

            <div class="row-input">
                <input type="text" name="storagecostname"
                       value="@if($storagecostname) {{$storagecostname}} @endif" >
            </div>

            <label class="row-label">货品名称：</label>
            <div class="row-input required">

                <input type="text" name="goods_sysno" id="j_ztree_menus2" data-toggle="selectztree"  data-tree="#j_select_tree2" readonly value="{{$goodsname}}" data-rule="required" >
                <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true" data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck" data-on-click="S_NodeClick">
                    @foreach($goods as $info)
                        <li data-id="{{$info['sysno']}}" data-pid="{{$info['parent_sysno']}}" @if($info['sysno'] == $goods_sysno ) data-checked='true' @endif >{{$info['goodsname']}}</li>
                    @endforeach
                </ul>


            </div>
            <label class="row-label">储罐材料：</label>

            <div class="row-input">
                <select name="storagetank_category_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%">
                    <option value="">请选择</option>
                    @foreach($storagetank_category as $item)
                        <option value="{{$item['sysno']}}"
                                @if($item['sysno'] == $storagetank_category_sysno) selected @endif>
                            {{$item['storagetank_categoryname']}}
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="row-label">计量单位：</label>

            <div class="row-input">
                <select name="unit" data-toggle="selectpicker" data-rule="required" data-width="100%">
                    <option value="">请选择</option>
                    @foreach($units as $item)
                        <option value="{{$item['sysno']}}"
                                @if($item['sysno'] == $unit) selected @endif>
                            {{$item['unitname']}}
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="row-label">首期单价(30天)：</label>

            <div class="row-input required">
                <input type="text" data-rule="required;number" name="startingprice" value="{{$startingprice}}">
            </div>

            <label class="row-label">超期单价(天)：</label>

            <div class="row-input required">
                <input type="text" data-rule="required;number" name="overdueprice" value="{{$overdueprice}}">
            </div>

            <label class="row-label">最小启存量(吨)：</label>

            <div class="row-input required">
                <input type="text" data-rule="required;number" name="minstock" value="{{$minstock}}">
            </div>


            <label class="row-label">仓储费类型：</label>

            <div class="row-input required">
                <input type="radio" name="storagecosttype" data-toggle="icheck" value="1" data-rule="checked"
                       data-label="长约&nbsp;&nbsp;" @if( !$storagecosttype || $storagecosttype ==1) checked @endif>
                <input type="radio" name="storagecosttype" data-toggle="icheck" value="2" data-label="短约"
                       @if($storagecosttype ==2) checked @endif>
            </div>

            <label class="row-label">状态：</label>

            <div class="row-input required">
                <input type="radio" name="status" data-toggle="icheck" value="1" data-rule="checked"
                       data-label="可用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                <input type="radio" name="status" data-toggle="icheck" value="2" data-label="停用"
                       @if($status ==2) checked @endif>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
     <li>
            <button type="button" id="treesubmit" class="btn-success" data-icon="save">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>

<script>

    //提交节点dataid
    $("#treesubmit").click(function(event) {

        var treestring = "";

        $("#qx-checkNode ul.ztree").each(function() {

            var treeObj=$.fn.zTree.getZTreeObj($(this).attr("id"));

            var nodesChecked = treeObj.getCheckedNodes(true);

            for (var i = nodesChecked.length - 1; i >= 0; i--) {
                treestring+=nodesChecked[i].id+",";
            }
        });

        $("#treedata").val(treestring.substr(0,treestring.length-1));
        $('#treeform').submit();
    });


    //选择事件
    function S_NodeCheck(e, treeId, treeNode) {
        var zTree = $.fn.zTree.getZTreeObj(treeId),
            nodes = zTree.getCheckedNodes(true)
        var ids = '', names = '',unitids='',unitname=''

        for (var i = 0; i < nodes.length; i++) {
            ids   += ','+ nodes[i].id
            names += ','+ nodes[i].name

        }
        if (ids.length > 0) {
            ids = ids.substr(1), names = names.substr(1)
        }

        var $from = $('#'+ treeId).data('fromObj')

        if ($from && $from.length){
            $from.val(names);
            $("#parentId").val(ids);

        }
    }
    //单击事件
    function S_NodeClick(event, treeId, treeNode) {
        var zTree = $.fn.zTree.getZTreeObj(treeId)

        zTree.checkNode(treeNode, !treeNode.checked, true, true)

        event.preventDefault()
    }


</script>