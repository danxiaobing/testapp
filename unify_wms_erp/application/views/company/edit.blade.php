    <script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json" enctype="multipart/form-data">
        <input type="hidden" id="treedata" name="treedata">
        <input type="hidden" name="id" value="{{$id}}">
        <div class="bjui-row col-3">

            <label class="row-label">公司编号</label>
            <div class="row-input required">
                <input type="text" name="companyno" value="{{$companyno}}" data-rule="required;number">
            </div>


            <label class="row-label">公司名称</label>
            <div class="row-input required">
                <input type="text" name="companyname" value="{{$companyname}}" data-rule="required">
            </div>

            <label class="row-label">公司简称</label>
            <div class="row-input">
                <input type="text" name="companysname" value="{{$companysname}}">
            </div>

            <label class="row-label">仓库地址</label>
            <div class="row-input">
                <input type="text" name="warehouseaddress" value="{{$warehouseaddress}}">
            </div>

            <label class="row-label">联系电话</label>
            <div class="row-input">
                <input type="text" name="contacttel" value="{{$contacttel}}">
            </div>

            <label class="row-label">联系传真</label>
            <div class="row-input">
                <input type="text" name="contactfax" value="{{$contactfax}}">
            </div>

            <label class="row-label">邮政编码</label>
            <div class="row-input">
                <input type="text" name="postcode" value="{{$postcode}}" data-rule="zipcode">
            </div>

            <label class="row-label">法人代表</label>
            <div class="row-input">
                <input type="text" name="legalperson" value="{{$legalperson}}" data-rule="chinese" >
            </div>

            <label class="row-label">开户行名称</label>
            <div class="row-input">
                <input type="text" name="bank" value="{{$bank}}" >
            </div>

            <label class="row-label">开户行账号</label>
            <div class="row-input">
                <input type="text" name="bank_account" value="{{$bank_account}}" >
            </div>

            <label class="row-label">是否默认开票公司</label>
            <div class="row-input required">
                <input type="radio" name="isdefault"  data-toggle="icheck" value="0" data-rule="checked" data-label="否&nbsp;&nbsp;" @if( !$isdefault || $isdefault ==0) checked @endif>
                <input type="radio" name="isdefault"  data-toggle="icheck" value="1" data-label="是" @if($isdefault ==1) checked @endif>
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

     <li><button type="button" id="treesubmit" class="btn-success" data-icon="save">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
       
    </ul>
</div>