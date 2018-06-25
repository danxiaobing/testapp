<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockindeclarelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围</label>
                <div class="row-input">
                    <div class="input-group input-daterange">
                        <input type="text" class="form-control" name="startdate" data-toggle="datepicker" value="{{date('Y-m-d',strtotime('-1 months'))}}" id="declare_startdate" placeholder="开始日期" >
                        <div class="input-group-addon">to</div>
                        <input type="text" class="form-control" name="enddate"  data-toggle="datepicker" id="declare_enddate" value="{{date('Y-m-d')}}" placeholder="结束日期" >
                    </div>
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <select id='declare_customer_sysno' name="sysno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($customers['list'] as $value)
                        	<option value="{{$value['sysno']}}">{{$value['customername']}}</option>
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
    <table class="table table-bordered" id="stockindeclarelist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showdeclare',
            addLocation: 'last',
            dataUrl: '/report_stockindeclare/ListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:14},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:false,
            hScrollbar:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center'}">罐号</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户</th>
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'updated_at',align:'center',width:95}">进货时间</th>
            <th data-options="{name:'shipname',align:'center'}">进货船名</th>
            <th data-options="{name:'tobeqty',align:'center'}">提单量</th>
            <th data-options="{name:'bussinesscheckqty',align:'center'}">商检量</th>
            <th data-options="{name:'release_num',align:'center'}">总报关量</th>
            <th data-options="{name:'release_beqty',align:'center'}">客户报关可发量</th>
            <th data-options="{name:'storagetank_beqty',align:'center'}">储罐报关可发量</th>
            <th data-options="{name:'unrelease_num',align:'center'}">罐存未报关量</th>
            <th data-options="{name:'storagetankoutqty',align:'center'}">罐出库量</th>
            <th data-options="{name:'storagetankqty',align:'center'}">罐结存量</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showdeclare">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="exceltoout()"></i>EXCEL导出</button>
</div>
<script type="text/javascript">
    function exceltoout(){
        var startdate = $("#declare_startdate").val();
        var enddate = $("#declare_enddate").val();
        var customer_sysno = $("#declare_customer_sysno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/Report_Stockindeclare/dbtoexcel/',
            type:'POST',
            data:{customer_sysno:customer_sysno, startdate:startdate,enddate:enddate},
            successCallback: function(json, options) {

            }
        });
    }

</script>