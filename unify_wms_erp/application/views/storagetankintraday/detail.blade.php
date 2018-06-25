<div class="bjui-pageContent">
    <fieldset>
        <legend style="font-weight:normal;"><h4><strong>基本信息</strong></h4></legend>
        <div class="bjui-row col-3">
            <label class="row-label">查询日期</label>
            <div class="row-input">
                <input type="text" name="bar_date" data-toggle="datepicker" value="{{$data['bar_date']}}" readonly>
            </div>

            <label class="row-label">储罐号</label>
            <div class="row-input">
                <input type="text" name="storagetankname" value="{{$data['storagetankname']}}" readonly>
                <input type="hidden" name="storagetank_sysno" value="{{$data['storagetank_sysno']}}">
            </div>

            <label class="row-label">储罐性质</label>
            <div class="row-input">
                <input type="text" name="storagetanknature" value="@if($data['storagetanknature'] == 1) 内贸罐 @elseif($data['storagetanknature'] == 2) 外贸罐 @else 保税罐 @endif" readonly>
            </div>

            <label class="row-label">品名</label>
            <div class="row-input">
                <input type="text" name="goodsname" value="{{$data['goodsname']}}" readonly>
                <input type="hidden" name="goods_sysno" value="{{$data['goods_sysno']}}">
            </div>

            <label class="row-label">结存量</label>
            <div class="row-input">
                <input type="text" name="endstock" value="{{$data['endstock']}}" readonly>
            </div>
        </div>
    </fieldset>
    <br>


    <table class="table table-bordered" id="storagetankintraday-detail-table" data-toggle="datagrid" data-options="{
            tableWidth:'99%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#storagetankintraday_detail_tb',
            dataUrl: '/report_storagetankintraday/inAndOutDetailJson/storagetank_sysno/'+{{$data['storagetank_sysno']}}+'/goods_sysno/'+{{$data['goods_sysno']}}+'/bar_date/'+'{{$data['bar_date']}}',
            paging: false,
            filterThead:false,
            addLocation:'first'
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center'}">客户名称</th>
            <th data-options="{name:'doc_time',align:'center'}">入库日期</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'unit',align:'center'}">计量单位</th>
            <th data-options="{name:'endingstock',align:'center'}">结存量</th>
        </tr>
        </thead>
    </table>
</div>

<div id="storagetankintraday_detail_tb">
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="storagetankintraday_detail_export">EXCEL导出</button>
</div>

<script type="text/javascript">
    $('#storagetankintraday_detail_export').click(function(event) {
        var Obj = $.CurrentNavtab.find('#storagetankintraday-detail-table').data('allData');
        BJUI.ajax('ajaxdownload', {
            url:'/report_storagetankintraday/detail_dbtoexcel/',
            type:'POST',
            data:{data:Obj},
            successCallback: function(json, options) {

            }
        });
    });
</script>