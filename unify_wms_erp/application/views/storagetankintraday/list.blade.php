<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#storagetankintraday-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">查询日期</label>
                <div class="row-input">
                    <input type="text" name="bar_date" id="storagetankintraday_bar_date" data-toggle="datepicker" value="{{$bookingoutdate}}" readonly>
                </div>

                <label class="row-label">储罐</label>
                <div class="row-input">
                    <select name="storagetank_sysno" id="storagetankintraday_storagetank_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($storagetankList as $value)
                            <option value="{{$value['sysno']}}">{{$value['storagetankname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" id="storagetankintraday_goods_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($goodslist as $value)
                            <option value="{{$value['sysno']}}">{{$value['goodsname']}}</option>
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
    <table class="table table-bordered" id="storagetankintraday-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#storagetankintraday_tb',
            addLocation: 'last',
            dataUrl: '/report_storagetankintraday/listJson',
            dataType: 'json',
            editMode:false,
            jsonPrefix: 'obj',
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center',width:280}">储罐号</th>
            <th data-options="{name:'storagetanknature',align:'center',render:function(value){if(value=='1') {return '内贸罐'} else if(value=='2') {return '外贸罐'} else if(value=='3') {return '保税罐'}}}">储罐性质</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'unit',align:'center',render:function(){return '吨'}}">计量单位</th>
            <th data-options="{name:'endstock',align:'center',calc:'sum'}">结存量</th>
            <th data-options="{name:'info',align:'center',render:storagetankintraday_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="storagetankintraday_tb">
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="storagetankintraday_export">EXCEL导出</button>
</div>
<script type="text/javascript">
    function storagetankintraday_operation(val,data){
        return '<button type="button" class="btn-green" data-source='+val+' onclick="see_storagetankintradayDetail(this)">联查明细</button>';
    }

    function see_storagetankintradayDetail(vals){
        var data = $(vals).attr('data-source');
        var bar_date = $('#storagetankintraday_bar_date').val();
        BJUI.navtab({
            id:'storagetankintraday001',
            url:'/report_storagetankintraday/detailList/'+Math.random(),
            type:'POST',
            data:{data:data,bar_date:bar_date},
            title:'储罐日明细表'
        });
        
    }

    $('#storagetankintraday_export').click(function(event) {
        var bar_date = $('#storagetankintraday_bar_date').val();
        var storagetank_sysno = $('#storagetankintraday_storagetank_sysno').val();
        var goods_sysno = $('#storagetankintraday_goods_sysno').val();
        
        BJUI.ajax('ajaxdownload', {
            url:'/report_storagetankintraday/dbtoexcel/',
            type:'POST',
            data:{bar_date:bar_date, storagetank_sysno:storagetank_sysno,goods_sysno:goods_sysno},
            successCallback: function(json, options) {

            }
        });
    });
</script>