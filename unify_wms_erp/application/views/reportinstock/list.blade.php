<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportinstocklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">时间范围</label>
                <div class="row-input required datawidth">
                    <input id="date1" type="text" name="date1" data-rule="required" value="{{date('Y-m-d')}}" placeholder="开始时间"  data-toggle="datepicker" >
                </div>
                <div class="row-input required datawidth">
                    <input id="date2" type="text" name="date2" data-rule="required" value="{{date('Y-m-d')}}" placeholder="结束时间"  data-toggle="datepicker">
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select id="instock_customer_sysno" name="customer_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($customers['list'] as $value)
                            <option value="{{$value['sysno']}}">{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select id="goods_sysno" name="goods_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($goods['list'] as $value)
                            <option value="{{$value['sysno']}}">{{$value['goodsname']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="instockgoodsname" name="goodsname">
                </div>

                <label class="row-label">进货单号</label>
                <div class="row-input">
                    <input type="text" name="stockinno" value="" placeholder="进货单号">
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reportinstocklist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showinstock',
            addLocation: 'last',
            dataUrl: '/report_reportinstock/ListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:13},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'stockinno',align:'center'}">进货单号</th>
            <th data-options="{name:'stockintype',align:'center',render:function(value){if(value=='1') {return '船入库'} else if(value=='2') {return '车入库'}else if(value=='3') {return '管入库'}}}">类型</th>
            <th data-options="{name:'stockindate',align:'center'}">入库日期</th>
            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'shipname',align:'center',render:function(value,data){ if(data.stockintype=='3'){return '管输'}}}">进货车船名</th>
            <th data-options="{name:'takegoodsnum',align:'center',calc:'sum',render:function(value){ return parseFloat(value).toFixed(3)}}">进货数量/货转量</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum',render:function(value){ return parseFloat(value).toFixed(3)}}">商检量</th>
            <th data-options="{name:'sysno',align:'center',render:translist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showinstock">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="instocksignout()"></i>EXCEL导出</button>
</div>
<script type="text/javascript">
    $.CurrentNavtab.find("#goods_sysno").change(function (){
        $("#instockgoodsname").val($.CurrentNavtab.find("#goods_sysno option:selected").text());
    });

    function translist_operation(val,data){
        return '<button type="button" class="btn-green" onclick="see_transdetail('+val+')">查看明细</button>';
    }

    function see_transdetail(val){
        BJUI.navtab('closeTab', 'navtab999');

        var date1 = $("#date1").val();
        var date2 = $("#date2").val();

        BJUI.navtab({
            id:'navtab999',
            url:'/report_reportinstock/detaillist/',
            type: 'post',
            data:{id:val,date1:date1,date2:date2},
            title:'客户进出货明细'
        });
    }

    function instocksignout(){
        var date1 = $.CurrentNavtab.find("#date1").val();
        var date2 = $.CurrentNavtab.find("#date2").val();
        var customer_sysno = $.CurrentNavtab.find("#instock_customer_sysno option:selected").val();
        var goods_sysno = $.CurrentNavtab.find("#goods_sysno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/report_reportinstock/excel/',
            type:'POST',
            data:{date1: date1,date2:date2,customer_sysno:customer_sysno,goods_sysno:goods_sysno},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>