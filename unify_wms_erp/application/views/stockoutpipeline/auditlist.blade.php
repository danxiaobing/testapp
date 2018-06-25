<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#pipeline-auditlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">出库单号</label>
                <div class="row-input">
                    <input type="text" name="bar_no" id='pipeline_auditlist_bar_no' value="" placeholder="出库单号"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" id='pipeline_auditlist_bar_name' value="" placeholder="客户名称"></div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="" data-toggle="selectpicker" data-width="100%" disabled="true">
                        <option value="3">待审核</option>

                    </select>
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
    <table class="table table-bordered" id="pipeline-auditlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#pipeline_auditlist_tb',
        addLocation: 'last',
        dataUrl: '/stockout/pipelineAuditListJson',
        dataType: 'json',
        paging: {pageSize:12},
        showCheckboxcol: false,
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
                <th  data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
                <th  data-options="{name:'goodsname',align:'center'}">品名</th>
                <th  data-options="{name:'qualityname',align:'center'}">规格</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'} else  {return ''}}}">货物性质</th>
                <th  data-options="{name:'tobeqty',align:'center'}">数量(吨)</th>
                <th  data-options="{name:'cs_employeename',align:'center'}">客服</th>
                <th data-options="{name:'stockoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='5') {return '作废'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>

   




</div>
<div id="pipeline_auditlist_tb">
    <button type="button" class="btn btn-green" data-icon="gavel"  id="pipeline_auditlist_audit_btn">审核</button>
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="pipeline_auditlist_excel_btn">EXCEL导出</button>
</div>

<script type="text/javascript">
        $("#pipeline_auditlist_audit_btn").click(function() {

            var data  = $("#pipeline-auditlist-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id: 'shipoutprecheck666',
                url: '/stockout/pipelineAudit/id/'+data[0].sysno,
                title: '审核船出库订单'
            });    
        });

        $('#pipeline_auditlist_excel_btn').click(function(event) {

            var bar_no = $('#pipeline_auditlist_bar_no').val();
            var bar_name = $('#pipeline_auditlist_bar_name').val();

            BJUI.ajax('ajaxdownload', {
                url:'/stockout/pipelinedbtoexcel/',
                type:'POST',
                data:{bar_no:bar_no, bar_name:bar_name},
                successCallback: function(json, options) {
                    
                }
            });
        });
</script>