<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json" enctype="multipart/form-data">
        <input type="hidden" id="treedata" name="treedata">
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="parentId" id="parentId" value="{{$department_sysno}}">
        <div class="bjui-row col-2">
            <label class="row-label">员工照片</label>
            <div class="row-input">
                <input type="hidden" name="old_employeephoto" value="{{$employeephoto}}" />
                <input type="hidden" name="employeephoto" id="employeephoto" value="">
                <input type="file" data-name="custom.pic" data-toggle="webuploader" data-options="
                {
                    pick: {label: '点击选择图片'},
                    server: '/employee/ajaxUpload',
                    fileNumLimit: 1,
                    formData: {'backid':'employeephoto'},
                    required: false,
                    uploaded: '{{$employeephoto}}',
                    accept: {
                        title: '图片',
                        extensions: 'jpg,png,pdf,txt',
                        mimeTypes: '.jpg,.png,.pdf,.txt'
                    }
                }">
            </div>
            <br />
            <label class="row-label">员工姓名</label>
            <div class="row-input required">
                <input type="text" name="employeename" value="{{$employeename}}" data-rule="required;chinese">
            </div>

            <label class="row-label">员工编号</label>
            <div class="row-input required">
                <input type="text" name="employeeno" value="{{$employeeno}}" data-rule="required">
            </div>

            <label class="row-label">电子邮箱</label>
            <div class="row-input">
                <input type="text" name="employeeemail" value="{{$employeeemail}}" data-rule="email">
            </div>

            <label class="row-label">联系电话</label>
            <div class="row-input">
                <input type="text" name="employeecontacttel" value="{{$employeecontacttel}}">
            </div>

            <label class="row-label">职称</label>
            <div class="row-input">
                <input type="text" name="employeetitle" value="{{$employeetitle}}" data-rule="chinese">
            </div>

            <label class="row-label">所属部门：</label>

            <div class="row-input required">
                <input type="text" name="department_sysno" id="j_ztree_menus2"  data-toggle="selectztree"  
                       data-tree="#j_select_tree2" readonly  value="{{$departmentname}}" data-rule="required">
                <ul id="j_select_tree2" class="ztree hide" data-toggle="ztree" data-expand-all="true"
                    data-check-enable="true" data-chk-style="radio" data-radio-type="all" data-on-check="S_NodeCheck"
                    data-on-click="S_NodeClick">
                    @foreach($department as $info)
                        <li data-id="{{$info['sysno']}}" data-pid="{{$info['parent_sysno']}}"
                            @if($info['sysno'] == $department_sysno ) data-checked='true' @endif >{{$info['departmentname']}}
                        </li>
                    @endforeach
                </ul>

            </div>

            <label class="row-label">所属岗位: </label>
            <div class="row-input">
                <select name="position_sysno" data-toggle="selectpicker" data-width="100%">
                    <option value="-100">请选择岗位</option>
                    @foreach($position as $did=>$item)
                        <option value="{{$did}}" @if($did == $position_sysno) selected @endif>{{$item}}</option>
                    @endforeach
                </select>
            </div>

            <label class="row-label">员工号</label>
            <div class="row-input">
                <input type="text" name="employeeid" value="{{$employeeid}}" data-rule="number">
            </div>


            <label class="row-label">性别</label>
            <div class="row-input">
                <input type="radio" name="employeegender"  data-toggle="icheck" value="0" data-label="女&nbsp;&nbsp;" @if($employeegender ==0) checked @endif>
                <input type="radio" name="employeegender"  data-toggle="icheck" value="1" data-label="男" @if($employeegender ==1) checked @endif>
            </div>


            <label class="row-label">出生日期</label>
            <div class="row-input">
                <input type="text" name="employeebirthdate" value="{{$employeebirthdate}}" data-toggle="datepicker" >
            </div>

            <label class="row-label">员工民族</label>
            <div class="row-input">
                <input type="text" name="employeenation" value="{{$employeenation}}" data-rule="chinese">
            </div>

            <label class="row-label">员工籍贯</label>
            <div class="row-input">
                <input type="text" name="employeeorigin" value="{{$employeeorigin}}" data-rule="chinese">
            </div>

            <label class="row-label">婚姻状况</label>
            <div class="row-input">
                <input type="text" name="employeemarital" value="{{$employeemarital}}" data-rule="chinese">
            </div>

            <label class="row-label">政治面貌</label>
            <div class="row-input">
                <input type="text" name="employeepolitics" value="{{$employeepolitics}}" data-rule="chinese">
            </div>

            <label class="row-label">学历</label>
            <div class="row-input">
                <input type="text" name="employeeeducation" value="{{$employeeeducation}}" data-rule="chinese">
            </div>

            <label class="row-label">专业</label>
            <div class="row-input">
                <input type="text" name="employeemajor" value="{{$employeemajor}}" data-rule="chinese">
            </div>

            <label class="row-label">毕业院校</label>
            <div class="row-input">
                <input type="text" name="employeeuniversity" value="{{$employeeuniversity}}" data-rule="chinese">
            </div>

            <label class="row-label">联系地址</label>
            <div class="row-input">
                <input type="text" name="employeecontactaddress" value="{{$employeecontactaddress}}">
            </div>

            <label class="row-label">身份证号</label>
            <div class="row-input">
                <input type="text" name="employeeidnumber" value="{{$employeeidnumber}}" data-rule="IDcard">
            </div>

            <label class="row-label">银行帐号</label>
            <div class="row-input">
                <input type="text" name="employeebankaccount" value="{{$employeebankaccount}}" data-rule="number">
            </div>

            <label class="row-label">入职日期</label>
            <div class="row-input">
                <input type="text" name="employeeentrydate" value="{{$employeeentrydate}}" data-toggle="datepicker" >
            </div>

            <label class="row-label">合同期限</label>
            <div class="row-input">
                <input type="text" name="employeecontractperiod" value="{{$employeecontractperiod}}" data-rule="date">
            </div>

            <label class="row-label">聘用形式</label>
            <div class="row-input">
                <input type="text" name="employeeemploymentform" value="{{$employeeemploymentform}}" data-rule="chinese">
            </div>

            <label class="row-label">简历</label>
            <div class="row-input">
                <textarea type="text" name="employeeresume" >{{$employeeresume}}</textarea>
            </div>

            <label class="row-label">备注</label>
            <div class="row-input">
                <textarea type="text" name="employeeremarks" >{{$employeeremarks}}</textarea>
            </div>

            <label class="row-label">在职状态</label>
            <div class="row-input">
                <input type="radio" name="employeeinservicestate"  data-toggle="icheck" value="1" data-label="在职&nbsp;&nbsp;" @if($employeeinservicestate ==1) checked @endif>
                <input type="radio" name="employeeinservicestate"  data-toggle="icheck" value="2" data-label="离职" @if($employeeinservicestate ==2) checked @endif>
            </div>

            <label class="row-label">状态</label>
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