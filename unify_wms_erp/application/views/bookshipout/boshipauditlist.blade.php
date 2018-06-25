<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="boship_prechecklist_bar" data-options="{searchDatagrid:$.CurrentNavtab.find('#boshipprechecklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">预约单号</label>
                <div class="row-input">
                    <input type="text" name="bookingoutno" data-toggle="" value="" placeholder="船出库预约单号">

                </div>

                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" id='begin_time' value="" data-toggle="datepicker" placeholder="预约开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" id='end_time' value="" data-toggle="datepicker" placeholder="预约结束时间"></div>

                <label class="row-label">客户</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称">
                </div>
                
                <label class="row-label">提货单号</label>
                <div class="row-input">
                    <input type="text" id='boshipcheck_bar_receivenumber' name="bar_receivenumber" value="{{$bar_receivenumber or ''}}" placeholder="提货单号">
                </div>

                 <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="bar_goodsname" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['goodsname']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div> 
                
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
                    <select name="status" data-toggle="selectpicker"  data-width="100%" disabled="true">
                        <option value="4">待审核</option>
                    </select>
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" id="search" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>

            </div>
            
        </fieldset>
    </form>
</div>

<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="boshipprechecklist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#ship_precheck__tb',
        addLocation: 'last',
        dataUrl: '/bookout/shipauditJson/bar_status/4',
        dataType: 'json',
        editMode:false,
        paging: {pageSize:10},
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
                <th data-options="{name:'customer_name',align:'center',width:280}">客户</th>
                <th  data-options="{name:'receiveunitname',align:'center',width:280}">提货单位</th>
                <th  data-options="{name:'receivenumber',align:'center',width:200}">提货单号</th>
                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'bookingoutqty',align:'center'}">提货数量</th>
                <th  data-options="{name:'cs_employeename',align:'center'}">客服</th>
                <th  data-options="{name:'shipname',align:'center'}">船名</th>      
                <th data-options="{name:'bookingoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'} else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6') {return '已完成'} else if(value=='7') {return '退回'} else if(value=='8') {return '作废'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="ship_precheck__tb">
    <button type="button" class="btn btn-green" data-icon="gavel"  id="ship_precheck_btn">审核</button>
</div>

<script type="text/javascript">
        $("#ship_precheck_btn").click(function() {
            var data  = $("#boshipprechecklist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！');
                return false;
            }

            BJUI.navtab({
                id: 'shipprecheck',
                url: '/bookout/shipedit/type/audit/id/'+data[0].sysno,
                title: '审核船出库预约单'
            });

        });
</script>