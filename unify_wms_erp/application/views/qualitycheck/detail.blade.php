<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="qualitycheckdetail-form" class="datagrid-edit-form"
              data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">

            <div class="bjui-row col-2">
                <input type="hidden" name="sysno" value="{{$sysno}}">
                <label class="row-label">货品名称</label>
                <div class="row-input">
	                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                    <input type="text" name="goodsname" value="{{$goodsname or ''}}" readonly>
<!--                     <select name="goods_sysno" data-size="5" id="qualitycheck_id" data-toggle="selectpicker" data-live-search="true" data-rule="required"  data-width="100%"   >
                        <option value="">请选择</option>
                        @foreach($goodsList as $item)
                        <option value="{{$item['goods_sysno']}}" @if($goods_sysno==$item['goods_sysno']) selected @endif>{{$item['goodsname']}}</option>
                        @endforeach
                    </select> -->
                </div>

                <label class="row-label">检查时间</label>
                <div class="row-input required">
                    <input type="text" name="checktime" value="{{$checktime or ''}}" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" placeholder="检查时间" data-rule="required" @if($handlestatus == 'audit') readonly @endif>
                </div>

                <label class="row-label">是否合格</label>

                <div class="row-input required">
                    <select name="ischecked" data-size="5" id="qualitycheck_ischecked" data-toggle="selectpicker" data-live-search="true" data-rule="required"  data-width="100%" @if($handlestatus == 'audit') disabled @endif>
                    	<option value="">请选择</option>
                    	<option value="1" @if($ischecked==1) selected @endif>合格</option>
                    	<option value="2" @if($ischecked==2) selected @endif>不合格</option>
                    </select>
                </div>

                @if($handlestatus == 'audit')
                <label class="row-label">是否让步</label>
                <div class="row-input required">
                    <select name="isskip" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
                        <option value="0">请选择</option>
                    	<option value="1" @if($isskip==1) selected @endif>让步</option>
                    	<option value="2" @if($isskip==2) selected @endif>不让步</option>
                    </select>
                </div>
                @endif

                <label class="row-label">记录人</label>

                <div class="row-input required">
                    <select name="created_user_sysno" id="created_user_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%"  @if($handlestatus == 'audit') disabled @endif>
						<option value="">请选择</option>
						@foreach($employeelist as $item)
						<option value="{{$item['sysno']}}" @if($item['sysno'] == $created_user_sysno) selected @endif>{{$item['employeename']}}</option>
						@endforeach
                    </select>
					<input type="hidden" name="created_employeename" id="created_employeename" value="{{$created_employeename}}">
                </div>

                <label class="row-label">备注</label>

                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3"  @if($handlestatus == 'audit') readonly @endif>{{$memo}}</textarea>
                </div>

            </div>
            <div class="remarks">
                <fieldset id="qualitycheckdetail_file">
                    <legend>上传检查单</legend>
                    <input type="file" data-name="" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'qualitycheck',action:'qualitycheck-edit',tempname:'{{$timesonly}}'},
                            required: false,
                            uploaded: '{{ $u_upload }}',
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
			<input type="hidden" id="u_upload" name="u_upload" value="">
            <input type="hidden" id="last_upload" name="last_upload" value="{{$u_upload}}">

        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            <button type="button" class="btn-green" data-icon="save" onclick="saveReceipe()">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
    </ul>
</div>


<script type="text/javascript">
	 function saveReceipe() {
        var handlestatus = "{{$handlestatus}}";
        if (handlestatus == 'audit') {
            $("#created_user_sysno").removeAttr('disabled');
            $("#qualitycheck_ischecked").removeAttr('disabled');
        }
        var last = $('#last_upload').val();
        var u_length = $(".upload").length;
        var up_array = new Array();
        for(var i=0;i<u_length;i++){
            up_array[i] = $(".upload").eq(i).val();
        }

        $('#u_upload').val(up_array);

	 	$('#qualitycheckdetail-form').isValid(function (v) {
	 		if(v){
	 			// $(":input[name='goodsname']").val($("#qualitycheck_id option:selected").text());
	 			$('#created_employeename').val($('#created_user_sysno option:selected').text());
		        var data = $("#qualitycheckdetail-form").serializeJson();
		        var allData = $("#qualitycheck-detail-table").data('allData');

		        if (handlestatus == 'add') {
		            if (typeof  allData != 'undefined') {
		                allData.push(data);
		            } else {
		                allData = [data];
		            }
		            $.CurrentNavtab.find('#qualitycheck-detail-table').datagrid('reload', {data: allData});
		            BJUI.dialog('closeCurrent');
		        }
		        if (handlestatus == 'edit') {
		            $.CurrentNavtab.find('#qualitycheck-detail-table').datagrid('updateRow', "{{$gridIndex}}", data);
		            var obj = $.CurrentNavtab.find('#qualitycheck-detail-table').data('allData');
		            obj["{{$gridIndex}}"] = data;
		            $('#qualitycheck-detail-table').datagrid('reload', {data: allData});
		            BJUI.dialog('closeCurrent', '');
		        }
                if (handlestatus == 'audit') {
                    $.CurrentNavtab.find('#qualitycheck-detail-table').datagrid('updateRow', "{{$gridIndex}}", data);
                    var obj = $.CurrentNavtab.find('#qualitycheck-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#qualitycheck-detail-table').datagrid('reload', {data: allData});
                    BJUI.dialog('closeCurrent', '');
                }
	 		}
    	});
  }

</script>