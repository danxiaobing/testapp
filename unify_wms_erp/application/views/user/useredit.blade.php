<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" id="treedata" name="treedata">
            <input type="hidden" id='useredit_id' name="id" value="{{$id}}">
            <div class="bjui-row col-1">

                <label class="row-label">用户名</label>
                <div class="row-input required">
                    <input type="text" name="username" value="{{$username}}" data-rule="required;email">
                </div>
                <form style="display:none">
                  <!-- <input type="password"/> -->
                <input type="password" style="width:0;height:0;visibility:hidden"/>
                </form>
                <label class="row-label">用户密码</label>
                <div class="row-input @if($id==0){{required}}@endif">
                    <input type="password" name="userpwd" value="" @if($id==0) data-rule='required' @endif>
                </div>

                <label class="row-label">绑定员工</label>
                <div class="row-input required">
                    <select id="selectemployee" name="employee_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" data-rule="required">
                        <option value="">请选择</option>
                        @foreach($employeelist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $employee_sysno) selected @endif>{{$item['employeename']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">真实姓名</label>
                <div class="row-input required">
                    <input type="text" id="realname" name="realname" value="{{$realname}}"  data-rule='required'>
                </div>

                <label class="row-label">状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($status ==2) checked @endif>
                </div>
                    <label class="row-label">用户角色分配</label>
                    <div id="qx-checkNode" class="row-input">
                     <div class="row">
                        @if(empty($userRoles))
                            @foreach ($rolelist as $mgroup)
                            <div class="col-xs-10">
                                <input type="checkbox" name="role[]" data-toggle="icheck" value="{{$mgroup['sysno']}}" data-label="{{$mgroup['rolename']}}" />
                                </div>
                                <br>
                            @endforeach
                        @else
                            @foreach ($rolelist as $mgroup)
                            <div class="col-xs-10" >
                                    <input type="checkbox" name="role[]" data-toggle="icheck" value="{{$mgroup['sysno']}}" data-label="{{$mgroup['rolename']}}"
                                           @foreach($userRoles as $roles)
                                           @if($roles['role_sysno']==$mgroup['sysno']) checked="" @endif
                                           @endforeach
                                    />
                                    </div>
                                <br>
                            @endforeach
                        @endif
                        </div>
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
<script>

    $("#selectemployee").change(function () {
        var realname = $("#realname").val();
        if(!realname){
            $("#realname").val($("#selectemployee option:selected").text());
        }  
    })

    
</script>