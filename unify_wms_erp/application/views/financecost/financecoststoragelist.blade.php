<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form id="editOk" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#financecoststoragelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">客户</label>

                    <div class="row-input">
                        <select name="customer_sysno" id="costcalc_customer_sysno"
                                data-nextselect="#costcalc_contract_sysno"
                                data-refurl="/customer/customercontractJson3/id/{value}" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" 
                                data-width="100%">
                            <option value="-100">全部</option>
                            @foreach($customerlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_name" id="costcalc_customername"
                               value="{{$customer_name}}">
                    </div>

                    <label class="row-label">合同编号</label>

                    <div class="row-input">
                        <select name="contract_sysno" id="costcalc_contract_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-width="100%">
                            <option value="-100">全部</option>
                            @foreach($contractslist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $contract_sysno) selected @endif>{{$item['contract_no']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="contract_no" id="costcalc_contractno" value="{{$contract_no}}">
                    </div>

                    <label class="row-label">船名</label>
                    <div class="row-input">
                    <input type="text" name="shipname" id="costcalc_shipname" value="{{$shipname or ''}}" placeholder="船名"></div>

                    <label class="row-label">结算期间:</label>

                    <div class="row-input datawidth">
                        <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="结算开始时间"></div>
                    <div class="row-input datawidth">
                        <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="结算结束时间"></div>

                    <label class="row-label">进货日期:</label>

                    <div class="row-input">
                        <input type="text" name="instockdate" value="" data-toggle="datepicker" placeholder="进货日期"></div>

                    <label class="row-label">品名</label>

                    <div class="row-input">
                        <select name="goods_sysno" id="costcalc_goods_sysno" data-size="5"
                                data-toggle="selectpicker" data-live-search="true" data-width="100%">
                            <option value="-100">全部</option>
                            @foreach($goods as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $goods_sysno) selected @endif>{{$item['goodsname']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="goodsname" id="costcalc_goods" value="{{$goodsname}}">
                    </div>

                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始计算</button>
                    </div>
                </div>

            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">

<script type="text/javascript">
// JS API 调用日期选择器
$.CurrentNavtab.find('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})

function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    // var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
    //         + " " + date.getHours() + seperator2 + date.getMinutes()
    //         + seperator2 + date.getSeconds();
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
    return currentdate;
}
</script>


    <table class="table table-bordered" id="financecoststoragelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarCustom:$('#financecoststoragelist_tb'),
        addLocation: 'last',
        dataUrl: 'financecost/listcalcJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:100},
        linenumberAll: true,
        filterThead:false,
        showCheckboxcol:true,
        showLinenumber:true,
        showTfoot:true,
        hScrollbar:true,
        tableWidth:'100%',
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'costno',width:125,align:'center'}">费用单</th>
                <th data-options="{name:'costdate',width:180,align:'center',render:function(value, data){return value + '-'+ data.costdateend }}">结算期间</th>
                <th data-options="{name:'contracttype',align:'center',render:function(value){if(value=='1') {return '长约'} else if(value=='2') {return '短约'} else if(value=='3') {return '包罐'} else if(value=='4') {return '包罐容'}} }">合同类型</th>
                <th data-options="{name:'contract_no',align:'center'}">合同编号</th>
                <th data-options="{name:'customer_name',align:'center'}">客户</th>
                <th data-options="{name:'instockdate',align:'center',render:function(value){if(value == ''){return '--'}else{return value}}}">进货日期</th>
                <th data-options="{name:'shipname',align:'center',render:function(value){if(value == ''){return '--'}else{return value}}}">船名</th>
                <th data-options="{name:'instockqty',align:'center'}">进货数量（吨）</th>
                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                <th data-options="{name:'costname',width:'120',align:'center'}">费用类型</th>
                <th data-options="{name:'costqty',align:'center',render:function(value){if(value == null){return '--'}else{return value}}}">计费数量（吨）</th>
                <th data-options="{name:'unitname',align:'center'}">计费单价</th>
                <th data-options="{name:'countcostdateend',align:'center'}">计价天数</th>
                <th data-options="{name:'totalprice',align:'center',calc:'sum'}">实际金额(元)</th>
                <th data-options="{name:'oldtotalprice',align:'center',calc:'sum',render:function(value){if(value == ''){return '--'}else{return value}}}">预计金额(元)</th>
                <th data-options="{name:'coststatus',align:'center',render:function(value){if(value=='2') {return '待开票'}
                    else if(value=='3') {return '开票待审核'} else if(value=='4') {return '已开票'} else if(value=='5') {return '已关闭'}
                    else  {return '未生效'}}}">开票状态
                </th>
            </tr>
        </thead>
    </table>
</div>

<div id="financecoststoragelist_tb">
    <button type="button" id="viewcoststoragelist_btn" class="btn btn-blue" data-icon="superscript" >生成仓储费</button>
    <button type="button" id="delcoststoragelist" class="btn btn-red" data-icon="reply" >取消计算</button>
    <button type="button"  class="btn btn-green" data-icon="filter" onclick="coststorage_list_signout()">EXCEL导出</button>
    <button type="button" id="editcoststorage_btn" class="btn btn-red" data-icon="edit" >修改费用</button>
    <!-- <button type="button" id="testcoststorage_btn" class="btn btn-green" data-icon="filter" >测试生成仓储费(商用版删除)</button> -->
</div>

<script type="text/javascript">
$(function(){
    $("#costcalc_customer_sysno").change(function(){
        var v=$("#costcalc_customer_sysno option:selected");

        $("#costcalc_customername").val(v.text());
    });
    $("#costcalc_contract_sysno").change(function(){
        var v=$("#costcalc_contract_sysno option:selected");

        $("#costcalc_contractno").val(v.text());
    });
    $("#costcalc_goods_sysno").change(function(){
        var v=$("#costcalc_goods_sysno option:selected");

        $("#costcalc_goods").val(v.text());
    });
});

$("#testcoststorage_btn").click(function () {
    BJUI.alertmsg('confirm', '确定要手动批量执行脚本吗！', {
        okCall: function() {
            BJUI.ajax('doajax',{
                type : 'POST',
                url:'/financecost/addFinancecostbyplan/',
                okCallback: function(json, options) {
                    BJUI.navtab('reloadFlag', 'navab457');
                }
            });
        }
    })
})

$("#editcoststorage_btn").click(function(){
    var selectedDatas=$('#financecoststoragelist-table').data('selectedDatas');
    var customer_sysno=$('#costcalc_customer_sysno').val();
    var contract_sysno=$('#costcalc_contract_sysno').val();

    if (selectedDatas != undefined && selectedDatas.length == 1) {
        BJUI.dialog({
            id:'cost-receipt-{{$id}}',
            url:'/financecost/editdetail/id/'+selectedDatas[0].sysno+'/customer_sysno/'+customer_sysno+'/contract_sysno/'+contract_sysno,
            type:'POST',
            data:{selectedDatasArray:selectedDatas[0]},
            title:'修改费用',
            width:700,
            height:500,
            mask:true
        });
    }else{
        BJUI.alertmsg('warn','请选中一行进行修改',{displayPosition:'middlecenter',displayMode:'fade'});
    }
    return;
})

$("#viewcoststoragelist_btn").click(function () {
    var checkdata=$('#financecoststoragelist-table').data('selectedDatas');
    if(checkdata && checkdata.length>0){
        var date = [];
        for(i=0;i<checkdata.length;i++){
            date[i] = checkdata[i].sysno;
        }
        BJUI.alertmsg('confirm', '确定要生成仓储费吗！', {
            okCall: function() {
                BJUI.ajax('doajax',{
                    type : 'POST',
                    url:'/financecost/statuschange/',
                    data:{date : date },
                    okCallback: function(json, options) {
                        BJUI.navtab('reloadFlag', 'navab457,navab309');
                    }
                });
            }
        })
        
    }else{
        BJUI.alertmsg('warn','未选中任何费用单！',{displayPosition:'middlecenter',displayMode:'fade'});
    }
})

$("#delcoststoragelist").click(function () {
    BJUI.navtab('reloadFlag', 'navab457');
})

function coststorage_list_signout() {
    var customer_sysno = $("#costcalc_customer_sysno option:selected").val();
    var contract_sysno = $("#costcalc_contract_sysno option:selected").val();
    var shipname = $("#costcalc_shipname").val();

    var data=$('#financecoststoragelist-table').data('allData');
    if(data=='' || data==null)
    {
        BJUI.alertmsg('warn','空数据无法导出',{displayPosition:'middlecenter',displayMode:'fade'});
        return;
    }
    BJUI.ajax('ajaxdownload', {
        url:'/financecost/excelstorage',
        type:'POST',
        data: {contract_sysno: contract_sysno, customer_sysno: customer_sysno, shipname: shipname},
        successCallback: function(json, options) {
            console.log(123);
        }
    });
}

</script>