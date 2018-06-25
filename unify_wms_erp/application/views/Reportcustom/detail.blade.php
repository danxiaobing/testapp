<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="reportcustom_detail" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportformcustomdetail-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">时间范围</label>
                <div class="row-input required datawidth">
                    <input type="text" id="startTime" name="startdate" value=" {{ $startTime }} " placeholder="开始时间"  data-toggle="datepicker"  data-rule="required"></div>
                <div class="row-input required datawidth">
                    <input type="text" id="endTime" name="enddate" value="{{ $endTime }} " placeholder="结束时间"  data-toggle="datepicker" data-rule="required"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input required ">
                    <select data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="6" id="customsysno" name="customername" data-rule="required">
                        <option value="{{$custom_sysno or ''}}" selected="">{{$customsname or '不限'}}</option>
                        @foreach($customerlist as $value)
                            <option value="{{$value['sysno']}}"  @if($value['sysno']==$customer_sysno) selected="selected"  @endif>{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">品名</label>
                <div class="row-input required">
                    <select data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="6" id="goodssyano" name="goodsname" data-rule="required">
                        <option value="{{$goods_name or ''}}" selected="">{{$goodsname or '不限'}}</option>
                        @foreach($goods as $val )
                            <option value="{{$val['sysno']}}"  @if(trim($val['sysno'])==trim($goods_sysno)) selected="selected"  @endif >{{$val['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>



                <label class="row-label">期初数量:</label>
                <div class="row-input">
                    <input type="text" id="ghoststockqty" value="{{$ghoststockqty or 0}}" readonly="">
                </div>

                <label class="row-label">期末余量:</label>
                <div class="row-input">
                    <input type="text" id="endmath" value="{{$endmath or 0}}" readonly="">
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit"  id="searchcustom" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>

            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reportformcustomdetail-table" data-toggle="datagrid" data-options="{
    	fullGrid:true,
        height: '100%',
        tableWidth : '100%',
        showToolbar: true,
        toolbarItem: 'export',
        addLocation: 'last',
        exportOption: {type:'file', options:{url:'/Reportcustom/export2',form:$('#reportcustom_detail')}},
        dataUrl: '/Reportcustom/detailJson/goods_sysno/{{$goods_sysno}}/customer_sysno/{{$customer_sysno}}/startTime/{{$startTime}}/endTime/{{$endTime}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
         hScrollbar:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'dateTime',align:'center'}">单据日期</th>
            <th data-options="{name:'dateno',align:'center',width:'180'}">单据编号</th>
            <th data-options="{name:'type',align:'center',render:function(value){if(value=='1'){return '入库单';}else if(value=='2'){return '出库单';} else if(value==3){ return '货权转入单'; } else if(value==4) {return '货权转出单'} else if (value==5) {return '清库单' } } }">单据类型</th>
            <th data-options="{name:'transport',align:'center'}">槽车/船名</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value==1) {return '保税';} else if(value==2) {return '外贸';} else if(value==3) {return '内贸转出口'; } else {return '内贸内销';} }}">货物性质</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){ if(!value || value=='') {return '吨'}}}">计量单位</th>
            <th data-options="{name:'qty1',align:'center',calc:'sum',render:function(value,data) {if(value=='' || !value) {return '0';}}}">商检量</th>
            <th data-options="{name:'qty3',align:'center',calc:'sum',render:function(value,data) {if(value=='' || !value) {return '0';}}}">货权转入量</th>
            <th data-options="{name:'qty2',align:'center',calc:'sum',render:function(value,data) {if(value=='' || !value) {return '0';}}}">出货量</th>
            <th data-options="{name:'qty4',align:'center',calc:'sum',render:function(value,data) {if(value=='' || !value){return '0';}}}">货权转出量</th>
            <th data-options="{name:'qty5',align:'center',calc:'sum',render:function(value,data) {if(value=='' || !value){return '0';}}}">清库量</th>
            <th data-options="{name:'balanceqty',align:'center'}">结存量</th>
            <th data-options="{name:'goodsname',align:'center', hide:'true'}">商品</th>
            <th data-options="{name:'customername',align:'center',hide:'true'}">客户</th>
            <th data-options="{name:'sysno',align:'center',render:customdetail_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<script>
    function customdetail_operation(value,data) {
        return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="look_doc('+data.type+','+data.sysno+','+data.stockintype+','+data.stockouttype+')">查看单据</button>';
    }

    function look_doc(type,sysno,stockintype,stockouttype){
        console.log(type);
        console.log(sysno);
        console.log(stockintype);
        console.log(stockouttype);
        var url = '';
        var id = sysno;
        if(type==1){
            if(stockintype==2){
                url = '/stockcarin/see/mode/eye/id/'+id;
                title = '查看车入库单据';
            }else if(stockintype==1){
                url = '/stockshipin/show/id/'+id;
                title = '查看船入库单据';
            }else if(stockintype==3){
                url = '/stockshipin/edit/type/eye/id/'+id;
                title = '查看管入库单据';
            }

        }else if(type==2){
            if(stockouttype==1){
                url = "/stockout/shipedit/type/view/id/"+id;
                title = '查看船出库单据';
            }else if(stockouttype==2){
                url = "/stockout/edit/type/view/id/"+id,
                        title = '查看车出库单据';
            }else if(stockouttype==3){
                url = "/stockout/pipelineEdit/type/view/id/"+id,
                        title = '查看管出库单据';
            }
        }else if(type==3){
            url =  '/stocktrans/lookstocktrank/id/' + id + '/val/1/look/look';
            title = '查看货权转移入单据';
        }else if(type==4){
            url =  '/stocktrans/lookstocktrank/id/' + id + '/val/1/look/look';
            title = '查看货权转移出单据';
        }else if(type==5){
            url =  '/clearstock/lookclearstock/id/' + id + '/val/1';
            title = '查看清库单据';
        }

        // console.log(id); return;
        if(id==0 || id==null){
            BJUI.alertmsg('error','数据异常,无法查看');
            return;
        }

        BJUI.navtab({
            id: 'look_stock',
            url: url,
            type: 'post',
            data:{id:id},
            title:title,
        });
    }


//搜索
    $('#searchcustom').click(function(){
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        var goods_sysno = $('#goodssyano').val();
        var customer_sysno = $('#customsysno').val();
//        if(!goods_sysno || !customer_sysno){
//            return;
//        }
        $.ajax({
            url:'/Reportcustom/ajaxgetqty/',
            type:'POST',
            data:{goods_sysno:goods_sysno,customer_sysno:customer_sysno,startTime:startTime,endTime:endTime},
            success:function(option){
                var obj = $.parseJSON(option);
                console.log(option);
                $('#ghoststockqty').val(obj.ghoststockqty);
                $('#endmath').val(obj.endmath);
            }
        });
    });
</script>











