<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="bookpipelineinform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" id="id" value="{{$id}}">
            <input type="hidden" name="stockintype" value="3">
            <input type="hidden" id="bookpipelineindetaildata" name="bookpipelineindetaildata" value="">

        <!--base message start-->
            <fieldset>
                <legend>入库预约单信息</legend>
                <br>
                <div class="bjui-row col-3">

                    <label class="row-label">入库预约单号</label>

                    <div class="row-input">
                        <input type="text" name="bookinginno"
                               value="@if($bookinginno){{$bookinginno}}@else{{系统自动生成}} @endif" readonly>
                    </div>

                    <label class="row-label">预约日期</label>

                    <div class="row-input required">
                        <input type="text" name="bookingindate" id="bookingindate"
                               value="@if($bookingindate){{date('Y-m-d',strtotime($bookingindate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date"
                               @if($bookinginstatus == 2 || $bookinginstatus == 7  || $id ==0)
                               data-toggle="datepicker"
                               @else
                               readonly
                                @endif>
                    </div>

                    <label class="row-label">单据状态</label>

                    <div class="row-input required">
                        <input type="hidden" id="bookinginstatus" name="bookinginstatus"
                               value="@if($bookinginstatus){{$bookinginstatus}}@else{{2}} @endif" readonly>
                        @foreach($bookinstatusnamelist as $item)
                            @if($item['id'] == $bookinginstatus)
                                <input type="text" name="bookinginstatusname" value="{{$item['name']}}" readonly>
                            @endif
                        @endforeach
                    </div>

                    <label class="row-label">客户</label>

                    <div class="row-input required">
                        <select name="obj.customer_sysno" id="booking_pipelinein_customer_sysno"
                                data-nextselect="#bookpipelinein_contract_sysno"
                                @if(($bookinginstatus != 2 && $bookinginstatus != 7  && $id !=0) || $viewtype=='look') disabled @endif
                                data-refurl="/customer/customercontractJson2/id/{value}/contracttype/1,2,3,4" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%">
                            <option value="">请选择</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="obj.customer_name" id="bookpipelinein_customername"
                               value="{{$customer_name}}">
                    </div>

                    <label class="row-label">合同编号</label>

                    <div class="row-input required">
                        <select name="contract_sysno" id="bookpipelinein_contract_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-rule="required"
                                data-width="100%"
                                @if(($bookinginstatus != 2 && $bookinginstatus != 7  && $id !=0) || $viewtype=='look') disabled @endif>
                            <option value="">请选择</option>
                            @if($contract_sysno)
                            <option value="{{$contract_sysno}}"
                                    @if($contract_sysno) selected @endif>{{$contract_no}}</option>
                            @endif
                        </select>
                        <input type="hidden" name="contract_no" id="bookpipelinein_contractno" value="{{$contract_no}}">
                    </div>

                    <label class="row-label">货品名称</label>
                    <div class="row-input">
                        <input type="text" id="bookpipelineincontractgoods" value="" readonly>
                    </div>

                    <label class="row-label">单据来源</label>

                    <div class="row-input">
                        <input type="hidden" name="docsource" value="@if($docsource){{$docsource}}@else{{1}}@endif">
                        <input type="text" value="@if($docsource ==2)国烨云仓@else手工创建@endif" readonly>
                    </div>

                    <label class="row-label">客服专员</label>

                    <div class="row-input">
                        <select name="cs_employee_sysno" id="cs_employee_sysno" data-size="5"
                                data-toggle="selectpicker"
                                @if(($bookinginstatus != 2 && $bookinginstatus != 7  && $id !=0) || $viewtype=='look') disabled
                                @endif
                                data-live-search="true"  data-width="100%">
                            <option value="">请选择</option>
                            @foreach($employeelist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $cs_employee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cs_employeename" id="cs_employeename" value="{{$cs_employeename}}">
                    </div>

                    <br>
  
                    <label class="row-label">管线预约</label>

                    <div class="row-input required">
                        <select name="ispipelineorder"  data-size="5"  data-toggle="selectpicker" @if(($bookinginstatus != 2 && $bookinginstatus != 7  && $id !=0) || $viewtype=='look') disabled @endif  data-rule="required" data-width="100%">
                            <option value="1" @if($ispipelineorder==1 || !$ispipelineorder) selected @endif>是</option>
                            <option value="2" @if($ispipelineorder==2) selected @endif>否</option>
                        </select>
                    </div>


                    <label class="row-label">品质检查预约</label>

                    <div class="row-input required">
                        <select name=""  data-size="5"
                                data-toggle="selectpicker"
                                disabled
                                data-rule="required" data-width="100%">
                            <option value="1" @if($isqualitycheck==1 || !$isqualitycheck) selected @endif>是</option>
                            <option value="2">否</option>
                        </select>
                        <input type="hidden" name="isqualitycheck" value="1">
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>入库单明细</legend>
                    <div class="table-edit">
                        <table class="table table-bordered" id="bookpipelinein-detail-table" data-toggle="datagrid"
                               data-options="{
                                    height:'100%',
                                    filterThead:false,
                                    @if($type =='addattach'  || $type=='review' || $type=='back' || $viewtype=='look')
                                       showToolbar: false,
                                    @else  showToolbar: true,
                                    @endif
                                       toolbarCustom:$.CurrentNavtab.find('#bookpipelinein_tb'),
                                       local: 'local',
                                       dataUrl: '/bookshipin/adddetailJson/id/{{$id}}',
                                    dataType: 'json',
                                    jsonPrefix: 'obj',
                                    paging: false,
                                    fullGrid:true,
                                    showTfoot:true,
                                    linenumberAll: true
                               }">
                            <thead>
                            <tr data-options="{name:'sysno'}">
                                <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                                <th data-options="{name:'goods_quality_name',align:'center'}">规格</th>
                                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税';} else if(value=='2') {return '外贸';} else if(value=='3') {return '内贸转出口';} else if(value=='4') {return '内贸内销';}}}">货物性质</th>
                                <th data-options="{name:'unitname',align:'center',width:100,render:function(value){if(value=='') {return '吨'} else {return value}}}">计量单位</th>
                                <th data-options="{name:'bookinginqty',calc:'sum',align:'center'}">数量</th>
                                <th data-options="{name:'bookingindate',align:'center'}">预计到货日期</th>
                                <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
                                <th data-options="{name:'memo',align:'center'}">备注</th>
                                <th data-options="{name:'goods_sysno',align:'center',hide:true}">产品id</th>
                                <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">进货罐号</th>
                                <th data-options="{name:'goods_quality_sysno',align:'center',hide:true}">规格</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </fieldset>
                <!--project end-->
                <!--upload start-->
                <div class="comuser-add" style="display: none">
                    <!-- 自带bug -->
                    <div>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookspipelinein',action:'booking'},
                            required: false,
                            uploaded: '',
                            basePath: '',
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                            }
                        }"
                        >
                    </div>
                </div>
                <!-- 临时解决end -->
                <div class="comuser-add-left">
                    <fieldset class="customerfieldset">
                        <legend>上传管入库预约单</legend>
                        <input type="file" data-name="attachment[]" data-toggle="webuploader" data-options="
                        {
                            pick: {label: '点击选择图片'},
                            server: '/attachment/uploadjson',
                            fileNumLimit: 10,
                            formData: {module:'bookspipelinein',action:'booking'},
                            required: false,
                            uploaded: '{{ $uploaded1 }}',
                            basePath: '/attachment/preview/id/',
                            deletePath: @if($type =='edit' || $type =='back' || $type=='addattach')
                                '/attachment/deljson/'
                            @else
                                '/attachment/deljson/type/1'
                            @endif,
                            accept: {
                                title: '图片',
                                extensions: 'jpg,png,txt,pdf',
                                mimeTypes: '.jpg,.png,.txt,.pdf'
                            }
                        }"
                        >
                    </fieldset>
                </div>

            </div>
            <!--upload end-->
            <div class="remarks">

                @if($type == 'sure' || $type == 'back' ||($docsource == 2 && $bookinginstatus == 2))
                    <fieldset>
                        <legend>意见</legend>
                        <textarea id="bookpipelineinmarks" name="stockmarks" data-toggle="autoheight" cols="auto" rows="3"
                                  placeholder="请在此处填写"></textarea>
                    </fieldset>
                @endif
                @if($type == 'review')
                    <fieldset>
                        <legend>操作</legend>
                        <textarea id="bookpipelineinmarks" name="stockmarks" data-toggle="autoheight" cols="auto" rows="3"
                                  placeholder="请在此处填写审核意见">{{$stockmarks}}</textarea>
                    </fieldset>
                @endif
                <br><br>
                <div class="text-center btns-user">
                    @if($type == 'review')
                        <button  type="button" onclick="bookpipelineinsubmit(5)"
                                class="btn btn-green btn-lg">审核通过
                        </button>&nbsp;&nbsp;&nbsp;
                        <button  type="button" onclick="bookpipelineinsubmit(7)"
                                class="btn btn-red btn-lg">审核不通过
                        </button>&nbsp;&nbsp;&nbsp;
                    @endif
                    @if($type == 'edit' || $type!='look')
                        @if($docsource == 2 && $bookinginstatus == 2)
                            <button  type="button" onclick="bookpipelineinsubmit(4)"
                                    class="btn btn-green btn-lg">提交
                            </button>&nbsp;&nbsp;&nbsp;
                            <button  type="button" onclick="bookpipelineinsubmit(10)"
                                    class="btn btn-red btn-lg">驳回
                            </button>&nbsp;&nbsp;&nbsp;
                        @elseif($bookinginstatus < 3 || $bookinginstatus == 7)
                            <button  type="button" onclick="bookpipelineinsubmit(2)"
                                    class="btn btn-green btn-lg">保存
                            </button>&nbsp;&nbsp;&nbsp;
                            <button  type="button" onclick="bookpipelineinsubmit(4)"
                                    class="btn btn-green btn-lg">提交
                            </button>&nbsp;&nbsp;&nbsp;
                        @endif
                    @endif
                    @if($type == 'back')
                        <button type="button" onclick="bookpipelineinsubmit(7)"
                                class="btn btn-red btn-lg">退回
                        </button>&nbsp;&nbsp;&nbsp;
                    @endif

                        @if($type == 'look')
                            <button id="bookpipein1" type="button" onclick="bookpipeinPrint()" class="btn btn-success btn-lg">打印设计</button>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button id="bookpipein2" type="button" onclick="bookpipeinPrint()" class="btn btn-success btn-lg">打印入库预约单</button>&nbsp;&nbsp;&nbsp;&nbsp;
                        @endif
                    <button type="button" onclick="showRecords()" class="btn btn-gray btn-lg">操作记录</button>
                </div>
                <br><br>
                <div class="remarks hideshow" style="display: none;">
                    <fieldset>
                        <legend>操作记录明细</legend>
                        <div class="addTable">
                        </div>
                    </fieldset>
                </div>
                <br><br>
            </div>
        </form>
    </div>
