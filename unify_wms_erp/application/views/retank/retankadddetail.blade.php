<div class="bjui-pageContent">
<form id="stockshipinadddetail" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <label class="row-label">客户</label>
        <input type="hidden" name="release_num" value="{{$release_num}}">
        <div class="row-input ">
            <input type="hidden" name="customer_sysno" id="customer_sysno" readonly value="{{$customer_sysno}}">
            <input type="text" name="customername" id="customer_name" readonly value="{{$customername}}">
        </div>

        <label class="row-label">库存单号</label>
        <div class="row-input ">
            <input type="hidden" name="storagetank_sysno" value="{{$stockretank_out_sysno}}">
            <input type="hidden" name="stockin_sysno" value="{{$stockin_sysno}}">
            <input type="text" name="stockin_no" value="{{$stockin_no}}" readonly  >
        </div>

        <label class="row-label">船名</label>
        <div class="row-input">
            <input type="text" name="shipname" readonly  value="{{$shipname}}">
        </div>

        <label class="row-label">倒出罐号</label>
        <div class="row-input ">
            <input type="hidden" name="stockretank_out_sysno" readonly value="{{$stockretank_out_sysno}}">
            <input type="hidden" name="out_stock_sysno" readonly value="{{$out_stock_sysno}}">
            <input type="text" name="stockretank_out_no" readonly value="{{$stockretank_out_no}}">
        </div>

        <label class="row-label">品名</label>
        <input type="hidden" value="{{$gsysno}}">
        <div class="row-input ">
            <input type="text" name="goodsname" readonly  value="{{$goodsname}}">
            <input type="hidden" name="tank2.goodsname" readonly  value="{{$goodsname}}">
        </div>

        <label class="row-label">规格</label>
        <div class="row-input ">
            <input type="text" name="qualityname" readonly  value="{{$qualityname}}">
        </div>

        <label class="row-label">计量单位</label>
        <div class="row-input ">
            <input type="text" name="tank1.unitname" readonly  value="{{$unitname}}">
        </div>

        <label class="row-label">储罐结存量</label>
        <div class="row-input ">
            <input type="hidden" name="goodsnature" readonly  value="{{$goodsnature}}"  >
            <input type="text" name="tank1.tank_stockqty" readonly  value="{{$tank_stockqty}}"  >
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
        <div class="row-input ">
            <input type="hidden" name="actualcapacity" readonly value="{{$actualcapacity}}">
            <input type="hidden" id="tank_stockqty2" name="tank_stockqty2" readonly value="{{$tank_stockqty2}}">
            <input type="hidden" name="stockretank_in_sysno" readonly value="{{$stockretank_in_sysno}}">
            <input type="text" name="stockretank_in_no" readonly value="{{$stockretank_in_no}}">
        </div>

        <label class="row-label">申请倒入数量</label>
        <div class="row-input ">
            <input type="text" id="bookingretankqty" name="bookingretankqty" value="{{$bookingretankqty}}"  readonly>
        </div>

        <label class="row-label">实际倒入数量</label>
        <div class="row-input required">
            <input type="text" id="stockretankqty" name="stockretankqty" value="{{$stockretankqty}}" data-rule="required;number;range(0~);not0">
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
            if(v){
                var data  = $("#stockshipinadddetail").serializeJson();
                console.log(data);
                var allData  = $("#retank-detail-table").data('allData');
                data.stockretank_out_no = data['stockretank_out_no'];//出罐罐号
                data.goodsname = data['goodsname'];//品名
                data.tank_stockqty = data['tank1.tank_stockqty'];//现存量
                data.qualityname = data['qualityname'];//规格
                data.unitname = data['tank1.unitname'];//计量单位
                data.stockretank_in_no = data['stockretank_in_no'];//入罐罐号
                data.stockretank_out_sysno = data['stockretank_out_sysno'];
                data.stockretank_in_sysno = data['stockretank_in_sysno'];//入罐id
                data.out_stock_sysno=data['out_stock_sysno'];//移出罐库存记录id
                data.release_num=data['release_num']
                var out_goodsname = data['tank1.goodsname'];
                var in_goosname = data['tank2.goodsname'];
                var stockretankqty = data.stockretankqty;
                var actualcapacity=data.actualcapacity
                var  tank_stockqty2 = $('#tank_stockqty2').val();//现存量
                if (parseFloat(stockretankqty) <= parseFloat(actualcapacity-tank_stockqty2)) {
                    if (handlestatus == 'add') {
                        if(typeof  allData != 'undefined'){
                            allData.push(data);
                        }else{
                            allData = [data] ;
                        }
                        $('#retank-detail-table').datagrid('reload',  {data:allData});
                        BJUI.dialog('closeCurrent','');
                    }else if (handlestatus == 'edit') {
                        $.CurrentNavtab.find('#retank-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                        var obj = $.CurrentNavtab.find('#retank-detail-table').data('allData');
                        obj["{{$gridIndex}}"] = data;
                        $('#retank-detail-table').datagrid('reload',  {data:obj});
                        BJUI.dialog('closeCurrent','');
                    }
                }else{
                    BJUI.alertmsg('warn','<h4>罐容不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                }
            }
        })
    }
</script>