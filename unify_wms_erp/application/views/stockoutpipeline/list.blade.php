<script language="javascript" src="/static/common/js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#pipeline-list-table')}">
        <input type="hidden" id="pipeleine_list_print_data" name="print_data" value="">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">出库单号</label>
                <div class="row-input">
                    <input type="text" id='pipeline_bar_no' name="bar_no" value="" placeholder="出库单号"></div>
                
                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" id='pipeline_list_begin_time' name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" id='pipeline_list_end_time' name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" id='pipeline_list_bar_name' name="bar_name" value="" placeholder="客户名称"></div>
                </br>
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select id='pipeline_list_bar_stockoutstatus' name="bar_stockoutstatus" data-toggle="selectpicker" data-width="100%" name="bar_stockinstatus">
                        <option value="-100" selected="">不限</option>
                        <option value="2">暂存</option>
                        <option value="8">待执行</option>
                        <option value="3">待审核</option>
                        <option value="4">已完成</option>
                        <option value="5">作废</option>
                    </select>
                </div>

                <label class="row-label"></label>
                <div class="row-input">
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
                <input type="hidden" name="stockouttype" value="{{$stockouttype}}">
            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="pipeline-list-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        showToolbar: true,
        toolbarItem: 'edit,|,del',
        toolbarCustom:'#pipelineout_list_tb',
        addLocation: 'last',
        dataUrl: '/stockout/pipelineListJson',
        dataType: 'json',
        editMode: {navtab:{title:'管出库订单编辑',id:'navab1024'}},
        editUrl: '/stockout/pipelineEdit/id/{sysno}',
        delUrl:'/stockout/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
        hScrollbar:true

    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'stockoutno',align:'center',width:200}">出库单号</th>
                <th data-options="{name:'customername',align:'center',width:280}">客户</th>
                <th  data-options="{name:'goodsname',align:'center'}">品名</th>
                <!-- <th  data-options="{name:'qualityname',align:'center'}">规格</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'} else  {return ''}}}">货物性质</th> -->
                <th  data-options="{name:'takeqty',align:'center'}">预提数量(吨)</th>
                <th  data-options="{name:'bussinesscheckqty',align:'center'}">罐检数量(吨)</th>
                <th  data-options="{name:'cs_employeename',align:'center'}">客服</th>
                <th data-options="{name:'stockoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已完成'} else if(value=='5') {return '作废'} else if(value=='6') {return '退回'}else if(value=='8') {return '待执行'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>

</div>
<div id="pipelineout_list_tb">
    <button type="button" class="btn btn-blue" data-icon="eye"  id="pipeline_list_view_btn">查看</button>
    <button type="button" class="btn btn-green" data-icon='gavel'  id="pipeline_list_audit_btn">审核</button>
    <button type="button" class="btn btn-red" data-icon="scissors" id="pipeline_list_cancel_btn">作废</button>
    <button type="button" class="btn btn-green" data-icon="print"  onclick="pipelineStockoutPrint(2)">打印核单</button>
    <button type="button" class="btn btn-green" data-icon="print"  onclick="pipelineStockoutPrint(1)">打印出库单</button>
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="pipeline_list_excel_btn">EXCEL导出</button>

</div>


