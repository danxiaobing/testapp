
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="temreceivable-dialog-form" action="" class="datagrid-edit-form"  data-data-type="json" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="type" id="type" value="{{$type}}">
            <div class="bjui-row col-2">

                <label class="row-label">费用名称</label>
                <div class="row-input required">
                    <select name="costname" id="costname" data-nextselect=""data-refurl="" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        <option value="停泊费" @if($list['costname']=='停泊费') selected @endif>停泊费</option>
                        <option value="安保费" @if($list['costname']=='安保费') selected @endif>安保费</option>
                        <option value="装船费" @if($list['costname']=='装船费') selected @endif>装船费</option>
                        <option value="卸船费" @if($list['costname']=='卸船费') selected @endif>卸船费</option>
                    </select>
                </div>

                <label class="row-label">费用金额</label>
                <div class="row-input required">
                    <input type="text" name="totalprice" id="totalprice" value="{{$list['totalprice']}}" data-rule="required;number;range[0~]">
                </div>


                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$list['memo']}}</textarea></div>
            </div>
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>

        <li>
            <button type="button" class="btn-green" data-icon="save" id="saveTemreceivable">保存</button>
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
    $('#saveTemreceivable').click(function(){
        var type = $("#type").val();
        $('#temreceivable-dialog-form').isValid(function(v){
            if(v){

                var data  = $("#temreceivable-dialog-form").serializeJson();
                var allData  = $("#temreceivable-detail-table").data('allData');

                if (type == 'add') {

                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }
                    console.log(allData);
                    console.log(data);

                    $('#temreceivable-detail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent','');
                }else if (type == 'edit') {
                    $.CurrentNavtab.find('#temreceivable-detail-table').datagrid('updateRow', "{{$list['gridIndex']}}" , data);
                    var obj = $.CurrentNavtab.find('#temreceivable-detail-table').data('allData');
                    obj["{{$list['gridIndex']}}"] = data;
                    $('#temreceivable-detail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }

            }
        })





    })



</script>