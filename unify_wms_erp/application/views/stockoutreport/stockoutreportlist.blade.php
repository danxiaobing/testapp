

<div class="bjui-pageContent clearfix">
<div style="position: absolute;top: 0px;right: 0;width: 580px;height: 50px;z-index: 99;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockoutreportlist-table')}">
       
            <div class="bjui-row col-2">

                <label class="row-label">时间范围</label>
                <div class="row-input">
                    <input type="text" name="daterange" value="{{date('Y')}}" placeholder="时间" class="daterangepicker" id="stockoutreportlist_daterange">
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>

    </form>
</div>
    <table class="table table-bordered" id="stockoutreportlist-table" data-toggle="datagrid" data-options="{
            height: '100%',
            showToolbar: true,
            toolbarCustom:'#showreportlist',
            addLocation: 'last',
            dataUrl: '/Report_StockoutReport/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: false,
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            fieldSortable: false,
            fullGrid:true
        }">
        <thead>
        <tr>
            <th data-options="{name:'month',align:'center'}">月份</th>
            <th data-options="{name:'beqty',align:'center'}">出库总量</th>
            <th data-options="{name:'shipstockoutqty',align:'center'}">船出库总量</th>
            <th data-options="{name:'shipnum1',align:'center'}">船数(外贸)</th>
            <th data-options="{name:'shipnum2',align:'center'}">船数(内贸)</th>
            <th data-options="{name:'shipstockoutqty1',align:'center'}">船出库(外贸)</th>
            <th data-options="{name:'shipstockoutqty2',align:'center'}">船出库(内贸)</th>
            <th data-options="{name:'pountsoutqty',align:'center'}">槽车出库总量</th>
            <th data-options="{name:'carnum',align:'center'}">车数</th>
            <th data-options="{name:'bucketnumber',align:'center'}">罐桶数</th>
            <th data-options="{name:'pipelineoutqty',align:'center'}">管出库总量</th>
            <th data-options="{name:'pipelineoutqty1',align:'center'}">管出库(外贸)</th>
            <th data-options="{name:'pipelineoutqty2',align:'center'}">管出库(内贸)</th>
        </tr>
        </thead>
    </table>
</div>
<div id="showreportlist">
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="excelout()">EXCEL导出</button>
</div>
<script type="text/javascript">
    $('.daterangepicker').datetimepicker({
        //language:  'fr',
        format: 'yyyy',  
        weekStart: 1,  
        autoclose: true,  
        startView: 4,  
        minView: 4,
        forceParse: false,
        language: 'zh-CN'

    });


    function excelout(){
        var daterange = $('#stockoutreportlist_daterange').val();
        console.log(daterange);
        BJUI.ajax('ajaxdownload', {
            url:'/Report_Stockoutreport/dbtoexcel/',
            type:'POST',
            data:{daterange:daterange},
            successCallback: function(json, options) {

            }
        });
    }
</script>
