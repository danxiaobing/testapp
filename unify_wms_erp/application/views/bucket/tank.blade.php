<div class="bjui-pageHeader">
    <form id="bucket-form" data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#bucket-tank-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围</label>
                <div class="row-input">
                    <input type="text" name="select_data" value="{{date('Y-m',time())}}"  class="datepicker">
                    {{--<select name="daterange" data-toggle="selectpicker"  data-width="50%" id="stockoutreportlist_daterange">--}}
                        {{--<option value="">全部</option>--}}
                        {{--<option value="2015">2015</option>--}}
                        {{--<option value="2016">2016</option>--}}
                        {{--<option value="2017" selected>2017</option>--}}
                        {{--<option value="2018">2018</option>--}}
                        {{--<option value="2019">2019</option>--}}
                        {{--<option value="2020">2020</option>--}}
                    {{--</select>--}}
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
    <table class="table table-bordered" id="bucket-tank-table" data-toggle="datagrid" data-options="{
            height:'100%',

            showToolbar: true,
            toolbarItem: 'export',
            addLocation: 'last',
            dataUrl: '/Report_Stockoutbucket/tankJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            exportOption: {type:'file', options:{url:'/Report_Stockoutbucket/tankToExcel',form:$('#bucket-form')}},
            paging: {pageSize:12},
            showCheckboxcol: false,
            linenumberAll: true,
            filterThead:false,
            showLinenumber: true,
            tableWidth:'100%',
            hScrollbar:true,
            showTfoot:false,
            fieldSortable: false,
        }">
        <thead>
        <tr>
            <th data-options="{name:'day', width:100, align:'center'}">日期</th>
            <th data-options="{name:'count', width:80, align:'center'}">磅单数</th>
            <th data-options="{name:'ship_count', width:80, align:'center'}">发船数</th>
            <th data-options="{name:'pound_count', width:100, align:'center'}">槽车数量</th>
            {{--<th data-options="{name:'bucket_out',align:'center'}">堆桶场地发货吨位</th>--}}
            {{--<th data-options="{name:'bucket_qty',align:'center'}">堆桶场地结存</th>--}}
            {{--<th data-options="{name:'bucketnumber',align:'center'}">罐桶总数</th>--}}
            @foreach($list as $value)
            <th data-options="{name:'{{$value['sysno']}}', width:100, align:'center'}">{{$value['goodsname']}}</th>
            @endforeach
        </tr>
        </thead>
    </table>
</div>
<script type="text/javascript">
    $('.datepicker').datetimepicker({
        //language:  'fr',
        format: 'yyyy-mm',
        weekStart: 1,
        autoclose: true,
        startView: 3,
        minView: 3,
        forceParse: false,
        language: 'zh-CN'

    });
</script>
