<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#lading-table')}">
        <fieldset>
            <legend style="font-weight:normal;">搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" id="startdate" name="startdate" value="{{date('Y-m-d',strtotime('-1 months'))}}" data-toggle="datepicker" data-rule="required">
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="enddate" name="enddate" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" data-rule="required">
                </div>

                <label class="row-label">费用承担方</label>
                <div class="row-input">
                    <select id="ladingcustomer_sysno" name="customer_sysno" data-rule="" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size='10' >
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text"  name="shipname" value="{{$shipname}}">
                </div>
                <label class="row-label">费用状态</label>
                <div class="row-input">
                    <select  name="coststatus" data-rule="" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size='10' >
                        <option value="">全部</option>
                            <option value="1">未生效</option>
                            <option value="2">待开票</option>
                            <option value="3">开票待审核</option>
                            <option value="4">已开票</option>
                            <option value="5">已关闭</option>
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
    <table class="table table-bordered" id="lading-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:$('#lading_edit_tool'),
        {{--addLocation: 'last',--}}
        dataUrl: '/Lading/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot: true,
        fullGrid:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'first_customername',align:'center'}">开单公司</th>
            <th data-options="{name:'sale_customername',align:'center'}">转出方</th>
            <th data-options="{name:'buy_customername',align:'center'}">转入方</th>
            <th data-options="{name:'customer_name',align:'center'}">费用承担方</th>
            <th data-options="{name:'receivestart',align:'center'}">提货开始日期</th>
            <th data-options="{name:'receiveend',align:'center'}">提货结束日期</th>
            <th data-options="{name:'created_at',align:'center'}">计费日期</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){if(value){ return value;}else{  return '吨';}}}">计量单位</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单数量</th>
            <th data-options="{name:'takegoodsqty',align:'center'}">已提数量</th>
            <th data-options="{name:'costqty',align:'center'}">结存量</th>
            <th data-options="{name:'sumpricre',align:'center', calc:'sum'}">实际金额</th>
            <th data-options="{name:'',align:'center',render:ladingeditdetail}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="lading_edit_tool">
    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="ladingExcelAction()">Excel导出</button>
</div>
<script>

        function ladingeditdetail(value,data) {
            return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="LadingDetail('+data.cost_sysno+')">联查明细</button>';
        }
        function LadingDetail(cost_sysno){
            var startTime = $('#startTime').val();
            var endTime = $('#endTime').val();
            BJUI.navtab({
                id:'lodingDetail',
                type:'POST',
                url:'/lading/detail/cost_sysno/'+cost_sysno,
                data:{cost_sysno:cost_sysno,startTime:startTime,endTime:endTime},
                title:'费用明细',
            });
        };

        function ladingExcelAction(){
            BJUI.ajax('ajaxdownload', {
                url:'/lading/excel/',
                type:'POST',
                successCallback: function(json, options) {
                    //console.log(123);
                }
            });
        }

</script>

