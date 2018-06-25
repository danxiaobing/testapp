<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportinstocklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围</label>
                <div class="row-input datawidth required">
                    <input id="tankdetail_date1" type="text" name="date1" data-rule="required" value="@if($date1){{$date1}}@else{{date('Y-m-d',strtotime("-1 month"))}}@endif" placeholder="开始时间"  data-toggle="datepicker" >
                </div>
                <div class="row-input datawidth required">
                    <input id="tankdetail_date2" type="text" name="date2" data-rule="required" value="@if($date2){{$date2}}@else{{date('Y-m-d',time())}}@endif" placeholder="结束时间"  data-toggle="datepicker">
                </div>

                <label class="row-label">储罐号</label>
                <div class="row-input required">
                    <select id="tankdetail_tankno" name="tankno" data-rule="required" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size='10' >
                        <option value="">请选择</option>
                        @foreach($storagetank as $key=>$value)
                            <option value="{{$key}}" @if($key==$tankno) selected="selected" @endif>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <br>

                <label class="row-label">期初数量</label>
                <div class="row-input">
                    <input id="startqty" type="text" name="startqty" value="{{$startqty}}" readonly>
                </div>
                <label class="row-label">期末数量</label>
                <div class="row-input">
                    <input id="totalstockqty" type="text" name="totalstockqty" value="{{$totalstockqty}}" readonly>
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search" onclick="getStartAndEnd()">开始搜索</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reportinstocklist-table" data-toggle="datagrid" data-options="{
            fullGrid:true,
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showtankdaydetail',
            addLocation: 'last',
            dataUrl: '/report_reportinstock/ListJson',
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
            <th data-options="{name:'stockinno',align:'center'}">单据日期</th>
            <th data-options="{name:'stockindate',align:'center'}">单据编号</th>
            <th data-options="{name:'stockindate',align:'center'}">单据类型</th>
            <th data-options="{name:'stockindate',align:'center'}">槽车/船名</th>
            <th data-options="{name:'storagetankname',align:'center'}">商检量</th>
            <th data-options="{name:'goodsname',align:'center'}">出货量</th>
            <th data-options="{name:'customername',align:'center'}">货权转出量</th>
            <th data-options="{name:'shipname',align:'center',calc:'sum'}">结存量</th>
            <th data-options="{name:'sysno',align:'center',render:tanklist_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showtankdaydetail">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="tanksignout()"></i>EXCEL导出</button>
</div>
<script type="text/javascript">
    function tanklist_operation(val,data){
        return '<button type="button" class="btn-green" onclick="see_tankdetail('+val+','+data.startqty+','+data.totalstockqty+')">查看单据</button>';
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