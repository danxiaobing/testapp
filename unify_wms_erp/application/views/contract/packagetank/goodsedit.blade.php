<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" class="datagrid-edit-form" action="" method="post" data-data-type="json">
        <input type="hidden" name="id" value="{{$id}}">
        <div class="bjui-row col-2">
            <label class="row-label">品名:</label>
            <div class="row-input required">
                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                <input type="text" name="goodsname" value="{{$goodsname}}" readonly
                       data-rule="required" data-toggle="findgrid" data-options="{
                        dialogOptions: {width:'800',height:'600',title:'商品名称',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:10},
                            dataUrl: '/goods/getGoodsandprice',
                            columns: [
                                {name:'goodsname',align:'center',label:'商品名称'},
                                {name:'unitname',align:'center',label:'单位名称'},
                                {name:'islongterm',align:'center',label:'长期品种'},
                                {name:'rate_waste',align:'center',label:'内控损耗'},
                                {name:'controlproportion',align:'center',label:'控货比重'},
                                {name:'controlprice',align:'center',label:'控货单价'},
                            ],
                            showLinenumber:true
                        },
                    }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">计量单位</label>
            <div class="row-input">
                <input type="hidden" name="unit_sysno" >
                <input type="text" name="unitname" value="{{$unitname}}" readonly>
            </div>

            <label class="row-label">货物性质</label>
            <div class="row-input">
                <select name="goodsnature"  data-toggle="selectpicker" data-width="100%"
                        data-live-search="true" data-size="10">
                    <option value="">请选择</option>
                    <option value="1" @if($goodsnature==1) selected @endif>保税</option>
                    <option value="2"@if($goodsnature==2) selected @endif>外贸</option>
                    <option value="3"@if($goodsnature==3) selected @endif>内贸转出口</option>
                    <option value="4"@if($goodsnature==4) selected @endif>内贸内销</option>
                </select>
            </div>

            <label class="row-label">规格</label>
            <div class="row-input">
                <select id="quality" name="goods_quality_sysno" data-toggle="selectpicker"
                        data-width="100%" data-live-search="true" data-size="10">
                    <option value="">请选择</option>
                    @foreach($qualitylist as $item)
                        <option value="{{$item['sysno']}}" @if($item['sysno']==$goods_quality_sysno) selected @endif>{{$item['qualityname']}}</option>
                    @endforeach
                </select>
            </div>

            <label class="row-label">合约损率‰</label>
            <div class="row-input required">
                <input type="text" name="contractrate" value="{{$contractrate}}" data-rule="required number">
            </div>

            @if($zuguantype==3)
            <label class="row-label">储罐编号</label>
            <div class="row-input required">
                <input type="hidden" id="con_storagetank_sysno" name="obj.storagetank_sysno" value="{{$storagetank_sysno}}">
                <input type="text" id="con_storagetankname" name="obj.storagetankname" value="{{$storagetankname}}" readonly
                       data-rule="required " data-toggle="findgrid" data-options="{
                        group: 'obj',
                        include: 'storagetank_sysno:storagetank_sysno, storagetankname:storagetankname,theoreticalcapacity:theoreticalcapacity',
                        dialogOptions: {width:'800',height:'600',title:'储罐名称',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:10},
                            dataUrl: '/storagetank/getBGstoragetank',
                            columns: [
                                {name:'areaname',align:'center',label:'片区名称'},
                                {name:'storagetankname',align:'center',label:'储罐名称'},
                                {name:'theoreticalcapacity',align:'center',label:'储罐容量'},
                                {name:'storagetank_categoryname',align:'center',label:'不能储放材质'},
                            ],
                            showLinenumber:true
                        },
                    }" placeholder="点放大镜按钮查找">
            </div>
            @endif

            <label class="row-label">容量（m³)</label>
            <div class="row-input required">
                <input type="text" id="theoreticalcapacity" name="theoreticalcapacity" value="{{$capacity}}" data-rule="required number">
            </div>

            <label class="row-label">溢罐吨数</label>
                <div class="row-input">
                    <input type="hidden" id="density" name="density">
                    <input type="text" id="overcapacity" name="overcapacity" value="{{$overcapacity}}" data-rule="number">
                </div>

            <label class="row-label">中转量</label>
            <div class="row-input required">
                <input type="text" name="yearqty" value="@if(isset($yearqty)){{$yearqty}}@else{{0}}@endif" data-rule="required number">
            </div>

            <label class="row-label">租金</label>
            <div class="row-input required">
                <input type="text" name="yearamount" value="{{$yearamount}}" data-rule="required number"style="width:70%;"><span>元/月</span>
            </div>

            <label class="row-label">超中转量费</label>
            <div class="row-input required">
                <input type="text" name="exyearrate" value="{{$exyearrate}}" data-rule="required number"style="width:70%;"><span>元/吨/天</span>
            </div>

            <label class="row-label">溢罐首期仓储费</label>
            <div class="row-input">
                <input type="text" name="overfirstpayment" value="{{$overfirstpayment}}" data-rule="number"style="width:70%;"><span>元/吨·（30天）</span>
            </div>

            <label class="row-label">溢罐超期仓储费</label>
            <div class="row-input">
                <input type="text" name="overlastpayment" value="{{$overlastpayment}}" data-rule="number"style="width:70%;"><span>元/吨/天</span>
            </div>

            <label class="row-label">开票公司</label>
            <div class="row-input">
                <select id="company" name="invoice_company_sysno" data-nextselect="#cs_employeename" data-toggle="selectpicker"
                        data-width="100%" data-live-search="true" data-size="10">
                    <option value="">请选择</option>
                    @foreach($companylist['list'] as $item)
                        <option value="{{$item['sysno']}}" @if($item['sysno']==$invoice_company_sysno) selected @elseif(!isset($invoice_company_sysno)&&$item['isdefault'] == 1) selected @endif>{{$item['companyname']}}</option>
                    @endforeach
                </select>
            </div>

            <label class="row-label">备注</label>
            <div class="row-input">
                <textarea name="memo">{{$memo}}</textarea>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            @if($handlestatus=='edit')
                <button type="button" id="contractgoods_edit" class="btn-green" data-icon="save">保存</button>
            @else
                <button type="button" id="contractgoods_save" class="btn-green" data-icon="save">保存</button>
            @endif
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>
<script type="text/javascript">
    $("#contractgoods_save").click(function (){
        $('#treeform').isValid(function(v){
            if(v){
                var data  = $("#treeform").serializeJson();
                var allData  = $.CurrentNavtab.find("#goods-detail-table").data('allData');

                var qualityname = $("#quality option:selected").text();
                var companyname = $("#company option:selected").text();
                var storagetank_sysno = $("#con_storagetank_sysno").val();
                var storagetankname = $("#con_storagetankname").val();
                if(qualityname!='请选择')
                    data.qualityname = qualityname;
                data.capacity = data.theoreticalcapacity;
                data.companyname = companyname;
                data.storagetank_sysno = storagetank_sysno;
                data.storagetankname = storagetankname;

                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }
                $.CurrentNavtab.find('#goods-detail-table').datagrid('reload',  {data:allData});

                BJUI.dialog('closeCurrent');
            }
        })
    })

    $("#contractgoods_edit").click(function (){
        $('#treeform').isValid(function(v){
            if(v){
                var data  = $("#treeform").serializeJson();
                var qualityname = $("#quality option:selected").text();
                var companyname = $("#company option:selected").text();
                var storagetank_sysno = $("#con_storagetank_sysno").val();
                var storagetankname = $("#con_storagetankname").val();
                if(qualityname != '请选择')
                    data.qualityname = qualityname;
                data.capacity = data.theoreticalcapacity;
                data.companyname = companyname;
                data.storagetank_sysno = storagetank_sysno;
                data.storagetankname = storagetankname;

                $.CurrentNavtab.find('#goods-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                var obj = $.CurrentNavtab.find('#goods-detail-table').data('allData');
                obj["{{$gridIndex}}"] = data;
                $.CurrentNavtab.find('#goods-detail-table').datagrid('reload', {data:obj});
                BJUI.dialog('closeCurrent');
            }
        })
    })

</script>