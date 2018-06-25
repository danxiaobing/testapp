<div class="bjui-pageContent">
	
	@foreach($messageList as $value)
	<div class="messageA" data-toggle="collapse" data-target="#{{$value['sysno']}}" aria-expanded="false" aria-controls="collapseOne">
	   <h5>{{$value['subject']}}<span class="badge bred confirmBtn">删除</span><span class="badge @if($value['type'] == '未读')bgreen @endif">{{$value['type']}}</span></h5>
	</div>
	<div class="messageB collapse" id="{{$value['sysno']}}" onclick="clickMessage({{$value['sysno']}})">{{$value['content']}}</div>
	@endforeach
	
</div>
<div class="bjui-pageFooter">
    <ul>
        <!-- <li><button type="submit" class="btn-green" data-icon="check">确认</button></li> -->
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>
<script type="text/javascript">
	$.CurrentDialog.find('.messageA').each(function(index, el) {
	  	$(this).click(function(event) {
	  		var divId = $(this).attr('data-target');
	  		var thisO = $(this);
		  	BJUI.ajax('doajax', {
			    url: 'index/updateMessage',
			    type:'POST',
			    data:{id:divId,viewstatus:2},
			    loadingmask: false,
			    okCallback: function(json, options) {
			    	if (json.code == 200) {
			    		thisO.find('span').eq(1).removeClass('bgreen').html('已读');
			    	} 
			    }
			})
	  });	
	});
	$.CurrentDialog.find('.confirmBtn').each(function(index, el) {
		$(this).click(function(event) {

			var thisS = $(this);

		    BJUI.alertmsg('confirm', '是否确定删除此条信息提示！', {
		        okCall: function() {

		        	var divId = thisS.parent('h5').parent('div').attr('data-target');
		        	deleteMessage(divId,thisS);
		        	
		        }
		    })

		})
	});

	function deleteMessage(divId,thisS) {
		BJUI.ajax('doajax', {
		    url: 'index/updateMessage',
		    type:'POST',
		    data:{id:divId,isdel:1},
		    loadingmask: false,
		    okCallback: function(json, options) {
		    	if(json.code == 200){
		    		thisS.parent('h5').parent('div').remove();
			    	$.CurrentDialog.find(divId).remove();
			        BJUI.alertmsg('ok', '成功删除！');
		    	}else{
		    		BJUI.alertmsg('warn', '删除失败',{displayPosition:'middlecenter',displayMode:'fade'});
		    	}
		    }
		        
		})
		
	}

	function clickMessage(id) {
		BJUI.dialog('close', 'system-messages');
		BJUI.ajax('doajax', {
		    url: 'index/getMessageInfo',
		    type:'POST',
		    data:{id:id},
		    loadingmask: false,
		    okCallback: function(json, options) {
		        if(json.code == 200){
		        	var url = json.control + '/' + json.action + '/id/' + json.sysno;
		        	BJUI.navtab({
		                // id:'navtab',
		                url:url,
		                title:'列表页',
		            });
		        }else{
		        	BJUI.alertmsg('warn', '跳转失败',{displayPosition:'middlecenter',displayMode:'fade'});
		        }
		    }
		})
	}
	



</script>