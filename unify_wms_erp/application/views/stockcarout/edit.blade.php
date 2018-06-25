<div class="bjui-pageContent">
    <div style="width:90%;margin: 0 auto;">
        <br><br>
        <form id="car-stockout-form" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" id="car_stockout_cardata" name="stockoutcardata">
            <fieldset>
                <legend>出库单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">出库单号</label>
                    <div class="row-input">
                        <input type="text" name="stockoutno" value="{{$stockoutno or ''}}" readonly>
                    </div>
                    <label class="row-label">出库日期</label>
                    <div class="row-input">
                        <input type="text" name="stockoutdate" data-toggle="datepicker" value="@if($stockoutdate) {{ $stockoutdate }} @else {{date('Y-m-d')}} @endif" readonly>
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input">
                        <input type="text" name="status" value="{{ $status }}" disabled>
                    </div>

                    <input type="hidden" name="customer_sysno" value="{{$customer_sysno}}"/>

                    <label class="row-label">客户</label>
                    <div class="row-input">
                        <select name="customer_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled>
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customername" value="{{$customername}}">
                    </div>



                    <input type="hidden" name="booking_out_sysno" value="{{$booking_out_sysno}}"/>

                    <label class="row-label">提货单号</label>
                    <div class="row-input">
                        <input type="text" name="takegoodsno" value="{{$takegoodsno}}"
                               readonly>
                    </div>

                    <label class="row-label">提货公司</label>
                    <div class="row-input">
                        <input type="text" name="takegoodscompany" value="{{$takegoodscompany}}" readonly>
                    </div>

                    <label class="row-label">提货区间</label>
                    <div class="row-input">
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" id='car_stockout_receivestart' name="receivestart" data-toggle="datepicker" value="{{$receivestart}}" placeholder="开始日期" readonly>
                            <div class="input-group-addon">至</div>
                            <input type="text" class="form-control" id='car_stockout_receiveend' name="receiveend" data-toggle="datepicker" value="{{$receiveend}}" placeholder="结束日期" readonly>
                        </div>
                    </div>

                    <label class="row-label">是否逾期</label>
                    <div class="row-input">
                        <input type="text" name="receiveover" value="{{$receiveover}}" readonly>
                    </div>
                </div>
                <br>
            </fieldset>
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>出库单信息</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="stockout-car-detail" data-toggle="datagrid" data-options="{
                            height:'100%',
                            filterThead:false,
                            showToolbar: false,
                            data:{{$detaillist}},
                            paging: false,
                            linenumberAll: true,
                            fullGrid:true,
                            columnResize: false,
                            showTfoot: true,
                            fieldSortable: false,
                            local: 'local'
                        }">
                        <thead>
                        <tr data-options="{name:'stock_sysno'}">
                            <th data-options="{name:'stockin_no',align:'center'}">入库单号</th>
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'qualityname',align:'center'}">规格</th>
                            <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';  }}}">
                                货物性质
                            </th>
                            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                            <th data-options="{name:'inshipname',align:'center'}">船名</th>
                            <th data-options="{name:'instockqty',align:'center'}">入库数量</th>
                            <th data-options="{name:'introduceqty',align:'center'}">提单总量</th>
                            <th data-options="{name:'tobeqty',align:'center',calc:'sum'}">提单数量</th>
                            <th data-options="{name:'takeqty',align:'center',calc:'sum'}">结存数</th>
                            <th data-options="{name:'beqty',align:'center',calc:'sum'}">实提数量</th>
                            <th data-options="{name:'memo',align:'center'}">备注</th>
                            <th data-options="{name:'bookout_detail_sysno',align:'center',hide:true}">预约单号id</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                            <th data-options="{name:'stock_sysno',align:'center',hide:true}">库存id</th>
                            <th data-options="{name:'stockno',align:'center',hide:true}">库存单号</th>
                            <th data-options="{name:'stockqty',align:'center',hide:true}">可用数量</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">出货罐号id</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名ID</th>
                            <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">货品品质ID</th>
                            <th data-options="{name:'expresscompanyname',align:'center',hide:true}">送货公司名称</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:true}">入库单号ID</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
                <br>
            <div class="remarks">
                <fieldset>
                    <legend>出库车辆信息</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="stockout-car-table" data-toggle="datagrid" data-options="{
                                include: 'carid,carname,mobilephone,idcard,carmarks',
                                filterThead:false,
                                @if($type == 'addcar')
                                showToolbar: true,
                                toolbarCustom:$.CurrentNavtab.find('#custom_stockout_car_tb'),
                                @endif
                                data:{{$carlist}},
                                paging: false,
                                linenumberAll: true,
                                fullGrid:true,
                                fieldSortable: false,
                                local: 'local'
                            }">
                            <thead>
                            <tr data-options="{id:'sysno'}">
                                <th data-options="{name:'carid',align:'center'}">车牌号</th>
                                <th data-options="{name:'carname',align:'center'}">司机</th>
                                <th data-options="{name:'mobilephone',align:'center'}">手机</th>
                                <th data-options="{name:'idcard',align:'center'}">身份证</th>
                                <th data-options="{name:'cartakeqty',align:'center'}">预提货数量(吨)</th>
                                <th data-options="{name:'carmarks',align:'center'}">备注</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
            </div>
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>附件</legend>
                    <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                {
                    pick: {label: '点击选择附件'},
                    server: '/attachment/uploadjson',
                    fileNumLimit: 10,
                    formData: {module:'stockout',action:'receipt',doc_sysno:'{{$id}}'},
                    required: false,
                    uploaded: '{{ $uploaded }}',
                    basePath: '/attachment/preview/id/',
                    deletePath:'/attachment/deljson/',
                    accept: {
                        title: '图片',
                        extensions: 'jpg,png,pdf,txt',
                        mimeTypes: '.jpg,.png,.pdf,.txt'
                    }
                }"
                    >
                </fieldset>

            </div>
            <br><br>
            <div class="text-center ">
                @if($type == 'addcar')
                <button type="submit" id="carsubmit" class=" btn-success btn-lg">提交</button>
                @endif
                @if($type == 'delay')
                <button type="button" class=" btn-success btn-lg" onclick="stockoutDelay()">延期提交</button>
                @endif
                <button type="button" onclick="showRecords()" class="btn btn-lg">查看操作记录</button>
            </div>
    </form>
        <br><br>
        <div class="remarks hideshow" style="display: none;">
            <fieldset>
                <legend>操作记录明细</legend>
                <div class="addTable"></div>
            </fieldset>
        </div>
        <br><br><br>
    </div>
