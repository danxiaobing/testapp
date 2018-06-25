<div class="bjui-pageContent">
    <form id="{{$prefix}}adddetail" action="/stocktrans/detailsubmit" class="datagrid-edit-form prefix_adddetail" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
            <label class="row-label">库存ID</label>
            <div class="row-input required">
              {{--  <input type="hidden" name="obj.sysno" value="{{$out_stock_sysno}}" readonly> id--}}
                <input type="hidden" name="obj.stockin_no" value="{{$stockin_no}}" readonly> {{--来源单号--}}
                <input type="hidden" name="obj.goodsname" id="obj_goodsname" value="{{$goodsname}}" readonly> {{--商品名--}}
                <input type="hidden" name="obj.goods_sysno" id="obj_goods_sysno" value="{{$goods_sysno}}" readonly>{{--d--}}
                <input type="hidden" name="obj.unitname" id="obj_unitname" value="{{$unitname}}" readonly>{{--单位--}}
                <input type="hidden" name="obj.qualityname" value="{{$qualityname}}" readonly> {{--***规格--}}
                <input type="hidden" name="obj.stockno" value="{{$stockno}}" readonly>
                <input type="hidden" id="goodsnature" name="obj.goodsnature" value="{{$goodsnature}}" readonly> {{--***货物性质--}}
                {{-- <input type="hidden" name="obj.stockqty" value="{{$stockqty}}" readonly> --}}{{--可用库数量--}}
               {{-- <input type="hidden" name="obj.goodsqualityname" value="{{$goodsqualityname}}" readonly>--}}
                <input type="hidden" name="obj.goodsqualityname" value="{{$qualityname}}" readonly>{{--规格--}}
                <input type="hidden" name="obj.firstfrom_sysno" value="{{$firstfrom_sysno}}" readonly>
                <input type="hidden" name="obj.contract_sysno" value="{{$contract_sysno}}" readonly>
                <input type="hidden" name="obj.instockqty" value="{{$instockqty}}" readonly> {{--入库数量--}}
                <input type="hidden" id="release_num" name="obj.release_num" value="{{$release_num}}" readonly> {{--报关数量--}}
                <input type="hidden" id="unrelease_num" name="obj.unrelease_num" value="{{$unrelease_num}}" readonly> {{--未报关数量--}}
                <input type="text" name="obj.sysno" id="obj_sysno" value="{{$out_stock_sysno}}" data-rule="required" data-toggle="findgrid" readonly data-options="{
            group: 'obj',
            include: 'sysno:sysno,instockqty:instockqty,stockqty:stockqty,stockin_no:stockin_no,goodsname:goodsname,shipname:shipname,storagetank_sysno:storagetank_sysno,storagetankname:storagetankname,tank_stockqty:tank_stockqty,goods_sysno:goods_sysno,unitname:unitname,stockno:stockno,goodsqualityname:goodsqualityname,goodsnature:goodsnature,storagetankname:storagetankname,storagetank_sysno:storagetank_sysno,firstfrom_sysno:firstfrom_sysno,contract_sysno:contract_sysno,release_num:release_num,unrelease_num:unrelease_num',
            dialogOptions: {width:'1000',height:'500',title:'库存详细信息',maxable:true,resizable:true,mask:true},
            gridOptions:{
                width:'80%',
                tableWidth:'97%',
                local: 'local',
                paging: {pageSize:5},
                @if($prefix=='edit')
                        dataUrl: '/stocktrans/stockListJson/cid/'+{{$sale_customer_sysno}},
                  @else
                        dataUrl: '/stocktrans/stockListJson/cid/'+$('#{{$prefix}}sale_customer_sysno').val(),
                  @endif
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

                        ],
                        showLinenumber:false
                    },
                }" placeholder="点放大镜按钮查找">

            </div>
            <label class="row-label">剩余数量(吨)</label>
            <div class="row-input required">
                <input type="text" id="stock_qty" name="obj.stockqty" data-rule="required;number;range[0~]" value="{{$stockqty}}" readonly>
            </div>

            <label class="row-label">转移数量(吨)</label>
            <div class="row-input required">
                <input type="text" name="transqty" id="stocktrank_transqty" value="{{$transqty}}" data-rule="required;number;range[0~];range(0~);not0">
            </div>

            <label class="row-label">储罐编号</label>
            <div class="row-input required">
                <input type="text" name="obj.storagetankname" id="storagetankname" value="{{$storagetankname}}" data-rule="required" readonly>
                <input type="hidden" name="obj.storagetank_sysno" id="storagetank_sysno" value="{{$storagetank_sysno}}"  readonly>
                <input type="hidden" name="obj.tank_stockqty" id="tank_stockqty"  value="{{$tank_stockqty}}"  readonly>
            </div>
            {{--<div class="row-input required">
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
                <input type="hidden" name="storagetanksysno" id="storagetanksysno" value="{{$storagetanksysno}}">
            </div>
--}}
            {{--绑定储罐
            <div class="row-input">
                <input type="text" id="trank_storagetankname" name="trank.storagetankname" value="{{$storagetankname}}" size="18" data-rule="required" readonly>
                <input type="hidden" id="storagetank_sysno" name="trank.sysno" value="{{$storagetank_sysno}}" size="18" readonly>
                <input type="hidden" name="trank.goodsname" value="{{$goodsname}}"  readonly>
                <input type="hidden" name="trank.tank_stockqty" id="trank_tank_stockqty"  value="{{$tank_stockqty}}"  readonly>--}}{{--当前存放量--}}{{--
                <a href="javascript:;" onclick="findgrid_stocktrank(this);">绑定储罐</a>
            </div>--}}

            <label class="row-label">船名</label>
            <div class="row-input required">
                <input type="text" id="shipname" name="obj.shipname" data-rule="required;" value="{{$shipname}}" readonly>
            </div>


            <label class="row-label">备注:</label>
            &nbsp;&nbsp;
            <div class="row-input">
            <textarea name="memo" id="memo" data-toggle="autoheight" rows="3">{{$memo}}</textarea>
            <input type="hidden" id="sysno" name="sysno" value="{{$sysno}}">
        </div>
        </div>

    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        @if($prefix=='edit')
            <li><button type="button" id="stocktrankdetail_edits" class="btn-green" data-icon="save">修改</button></li>
        @else
            <li><button type="button" id="stocktrank_save" class="btn-green" data-icon="save">保存</button></li>
        @endif
    </ul>
