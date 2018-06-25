<div class="bjui-pageContent">
    <form id="stockcarin-cars-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">

            <label class="row-label">车牌</label>
            <div class="row-input required">

                <input type="text" name="carid" id="stockcarincarid" value="{{$carid}}" data-rule="required" data-toggle="findgrid" data-options="{
                dialogOptions: {width:'800',height:'500',title:'车辆详细信息',maxable:true,resizable:true,mask:true},
                gridOptions: {
                    width:'100%',
                    height:'100%',
                    tableWidth:'99.8%',
                    local: 'local',
                    paging: {pageSize:20},
                    dataUrl: '/Supplier/addcarlist',
                    columns: [
                        {name:'sysno', label:'id'},
                        {name:'carid', label:'车牌号码'},
                        {name:'carname', label:'司机姓名'}
                    ],
                    showLinenumber:false
                },
            }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">司机姓名</label>
            <div class="row-input">
                <input type="text" name="carname" value="{{$carname}}">
            </div>

            <label class="row-label">手机号</label>
            <div class="row-input">
                <input type="text" name="mobilephone" value="{{$mobilephone}}" data-rule="mobile">
            </div>

            <label class="row-label">身份证</label>
            <div class="row-input">
                <input type="text" name="idcard" value="{{$idcard}}" data-rule="IDcard">
            </div>

            <label class="row-label">备注:</label>
            <div class="row-input">
                <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo}}</textarea>
            </div>
        </div>

    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveBocars()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li  id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    function saveBocars() {
        var handlestatus = $("#handlestatus").html();
        $('#stockcarin-cars-form').isValid(function(v){
            if (v) {
                var carid = $("#stockcarincarid").val();
                BJUI.ajax('doajax', {
                    url: 'supplier/checkcarid/carid/'+carid,
                    loadingmask: true,
                    okCallback: function(json, options) {
                        if(json == 1){
                            var data  = $("#stockcarin-cars-form").serializeJson();
                            var allData  = $("#stockcarin-cars-table").data('allData');
                            if (handlestatus == 'add') {
                                if(typeof  allData != 'undefined'){
                                    allData.push(data);
                                }else{
                                    allData = [data] ;
                                }

                                $('#stockcarin-cars-table').datagrid('reload',  {data:allData});
                                BJUI.dialog('closeCurrent');
                            }
                            if (handlestatus == 'edit') {
                                $.CurrentNavtab.find('#stockcarin-cars-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                                var obj = $.CurrentNavtab.find('#stockcarin-cars-table').data('allData');
                                obj["{{$selectedDatas['gridIndex']}}"] = data;
                                BJUI.dialog('closeCurrent','');
                            }
                        }else{
                            BJUI.alertmsg('warn', '车辆已经停用，请重新填写',{displayPosition:'middlecenter',displayMode:'fade'});
                            return ;
                        }

                    }
                });
            }else{
                console.log('no');
            }
        });
    }
</script>