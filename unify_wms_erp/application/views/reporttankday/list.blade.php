<div class="bjui-pageHeader">
    <form id="searchtankday" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportinstocklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围</label>
                <div class="row-input required">
                    <input id="tankdaydate" type="text" name="tankdaydate" data-rule="required" value="{{date('Y-m-d',time())}}" placeholder="开始时间"  data-toggle="datepicker" >
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select id="customer_sysno" name="customer_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($customers['list'] as $value)
                        	<option value="{{$value['sysno']}}">{{$value['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">储罐</label>
                <div class="row-input">
                    <select id="storagetank_sysno" name="storagetank_sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($storagetanks['list'] as $value)
                            <option value="{{$value['sysno']}}">{{$value['storagetankname']}}</option>
                        @endforeach
                    </select>
                </div>
                <br>

                <label class="row-label">船名</label>
                <div class="row-input">
                    <input id="shipname" type="text" name="shipname">
                </div>

                <label class="row-label">货名</label>
                <div class="row-input">
                    <input id="goodsname" type="text" name="goodsname">
                </div>

                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" id="sssssss" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reportinstocklist-table" data-toggle="datagrid" data-options="{
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showtankday',
            addLocation: 'last',
            dataUrl: '/report_reporttankday/ListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            tableWidth:'100%',
            showTfoot:true,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'customername',width:260,align:'center'}">客户</th>
            <th data-options="{name:'goodsname',width:100,align:'center'}">品名</th>
            <th data-options="{name:'doc_time',width:120,align:'center'}">进货时间</th>
            <th data-options="{name:'shipname',width:120,align:'center'}">进货船名</th>
            <th data-options="{name:'beqty',width:120,align:'center',calc:'sum'}">商检量</th>
            <th data-options="{name:'qichu',width:120,align:'center',calc:'sum'}">昨日结存量</th>
            <th data-options="{name:'out_num',width:120,align:'center',calc:'sum'}">今日出库量</th>
            <th data-options="{name:'transout',width:120,align:'center',calc:'sum'}">今日货转出量</th>
            <th data-options="{name:'tankout',width:120,align:'center',calc:'sum'}">今日倒出量</th>
            <th data-options="{name:'wastage',width:100,align:'center',calc:'sum'}">损耗量</th>
            <th data-options="{name:'end_num',width:120,align:'center',calc:'sum'}">今日结存量</th>
            <th data-options="{name:'storagetankname',width:80,align:'center'}">罐号</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showtankday">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="tanksignout()"></i>EXCEL导出</button>
    {{--<button type="button"  class="btn btn-green" data-icon="sign-out" onclick="overdueullage()"></i>超期损耗测试</button>--}}
</div>
<script type="text/javascript">

    function overdueullage(){
        BJUI.ajax('doajax', {
            url:'/Report_Reporttankday/overdueullage/',
            type:'POST',
            data:{},
            okCallback: function(json, options) {
                if(json.mes ==1){
                    BJUI.alertmsg('ok','测试成功');
                }
            }
        });
    }

    function tanksignout(){
        var tankdaydate = $.CurrentNavtab.find("#tankdaydate").val();
        var customer_sysno = $.CurrentNavtab.find("#customer_sysno option:selected").val();
        var storagetank_sysno = $("#storagetank_sysno option:selected").val();
        var shipname = $.CurrentNavtab.find("#shipname").val();
        var goodsname = $.CurrentNavtab.find("#goodsname").val();

        BJUI.ajax('ajaxdownload', {
            url:'/Report_Reporttankday/Excel/',
            type:'POST',
            data:{tankdaydate: tankdaydate,customer_sysno:customer_sysno,storagetank_sysno:storagetank_sysno,shipname:shipname,goodsname:goodsname},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>