</div>
@if($type == 'addcar')
    <div id="custom_stockout_car_tb">
        <button type="button" class="btn btn-blue" data-icon="add" onclick="addStockoutCar()"><i class="fa fa-plus"></i>添加</button>
        <button type="button" class="btn btn-green" onclick="editStockoutCar()"><i class="fa fa-edit"></i>编辑</button>
        <button type="button" class="btn btn-red"  onclick="delStockoutCar()"><i class="fa fa-times"></i>删除</button>
    </div>

@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    addLog($.CurrentNavtab.find('.addTable'), {{$id}}, '6');

    $("#carsubmit").click(function () {
     
        var carObj = $.CurrentNavtab.find("#stockout-car-table").data('allData');
/*
        if (carObj == '') {
            BJUI.alertmsg('warn', '请填写出库车辆信息',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }*/

/*        if(carObj.length < 1){
            BJUI.navtab('closeCurrentTab', '');
            return;
        }*/
        $.CurrentNavtab.find("#car_stockout_cardata").val(JSON.stringify(carObj));
        BJUI.ajax('ajaxform', {
            url: '/stockout/addcarJson/id/' + '{{$id}}',
            form: $.CurrentNavtab.find('#car-stockout-form'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                updateCarinfo(carObj);
                BJUI.navtab('reloadFlag', 'navab278');
                BJUI.navtab('closeCurrentTab','');
            }
        })
    });

    //更新车辆信息
    function updateCarinfo(cardata) {
        var cardata = cardata;
        BJUI.ajax('doajax',{
            url:"/supplier/updateCarinfo",
            type:'POST',
            data:{cardata,cardata},
        });
    }

    function addStockoutCar() {
        var Obj = $.CurrentNavtab.find('#stockout-car-table').data('allData');
        BJUI.dialog({
            id: 'stockout-car-{{$id}}',
            url: '/stockout/caredit/handlestatus/add/',
            data: {id: "{{$id}}",carlist:Obj},
            type: 'POST',
            title: '车辆信息',
            width: 900,
            height: 480,
            mask:true,

        });
    }

    function editStockoutCar() {
        var Obj = $('#stockout-car-table').data('selectedDatas');
        if (Obj == undefined) {
            BJUI.alertmsg('warn','<h4>请选中一行进行修改!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }else{
            BJUI.dialog({
                id: 'stockout-car-{{$id}}',
                url: '/stockout/caredit/handlestatus/edit/',
                data: {booking_sysno: "{{$booking_out_sysno}}",carData:Obj[0]},
                type: 'POST',
                title: '车辆信息',
                width: 900,
                height: 480,
                mask:true,

            });
        }
    }

/*    function delStockoutCar(){
        var arr = $('#stockout-car-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }

            BJUI.alertmsg('confirm', '是否删除此车辆！', {

                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/stockout/delCarJson/ids/'+data,
                        type: 'GET',
                        okcallback:function(option){
                        }
                    });
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    };*/

    function delStockoutCar()
    {
        var selectdata = $.CurrentNavtab.find('#stockout-car-table').data('selectedDatas');
        if (selectdata == undefined) {
            BJUI.alertmsg('warn','请选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        } else {
            BJUI.alertmsg('confirm', '是否删除此车辆！', {

                okCall: function() {

                    $.ajax({
                        url: '/stockout/delCarJson/ids/'+selectdata[0].stockout_sysno+'/carid/'+selectdata[0].carid,
                        dataType: 'json',
                        type: 'GET',
                        success:function(json,option){
                           console.log(json);
                            if(json.code==200){
                                var allData = $("#stockout-car-table").data('allData');
                                for (var i = selectdata.length - 1; i >= 0; i--) {
                                    allData = allData.remove(selectdata[i].gridIndex);
                                }
                                $.CurrentNavtab.find('#stockout-car-table').datagrid('reload', {data: allData});
                            }else if(json.code==300) {
                                    BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                                    return false;

                            }


                        }
                    });

                }
            });
        }

    }

    function stockoutDelay(){
        var receivestart = $('#car_stockout_receivestart').val();
        var detailData = $.CurrentNavtab.find("#stockout-car-detail").data('allData');
        if(!receivestart){
            BJUI.alertmsg('warn','开始时间必须填写',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var receiveend = $('#car_stockout_receiveend').val();
        BJUI.ajax('doajax', {
            url: '/stockout/stockoutDelayJson/id/'+{{$id}},
            type: 'POST',
            data:{receivestart:receivestart,receiveend:receiveend,detailData:detailData},
            okCallback:function(json,option){
                if(json.code == 300){
                    BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }else if(json.code == 200){
                    BJUI.navtab('reloadFlag', 'navab278');
                    BJUI.navtab('closeCurrentTab','');
                }
            }
        });
    }

</script>