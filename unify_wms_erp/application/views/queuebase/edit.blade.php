<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="queuebase-edit-form" class="datagrid-edit-form" 
              data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
			<input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-1">

                <label class="row-label">类别：</label>
				<div class="row-input required">
                    <select name="queuetype" id="queuebase_queuetype"
                            data-nextselect="#queuebase_queuetype_sysno"
                            data-refurl="/Queuebase/getQueueno/queuetype/{value}" data-size="5"
                            data-toggle="selectpicker" data-live-search="true" data-rule="required"
                            data-width="100%"
							@if($id) disabled @endif
                            >
                        <option value="">请选择</option>
                        <option value="1" @if($queuetype==1) selected @endif>鹤位号</option>
                        <option value="2" @if($queuetype==2) selected @endif>储罐号</option>
                    </select>
                </div>

                <label class="row-label">类别编号：</label>

                <div class="row-input required">
                        <select name="queuetype_sysno" id="queuebase_queuetype_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required" @if($id) disabled @endif
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($queuenoList as $item)
							<option value="{{$item['sysno']}}" @if($item['sysno']==$queuetype_sysno) selected @endif>{{$item['name']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="queueno" id="queuebase_queueno" value="{{$queueno}}">
                </div>
				<br>
                <label class="row-label">品名：</label>

                <div class="row-input">
					<input type="text" name="goodsname" id="queuebase_goodsname" value="{{$goodsname}}" readonly>
					<input type="hidden" name="goods_sysno" id="queuebase_goods_sysno" value="{{$goods_sysno}}">
                </div>
				
				<br>

                <label class="row-label">单位等候时间(分钟)：</label>

                <div class="row-input required">
					<input type="text" name="queuetime" value="{{$queuetime}}" placeholder="单位等候时间" data-rule="required;not0">
                </div>

				<br>
                <label class="row-label">操作状态：</label>

                <div class="row-input">
                    <input type="radio" name="status"  data-toggle="icheck" value="1" data-label="启用" @if($status==1) checked @endif checked>
                    <input type="radio" name="status"  data-toggle="icheck" value="2" data-label="停用" @if($status==2) checked @endif>
                </div>

            </div>
			

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
	$('#queuebase_queuetype_sysno').change(function(){
		$('#queuebase_queueno').val($('#queuebase_queuetype_sysno option:selected').text());
		var id = $(this).val();
		var type = $('#queuebase_queuetype option:selected').val();
		$.ajax({
			url:'/Queuebase/getQueueno/queuetype/'+type+'/sysno/'+id,
			type:'get',
			success:function(json){
				var Obj = JSON.parse(json);
				$('#queuebase_goodsname').val(Obj[0].goodsname);
				$('#queuebase_goods_sysno').val(Obj[0].goods_sysno);
			}
		})
	});


	function saveReceipe()
	{
		BJUI.ajax('ajaxform', {
		    url: "{{$action}}",
		    form: $.CurrentDialog.find('#queuebase-edit-form'),
		    validate: true,
		    loadingmask: true,
		    okCallback: function(json, options) {
		    	BJUI.dialog('closeCurrent','');
		    	BJUI.navtab('reloadFlag', 'navab529');
		    }
		});
	}

</script>