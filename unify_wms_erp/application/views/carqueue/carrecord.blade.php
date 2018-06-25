<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#carrecord-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">单据类型：</label>
                <div class="row-input">
                    <select name="doc_type"  data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">全部</option>
                        <option value="1">车入库</option>
                        <option value="2">车出库</option>
                    </select>
                </div>

                <label class="row-label">车牌号：</label>
                <div class="row-input">
                    <input type="text"  name="carid" value="" placeholder="车牌号">
                </div>

                <label class="row-label">库区车辆：</label>
                <div class="row-input">
                    <select name="carstatus"  data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="1">当前库区车辆</option>
                        <option value="2,3">历史库区车辆</option>
                    </select>
                </div>


                <label class="row-label">作业日期:</label>
                <div class="row-input datawidth">
                    <input type="text"  name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text"  name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>


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

    <table class="table table-bordered" id="carrecord-list-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        showToolbar: true,
        toolbarCustom:'#carrecord_list_tb',
        addLocation: 'last',
        dataUrl: '/Carqueue/carrecordlistJson',
        dataType: 'json',
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
                <th data-options="{name:'doc_type',align:'center',render:function(value){if(value==1){return '车入库';}else{return '车出库';}}}">单据类型</th>
                <th data-options="{name:'carid',align:'center'}">车牌号</th>
                <th  data-options="{name:'carname',align:'center'}">司机</th>
                <th  data-options="{name:'mobilephone',align:'center'}">联系方式</th>
                <th  data-options="{name:'disablestatus',align:'center',render:function(value){if(value==1){return '是'}else if(value==0){return '否';}}}">是否禁用</th>
                <th data-options="{name:'queueno',align:'center'}">鹤位号/罐号</th>
                <th  data-options="{name:'goodsname',align:'center'}">货品</th>
                <th data-options="{name:'estimateqty',align:'center'}">预计作业吨数（吨）</th>
                <th data-options="{name:'loadometer',align:'center'}">地磅</th>
                <th  data-options="{name:'arrivaltime',align:'center',render:function(value){if(!value){return '--';}}}">进入库区</th>
                <th  data-options="{name:'firstweightime',align:'center',render:function(value){if(!value){return '--';}}}">一次过磅</th>
                <th  data-options="{name:'secondweightime',align:'center',render:function(value){if(!value){return '--';}}}">二次过磅</th>
                <th data-options="{name:'carstatus',align:'center',render:function(value){if(value==1){return '库区作业';}else if(value==2){return '完成作业';}else if(value==3){return '作废';}}}">车辆状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="carrecord_list_tb">
    <button type="button" class="btn btn-green" data-icon="eye"  onclick="Carchange('over')">完成作业</button>
    <button type="button" class="btn btn-red" data-icon="close"  onclick="Carchange('void')">作废</button>
</div>
<script>
    function Carchange(action)
    {
        var checkdata=$.CurrentNavtab.find('#carrecord-list-table').data('selectedDatas');
        var sysno = checkdata[0]['sysno'];
        BJUI.ajax('doajax', {
            url: '/Carqueue/carrecordChange',
            type:'POST',
            loadingmask: true,
            data:{id:sysno,action:action},
            okCallback: function(json, options) {
                BJUI.navtab('reload', 'navab528');
            }
        });
    }
</script>