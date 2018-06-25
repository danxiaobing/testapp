<div class="bjui-pageContent">
    <form id="clear_adddetail" action="/clearstock/detailsubmit" class="datagrid-edit-form prefix_adddetail" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
            <label class="row-label">库存ID</label>
            <div class="row-input required">
              {{--  <input type="hidden" name="obj.sysno" value="{{$out_stock_sysno}}" readonly> id--}}
                {{--<input type="hidden" name="obj.stockin_no" value="{{$stockin_no}}" readonly> --}}{{--来源单号--}}
                {{--<input type="hidden" name="obj.instockdate" value="{{$instockdate}}" readonly> --}}{{--入库时间--}}
                {{--<input type="hidden" name="obj.goodsname" id="obj_goodsname" value="{{$goodsname}}" readonly> --}}{{--商品名--}}
                {{--<input type="hidden" name="obj.goods_sysno" id="obj_goods_sysno" value="{{$goods_sysno}}" readonly>--}}{{--d--}}
                {{--<input type="hidden" name="obj.unitname" id="obj_unitname" value="{{$unitname}}" readonly>--}}{{--单位--}}
                {{--<input type="hidden" name="obj.qualityname" value="{{$qualityname}}" readonly> --}}{{--***规格--}}
                {{--<input type="hidden" name="obj.stockno" value="{{$stockno}}" readonly>--}}
                <input type="hidden" id="goodsnature" name="obj.goodsnature" value="" readonly> {{--***货物性质--}}
                <input type="hidden" id="storagetank_sysno" name="obj.storagetank_sysno" value="" readonly> {{--***储罐id--}}
                <input type="hidden" id="customer_sysno" name="obj.customer_sysno" value="" readonly> {{----}}{{--客户id--}}
                <input type="hidden" id="customername" name="obj.customername" value="" readonly> {{----}}{{--冗余客户名称--}}
                <input type="hidden" id="goods_sysno" name="obj.goods_sysno" value="" readonly> {{----}}{{--货品id--}}
               {{-- <input type="hidden" name="obj.goodsqualityname" value="{{$goodsqualityname}}" readonly>--}}
                {{--<input type="hidden" name="obj.goodsqualityname" value="{{$qualityname}}" readonly>--}}{{--规格--}}
                {{--<input type="hidden" name="obj.firstfrom_sysno" value="{{$firstfrom_sysno}}" readonly>--}}
                {{--<input type="hidden" name="obj.contract_sysno" value="{{$contract_sysno}}" readonly>--}}
                {{--<input type="hidden" name="obj.instockqty" value="{{$instockqty}}" readonly> --}}{{--入库数量--}}
                {{--<input type="hidden" id="release_num" name="obj.release_num" value="{{$release_num}}" readonly> --}}{{--报关数量--}}
                {{--<input type="hidden" id="unrelease_num" name="obj.unrelease_num" value="{{$unrelease_num}}" readonly> --}}{{--未报关数量--}}
                <input type="text" name="obj.sysno" id="obj_sysno" value="{{$out_stock_sysno}}" data-rule="required" data-toggle="findgrid" readonly data-options="{
            group: 'obj',
            include: 'outstockqty:outstockqty,sysno:sysno,customername:customername,customer_sysno:customer_sysno,instockdate:instockdate,instockqty:instockqty,stockqty:stockqty,stockin_no:stockin_no,goodsname:goodsname,shipname:shipname,storagetank_sysno:storagetank_sysno,storagetankname:storagetankname,tank_stockqty:tank_stockqty,goods_sysno:goods_sysno,unitname:unitname,stockno:stockno,goodsqualityname:goodsqualityname,goodsnature:goodsnature,storagetankname:storagetankname,storagetank_sysno:storagetank_sysno,firstfrom_sysno:firstfrom_sysno,contract_sysno:contract_sysno,release_num:release_num,unrelease_num:unrelease_num',
            dialogOptions: {width:'1000',height:'500',title:'库存详细信息',maxable:true,resizable:true,mask:true},
            gridOptions:{
                width:'80%',
                tableWidth:'97%',
                local: 'local',
                paging: {pageSize:5},
                {{--@if($prefix=='edit')--}}
                        dataUrl: '/clearstock/stockListJson/cid/'+{{$customer_sysno}},
                  {{--@else--}}
                        {{--dataUrl: '/stocktrans/stockListJson/cid/'+$('#{{$prefix}}sale_customer_sysno').val(),--}}
                  {{--@endif--}}
                        columns: [
                           {name:'sysno', label:'id',align:'center'},
                            {name:'stockin_no', label:'入库单号',align:'center'},
                            {name:'instockdate', label:'入库日期',align:'center'},
                            {name:'goodsname', label:'品名',align:'center'},
                             {name:'shipname', label:'船名',align:'center',render:function(value){if(!value || value==''){return '--' } }},
                            {name:'goodsqualityname', label:'质量标准',align:'center'},
                            {name:'instockqty', label:'入库数量',align:'center'},
                            {name:'numberqty', label:'余量',align:'center'},
                            {name:'clockqty', label:'锁定数量',align:'center'},
                            {name:'stockqty', label:'可用数量',align:'center'},
                            {name:'storagetankname', label:'储罐号',align:'center'},
                            {name:'tank_stockqty', label:'储罐当前容量',align:'center'},
                            {name:'release_num', label:'报关数量',align:'center',render:function(value){if(value=='' || !value) {return '0'; }}},
                            {name:'goodsnature', label:'货物性质',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}},
                            {name:'customername', label:'客户',align:'center'},
                            {name:'outstockqty', label:'out',align:'center'},

                        ],
                        showLinenumber:false
                    },
                }" placeholder="点放大镜按钮查找">

            </div>
            <input type="hidden" id="stockin_no" name="obj.stockin_no" value="" readonly>
            <label class="row-label">品名</label>
            <div class="row-input">
                <input type="text" id="goodsname" name="obj.goodsname" value="" readonly>
            </div>

            <label class="row-label">入库日期</label>
            <div class="row-input">
                <input type="text" id="instockdate" name="obj.instockdate" value="" readonly>
            </div>

            <label class="row-label">船名</label>
            <div class="row-input">
                <input type="text" id="shipname" name="obj.shipname" value="" readonly>
            </div>
            <label class="row-label">商检量</label>
            <div class="row-input">
                <input type="text" id="instockqty" name="obj.instockqty" value="" readonly>
            </div>
            <label class="row-label">结存量</label>
            <div class="row-input">
                <input type="text" id="stockqty" name="obj.stockqty" value="" readonly>
            </div>
            <label class="row-label">储罐号</label>
            <div class="row-input">
                <input type="text" id="storagetankname" name="obj.storagetankname" value="" readonly>
            </div>
            <label class="row-label">清库量</label>
            <div class="row-input required">
                <input type="text" id="tankclearqty" name="tankclearqty" data-rule="required" value="" >
                <input type="hidden" id="outstockqty" name="obj.outstockqty" data-rule="required" value="" >
            </div>

            <label class="row-label">备注:</label>
            &nbsp;&nbsp;
            <div class="row-input">
            <textarea name="memo" id="memo" data-toggle="autoheight" rows="3"></textarea>
                <input type="hidden" id="firstfrom_sysno" name="obj.firstfrom_sysno" value="">
        </div>
        </div>

    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="button" id="clearstock_save" class="btn-green" data-icon="save">保存</button></li>
    </ul>
