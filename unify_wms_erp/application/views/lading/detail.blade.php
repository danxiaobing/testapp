<div class="bjui-pageHeader">
    {{--<form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#lading-detail-table')}">--}}
        {{--<fieldset>--}}
            {{--<legend style="font-weight:normal;">搜索：</legend>--}}
            {{--<div class="bjui-row col-3">--}}

                {{--<label class="row-label">费用承担方</label>--}}
                {{--<div class="row-input">--}}
                    {{--<select id="ladingcustomer_sysno" name="customer_sysno" data-rule="" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size='10' >--}}
                        {{--<option value="">全部</option>--}}
                        {{--@foreach($customerlist as $item)--}}
                            {{--<option value="{{$item['sysno']}}">{{$item['customername']}}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
                {{--<div class="row-input">--}}
                    {{--<div class="btn-group">--}}
                        {{--<button type="submit" class="btn-green" data-icon="search">开始搜索</button>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}

        {{--</fieldset>--}}
    {{--</form>--}}
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="lading-detail-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:$('#lading_detail_tool'),
        addLocation: 'last',
        dataUrl: 'Lading/detailJson/cost_sysno/{{$cost_sysno}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: false,
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
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
            <th data-options="{name:'costdate',align:'center'}">计费日期</th>
            <th data-options="{name:'unitname',align:'center',render:function(value){if(value){ return value;}else{  return '吨';}}}">计量单位</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'takegoodsnum',align:'center'}">提单数量</th>
            <th data-options="{name:'takegoodsqty',align:'center'}">已提数量</th>
            <th data-options="{name:'costqty',align:'center'}">结存量</th>
            <th data-options="{name:'costqty',align:'center'}">超期吨数</th>
            <th data-options="{name:'unitprice',align:'center'}">单价（吨/天/元）</th>
            <th data-options="{name:'totalprice',align:'center'}">实际金额</th>
            <th data-options="{name:'oldtotalprice',align:'center'}">预计金额（元）</th>
        </tr>
        </thead>
    </table>
</div>
<div id="lading_detail_tool">
    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="ladingDetailAction()">Excel导出</button>
    <button type="button" class="btn btn-green" data-icon="filter" id="edit_lading_price_btn" >修改费用</button>
</div>
<script>

        function ladingdetail(value,data) {
            return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="lookLadingDetail('+data.cost_sysno+')">联查明细</button>';
        }
        function lookLadingDetail(cost_sysno){
            var startTime = $('#startTime').val();
            var endTime = $('#endTime').val();
            BJUI.navtab({
                type:'POST',
                url:'/lading/detail/cost_sysno/'+cost_sysno,
                data:{cost_sysno:cost_sysno,startTime:startTime,endTime:endTime},
                title:'费用明细',
            });
        };

        function ladingDetailAction(){
            BJUI.ajax('ajaxdownload',{
                url:'/lading/excelDetail/cost_sysno/{{$cost_sysno}}',
                type:'POST',
                successCallback: function(json, options) {
                    //console.log(123);
                }
            });

        }

        $("#edit_lading_price_btn").click(function(){
            var selectedDatas=$('#lading-detail-table').data('selectedDatas');


            if (selectedDatas != undefined && selectedDatas.length == 1) {
                BJUI.dialog({
                    id:'cost-receipt-{{$id}}',
                    url:'/lading/editdetail/sysno/'+selectedDatas[0].sysno,
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

</script>