</div>
<!-- <div id="stockcarin_addcar_tb">
    <button type="button" class="btn btn-blue" data-icon="add" onclick="addCars();"><i class="fa fa-plus"></i> 添加车辆</button>
</div> -->

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    /*
    * 绑定储罐
    * */
   /* function findgrid_stocktrank(obj) {
        goods_sysno = $('#obj_goods_sysno').val();
        if(!goods_sysno || goods_sysno=='' || goods_sysno==null){
            BJUI.alertmsg('warn','<h4>请先选择库存!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        BJUI.findgrid({
            group: 'trank',
            include: 'sysno:sysno,storagetankname:storagetankname,goods_sysno:goods_sysno,tank_stockqty:tank_stockqty,orderinqty:orderinqty,orderoutqty:orderoutqty',
            dialogOptions: {width:'1000',height:'500',title:'储罐详细信息',maxable:true,resizable:true,mask:true},
            gridOptions:{
                width:'80%',
                tableWidth:'97%',
                local: 'local',
                paging: {pageSize:15},
                dataUrl: '/stocktrans/storagetankList/goods_sysno/'+goods_sysno,
                columns: [
                    {name:'sysno', label:'id',align:'center'},
                    {name:'storagetankname', label:'储罐名称',align:'center'},
                    {name:'goodsname', label:'商品名称',align:'center'},
                    {name:'tank_stockqty', label:'当前存放量',align:'center'},
                    {name:'orderinqty', label:'待入量',align:'center'},
                    {name:'orderoutqty', label:'待出量',align:'center'},
                ],
                showLinenumber:false

            },
        })
    }*/

    /*
     * 添加修改明细功能
     * */
    $("#stocktrankdetail_edits").click(function (){
        $('.prefix_adddetail').isValid(function(v){
            if(v){
                var data  = $(".prefix_adddetail").serializeJson();
               // console.log(data);return;
                data.out_stock_sysno = data['obj.sysno'];//来源单号id
                data.stockin_no = data['obj.stockin_no'];//来源单号
                data.goodsname = data['obj.goodsname'];//品名
                data.goods_sysno =data['obj.goods_sysno']//品名id
                data.goodsnature = data['obj.goodsnature']; //性质
                data.qualityname =data['obj.goodsqualityname'] ;//规格
                data.unitname = data['obj.unitname'];//计量单位
                data.stockqty = data['obj.stockqty'];//可用库数量
                data.storagetankname = data['obj.storagetankname'];//罐号
                data.storagetank_sysno = data['obj.storagetank_sysno'];//罐罐id
                data.tank_stockqty = data['obj.tank_stockqty'];//罐罐罐容
                data.firstfrom_sysno = data['obj.firstfrom_sysno'];//来源单号的id
                data.contract_sysno = data['obj.contract_sysno'];//计费合同
                data.instockqty = data['obj.instockqty'];//入库数量
                data.release_num = data['obj.release_num'];//报关数量
                data.unrelease_num = data['obj.unrelease_num'];//未报关数量
                data.goodsnature = data['obj.goodsnature'];//货物性质
                data.shipname = data['obj.shipname'];//船名

              //  data.storagetankname = $('#storagetank_sysno  option:selected').text();//储罐编号
             //   data.storagetank_sysno = $('#storagetank_sysno  option:selected').val();//储罐id
             //     data.storagetankname =data['trank.storagetankname'];     //储罐名称
             //     data.storagetank_sysno = data['trank.sysno'];//储罐id

                var stockqty = data['obj.stockqty'];//可用库数量
                var transqty = $('#stocktrank_transqty').val();//转移数量
                var tank_stockqty = $('#tank_stockqty').val();//储罐当前存放量
                var release_num = $('#release_num').val();//报关数量
                var goodsnature = $('#goodsnature').val();//货物性质
                console.log(data);
                //  data.sysno = data['sysno'];//入库单ID
                if(parseInt(transqty)<= parseInt(stockqty)){
                    if(parseFloat(transqty)<=parseFloat(tank_stockqty)){
                        if((parseInt(goodsnature) !=4 && parseFloat(transqty)<=parseFloat(release_num))|| parseFloat(goodsnature) ==4){
                    $.CurrentNavtab.find(".prefix_retankdetail").datagrid('updateRow', "{{$gridIndex}}" ,data);
                    var obj = $.CurrentNavtab.find(".prefix_retankdetail").data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('.prefix_retankdetail').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent');
                    }else {
                            BJUI.alertmsg('warn','<h4>报关数量不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                     }
                    }else {
                        BJUI.alertmsg('warn','<h4>罐容不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                    }
                }else{
                    BJUI.alertmsg('warn','<h4>库存不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                }

            }
        })
    })
    //保存
    $('#stocktrank_save').click(function(){
      //获取选中的入库单主键或货权转移单主键
        var transqty = $('#stocktrank_transqty').val(); //转移数量
        var stockqty = $('#stock_qty').val(); //转移数量
        var obj_sysno = $('#obj_sysno').val(); //出库库存id
        var obj_goods_sysno = $('#obj_goods_sysno').val();  //商品id
        var obj_unitname = $('#obj_unitname').val();  //计量单位
        var obj_goodsname = $('#obj_goodsname').val();   //商品名称
        var memo = $('#memo').val();   //备注
        var sysno = $('#sysno').val(); //sysno
        var storagetank_name = $('#storagetank_sysno option:selected').text();//储罐名称备用
        var storagetank_sysno = $.CurrentDialog.find('#storagetank_sysno').val();
        var storagetankname = $('#storagetankname').val();//储罐名称
        var tank_stockqty = $('#tank_stockqty').val();//储罐当前存放量
        var release_num = $('#release_num').val();//报关数量
        var goodsnature = $('#goodsnature').val();//货物性质
        var shipname =  $.CurrentDialog.find('#shipname').val();//船名
        console.log(transqty);

        $('.prefix_adddetail').isValid(function(v){
            if(v){
                if(parseFloat(transqty)<=parseFloat(stockqty)){
                    if(parseFloat(transqty)<=parseFloat(tank_stockqty)){
                        if((parseInt(goodsnature) !=4 && parseFloat(transqty)<=parseFloat(release_num))|| parseFloat(goodsnature) ==4){
                        BJUI.ajax('doajax',{
                            url:'/stocktrans/detailsubmit/',
                            data:{sysno:sysno,transqty:transqty,obj_sysno:obj_sysno,obj_goods_sysno:obj_goods_sysno,obj_unitname:obj_unitname,obj_goodsname:obj_goodsname,memo:memo,storagetank_sysno:storagetank_sysno,storagetank_name:storagetank_name,storagetankname:storagetankname,shipname:shipname},
                            validate: true,
                            loadingmask:true,
                            okCallback:function(json){
                                var allData  = $(".prefix_retankdetail").data('allData');
                                if(typeof  allData != 'undefined'){
                                    allData.push(json);
                                }else{
                                    allData = [json] ;
                                }
                                $('.prefix_retankdetail').datagrid('reload',{data:allData});
                                BJUI.dialog('closeCurrent','');
                            }
                        });
                        }else {
                            BJUI.alertmsg('warn','<h4>报关数量不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                        }
                    }else {
                        BJUI.alertmsg('warn','<h4>罐容不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                    }
                }else {
                    BJUI.alertmsg('warn','<h4>库存不足!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                }
            }
        });
    });

    function addCars()
    {
        alert('addCars');
    }
</script>