</div>

<script type="text/javascript">
    //保存
    $('#clearstock_save').click(function(){
//        var stockin_no = $('#stockin_no').val(); //入库单号
//        var goodsname = $('#goodsname').val(); //品名
//        var instockdate = $('#instockdate').val(); //入库日期
//        var shipname = $('#shipname').val();  //船名
//        var instockqty = $('#instockqty').val();  //商检量
        var stockqty = $('#stockqty').val();   //结存量
//        var memo = $('#memo').val();   //备注
//        var sysno = $('#obj_sysno').val(); //sysno
        var tankclearqty = $('#tankclearqty').val();//清库量
//        var storagetankname = $('#storagetankname').val();//储罐名称
        $('#clear_adddetail').isValid(function(v){
            if(v){
                if (parseFloat(tankclearqty) >=0){
                    if(parseFloat(tankclearqty)<=parseFloat(stockqty)){
                        var data = $("#clear_adddetail").serializeJson();
                        data.stockin_no = data['obj.stockin_no'];
                        data.goodsname = data['obj.goodsname'];
                        data.instockdate = data['obj.instockdate'];
                        data.shipname = data['obj.shipname'];
                        data.instockqty = data['obj.instockqty'];
                        data.stockqty = data['obj.stockqty'];
                        data.storagetankname = data['obj.storagetankname'];
                        data.tankclearqty = data['tankclearqty'];
                        data.memo = data['memo'];
                        data.sysno =data['sysno'];
                        data.stockin_sysno = data['obj.firstfrom_sysno'];
                        data.goodsnature = data['obj.goodsnature'];
                        data.storagetank_sysno = data['obj.storagetank_sysno'];
                        data.customer_sysno = data['obj.customer_sysno'];
                        data.customername = data['obj.customername'];
                        data.goods_sysno = data['obj.goods_sysno'];
                        data.sysno = data['obj.sysno'];
                        data.outstockqty = data['obj.outstockqty'];
                        var allData = $("#clearstock-detail-table").data('allData');

                        if (typeof  allData != 'undefined') {
                            allData.push(data);
                        } else {
                            allData = [data];
                        }

                        $.CurrentNavtab.find('#clearstock-detail-table').datagrid('reload', {data: allData});
                        BJUI.dialog('closeCurrent');
//
                    }else {
                        BJUI.alertmsg('warn','<h4>结存量不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                    }
                }else {
                    BJUI.alertmsg('warn','<h4>清库量必须大于零!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                }
            }
        });
    });

    function addCars()
    {
        alert('addCars');
    }
</script>