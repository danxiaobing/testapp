<div class="bjui-pageContent">
<form id="bocar_caredit_form" action="/bookout/detailsubmit" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <label class="row-label">车牌</label>
        <div class="row-input required">
            <input type="text" name="carid" size="23" id="bocarcaredit_carid" value="{{$selectedDatas['carid'] or ''}}" data-rule="required" data-toggle="findgrid" data-options="{
            include: 'carid,carname,mobilephone,weight,idcard',
            dialogOptions: {width:'800',height:'500',title:'车辆详细信息',maxable:true,resizable:true,mask:true},
            gridOptions: {
                width:'100%',
                height:'100%',
                tableWidth:'99.8%', 
                local: 'local',
                paging: {pageSize:20},
                dataUrl: '/supplier/addcarlist',
                editUrl: '/supplier/carlist',
                columns: [
                    {name:'sysno', label:'id'},
                    {name:'carname', label:'司机姓名'},
                    {name:'carid', label:'车牌号码'},
                    {name:'idcard', label:'身份证'}
                ],
                showLinenumber:false
            },
        }" placeholder="点放大镜按钮查找">
        </div>

        <label class="row-label">司机</label>
        <div class="row-input">
            <input type="text" size="23" name="carname" value="{{$selectedDatas['carname'] or ''}}">
        </div>

        <label class="row-label">手机号</label>
        <div class="row-input">
            <input type="text" size="23" name="mobilephone" value="{{$selectedDatas['mobilephone'] or ''}}">
        </div>

        <label class="row-label">身份证</label>
        <div class="row-input">
            <input type="text" size="23" name="idcard" value="{{$selectedDatas['idcard'] or ''}}" data-rule="IDcard">
        </div>

        <label class="row-label">预提货数量</label>
        <div class="row-input">
            <input type="text" size="23" name="cartakeqty" value="{{$selectedDatas['cartakeqty'] or ''}}">吨
        </div>

        <label class="row-label">备注:</label>
        <div class="row-input">
            <textarea name="carmarks" size="23" data-toggle="autoheight" cols="auto" rows="3">{{$selectedDatas['carmarks'] or ''}}</textarea>
        </div>
    </div>
</form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveBocarCar()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li id="bocar_edit_handlestatus" style="display: none">{{$handlestatus or ''}}</li>
    </ul>
</div>


<script type="text/javascript">
    function saveBocarCar() {
        var handlestatus = $("#bocar_edit_handlestatus").html();
        $('#bocar_caredit_form').isValid(function(v){
            if (v) {
                var carid = $("#bocarcaredit_carid").val();
                BJUI.ajax('doajax', {
                    url: 'supplier/checkcarid/carid/'+carid,
                    loadingmask: true,
                    okCallback: function(json, options) {
                        if(json == 1){
                            var data  = $("#bocar_caredit_form").serializeJson();
                            var allData  = $("#bocar-editcar-table").data('allData');
                            if (handlestatus == 'add') {
                                if(typeof  allData != 'undefined'){
                                    allData.push(data);
                                }else{
                                    allData = [data] ;
                                }

                                $('#bocar-editcar-table').datagrid('reload',  {data:allData});
                                BJUI.dialog('closeCurrent', 'bocar-car-{{$id}}');
                            }
                            if (handlestatus == 'edit') {
                                $.CurrentNavtab.find('#bocar-editcar-table').datagrid('updateRow', "{{$selectedDatas['gridIndex']}}" , data);
                                var obj = $.CurrentNavtab.find('#bocar-editcar-table').data('allData');
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