</div>
@if($type == 'edit' || !$type && $viewtype!='look')
    <div id="bookpipelinein_tb">
        <button type="button" class="btn btn-blue" onclick="addbookshipin()"><i class="fa fa-plus"></i> 添加</button>
        <button type="button" class="btn btn-red" onclick="subbookshipin()"><i class="fa fa-times"></i> 删除</button>
        <button type="button" class="btn btn-green" onclick="editbookshipin()"><i class="fa fa-edit"> 修改</i></button>
    </div>
@elseif($type =='sure' || $type=='register')
    <div id="bookpipelinein_tb">
        <button type="button" class="btn btn-green" onclick="editbookshipin()">
            <i class="fa fa-pencil-square-o">编辑</i>
        </button>
    </div>
@endif
<script src="/static/common/js/common.js"></script>
<script type="text/javascript">
    // JS API 调用日期选择器
    $.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd', minDate: '2016-10-01'})

    //----------------------操作记录

    addLog($.CurrentNavtab.find('.addTable'),{{$id or 0}}, '23');

    //----------------------操作记录 end
</script>

<script type="text/javascript">
    $(document).on(BJUI.eventType.afterInitUI, function (event) {
        //合计数量初始化
        var newcount = 0;
        var allData = $.CurrentNavtab.find("#bookpipelinein-detail-table").data('allData');
        if (allData != undefined) {
            for (var i = allData.length - 1; i >= 0; i--) {
                newcount = parseFloat(newcount) + parseFloat(allData[i].bookinginqty);
            }

            $.CurrentNavtab.find('#totalcount').html(newcount);
        } else {
            $.CurrentNavtab.find('#totalcount').html(0);
        }
    });
    $(function () {

/*        $("#isbusinesscheck").change(function () {
            var v = $("#isbusinesscheck").val();
            if (v == 0) {
                $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input");
                $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "");
                $.CurrentNavtab.find(".isbusinesscheckview").hide();
            }
            else {
                $.CurrentNavtab.find("#businesschecktypeview").attr("class", "row-input required");
                $.CurrentNavtab.find("#businesschecktype").attr("data-rule", "required");
                $.CurrentNavtab.find(".isbusinesscheckview").show();
            }
        });*/

        $("#booking_pipelinein_customer_sysno").change(function () {
            var v = $("#booking_pipelinein_customer_sysno option:selected");

            $("#bookpipelinein_customername").val(v.text());
            $.CurrentNavtab.find('#bookpipelinein-detail-table').datagrid('reload', {data: []});
            //合计数量
            $.CurrentNavtab.find('#totalcount').html(0);
        });

    });

    function addbookshipin() {
        var customer_sysno = $.CurrentNavtab.find('#booking_pipelinein_customer_sysno').val();
        var contract_sysno = $.CurrentNavtab.find('#bookpipelinein_contract_sysno').val();
        if (customer_sysno.length > 0 && contract_sysno.length > 0) {
            BJUI.dialog({
                id: 'sotckshipin-detail-{{$id}}',
                url: '/bookpipelinein/detailedit/handlestatus/add/uid/' + customer_sysno + '/cid/' + contract_sysno,
                title: '增加入库单明细',
                mask: true,
                width: 700,
                height: 600,
                mask: true
            });
        } else {
            BJUI.alertmsg('warn', '请先选中客户和合同再添加明细单');
        }
        return;
    }

    function subbookshipin() {

        var selectdata = $.CurrentNavtab.find('#bookpipelinein-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $("#bookpipelinein-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#bookpipelinein-detail-table').datagrid('reload', {data: allData});
        }
    }

    function editbookshipin() {
        var selectedDatas = $.CurrentNavtab.find("#bookpipelinein-detail-table").data('selectedDatas');
        var customer_sysno = $.CurrentNavtab.find('#booking_pipelinein_customer_sysno').val();
        var contract_sysno = $.CurrentNavtab.find('#bookpipelinein_contract_sysno').val();

        if (selectedDatas != undefined && selectedDatas.length == 1 && customer_sysno.length > 0 && contract_sysno.length > 0) {
            BJUI.dialog({
                url: '/bookpipelinein/detailedit/handlestatus/edit/uid/' + customer_sysno + '/cid/' + contract_sysno + '/type/' + "{{$type}}",
                type: 'POST',
                data: {selectedDatasArray: selectedDatas[0], status: '{{$bookinginstatus}}'},
                mask: true,
                title: '管入库单明细',
                width: 700,
                height: 600,
                mask: true
            });
        } else {
            BJUI.alertmsg('warn', '请选中一行进行修改');
        }
        return;
    }

    function bookpipelineinsubmit(step) {
        if (step == 2) {
            var status = $("#bookinginstatus").val();
            if (status == 7) {
                $.CurrentNavtab.find("#bookinginstatus").val(7);
            } else {
                $.CurrentNavtab.find("#bookinginstatus").val(step);
            }
        } else if (step == 7) {
            $.CurrentNavtab.find("#bookpipelineinmarks").attr("data-rule", "required");
            $.CurrentNavtab.find("#bookinginstatus").val(7);
        } else if(step == 10){
            $.CurrentNavtab.find("#bookpipelineinmarks").attr("data-rule", "required");
            $.CurrentNavtab.find("#bookinginstatus").val(10);
        } else {
            $.CurrentNavtab.find("#bookinginstatus").val(step);
            $.CurrentNavtab.find('#bookpipelineinform').data('validator').options.ignore = '#bookpipelineinmarks';
        }
        var Obj = $.CurrentNavtab.find('#bookpipelinein-detail-table').data('allData');



        $.CurrentNavtab.find("#bookpipelineindetaildata").val(JSON.stringify(Obj));

        // console.log($('#bookpipelineindetaildata').val());return;
        if($.CurrentNavtab.find("#cs_employee_sysno option:selected").val()!='')
        {
            $.CurrentNavtab.find("#cs_employeename").val($.CurrentNavtab.find("#cs_employee_sysno option:selected").text());
        }        
        $.CurrentNavtab.find("#bookpipelinein_contractno").val($.CurrentNavtab.find("#bookpipelinein_contract_sysno option:selected").text());
        $.CurrentNavtab.find("#bookpipelinein_customername").val($.CurrentNavtab.find("#booking_pipelinein_customer_sysno option:selected").text());

        $.CurrentNavtab.find('#bookingindate').removeAttr("disabled");
        $.CurrentNavtab.find('#booking_pipelinein_customer_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#bookpipelinein_contract_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#shipproxyname').removeAttr("disabled");
        $.CurrentNavtab.find('#issave').removeAttr("disabled");
        $.CurrentNavtab.find('#cs_employee_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#businesscheckunitname').removeAttr("disabled");

        $.ajax({
            url:'bookshipin/ajaxjudgestorage',
            data:{ bookshipindetaildata:Obj},
            type:'POST',
            success:function(options){
                var arr = $.parseJSON(options);
                if(arr.code==300){
                    BJUI.alertmsg('confirm', arr.message, {
                        okCall: function() {
                            submit();                          
                        }
                    });
                }else{

                    submit();
                }
            }

        });

    }

    function submit()
    {
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentNavtab.find('#bookpipelineinform'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('reloadFlag', 'navab515,navab514','navab513');
            }
        });        
    }


    $("#bookpipelinein_contract_sysno").change(function (){
        var contract_sysno = $("#bookpipelinein_contract_sysno").val();
        BJUI.ajax('doajax', {
            url:'/contract/contractgoodsjson/id/'+contract_sysno,
            loadingmask: true,
            okCallback: function(json, options) {
                var str = json_array(json).join(',');
               $("#bookpipelineincontractgoods").val(str);
            }
        });
    })

    function json_array(data){
        var len=eval(data).length;
        var arr=[];
        for(var i=0;i<len;i++){
            arr[i]=data[i].goodsname;
        }
        return arr;
    }
