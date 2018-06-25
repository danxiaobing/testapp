<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="pipeline-audit-form" data-options="{searchDatagrid:$.CurrentNavtab.find('#pipeline-audit-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" >
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">预约单号</label>
                <div class="row-input">
                    <input type="text" name="bookingoutno" data-toggle="" value="" placeholder="管出库预约单号">

                </div>

                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="" data-toggle="datepicker" placeholder="预约开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="" data-toggle="datepicker" placeholder="预约结束时间"></div>

                <label class="row-label">客户</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称">
                </div>
                <br/>
                <label class="row-label">单据来源</label>
                <div class="row-input">
                    <select name="bar_docsource" data-toggle="selectpicker"  data-width="100%">
                        <option value="">全部</option>
                        <option value="1">手工创建</option>
                        <option value="2">国烨云仓</option>
                    </select>
                </div>
                
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name=""   data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" disabled="true">
                        <option value="4">待审核</option>
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
    <table class="table table-bordered" id="pipeline-audit-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        showToolbar: true,
        toolbarCustom:'#pipeline_audit_tb',
        addLocation: 'last',
        exportOption: {type:'file', options:{url:'/bookout/pipelineDBtoexcel', form:$('#pipeline-audit-form') }},
        dataUrl: '/bookout/pipelineAuditJson',
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
                <th data-options="{name:'bookingoutno',align:'center',width:200}">预约单号</th>
                <th data-options="{name:'bookingoutdate',align:'center',width:95}">预约日期</th>
                <th data-options="{name:'customer_name',align:'center',width:280}">客户</th>
                <th data-options="{name:'receivenumber',align:'center',width:280}">提货单号</th>
                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                <th  data-options="{name:'bookingoutqty',align:'center'}">预提数量(吨)</th>
                <th data-options="{name:'bookingoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'} else if(value=='4') {return '待审核'} else if(value=='5') {return '提货中'} else if(value=='6') {return '已完成'} else if(value=='7') {return '退回'} else if(value=='8') {return '作废'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="pipeline_audit_tb">
    <button type="button" class="btn btn-green" data-icon="gavel"  id="pipeline_audit_btn">审核</button>
</div>

<script type="text/javascript">
        $("#pipeline_audit_btn").click(function() {

            var data  = $("#pipeline-audit-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }

            BJUI.navtab({
                id: 'navab505',
                url: '/bookout/pipelineEdit/id/'+data[0].sysno+"/type/audit",
                title: '审核管出库预约单'
            });

        });
</script>