<div class="bjui-pageContent">
    <form id="stockshipinadddetail" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
            <label class="row-label">客户</label>

            <div class="row-input required">
                <select name="customer_sysno" id="retank_customer_sysno" data-size="5"
                        data-toggle="selectpicker" data-live-search="true"
                        data-width="100%">
                    <option value="">请选择</option>
                    @foreach($customerlist as $item)
                        <option value="{{$item['customer_sysno']}}"
                                @if($item['customer_sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="customer_name" id="customer_name"
                       value="{{$customer_name}}">
            </div>

            <label class="row-label">库存单号</label>
            <div class="row-input required">
                <input type="hidden" name="storagetank_sysno" value="{{$stockretank_out_sysno}}">
                <input type="hidden" name="stockin_sysno" value="{{$stockin_sysno}}">
                <input type="hidden" name="sysno" value="{{$sysno}}">
                <input type="hidden" name="release_num" value="{{$release_num}}">
                <input type="text" name="stockin_no" value="{{$stockin_no}}" readonly data-rule="required" placeholder="点放大镜按钮查找" onclick="findgrid_test()">
            </div>

            <label class="row-label">船名</label>
            <div class="row-input ">
                <input type="text" name="shipname" readonly  value="{{$shipname}}">
            </div>

            <label class="row-label">罐号</label>
            <div class="row-input ">
                <input type="text" id="storagetankname" name="storagetankname" readonly  value="{{$stockretank_out_no}}">
            </div>

            <label class="row-label">品名</label>
            <input type="hidden" value="{{$gsysno}}">
            <div class="row-input ">
                <input type="text" name="goodsname" readonly  value="{{$goodsname}}">
                <input type="hidden" name="tank2.goodsname" readonly  value="{{$goodsname}}">
            </div>

            <label class="row-label">规格</label>
            <div class="row-input ">
                <input type="hidden" name="goodsnature" readonly  value="{{$goodsnature}}">
                <input type="text" name="qualityname" readonly  value="{{$qualityname}}">
            </div>

            <label class="row-label">计量单位</label>
            <div class="row-input ">
                <input type="text" name="unitname" readonly  value="{{$unitname}}">
            </div>

            <label class="row-label">结存量</label>
            <div class="row-input ">
                <input type="text" name="tank_stockqty" readonly  value="{{$tank_stockqty}}"  >
            </div>

            {{--<label class="row-label">货物性质</label>--}}
            {{--<div class="row-input required">--}}
                {{--<select data-toggle="selectpicker" name="goodsnature" data-width="100%" data-rule="required">--}}
                    {{--<option value="" selected="">请选择</option>--}}
                    {{--<option value="1" @if($goodsnature==1) selected @endif>保税</option>--}}
                    {{--<option value="2" @if($goodsnature==2) selected @endif>外贸</option>--}}
                    {{--<option value="3" @if($goodsnature==3) selected @endif>内贸转出口</option>--}}
                    {{--<option value="4" @if($goodsnature==4) selected @endif>内贸内销</option>--}}
                {{--</select>--}}
            {{--</div>--}}

            <label class="row-label">倒入罐号</label>
            <div class="row-input required">
                <input type="hidden" name="tank2.sysno" value="{{$stockretank_in_sysno}}">
                <input type="hidden" id="tank2_goods_sysno" name="tank2.goods_sysno" value="{{$goods_sysno}}">
                <input type="hidden"  id="actualcapacity" name="tank2.actualcapacity" value="{{$actualcapacity}}">
                <input type="hidden"  id="tank_stockqty" name="tank2.tank_stockqty" value="{{$tank_stockqty}}">
                <input type="text" name="tank2.storagetankname" id="tank2.storagetankname" value="{{$stockretank_in_no}}" data-rule="required"  data-toggle="findgrid" readonly data-options="{
                group: 'tank2',
                include: 'sysno:sysno,storagetankname:storagetankname,goodsname:goodsname,goods_sysno:goods_sysno,qualityname:qualityname,unitname:unitname,orderinqty:orderinqty,orderoutqty:orderoutqty,actualcapacity:actualcapacity,tank_stockqty:tank_stockqty',
                dialogOptions: {width:'800',height:'600',title:'储罐详情',maxable:true,resizable:true,mask:true},
                gridOptions: {
                    width:'100%',
                    height:'100%',
                    tableWidth:'99.8%',
                    local: 'local',
                    paging: {pageSize:10},
                    dataUrl: '/retank/getStocklistJson/goods_sysno/{{$goods_sysno}}',
                    columns: [
                        {name:'sysno', label:'id'},
                        {name:'storagetankname', label:'储罐编号'},
                        {name:'goodsname', label:'品名'},
                        {name:'qualityname', label:'规格'},
                        {name:'unitname', label:'计量单位'},
                        {name:'tank_stockqty', label:'现存量'},
                        {name:'orderinqty', label:'待入量'},
                        {name:'actualcapacity', label:'总罐容'}
                    ],
                    showLinenumber:false
                },
            }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">申请倒入数量</label>
            <div class="row-input required">
                <input type="text" id="bookingretankqty" name="bookingretankqty" value="{{$bookingretankqty}}" data-rule="required;number;range(0~);not0">
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
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveretank()">保存</button></li>
        <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    function findgrid_retank(obj) {
        goods_sysno = $('#tank2_goods_sysno').val();
        if(!goods_sysno || goods_sysno=='' || goods_sysno==null){
            BJUI.alertmsg('warn','<h4>请选择移入罐号!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        BJUI.findgrid({
            group: 'obj',
            include: 'sysno:sysno,instockqty:instockqty,stockqty:stockqty,stockin_no:stockin_no,goodsname:goodsname,goods_sysno:goods_sysno,unitname:unitname,stockno:stockno,goodsqualityname:goodsqualityname,goodsnature:goodsnature,storagetankname:storagetankname,storagetank_sysno:storagetank_sysno,firstfrom_sysno:firstfrom_sysno,contract_sysno:contract_sysno',
            dialogOptions: {width:'1000',height:'500',title:'库存详细信息',maxable:true,resizable:true,mask:true},
            gridOptions:{
                width:'80%',
                tableWidth:'97%',
                local: 'local',
                paging: {pageSize:15},
                dataUrl: '/retank/stockList/goods_sysno/'+goods_sysno,
                columns: [
                    {name:'sysno', label:'id',align:'center'},
                    {name:'customername', label:'客户',align:'center'},
                    {name:'stockno', label:'入库单号',align:'center'},
                    {name:'instockdate', label:'入库日期',align:'center'},
                    {name:'goodsname', label:'品名',align:'center'},
                    {name:'goodsqualityname', label:'质量标准',align:'center'},
                    {name:'instockqty', label:'入库数量',align:'center'},
                    {name:'numberqty', label:'余量',align:'center'},
                    {name:'clockqty', label:'锁定数量',align:'center'},
                    {name:'stockqty', label:'可用数量',align:'center'},

                ],
                showLinenumber:false
            },
        })
    }

    function saveretank(){
        var handlestatus = $("#handlestatus").html();
        $('#stockshipinadddetail').isValid(function(v){
            if(v) {
                var data = $("#stockshipinadddetail").serializeJson();
                console.log(data);
                var allData = $("#retank-detail-table").data('allData');
                data.stockretank_out_no = data['storagetankname'];//出罐罐号
                data.goodsname = data['goodsname'];//品名
                data.tank_stockqty = data['tank_stockqty'];//现存量
                data.qualityname = data['qualityname'];//规格
                data.unitname = data['unitname'];//计量单位
                data.stockretank_in_no = data['tank2.storagetankname'];//入罐罐号
                data.stockretank_out_sysno = data['storagetank_sysno'];
                data.stockretank_in_sysno = data['tank2.sysno'];//入罐id
                data.actualcapacity=data['tank2.actualcapacity'];
                data.shipname=data['shipname'];
                data.customer_sysno=data['customer_sysno'];

                data.stockin_no=data['stockin_no'];
                var customer_name=$('#retank_customer_sysno option:selected').html();
                data.customername=customer_name;

                var bookingretankqty = data.bookingretankqty;
                var actualcapacity =data['tank2.actualcapacity']
                var tank_stockqty = data['tank2.tank_stockqty'];//现存量
                var storagetankname = data['storagetankname'];
                var storagetankname2 = data['tank2.storagetankname'];

                if (storagetankname !== storagetankname2) {
                        if (parseFloat(bookingretankqty) <= parseFloat(actualcapacity-tank_stockqty)) {
                            if (handlestatus == 'add') {

                                if (typeof  allData != 'undefined') {
                                    allData.push(data);
                                } else {
                                    allData = [data];
                                }
                                console.log(allData);
                                console.log(data);

                                $('#retank-detail-table').datagrid('reload', {data: allData});
                                BJUI.dialog('closeCurrent', '');
                            } else if (handlestatus == 'edit') {
                                $.CurrentNavtab.find('#retank-detail-table').datagrid('updateRow', "{{$gridIndex}}", data);
                                var obj = $.CurrentNavtab.find('#retank-detail-table').data('allData');
                                obj["{{$gridIndex}}"] = data;
                                $('#retank-detail-table').datagrid('reload', {data: obj});
                                BJUI.dialog('closeCurrent', '');
                            }

                        } else {
                            BJUI.alertmsg('warn', '<h4>罐容不足!</h4>', {
                                displayPosition: 'middlecenter',
                                displayMode: 'fade'
                            });
                        }
                }else{
                    BJUI.alertmsg('warn', '<h4>罐号不能一样!</h4>', {displayPosition: 'middlecenter', displayMode: 'fade'});
                }
            }
        })
    }

    function findgrid_test() {
        var customer_sysno = parseFloat($("#retank_customer_sysno option:selected").val());
        var goods_sysno=parseFloat($("#tank2_goods_sysno ").val());
        if (customer_sysno) {
            BJUI.findgrid({
                dialogOptions: {width:'1200',height:'600',title:'储罐资料',maxable:true,resizable:true,mask:true},
                gridOptions: {
                    height:'100%',
                    tableWidth:'98%',
                    local: 'local',
                    paging: {pageSize:10},
                    @if($stocktype == 1)
                    dataUrl: '/retank/getRetankStockJson/goods_sysno/'+goods_sysno+'/customer_sysno/'+customer_sysno,
                    @else
                    dataUrl: '/retank/getRetankintsStockJson/goods_sysno/'+goods_sysno+'/customer_sysno/'+customer_sysno,
                    @endif
                    columns: [
                        {name:'sysno', label:'id',align:'center'},
                        {name:'stockin_no', label:'入库单号',align:'center'},
                        {name:'instockdate', label:'入库日期',align:'center'},
                        {name:'goodsname', label:'品名',align:'center'},
                        {name:'shipname', label:'船名',align:'center'},
                        {name:'qualityname', label:'质量标准',align:'center'},
                        {name:'storagetankname', label:'罐号',align:'center'},
                        {name:'instockqty', label:'入库数量',align:'center'},
                        {name:'clockqty', label:'锁定数量',align:'center'},
                        {name:'tank_stockqty', label:'可用数量'},
                        {name:'release_num', label:'报关数量',align:'center',render:function(value){if(value=='' || !value) {return '0'; }}},
                        {name:'goodsnature', label:'货物性质',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}},
                        {name:'customername', label:'客户',align:'center',width:'150'},
                    ],
                    showLinenumber:false
                },
            });
        }
    }
</script>