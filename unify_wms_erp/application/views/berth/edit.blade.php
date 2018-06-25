<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="berthform" action="{{$action}}" class="datagrid-edit-form"  data-data-type="json" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">

                <label class="row-label">泊位号</label>
                <div class="row-input required">
                    <input type="text" name="berthname" value="{{$list['berthname']}}" data-rule="required; berthname"  data-rule-berthname="[/^[a-zA-Z0-9]+$/, '编号无效! 仅支持字母与数字。']"  ></div>

                <label class="row-label">允许最大吃水</label>
                <div class="row-input required">
                    <input type="text" name="berthloadcapacity" value="{{$list['berthloadcapacity']}}" data-rule="required; "></div>

                <label class="row-label">泊位长度</label>
                <div class="row-input required">
                    <input type="text" name="berthlength" value="{{$list['berthlength']}}" data-rule="required; "></div>

                <label class="row-label">泊位水深</label>
                <div class="row-input required">
                    <input type="text" name="berthdeep" value="{{$list['berthdeep']}}" data-rule="required; "></div>

                <label class="row-label">核准停泊船型</label>
                <div class="row-input required">
                    <select name="berthtype" data-nextselect="" data-refurl="" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                        <option value="0" >请选择</option>
                        <option value="1" @if($list['berthtype']==1) selected @endif >不限</option>
                    </select>
                </div>

                <label class="row-label">核准停泊能力</label>
                <div class="row-input required">
                    <input type="text" name="berthloadweight" value="{{$list['berthloadweight']}}" data-rule="required; "></div>


                <label class="row-label">码头</label>
                <div class="row-input required">
                    <select name="wharf_sysno" id="wharf_syano" data-nextselect=""data-refurl="" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                       <option value="0">请选择</option>
                        @foreach($wharf as $value)
                            <option value="{{$value['sysno']}}" @if($value['sysno'] == $list['wharf_sysno']) selected @endif>{{$value['wharfname']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="wharfname" name="wharfname" value="{{$list['wharfname']}}">
                </div>



                <label class="row-label">状态</label>
                <div class="row-input required">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-rule="checked" data-label="启用&nbsp;&nbsp;" @if( !$list['status'] || $list['status'] ==1) checked @endif>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($list['status'] ==2) checked @endif></div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea name="berthmarks" data-toggle="autoheight" cols="auto" rows="3">{{$list['berthmarks']}}</textarea></div>
            </div>
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>

        <li>
            <button type="button" class="btn-green" data-icon="save" id="saveberth">保存</button>
        </li>
         <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>

    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">

    //保存
    $('#saveberth').click(function(){
        $('#berthform').isValid(function(v){
            if(v){
            var wharfname = $('#wharf_syano option:selected').text();
            $('#wharfname').val(wharfname);
            var Obj  = $("#berthform").serializeJson();
                console.log(Obj);
                BJUI.ajax('ajaxform',{
                    url:'{{$action}}',
                    form: $.CurrentDialog.find('#berthform'),
                    /*validate: true,*/
                    loadingmask:true,
                    okCallback:function(json){
                        BJUI.dialog('closeCurrent');
                        BJUI.navtab('refresh', 'menu487');

                    }
                });

            }
        });
    });




</script>