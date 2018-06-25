<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#customerloss-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">查询区间</label>
                <div class="row-input datawidth">
                    <input type="text" id="start_time" name="start_time" value="" placeholder="开始时间"  data-toggle="datepicker" >
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="end_time" name="end_time" value="{{date('Y-m-d',time())}}" placeholder="结束时间"  data-toggle="datepicker">
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select name="customer_sysno" id="customerloss_customer_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($customerlist as $value)
                        	<option value="{{$value['sysno']}}">{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <br>
                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" id="customerloss_goods_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">全部</option>
                        @foreach($goodslist as $value)
                            <option value="{{$value['sysno']}}">{{$value['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>
                
                {{--<label class="row-label">货物性质</label>--}}
                {{--<div class="row-input">--}}
                    {{--<select name="goodsnature" id="customerloss_goodsnature" data-size="10" data-toggle="selectpicker" data-live-search="true" data-width="100%" >--}}
                        {{--<option value="">全部</option>--}}
                        {{--<option value="1" @if($goodsnature == '1') {{selected}} @endif>保税</option>--}}
                        {{--<option value="2" @if($goodsnature == '2') {{selected}} @endif>外贸</option>--}}
                        {{--<option value="3" @if($goodsnature == '3') {{selected}} @endif>内贸转出口</option>--}}
                        {{--<option value="4" @if($goodsnature == '4') {{selected}} @endif>内贸内销</option>--}}
                    {{--</select>--}}
                {{--</div>--}}

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
    <table class="table table-bordered" id="customerloss-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#customerloss_tb',
            addLocation: 'last',
            dataUrl: '/report_customerloss/listJson',
            dataType: 'json',
            editMode:false,
            jsonPrefix: 'obj',
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:false,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">货品</th>
            <th data-options="{name:'created_at',align:'center'}">入库时间</th>
            <th data-options="{name:'shipname',align:'center',render:function(value){if(!value) {return '--'}}}">车/船名</th>
            <th data-options="{name:'storagestock',align:'center'}">商检量</th>
            <th data-options="{name:'outqty',align:'center'}">出库量</th>
            <th data-options="{name:'inqty',align:'center'}">货转量</th>
            <th data-options="{name:'ullage',align:'center'}">损耗量</th>
            <th data-options="{name:'endstock',align:'center'}">结存量</th>
            <th data-options="{name:'info',align:'center',render:customerloss_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="customerloss_tb">
    {{--<button type="button" class="btn btn-green" data-icon="sign-out"  id="customerloss_export">EXCEL导出</button>--}}
</div>
<script type="text/javascript">
    function customerloss_operation(val,data){
        return '<button type="button" class="btn-green" data-source='+val+' onclick="see_customerlossDetail(this)">查看明细</button>';
    }

    function see_customerlossDetail(vals){
        var data = $(vals).attr('data-source');
//        console.log(data);return;
        var start_time = $('#start_time').val();
        var end_time = $('#end_time').val();

        BJUI.navtab({
            id:'customerloss001',
            url:'/report_customerloss/detailList/'+Math.random(),
            type:'POST',
            data:{data:data,start_time:start_time,end_time:end_time},
            title:'客户损耗表明细'
        });
        
    }

    $('#customerloss_export').click(function(event) {
        var bar_date = $('#customerloss_bar_date').val();
        var customer_sysno = $('#customerloss_customer_sysno').val();
        var goods_sysno = $('#customerloss_goods_sysno').val();
        var goodsnature = $('#customerloss_goodsnature').val();
        
        BJUI.ajax('ajaxdownload', {
            url:'/report_customerloss/dbtoexcel/',
            type:'POST',
            data:{bar_date:bar_date, customer_sysno:customer_sysno,goods_sysno:goods_sysno,goodsnature:goodsnature},
            successCallback: function(json, options) {

            }
        });
    });
</script>