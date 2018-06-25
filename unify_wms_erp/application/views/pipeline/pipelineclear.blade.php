<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="pipelineclear-form" action="{{$action}}"  class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="pipeline_sysno" value="{{$id}}">
            <input type="hidden" name="pipelineno" value="{{$pipelineno}}">
            <input type="hidden" name="created_employeename" value="">
            <div class="bjui-row col-2">
                <label class="row-label">清理时间：</label>
                <div class="row-input required">
                    <input type="text"  name="cleartime" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" data-rule="required;date">
                </div>

                <label class="row-label">操作人：</label>
                <div class="row-input required">
	                <select name="created_user_sysno" id="created_user_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%">
						<option value="">请选择</option>
						@foreach($employeelist as $item)
						<option value="{{$item['sysno']}}" @if($item['sysno'] == $created_user_sysno) selected @endif>{{$item['employeename']}}</option>
						@endforeach
	                </select>
                </div>
                <label class="row-label">备注：</label>

                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo}}</textarea>
                </div>

            </div>

        </form>
    </div>
     <div class="remarks">
            <fieldset>
                <legend>清罐记录</legend>
                <div class="table-edit">
                    <table class="table table-bordered" id="pipeline_clear-table" data-toggle="datagrid" data-options="{
                        filterThead:false,
                        height: '100%',
                        tableWidth:'100%',
                        local: 'local',
                        data: {{$clearList}},
                        paging:false,
                        }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'pipelineno',align:'center'}">管线号</th>
                            <th data-options="{name:'cleartime',align:'center'}">清理时间</th>
                            <th data-options="{name:'created_employeename',align:'center'}">操作人</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
</div>

<div class="bjui-pageFooter">
    <ul>
     <li>
                <button id="pipeline_clear" type="button" class="btn-green" data-icon="save">洗管</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>
<script type="text/javascript">


    $("#pipeline_clear").click(function(){
    	$(':input[name=created_employeename]').val($('#created_user_sysno option:selected').text());
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentDialog.find('#pipelineclear-form'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.dialog('closeCurrent','');
                BJUI.navtab('refresh', 'menu486');
            },
        });

    })
</script>