if("{{$id}}")
{
    $(function(){
        var contract_sysno = $("#bookpipelinein_contract_sysno").val();
        BJUI.ajax('doajax', {
            url:'/contract/contractgoodsjson/id/'+contract_sysno,
            loadingmask: true,
            okCallback: function(json, options) {
                var str = json_array(json).join(',');
               $("#bookpipelineincontractgoods").val(str);
            }
        });
    });
}
//打印设计
    function bookpipeinPrint(type) {
        var id=$.CurrentNavtab.find("#id").val();

        BJUI.ajax('doajax', {
            url: "/bookpipelinein/executePrint/id/"+id,
            loadingmask: true,
            okCallback: function(json, options) {
                if(json.code == 300){
                    BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }else{

                    var LODOP; //声明为全局打印变量
                    //打印入库单字段布局
                    var date = new Date();
                    var now = date.getFullYear()+"-" + (date.getMonth()+1) + "-" + date.getDate();

                    var CreateStockIn = function CreateStockIn() {
                        data = json;
                        LODOP = getLodop();
                        LODOP.PRINT_INITA(0, 0, 1200, 600, "船入库单");
                        // LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");

                        LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
                        LODOP.SET_PRINT_STYLEA(2, "FontName", "黑体");
                        LODOP.SET_PRINT_STYLEA(2, "FontSize", 30);

                        LODOP.ADD_PRINT_TEXT(90, 130, 200, 24, now);
                        LODOP.ADD_PRINT_TEXT(140, 130, 200, 24, '管输');
                        LODOP.ADD_PRINT_TEXT(140, 380, 200, 24, data.goodsname);
                        LODOP.ADD_PRINT_TEXT(140, 630, 200, 24, data.beqty);
                        LODOP.ADD_PRINT_TEXT(190, 130, 200, 24, data.storagetankname);
                    }
                    Setup(CreateStockIn)

                }
            }
        })

    };
</script>