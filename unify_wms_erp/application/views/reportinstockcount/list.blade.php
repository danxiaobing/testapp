
<div class="bjui-pageContent clearfix">
<div style="position: absolute;top: 0px;right: 0;width: 580px;height: 50px;z-index: 99;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reportinstockcountlist-table')}">
            <div class="bjui-row col-2">

                <label class="row-label">时间范围</label>
                <div class="row-input required">
                    <input id="year" type="text" name="year" class="datepicker" data-rule="required" value="{{date('Y',time())}}" placeholder="开始时间" >
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
    </form>
</div>
    <table class="table table-bordered" id="reportinstockcountlist-table" data-toggle="datagrid" data-options="{
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showinstockcount',
            addLocation: 'last',
            dataUrl: '/report_reportinstockcount/ListJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging:false,
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            fullGrid:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'month',align:'center'}">月份</th>
            <th data-options="{name:'totalqty',align:'center'}">入库总量</th>
            <th data-options="{name:'shipqty',align:'center'}">船入库总量</th>
            <th data-options="{name:'shipoutnu',align:'center'}">船数（外贸）</th>
            <th data-options="{name:'shipinnu',align:'center'}">船数（内贸）</th>
            <th data-options="{name:'shipoutqty',align:'center'}">船入库数量（外贸）</th>
            <th data-options="{name:'shipinqty',align:'center'}">船入库数量（内贸）</th>
            <th data-options="{name:'carqty',align:'center'}">槽车入库总量</th>
            <th data-options="{name:'carnu',align:'center'}">车数</th>
            <th data-options="{name:'pipeInStockCount',align:'center'}">管线入库总量</th>
            <th data-options="{name:'pipeInStockCount_in',align:'center'}">管线入库总量(外贸)</th>
            <th data-options="{name:'pipeInStockCount_out',align:'center'}">管线入库总量(内贸)</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showinstockcount">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="signout()"></i>EXCEL导出</button>
</div>
<script type="text/javascript">
    $('.datepicker').datetimepicker({
        format: 'yyyy',
        weekStart: 1,
        autoclose: true,
        startView: 4,
        minView: 4,
        forceParse: false,
        language: 'zh-CN'

    });

    function signout(){
        var year = $("#year").val();

        BJUI.ajax('ajaxdownload', {
            url:'/report_reportinstockcount/excel/',
            type:'POST',
            data:{year: year},
            successCallback: function(json, options) {
                //console.log(123);
            }
        });
    }

</script>