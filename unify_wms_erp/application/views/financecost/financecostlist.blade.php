<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#financecostlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" id="costlist_begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" id="costlist_end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">费用单号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" id="costlist_bar_no" value="{{$bar_no or ''}}" placeholder="费用单号"></div>


                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" id="costlist_bar_name" value="{{$bar_name or ''}}" placeholder="客户名称"></div>

                <label class="row-label">合同编号</label>
                <div class="row-input">
                    <input type="text" name="bar_contract" id="costlist_bar_contract" value="{{$bar_contract or ''}}" placeholder="合同编号"></div>

                <label class="row-label">开票状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_coststatus" id="costlist_bar_coststatus">
                        <option value="-100" selected="">不限</option>
                        <option value="2">待开票</option>
                        <option value="3">开票待审核</option>
                        <option value="4">已开票</option>
                        <option value="5">已关闭</option>
                    </select>
                </div>
                <label class="row-label"></label>
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


    <table class="table table-bordered" id="financecostlist-table" data-toggle="datagrid" data-options="{
        hScrollbar:true,
        tableWidth:'1500',
        height: '100%',
        showToolbar: true,
        toolbarCustom:$('#financecostlist_tb'),
        addLocation: 'last',
        dataUrl: 'financecost/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        fullGrid:true,
        paging: {pageSize:20},
        linenumberAll: true,
        filterThead:false,
        showCheckboxcol:true,
        showLinenumber:true,
        hScrollbar:true,
        showTfoot:true
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
                <th data-options="{name:'totalprice',align:'center',calc:'sum'}">金额(元)</th>
                <th data-options="{name:'coststatus',align:'center',render:function(value){if(value=='2') {return '待开票'}
                    else if(value=='3') {return '开票待审核'} else if(value=='4') {return '已开票'} else if(value=='5') {return '已关闭'}
                    else  {return '未生效'}}}">开票状态
                </th>

            </tr>
        </thead>
    </table>
</div>

<div id="financecostlist_tb">
    <!-- <button type="button" id="viewcostlist_btn" class="btn btn-blue" data-icon="superscript" >仓储费计算</button> -->
    <button type="button"  class="btn btn-green" data-icon="filter" onclick="cost_list_signout()">EXCEL导出</button>
</div>

<script type="text/javascript">

$("#viewcostlist_btn").click(function () {
    BJUI.navtab({
        id:'navab457',
        url:'/financecost/calc',
        title:'仓储费计算',
    })
})

$("#delcostlist").click(function () {
    var data = $.CurrentNavtab.find("#financecostlist-table").data('selectedDatas');

    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要删除的费用单',{displayPosition:'middlecenter',displayMode:'fade'});
    }else{
        var sysno = data[0]['sysno'];
        BJUI.ajax('doajax', {
            url: '/financecost/delfinancecost',
            data:{sysno: sysno },
            okCallback: function(json, options) {
                BJUI.navtab('refresh', 'navtab309');
            }
        });

    }
})

function cost_list_signout() {
    var bar_name = $("#costlist_bar_name").val();
    var bar_contract = $("#costlist_bar_contract").val();
    var bar_no = $("#costlist_bar_no").val();
    var begin_time = $("#costlist_begin_time").val();
    var end_time = $("#costlist_end_time").val();
    var bar_coststatus = $("#costlist_bar_coststatus option:selected").val();

    var data=$('#financecostlist-table').data('allData');
    if(data=='' || data==null)
    {
        BJUI.alertmsg('warn','空数据无法导出',{displayPosition:'middlecenter',displayMode:'fade'});
        return;
    }

    BJUI.ajax('ajaxdownload', {
        url:'/financecost/excel/',
        type:'POST',
        data: {bar_name: bar_name, bar_no: bar_no, end_time: end_time, begin_time: begin_time, bar_coststatus: bar_coststatus, bar_contract: bar_contract},
        successCallback: function(json, options) {
            console.log(123);
        }
    });
}

</script>