<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" id="countgoodslist-excel" data-options="{searchDatagrid:$.CurrentNavtab.find('#countgoodslist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围:</label>
                <div class="row-input datawidth">
                    <!-- <input type="text" name="year"  value="{{date('Y')}}"  placeholder="开始时间" > -->
                    <input type="text" name="year" class="countgoods_datepicker" value="{{date('Y')}}" placeholder="开始时间">
                </div>
                
                <label class="row-label">统计内容</label>
                <div class="row-input">
                    <select name="type" data-toggle="selectpicker"  data-width="100%" class="show-tick" data-live-search="true" data-size='10'>
                    <option value="1">入库统计</option>
                    <option value="2">出库统计</option>
                    <option value="3">损耗统计</option>
                    <option value="4">存货统计</option>
                    </select></div>

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
    <table class="table table-bordered" id="countgoodslist-table" data-toggle="datagrid" data-options="{
        fullGrid:true,
        height: '100%',
        showToolbar: true,
        toolbarItem: 'export',
        dataUrl: 'Report_Countgoods/CountgoodsJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        exportOption: {type:'file', options:{url:'/Report_Countgoods/Excellist',form:$('#countgoodslist-excel')}},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        showTfoot:true,
        showNoDataTip:true,
        editMode:false,
    }">
        <thead>
        <tr data-options="{name:''}">
            <th data-options="{name:'goodsname',align:'center'}">品名</th>
            <th data-options="{name:'1',align:'center',calc:'sum'}">一月份</th>
            <th data-options="{name:'2',align:'center',calc:'sum'}">二月份</th>
            <th data-options="{name:'3',align:'center',calc:'sum'}">三月份</th>
            <th data-options="{name:'4',align:'center',calc:'sum'}">四月份</th>
            <th data-options="{name:'5',align:'center',calc:'sum'}">五月份</th>
            <th data-options="{name:'6',align:'center',calc:'sum'}">六月份</th>
            <th data-options="{name:'7',align:'center',calc:'sum'}">七月份</th>
            <th data-options="{name:'8',align:'center',calc:'sum'}">八月份</th>
            <th data-options="{name:'9',align:'center',calc:'sum'}">九月份</th>
            <th data-options="{name:'10',align:'center',calc:'sum'}">十月份</th>
            <th data-options="{name:'11',align:'center',calc:'sum'}">十一月份</th>
            <th data-options="{name:'12',align:'center',calc:'sum'}">十二月份</th>
            <th data-options="{name:'countnum',align:'center',calc:'sum'}">总量</th>
        </tr>
        </thead>
    </table>
</div>
<script>
$('.countgoods_datepicker').datetimepicker({
        //language:  'fr',
       format: 'yyyy',  
         weekStart: 1,  
         autoclose: true,  
         startView: 4,  
         minView: 4,
         forceParse: false,
         language: 'zh-CN'

    });
</script>