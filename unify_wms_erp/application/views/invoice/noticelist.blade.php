<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="inoticelist-bar" data-options="{searchDatagrid:$.CurrentNavtab.find('#inoticelist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">

                <label class="row-label">结算日期</label>
                <div class="row-input">
                    <div class="input-group input-daterange">
                        <input type="text" class="form-control" name="bar_startdate" @if($bar_startdate)value="{{$bar_startdate}}"@endif data-toggle="datepicker" id="noticelist_bar_startdate" placeholder="开始日期" >
                        <div class="input-group-addon">to</div>
                        <input type="text" class="form-control" name="bar_enddate" @if($bar_startdate)value="{{$bar_enddate}}"@endif  data-toggle="datepicker" id="noticelist_bar_enddate" placeholder="结束日期" >
                    </div>
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select name="bar_name"  id='noticelist_bar_name'  data-size="5"  data-toggle="selectpicker" data-live-search="true"  data-width="100%">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">发票抬头</label>
                <div class="row-input">
                    <select name="invoice_company_sysno"  id='noticelist_invoice_company_sysno'  data-size="5"  data-toggle="selectpicker" data-live-search="true"  data-width="100%">
                        <option value="">全部</option>
                        @foreach($companylist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $invoice_company_sysno) selected @endif>{{$item['companyname']}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- <label class="row-label">费用名称</label>
                <div class="row-input">
                    <select name="bar_cost"  id='noticelist_bar_cost'  data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        <option value="0">仓储费</option>
                        <option value="-1">管道输送费</option>
                        @foreach($othercost as $value)
                            <option value="{{$value['sysno']}}">{{$value['othercostname']}}</option>
                        @endforeach
                    </select>
                </div> -->
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="bar_status" id='noticelist_bar_status' data-size="5" data-toggle="selectpicker"  data-width="100%">
                        <option value="">全部</option>
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">已审核</option>
                        <option value="5">作废</option>
                        <option value="6">退回</option>
                        <option value="7">已关闭</option>
                    </select>
                </div>


                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" id="innoicesubmit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>

            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="inoticelist-table" data-toggle="datagrid" data-options="{
            tableWidth:'2500',
            height: '100%',
            gridTitle : '',
            showToolbar: true,
            toolbarItem: 'edit,|,del',
            toolbarCustom:'#custom_inotice_tb',
            addLocation: 'last',
            exportOption: {type:'file', options:{url:'/invoice/dbtoexcel', form:$('#inoticelist-bar') }},
            dataUrl: '/invoice/noticelistJson/cus/{{$customer_sysno}}/start/{{$bar_startdate}}/end/{{$bar_enddate}}',
            dataType: 'json',
            editMode: {navtab:{title:'开票通知单编辑',id:'navab314'}},
            editUrl: '/invoice/edit/id/{sysno}',
            delUrl:'/invoice/deljson',
            delPK:'sysno',
            paging: {pageSize:12},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            hScrollbar:true,
            showLinenumber:true,
            fieldSortable: false,
        }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'invoiceno',align:'center'}">开票通知单号</th>
                <th data-options="{name:'invoicedate',align:'center'}">通知日期</th>
                <th data-options="{name:'customer_name',align:'center'}">客户名称</th>
                <th data-options="{name:'invoicegoodsname',align:'center'}">开票品名</th>
                <th data-options="{name:'invoice_companyname',align:'center'}">发票抬头</th>
                <th data-options="{name:'isinvoice',align:'center',render:function(value){if(value=='1') {return '是'} else  {return '否'}}}">是否开票</th>
                <th  data-options="{name:'invoicenumber',align:'center'}">发票号</th>
                <th  data-options="{name:'period',align:'center'}">结算期间</th>
                <th  data-options="{name:'costtotal',align:'center'}">总金额</th>
                <th  data-options="{name:'costinvoice',align:'center'}">开票通知金额</th>
                <th  data-options="{name:'costdiscount',align:'center'}">折扣金额</th>
                <th  data-options="{name:'receivablecost',align:'center'}">已收款金额</th>
                <th  data-options="{name:'unreceivablecost',align:'center'}">未收款金额</th>
                <th data-options="{name:'invoicestatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='5') {return '作废'} else if(value=='6') {return '退回'} else if(value=='7') {return '已关闭'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="custom_inotice_tb">
    <button type="button" class="btn btn-blue" data-icon="eye"  id="custom_inotice_show_btn">查看</button>
    <!-- <button type="button" class="btn btn-blue" data-icon="filter"  id="custom_inotice_view_btn">附件</button> -->
    <button type="button" class="btn btn-green" data-icon="chain-broken"  id="custom_inotice_check_btn">审核</button>
    <button type="button" class="btn btn-green" data-icon="edit"  id="custom_inotice_add_btn">补充发票信息</button>
    <button type="button" class="btn btn-green" data-icon="print"  id="custom_inotice_print_btn">打印</button>
    <button type="button" class="btn btn-red" data-icon="scissors"  id="custom_inotice_cancel_btn">作废</button>
    <button type="button" class="btn btn-red" data-icon="close"  id="custom_inotice_close_btn">关闭</button>
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="custom_inotice_excel_btn">EXCEL导出</button>
    <!-- <button type="button" class="btn btn-blue" data-icon="filter"  id="custom_inotice_make_btn">生成收款单</button> -->
