<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">
                <label class="row-label">船编号</label>
                <div class="row-input ">
                    <input type="text" name="shipno" value="{{$shipno}}" >
                </div>

                <label class="row-label">船名</label>
                <div class="row-input required">
                    <input type="text" name="shipname" value="{{$shipname}}" data-rule="required">

                </div>

                <label class="row-label">所属公司</label>
                <div class="row-input">
                    <input type="text" name="company" value="{{$company}}">
                </div>

                <label class="row-label">船长</label>
                <div class="row-input">
                    <input type="text" name="captain" value="{{$captain}}">
                </div>

                <label class="row-label">联系方式</label>
                <div class="row-input">
                    <input type="text" name="shipcontact" value="{{$shipcontact}}" >
                </div>

                <label class="row-label">载重(吨）</label>
                <div class="row-input">
                    <input type="text" name="shiploadweight" value="{{$shiploadweight}}" >
                </div>

                <label class="row-label">长度(m)</label>
                <div class="row-input ">
                    <input type="text" name="shiplength" value="{{$shiplength}}" >
                </div>

                <label class="row-label">宽度(m)</label>
                <div class="row-input ">
                    <input type="text" name="shipwidth" value="{{$shipwidth}}" >
                </div>

                <label class="row-label">吃水(m)</label>
                <div class="row-input">
                    <input type="text" name="shiploadcapacity" value="{{$shiploadcapacity}}" >
                </div>

                <!-- <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea  name="shipmarks">{{$shipmarks}}</textarea>
                </div> -->

                <!-- <label class="row-label"></label>
                <div class="row-input"></div> -->

                <label class="row-label">操作状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if($status ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-rule="checked" data-label="停用" @if($status ==2) checked @endif>
                </div>
                
            </div>
               <div style="width:90%;margin:0 auto;">
                   <fieldset class="customerfieldset">
                        <legend>上传附件</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                                {
                                    pick: {label: '点击选择图片'},
                                    server: '/attachment/uploadjson',
                                    fileNumLimit: 10,
                                    formData: {module:'supplier',action:'ship'},
                                    required: false,
                                    uploaded: '{{ $uploaded }}',
                                    basePath: '/attachment/preview/id/',
                                    deletePath:'/attachment/deljson/',
                                    accept: {
                                        title: '图片',
                                        extensions: 'jpg,png,txt,pdf',
                                        mimeTypes: '.jpg,.png,.txt,.pdf'
                                    }
                                }"
                            >
                       </fieldset>
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
