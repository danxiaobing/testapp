<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">
                <label class="row-label">权限编码</label>
                <div class="row-input required">
                    <input type="text" name="privilegeno" value="{{$privilegeno}}" data-rule="required">
                </div>

                <label class="row-label">权限名称</label>
                <div class="row-input required">
                    <input type="text" name="privilegename" value="{{$privilegename}}" data-rule="required">

                </div>

                <label class="row-label">权限URL</label>
                <div class="row-input ">
                    <input type="text" name="privilegeresource" value="{{$privilegeresource}}" >
                </div>

                <label class="row-label">所属父权限</label>
                <div class="row-input required">
                    <select name="parent_sysno" data-toggle="selectpicker" data-rule="required" data-size="10" data-live-search="true" data-width="100%">
                        <option value="0">根权限</option>
                        @foreach($rootlist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $parent_sysno) selected @endif>{{$item['privilegename']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">权限类型</label>
                <div class="row-input required">
                    <input type="radio" name="parentsysnotype"  data-toggle="icheck" value="1" data-rule="checked" data-label="菜单&nbsp;&nbsp;"  @if(!$parentsysnotype || $parentsysnotype ==1 ) checked @endif >
                    <input type="radio" name="parentsysnotype"  data-toggle="icheck" value="2" data-label="显示权限&nbsp;&nbsp;" @if($parentsysnotype ==2) checked @endif>
                    <input type="radio" name="parentsysnotype"  data-toggle="icheck" value="3" data-label="操作权限" @if($parentsysnotype ==3) checked @endif>
                </div>

                <label class="row-label">权限图标</label>
                <div class="row-input ">
                    <input type="text" name="parentsysnoicon" value="{{$parentsysnoicon}}" >
                </div>

                <label class="row-label">权限Controller</label>
                <div class="row-input ">
                    <input type="text" name="privilegecontroller" value="{{$privilegecontroller}}" >
                </div>

                <label class="row-label">权限Action</label>
                <div class="row-input ">
                    <input type="text" name="privilegeaction" value="{{$privilegeaction}}" >
                </div>

                <label class="row-label">权限说明</label>
                <div class="row-input">
                    <input type="text" name="privilegedesc" value="{{$privilegedesc}}" >
                </div>

                <label class="row-label">显示顺序</label>
                <div class="row-input">
                    <input type="text" name="menuorder" value="{{$menuorder}}" >
                </div>

                <label class="row-label">需要验证</label>
                <div class="row-input required">
                    <input type="radio" name="needcheck"  data-toggle="icheck" value="1" data-rule="checked" data-label="需要&nbsp;&nbsp;"  @if( !isset($needcheck)  || $needcheck ==1 ) checked @endif >
                    <input type="radio" name="needcheck"  data-toggle="icheck" value="0" data-label="不需要&nbsp;&nbsp;" @if(isset($needcheck)  && $needcheck ==0) checked @endif>
                </div>


                <label class="row-label">状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($status ==2) checked @endif>
                </div>

                <label class="row-label">是否可用</label>
                <div class="row-input required">
                    <input type="radio" name="isdel" data-toggle="icheck" value="0" data-rule="checked" data-label="可用&nbsp;&nbsp;" @if($isdel ==0) checked @endif>
                    <input type="radio" name="isdel"  data-toggle="icheck" value="1" data-label="不可用" @if($isdel ==1) checked @endif>
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