</div>

<script type="text/javascript">
    @if($bar_startdate&&$bar_enddate&&$customer_sysno)
        //$("#innoicesubmit").onclick();
    @endif
        $("#custom_inotice_view_btn").click(function() {

            var data  = $("#inoticelist-table").data('selectedDatas');

            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }else{
                var obj = data[0];
                BJUI.dialog({
                    id:'attach-inotice-'+obj.sysno,
                    url:'/attachment/view/invoice/notice/'+obj.sysno,
                    title:'查看'+obj.invoiceno+"附件",
                    width:820,
                    height:660,
                    mask:true
                });
            }
        });

        $('#custom_inotice_show_btn').click(function() {
            var chks=$.CurrentNavtab.find("#inoticelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#inoticelist-table").data('selectedDatas');
            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'navab314',
                url:"/invoice/showContent/type/view/id/"+data[0].sysno,
                title:'查看'
            });

        });
        //打印
        $('#custom_inotice_print_btn').click(function() {
            var chks=$.CurrentNavtab.find("#inoticelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#inoticelist-table").data('selectedDatas');
            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'navab314',
                url:"/invoice/showContent/type/print/id/"+data[0].sysno,
                title:'打印'
            });

        });

        $('#custom_inotice_add_btn').click(function() {
            var chks=$.CurrentNavtab.find("#inoticelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#inoticelist-table").data('selectedDatas');
            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'inoticeadd',
                url:"/invoice/addInvoice/id/"+data[0].sysno,
                title:'补充发票信息'
            });

        });

        $('#custom_inotice_close_btn').click(function() {
            var chks=$.CurrentNavtab.find("#inoticelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#inoticelist-table").data('selectedDatas');
            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.alertmsg('confirm', '关闭之后不允许再收款，请确认是否关闭？', {
                        okCall: function() {
                            BJUI.ajax('doajax',{
                                id:'closeinvoice',
                                url: 'invoice/closeinvoice/id/'+data[0].sysno,
                                loadingmask: true,
                                okCallback: function(json, options) {
                                    BJUI.navtab('reloadFlag', 'navab315');
                                }
                            })                   
                        }
            });
        });

        $('#custom_inotice_check_btn').click(function() {
            var chks=$.CurrentNavtab.find("#inoticelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#inoticelist-table").data('selectedDatas');
            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'inoticecheck',
                url:"/invoice/showContent/type/check/id/"+data[0].sysno,
                title:'审核开票单'
            });

        });

        $('#custom_inotice_cancel_btn').click(function() {
            var chks=$.CurrentNavtab.find("#inoticelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#inoticelist-table").data('selectedDatas');
            if (data == '' || data == null ) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'inoticecheck',
                url:"/invoice/showContent/type/cancel/id/"+data[0].sysno,
                title:'作废开票单'
            });

        });

        $('#custom_inotice_excel_btn').click(function(event) {
            var bar_startdate = $('#noticelist_bar_startdate').val();
            var bar_enddate = $('#noticelist_bar_enddate').val();
            var bar_name = $('#noticelist_bar_name').val();
            var bar_cost = $('#noticelist_bar_cost').val();
            var bar_status = $('#noticelist_bar_status').val();
            var invoice_company_sysno = $('#noticelist_invoice_company_sysno').val();

            BJUI.ajax('ajaxdownload', {
                url:'/invoice/dbtoexcel/',
                type:'POST',
                data:{bar_startdate:bar_startdate, bar_enddate:bar_enddate,bar_name:bar_name,bar_cost:bar_cost,bar_status:bar_status,invoice_company_sysno:invoice_company_sysno},
                successCallback: function(json, options) {
                    
                }
            });
        });
</script>