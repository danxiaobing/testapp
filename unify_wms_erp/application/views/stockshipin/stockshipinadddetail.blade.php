<div class="bjui-pageContent">
<form id="stockshipin-detail-form" action="/stockshipin/detailsubmit" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <label class="row-label">品名</label>
        <div class="row-input required">
            <input type="hidden" name="obj.goods_sysno" value="{{$goods_sysno}}">
            <input type="text" name="obj.goodsname" value="{{$goodsname}}" readonly data-rule="required" data-toggle="findgrid" data-options="{
            group: 'obj',
            include: 'goodsname:goodsname, goods_sysno:sysno',
            dialogOptions: {width:'800',height:'500',title:'货品资料',maxable:true,resizable:true,mask:true},
            gridOptions: {
                width:'100%',
                height:'100%',
                tableWidth:'99.8%',                       
                local: 'local',
                paging: {pageSize:20},
                postData: {customer_sysno:{{$customer_sysno}}},
                dataUrl: '/customer/customergoodslistJson',
                columns: [
                    {name:'sysno', label:'id'},
                    {name:'goodsno', label:'货品编号'},
                    {name:'goodsname', label:'货品名称'}
                ],
                showLinenumber:false
            },
        }" placeholder="点放大镜按钮查找"></div>

        <label class="row-label">规格</label>
        <div class="row-input required">
            <select data-toggle="selectpicker" name="goods_quality_sysno" id="goods_quality_sysno" data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                <option value="" selected="">请选择</option>
                @foreach($goodsqualitylist as $item)
                    <option value="{{$item['sysno']}}" @if($item['sysno'] == $qualityname) selected @endif>{{$item['qualityname']}}</option>
                @endforeach
            </select>
            <input type="hidden" name="goods_quality_name" id="goods_quality_name" value="{{$goods_quality_name}}">
        </div>

        <label class="row-label">货物性质</label>
        <div class="row-input required">
            <select data-toggle="selectpicker" name="goodsnature" data-width="100%" data-rule="required">
                <option value="" selected="">请选择</option>
                <option value="1">保税</option>
                <option value="2">外贸</option>
                <option value="3">内贸转出口</option>
                <option value="4">内贸内销</option>
            </select>
        </div>
        
        <label class="row-label">日期</label>
        <div class="row-input required">
            <input type="text" name="goodsreceiptdate" value="@if($goodsreceiptdate){{date('Y-m-d',strtotime($goodsreceiptdate))}}@else{{date('Y-m-d')}}@endif" data-toggle="datepicker" data-rule="required;date"></div>
    
        <label class="row-label">通知数量</label>
        <div class="row-input required">
            <input type="text" name="tobeqty" value="{{$tobeqty}}" data-rule="required;number;range[0~]">
        </div>

        <label class="row-label">提单数量</label>

        <div class="row-input">
            <input type="text" name="takegoodsnum" value="{{$takegoodsnum}}" data-rule="number;range[0~]">
        </div>

        <label class="row-label">实际数量</label>
        <div class="row-input required">
            <input type="text" name="beqty" value="{{$beqty}}" data-rule="required;number;range[0~]">
        </div>

        <label class="row-label">进货罐号</label>
        <div class="row-input required">
            <select data-toggle="selectpicker" name="storagebank_sysno" id="storagebank_sysno" data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                <option value="" selected="">请选择</option>
                @foreach($storagetanklist as $item)
                    <option value="{{$item['sysno']}}" @if($item['sysno'] == $storagetankname) selected @endif>{{$item['storagetankname']}}</option>
                @endforeach
            </select>
            <input type="hidden" name="storagetankname" id="storagetankname" value="{{$storagetankname}}">
        </div>

        <label class="row-label">船名</label>
        <div class="row-input required">
            <input type="text" name="obj.shipname" value="{{$shipname}}" data-rule="required" data-toggle="findgrid" data-options="{
                group: 'obj',
                include: 'shipname:shipname',
                dialogOptions: {width:'800',height:'500',title:'船详细信息',maxable:true,resizable:true,mask:true},
                gridOptions: {
                    width:'100%',
                    height:'100%',
                    tableWidth:'99.8%', 
                    local: 'local',
                    paging: {pageSize:20},
                    dataUrl: '/supplier/shiplistJson/page/1',
                    editUrl: '/supplier/shiplist',
                    columns: [
                        {name:'sysno', label:'id'},
                        {name:'captain', label:'船长'},
                        {name:'shipname', label:'船名'}
                    ],
                    showLinenumber:false
                },
            }" placeholder="点放大镜按钮查找">
        </div>

        <label class="row-label">送货公司</label>
        <div class="row-input">
            <input type="text" name="expresscompanyname" value="{{$expresscompanyname}}" >
        </div>

        <label class="row-label"></label>
        <div class="row-input">
        </div>

        <label class="row-label">备注:</label>
        &nbsp;&nbsp;
        <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$memo}}</textarea>

    </div>

</form>
    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="subOutReceipe()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>

<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    function subOutReceipe() {
        $('#stockshipin-detail-form').isValid(function(v){
            if (v) {

                $("#goods_quality_name").val($("#goods_quality_sysno option:selected").text());
                $("#storagetankname").val($("#storagebank_sysno option:selected").text());
                var data  = $("#stockshipin-detail-form").serializeArray();

                var json = serializeObject(data);


                var allData  = $("#stockshipin-detail-table").data('allData');

                if(typeof  allData != 'undefined'){
                    allData.push(json);
                }else{
                    allData = [json] ;
                }

                $('#stockshipin-detail-table').datagrid('reload',  {data:allData});
                var test = JSON.stringify(json); 
                console.log(test);
                BJUI.dialog('closeCurrent');
            }else{
                console.log('no');
            }

        });
    }

    function serializeObject(a)
    {
        var o = {};
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
</script>