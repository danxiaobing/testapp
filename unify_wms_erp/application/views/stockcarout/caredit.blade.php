<div class="bjui-pageContent">
<form id="stockout_caredit_form" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
            <input type="hidden" id='caredit_cardata' name="cardata" value="{{$carlist}}">
            <label class="row-label">车牌</label>
            <div class="row-input required">
                <input type="text" id='caredit_carid' name="carid" value="{{$carData['carid']}}" data-rule="required" @if($handlestatus == 'add') data-toggle="findgrid" @endif readonly data-options="{
            include: 'carid,carname,mobilephone,weight,carmarks,idcard',
            dialogOptions: {width:'800',height:'500',title:'车辆详细信息',maxable:true,resizable:true,mask:true},
            gridOptions: {
                width:'100%',
                height:'100%',
                tableWidth:'99.8%',
                local: 'local',
                paging: {pageSize:20},
                dataUrl: '/supplier/addcarlist' ,
                columns: [
                    {name:'sysno', label:'id'},
                    {name:'carname', label:'司机姓名'},
                    {name:'carid', label:'车牌号码'},
                    {name:'mobilephone', label:'手机'},
                    {name:'idcard',label:'身份证'},
                    {name:'carmarks', label:'备注'}
                ],
                showLinenumber:false
            },
        }" placeholder="点放大镜按钮查找">
            </div>


            <label class="row-label">司机</label>
            <div class="row-input">
                <input type="text" name="carname" value="{{$carData['carname']}}" readonly>
            </div>

            <label class="row-label">手机</label>
            <div class="row-input">
                <input type="text" name="mobilephone" value="{{$carData['mobilephone']}}" data-rule="mobile" readonly>
            </div>

            <label class="row-label">身份证</label>
            <div class="row-input">
                <input type="text" name="idcard" value="{{$carData['idcard']}}" data-rule="IDcard" readonly>
            </div>

            <label class="row-label">预提货数量(吨)</label>
            <div class="row-input">
                <input type="text" name="cartakeqty" value="{{$carData['cartakeqty']}}">
            </div>

            <label class="row-label">备注</label>
            <div class="row-input">
                <textarea name="carmarks" data-toggle="autoheight" cols="auto" rows="3" readonly>{{$carData['carmarks']}} </textarea>
            </div>

        </div>
</form>

    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="subStockoutCar()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>


<script type="text/javascript">
    function make_carqty() {
        if($("#fullcarqty").val() == '' || $("#emptycarqty").val() == '')
                return;
        var carqty =  parseFloat($("#fullcarqty").val()) - parseFloat($("#emptycarqty").val());
        if(carqty < 0)
            carqty = 0;
        $("#carqty").val(parseFloat(carqty));
    }


    function subStockoutCar() {

        var handlestatus = $("#handlestatus").html();
        var cardata = JSON.parse($('#caredit_cardata').val());
        var carid =  $('#caredit_carid').val();

        if(handlestatus != 'edit'){
            for (var i = 0; i < cardata.length; i++) {
                if (carid == cardata[i]['carid']) {
                    BJUI.alertmsg('warn', '车牌号重复，请重新选择',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;
                }
            }
        }

        $('#stockout_caredit_form').isValid(function(v){
            if (v) {
                BJUI.ajax('doajax', {
                    url: "supplier/checkcarid/carid/"+carid,
                    loadingmask: true,
                    okCallback: function(json, options) {
                        if(json == 1){
                            var data  = $("#stockout_caredit_form").serializeJson();
                            var allData  = $.CurrentNavtab.find("#stockout-car-table").data('allData');
                            if (handlestatus == 'add') {
                                if(typeof  allData != 'undefined'){
                                    allData.push(data);
                                }else{
                                    allData = [data] ;
                                }
                                $.CurrentNavtab.find('#stockout-car-table').datagrid('reload',  {data:allData});
                                BJUI.dialog('closeCurrent','');
                            }else if (handlestatus == 'edit') {
                                $.CurrentNavtab.find('#stockout-car-table').datagrid('updateRow', "{{$carData['gridIndex']}}" , data);
                                var obj = $.CurrentNavtab.find('#stockout-car-table').data('allData');
                                obj["{{$carData['gridIndex']}}"] = data;
                                $('#stockout-car-table').datagrid('reload',  {data:obj});
                                BJUI.dialog('closeCurrent','');
                            }
                        }else{
                            BJUI.alertmsg('warn', '车辆已经停用，请重新填写',{displayPosition:'middlecenter',displayMode:'fade'});
                            return ;
                        }
                    }
                })
                
            }else{
                console.log('no');
            }

        });

    }
</script>