<script type="text/javascript">

        $('#pipeline_list_view_btn').click(function() {
            var chks=$.CurrentNavtab.find("#pipeline-list-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#pipeline-list-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'navab1024',
                url:"/stockout/pipelineEdit/type/view/id/"+data[0].sysno,
                title:'管出库订单查看'
            });
        });

        $("#pipeline_list_cancel_btn").click(function(){
            var chks=$.CurrentNavtab.find("#pipeline-list-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#pipeline-list-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'navab1024',
                url:'/stockout/pipelineedit/type/cancel/id/'+data[0].sysno,
                title:'管出库订单作废'
            });

        });

        $('#pipeline_list_excel_btn').click(function(event) {

            var bar_no = $('#pipeline_bar_no').val();
            var begin_time = $('#pipeline_list_begin_time').val();
            var end_time = $('#pipeline_list_end_time').val();
            var bar_name = $('#pipeline_list_bar_name').val();
            var bar_stockoutstatus = $('#pipeline_list_bar_stockoutstatus').val();

            BJUI.ajax('ajaxdownload', {
                url:'/stockout/pipelinedbtoexcel/',
                type:'POST',
                data:{bar_no:bar_no, begin_time:begin_time,end_time:end_time,bar_name:bar_name,bar_stockoutstatus:bar_stockoutstatus},
                successCallback: function(json, options) {
                    
                }
            });
        });
        
        $('#pipeline_list_audit_btn').click(function() {
            var chks=$.CurrentNavtab.find("#pipeline-list-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#pipeline-list-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id:'navab1024',
                url:"/stockout/pipelineEdit/type/audit/id/"+data[0].sysno,
                title:'管出库订单审核'
            });
        });

        function pipelineStockoutPrint(type) {
            var chks=$.CurrentNavtab.find("#pipeline-list-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#pipeline-list-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.ajax('doajax', {
                url: "/stockout/executePrint/id/"+data[0].sysno+"/type/"+type,
                loadingmask: true,
                okCallback: function(json, options) {
                    if(json.code == 300){
                        BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                        return false;
                    }else{

                        $('#pipeleine_list_print_data').val(JSON.stringify(json));

                       var LODOP; //声明为全局打印变量
                        //打印入库单字段布局
                        var date = new Date();
                        var now = date.getFullYear()+"-" + (date.getMonth()+1) + "-" + date.getDate();

                        var CreateStockIn = function CreateStockIn() {
                            var data = $('#pipeleine_list_print_data').val();
                            data = JSON.parse(data);
                            LODOP = getLodop();
                            LODOP.PRINT_INITA(0, 0, 1200, 600, "管出库单");
                            // LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A5");

                            LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
                            LODOP.SET_PRINT_STYLEA(2, "FontName", "黑体");
                            LODOP.SET_PRINT_STYLEA(2, "FontSize", 30);
                            if(type ==2){
                                LODOP.ADD_PRINT_TEXT(90, 130, 200, 24, now);
                                LODOP.ADD_PRINT_TEXT(140, 130, 200, 24, data.customername);
                                LODOP.ADD_PRINT_TEXT(140, 380, 200, 24, data.goodsname);
                                LODOP.ADD_PRINT_TEXT(140, 630, 200, 24, data.tobeqty+'吨');
                                LODOP.ADD_PRINT_TEXT(190, 130, 200, 24, data.storagetankname);
                                LODOP.ADD_PRINT_TEXT(240, 380, 200, 24, data.memo);
                                LODOP.ADD_PRINT_TEXT(360, 130, 200, 24, data.sby_employeename);
                            }else if(type == 1){
                                LODOP.ADD_PRINT_TEXT(90, 130, 200, 24, data.goodsname);
                                LODOP.ADD_PRINT_TEXT(140, 130, 200, 24, data.stockoutdate);
                                LODOP.ADD_PRINT_TEXT(190, 130, 200, 24, data.customername);
                                LODOP.ADD_PRINT_TEXT(240, 130, 200, 24, data.inshipname);
                                LODOP.ADD_PRINT_TEXT(290, 130, 200, 24, data.takegoodscompany);
                                LODOP.ADD_PRINT_TEXT(340, 130, 200, 24, data.takegoodsno);
                                LODOP.ADD_PRINT_TEXT(390, 130, 200, 24, data.tobeqty+'吨');
                                LODOP.ADD_PRINT_TEXT(490, 120, 200, 24, data.sby_employeename);
                                
                                LODOP.ADD_PRINT_TEXT(90, 380, 200, 24, '管线输送');
                                LODOP.ADD_PRINT_TEXT(140, 380, 200, 24, data.storagetankname);
                                LODOP.ADD_PRINT_TEXT(190, 380, 200, 24, '\\吨');
                                LODOP.ADD_PRINT_TEXT(240, 380, 200, 24, '\\吨');
                                LODOP.ADD_PRINT_TEXT(290, 380, 200, 24, data.beqty+'吨');
                                LODOP.ADD_PRINT_TEXT(340, 380, 200, 24, data.stockoutdate);
                                LODOP.ADD_PRINT_TEXT(300, 620, 200, 24, data.memo);
                            }
                            
                        }
                        Setup(CreateStockIn)
                    }
                }
            })
        };
</script>