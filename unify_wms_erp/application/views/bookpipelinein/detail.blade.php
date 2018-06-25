<div class="bjui-pageContent">
    <form id="bookpipelinein-detail-form" action="" class="datagrid-edit-form"
          data-toggle="validate" data-data-type="json">
        <input type="hidden" name="bookinginstatus" id="bookinginstatus" value="{{$status}}">
        <div class="bjui-row col-2">
            <label class="row-label">品名</label>
            <div class="row-input required">
                <input type="hidden" name="goods_sysno" readonly id="obj_goodsname" value="{{$goods_sysno}}">

                <input type="text" name="goodsname" readonly value="{{$goodsname}}" data-rule="required"
                       id="s_goodsname"
                       data-toggle="findgrid" data-options="{
                include: 'goodsname:goodsname, goods_sysno:sysno,unitname:unitname',
                dialogOptions: {width:'800',height:'500',title:'货品资料',maxable:true,resizable:true,mask:true},
                gridOptions: {

                    tableWidth:'90%',
                    local: 'local',
                    paging: {pageSize:5},
                    filterThead:true,
                    postData: {customer_sysno:{{$customer_sysno}},contract_sysno:{{$contract_sysno}}},
                    dataUrl: '/customer/customergoodslistJson',
                     columns: [
                                {name:'sysno', label:'id',align:'center'},
                                {name:'goodsno', label:'货品编号',align:'center'},
                                {name:'goodsname', label:'货品名称',align:'center'}
                            ],
                    showLinenumber:false,
                    fullGrid:true
                },
            }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">规格</label>

            <div class="row-input required">
                <select data-toggle="selectpicker" name="goods_quality_sysno" id="goods_quality_sysno"
                        data-width="100%" data-rule="required" data-live-search="true" data-size="10"
                        @if($type == 'sure') disabled @endif>
                    <option value="" selected="">请选择</option>
                    @foreach($goodsqualitylist as $item)
                        <option value="{{$item['sysno']}}"
                                @if($item['sysno'] == $goods_quality_sysno) selected @endif>
                            {{$item['qualityname']}}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="goods_quality_name" id="goods_quality_name" value="{{$goods_quality_name}}">
            </div>

            <label class="row-label">货物性质</label>

            <div class="row-input required">
                <select id="bookshipin_goodsnature" data-toggle="selectpicker" name="goodsnature"
                        data-width="100%" data-rule="required"
                        @if($type == 'sure') disabled @endif>
                    <option value="">请选择</option>
                    <option value="1" @if($goodsnature==1) selected @endif>保税</option>
                    <option value="2" @if($goodsnature==2) selected @endif>外贸</option>
                    <option value="3" @if($goodsnature==3) selected @endif>内贸转出口</option>
                    <option value="4" @if($goodsnature==4) selected @endif>内贸内销</option>
                </select>
            </div>

            <label class="row-label">计量单位</label>

            <div class="row-input">
                <input type="text" name="unitname" value="{{$unitname or '吨' }}" data-rule="required" readonly>
            </div>


            <label class="row-label">预计到货日期</label>

            <div class="row-input required">
                <input type="text" name="bookingindate" @if($type == 'sure') readonly @endif value="{{$bookingindate}}"
                       data-rule="required">
            </div>

            <label class="row-label">数量</label>

            <div class="row-input required">
                <input type="text" name="bookinginqty" @if($type == 'sure') readonly @endif value="{{$bookinginqty}}"
                       data-rule="required;number;range[0~]">
            </div>

            <label class="row-label">进货罐号</label>

            <div class="row-input required">
                <select data-toggle="selectpicker" name="storagetank_sysno" id="storagetank_sysno"
                        data-width="100%" data-rule="required" data-live-search="true" data-size="10"
                >
                    <option value="" selected="">请选择</option>
                    @foreach($storagetanklist as $item)
                        <option value="{{$item['sysno']}}"
                                @if($item['sysno'] == $storagetank_sysno) selected @endif>{{$item['storagetankname']}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="storagetankname" id="storagetankname" value="{{$storagetankname}}">
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
        <li>
            <button type="button" class="btn-green" data-icon="save" onclick="saveBoshipDetail()">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
        <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern: 'yyyy-MM-dd HH:mm:ss', minDate: '2016-10-01'})
</script>

<script type="text/javascript">

    $("#bookshipin_goodsnature").change(function () {
        $("#declarationdiv").attr("class", "row-input");
        $("#release_nodiv").attr("class", "row-input");
        $("#release_no").attr("data-rule", "a");
        $("#declaration").attr("data-rule", "a");
    })

    function saveBoshipDetail() {

        var bookinginstatus = parseFloat($("#bookinginstatus").val());
        //当预约单状态为已审核的时候 可以登记
        if (bookinginstatus == 5) {
            if ($("#bookshipin_goodsnature").val() == 2) {

                $("#release_nodiv").attr("class", "row-input required");
                $("#release_no").attr("data-rule", "required");

            } else if ($("#bookshipin_goodsnature").val() == 1) {

                $("#release_nodiv").attr("class", "row-input required");
                $("#release_no").attr("data-rule", "required");

                $("#declarationdiv").attr("class", "row-input required");
                $("#declaration").attr("data-rule", "required");
            }
        }

        $("#bookshipin_goodsnature").removeAttr('disabled');
        $("#goods_quality_sysno").removeAttr('disabled');

        var handlestatus = $("#handlestatus").html();
        var goods_quality_name = $("#goods_quality_sysno option:selected").text();
        var storagetankname = $("#storagetank_sysno option:selected").text();

        $('#bookpipelinein-detail-form').isValid(function (v) {
            if (v) {
                var data = $("#bookpipelinein-detail-form").serializeJson();
                var allData = $("#bookpipelinein-detail-table").data('allData');
                data.goods_quality_name = goods_quality_name;
                data.storagetankname = storagetankname;

                if (handlestatus == 'add') {

                    if (typeof  allData != 'undefined') {
                        allData.push(data);
                    } else {
                        allData = [data];
                    }

                    $('#bookpipelinein-detail-table').datagrid('reload', {data: allData});
                    BJUI.dialog('closeCurrent');
                }
                if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#bookpipelinein-detail-table').datagrid('updateRow', "{{$gridIndex}}", data);
                    var obj = $.CurrentNavtab.find('#bookpipelinein-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#bookpipelinein-detail-table').datagrid('reload', {data: allData});
                    BJUI.dialog('closeCurrent', '');
                }
            } else {
                console.log('no');
            }
        });
    }
</script>