<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportformtanklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围</label>
                <div class="row-input required">
                    <input id="date1" type="text" name="date1" data-rule="required" value="{{date('Y-m-d',strtotime('-1 months'))}}" placeholder="开始时间"  data-toggle="datepicker" >
                </div>
                <div class="row-input required">
                	<input id="date2" type="text" name="date2" data-rule="required" value="{{date('Y-m-d')}}" placeholder="结束时间"  data-toggle="datepicker">
                </div>

                <label class="row-label">储罐号</label>
                <div class="row-input">
                    <select id="tankno" name="tankno" data-toggle="selectpicker" data-size='10' data-width="100%" data-live-search="true">
                        <option value="">请选择</option>
                        @foreach($storagetank as $key=>$value)
                        	<option value="{{$key}}" @if($key==$tankno) selected="selected" @endif>{{$value}}</option>
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
    <table class="table table-bordered" id="reportformtanklist-table" data-toggle="datagrid" data-options="{
            fullGrid:true,
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showretanklist',
            addLocation: 'last',
            dataUrl: '/reportform/tankListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:13},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            showTfoot:true,
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center'}">储罐号</th>
            <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'startqty',align:'center',calc:'sum',render:function(value){if(!value) return 0; }}">期初数量</th>
            <th data-options="{name:'totalinstockqty',align:'center',calc:'sum',render:function(value){if(!value) return 0; }}">入库数量</th>
            <th data-options="{name:'inretank',align:'center',calc:'sum'}">倒罐入库数量</th>
            <th data-options="{name:'totaloutstockqty',align:'center',calc:'sum',render:function(value){if(!value) return 0; }}">出库数量</th>
            <th data-options="{name:'outretank',align:'center',calc:'sum'}">倒罐出库数量</th>
            <th data-options="{name:'totalcheckqty',align:'center',calc:'sum',render:function(value){if(!value) return 0; }}">盘点</th>
            <th data-options="{name:'totalstockqty',align:'center',calc:'sum'}">期末余量</th>
            <th data-options="{name:'sysno',align:'center',render:tanklist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showretanklist">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="tanksignout()"></i>EXCEL导出</button>
</div>
<script type="text/javascript">
    function tanklist_operation(val,data){
        return '<button type="button" class="btn-green" onclick="see_tankdetail('+val+','+data.startqty+','+data.totalstockqty+')">联查明细</button>';
    }

    function see_tankdetail(val,starqty,totalqty){
        var date1 = $("#date1").val();
        var date2 = $("#date2").val();

        console.log(date1);
        console.log(date2);

        BJUI.navtab({
            id:'menu175',
            url:'/reportform/tankdetail/sid/'+val,
            type: 'post',
            data:{startqty:starqty,totalstockqty:totalqty,date1:date1,date2:date2},
            title:'储罐收发存明细表'
        });
    }

    function tanksignout(){
        var date1 = $("#date1").val();
        var date2 = $("#date2").val();
        var tankno = $("#tankno option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/reportform/excel/',
            type:'POST',
            data:{date1: date1,date2:date2,tankno:tankno},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>