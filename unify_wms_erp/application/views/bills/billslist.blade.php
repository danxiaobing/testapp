<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#billslist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">对帐期间:</label>
                <div class="row-input datawidth">
                    <input type="text" id="billstartdate" name="startdate" value="{{date('Y-m-d',strtotime('-1 months'))}}" data-toggle="datepicker" data-rule="required">
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="billsenddate" name="enddate" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" data-rule="required">
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select id="billsecustomer_sysno" name="customer_sysno" data-rule="" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size='10' >
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
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
    <table class="table table-bordered" id="billslist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:$('#billslist_tool'),
        addLocation: 'last',
        dataUrl: 'bills/billslistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
        }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'customername',align:'center'}">客户名称</th>
                <th data-options="{name:'customercredit',align:'center'}">信用额度</th>
                <th data-options="{name:'ablecredit',align:'center',render:function(value,data){ return (parseFloat(data.customercredit)-(parseFloat(data.firstcost)+parseFloat(data.nowcost)-parseFloat(data.discountcost)-parseFloat(data.rececost))).toFixed(2)}}">可用信用额度</th>
                <th data-options="{name:'customerterm',align:'center'}">信用期限(月)</th>
                <th data-options="{name:'overflag',align:'center',render:function(value){if(value) return '是';else return '否';}}">超期</th>
                <th data-options="{name:'firstcost',align:'center'}">期初应收金额</th>
                <th data-options="{name:'nowcost',align:'center'}">本期发生额</th>
                <th data-options="{name:'discountcost',align:'center'}">折扣额</th>
                <th data-options="{name:'rececost',align:'center'}">本期收款额</th>
                <th data-options="{name:'remaincost',align:'center'}">未核销收款余额</th>
                <th data-options="{name:'lastcost',align:'center',render:function(value,data){return  (parseFloat(data.firstcost)+parseFloat(data.nowcost)-parseFloat(data.discountcost)-parseFloat(data.rececost)).toFixed(2)}}">期末应收金额</th>
                <th data-options="{name:'haveinvocost',align:'center'}">已开发票金额</th>
                <th data-options="{name:'notinvocost',align:'center'}">未开发票金额</th>
            </tr>
        </thead>
    </table>
</div>
<div id="billslist_tool">

    <button type="button" class="btn btn-green" data-icon="eye" onclick="seeinvo()">查开票通知单</button>

    <button type="button" class="btn btn-green" data-icon="align-center" onclick="seerece()">查收款单</button>

    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="signoutbills()">Excel导出</button>
</div>
<script>
    function signoutbills(){
        var startdate = $("#billstartdate").val();
        var enddate = $("#billsenddate").val();
        var customer_sysno = $("#billsecustomer_sysno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/bills/excel/',
            type:'POST',
            data:{startdate: startdate,enddate:enddate,customer_sysno:customer_sysno},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

    function seeinvo(){
        var selectdata = $("#billslist-table").data('selectedDatas');
        var startdate='',enddate='',customer_sysno=0;

        if(selectdata!=''&&selectdata!=null){
            startdate = $("#billstartdate").val();
            enddate = $("#billsenddate").val();
            customer_sysno = selectdata[0]['sysno'];
        }

        BJUI.navtab({
            id:'navab315',
            url:'/invoice/noticelist/',
            type: 'post',
            data:{startdate:startdate,enddate:enddate,customer_sysno:customer_sysno},
            title:'查看开票通知单'
        });
    }

    function seerece(){
        var selectdata = $("#billslist-table").data('selectedDatas');
        var startdate='',enddate='',customer_sysno=0;

        if(selectdata!=''&&selectdata!=null){
            startdate = $("#billstartdate").val();
            enddate = $("#billsenddate").val();
            customer_sysno = selectdata[0]['sysno'];
        }

        BJUI.navtab({
            id:'navab321',
            url:'/receivable/list/',
            type: 'post',
            data:{startdate:startdate,enddate:enddate,customer_sysno:customer_sysno},
            title:'查看收款单'
        });
    }

</script>

