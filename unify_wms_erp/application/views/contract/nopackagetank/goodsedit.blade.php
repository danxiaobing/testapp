<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <form id="treeform" class="datagrid-edit-form" action="" method="post"  data-data-type="json">
        <input type="hidden" name="id" value="{{$id}}">

        <div class="bjui-row col-2">

            <label class="row-label">品名:</label>
            <div class="row-input required">
                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                <input type="text" name="goodsname" value="{{$goodsname}}"  readonly
                       data-rule="required" data-toggle="findgrid" data-options="{
                        dialogOptions: {width:'800',height:'500',title:'商品名称',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:20},
                            dataUrl: '/goods/getGoodsandprice',
                            columns: [
                                {name:'goodsname', label:'商品名称'},
                                {name:'unitname',label:'单位名称'},
                                {name:'rate_waste',label:'内控损耗'},
                                {name:'islongterm',label:'长期品种'},
                                {name:'storagetank_categoryname',label:'不能存放的材质'},
                            ],
                            showLinenumber:true
                        },
                    }" placeholder="点放大镜按钮查找">
            </div>

            <label class="row-label">计量单位</label>
            <div class="row-input">
                <input type="hidden" name="unit_sysno" >
                <input name="unitname" value="{{$unitname}}" readonly>
            </div>

            <label class="row-label">规格</label>
            <div class="row-input">
                <select id="noquality" name="goods_quality_sysno" data-nextselect="#cs_employeename" data-toggle="selectpicker"
                        data-width="100%" data-live-search="true" data-size="10">
                    <option value="">请选择</option>
                    @foreach($qualitylist as $item)
                        <option value="{{$item['sysno']}}"@if($item['sysno']==$goods_quality_sysno) selected @endif>{{$item['qualityname']}}</option>
                    @endforeach
                </select>
            </div>

            <label class="row-label">货物性质</label>
            <div class="row-input">
                <select name="goodsnature" data-toggle="selectpicker" data-width="100%"
                        data-live-search="true" data-size="10" >
                    <option value="">请选择</option>
                    <option value="1" @if($goodsnature==1) selected @endif>保税</option>
                    <option value="2"@if($goodsnature==2) selected @endif>外贸</option>
                    <option value="3"@if($goodsnature==3) selected @endif>内贸转出口</option>
                    <option value="4"@if($goodsnature==4) selected @endif>内贸内销</option>
                </select>
            </div>

            <label class="row-label">预计数量</label>
            <div class="row-input">
                <input type="text" name="goodsqty" value="@if(isset($goodsqty)){{$goodsqty}}@endif"  data-rule="number">
            </div>

            @if($zuguantype==1)
                <label class="row-label">预计到货日期</label>
                <div class="row-input">
                    <input type="text" name="goodsdate" value="@if($goodsdate!='0000-00-00'){{$goodsdate}}@endif" data-toggle="datepicker" data-rule="date">
                </div>
            @elseif($zuguantype==2)
                <label class="row-label">预计到货日期</label>
                <div class="row-input required">
                    <input type="text" name="goodsdate" value="{{$goodsdate}}" data-toggle="datepicker" data-rule="date required">
                </div>
            @endif

            <label class="row-label">储罐使用费</label>
            <div class="row-input required">
                <input type="text" name="firststorageamount"  data-rule="required number" value="{{$firststorageamount}}" style="width: 78%;">
                <span>元/吨·30天</span>
            </div>

            <label class="row-label">超期费</label>
            <div class="row-input required">
                <input type="text"  name="lastamount" data-rule="required;number"value="{{$lastamount}}" style="width: 78%;">
                <span>元/吨/天</span>
            </div>

            <label class="row-label">首期损耗率‰</label>
            <div class="row-input required">
                <input type="text" name="firstlossrate" data-rule="required;number" value="{{$firstlossrate}}"  >
            </div>

            <label class="row-label">超期损耗率‰</label>
            <div class="row-input">
                <input type="text" name="lastlossrate"  data-rule="number" value="{{$lastlossrate}}" style="width: 80%;">
                <span>(/月)</span>
            </div>

            <label class="row-label">启用最小入库量</label>
            <div class="row-input required">
                <select id="isminstockin" name="isminstockin"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                    <option value="0" @if($isminstockin==0) selected @endif>否</option>
                    <option value="1" @if($isminstockin==1) selected @endif>是</option>
                </select>
            </div>

            <div id="isminstockin_div" @if(!isset($isminstockin)||$isminstockin==0) style="display: none"@endif>
                <label class="row-label">最小入库量</label>
                <div class="row-input required">
                    <input type="text" id="minnumber" name="minnumber" value="@if(isset($minnumber)){{$minnumber}}@endif" @if($isminstockin==1) data-rule="required number" @endif>
                </div>

                <label class="row-label">启用最小入库量计费</label>
                <div class="row-input required">
                    <select id="isminstockincost" name="isminstockincost"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                        <option value="0" @if($isminstockincost==0) selected @endif>否</option>
                        <option value="1" @if($isminstockincost==1) selected @endif>是</option>
                    </select>
                </div>

                <label class="row-label">启用最小入库量损耗</label>
                <div class="row-input required">
                    <select id="isminstockinullage" name="isminstockinullage"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                        <option value="0" @if($isminstockinullage==0) selected @endif>否</option>
                        <option value="1" @if($isminstockinullage==1) selected @endif>是</option>
                    </select>
                </div>
            </div>

            <label class="row-label">启用最小结存量</label>
            <div class="row-input required">
                <select id="isminbalance" name="isminbalance"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                    <option value="0" @if($isminbalance==0) selected @endif>否</option>
                    <option value="1" @if($isminbalance==1) selected @endif>是</option>
                </select>
            </div>

            <div id="isminbalance_div" @if(!isset($isminbalance)||$isminbalance==0) style="display: none"@endif>
                <label class="row-label">最小结存量</label>
                <div class="row-input required">
                    <input type="text" id="minbalancenumber" name="minbalancenumber" value="@if(isset($minbalancenumber)){{$minbalancenumber}}@endif" @if($isminbalance==1) data-rule="required number" @endif>
                </div>

                <label class="row-label">启用最小结存量计费</label>
                <div class="row-input required">
                    <select id="isminbalancecost" name="isminbalancecost"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                        <option value="0" @if($isminbalancecost==0) selected @endif>否</option>
                        <option value="1" @if($isminbalancecost==1) selected @endif>是</option>
                    </select>
                </div>

                <label class="row-label">启用最小结存量损耗</label>
                <div class="row-input required">
                    <select id="isminbalanceullage" name="isminbalanceullage"  data-toggle="selectpicker" data-rule="required" data-width="100%" data-size="10">
                        <option value="0" @if($isminbalanceullage==0) selected @endif>否</option>
                        <option value="1" @if($isminbalanceullage==1) selected @endif>是</option>
                    </select>
                </div>

            </div>

            <label class="row-label">开票公司</label>
            <div class="row-input">
                <select id="nocompany" name="invoice_company_sysno"  data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
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
                <button type="button" id="nocontractgoods_edit" class="btn-green" data-icon="save">保存</button>
            @else
                <button type="button" id="nocontractgoods_save" class="btn-green" data-icon="save">保存</button>
            @endif
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>
<script>
    $("#isminstockin").change(function (){
        if($('#isminstockin option:selected').val()==0){
            $("#isminstockin_div").attr('style','display:none');
            $("#minnumber").attr('data-rule','a');
            $("#minnumber").val('');
        }else{
            $("#isminstockin_div").attr('style','display:block');
            $("#minnumber").attr('data-rule','required number');
        }
    })

    $("#isminbalance").change(function (){
        if($('#isminbalance option:selected').val()==0){
            $("#isminbalance_div").attr('style','display:none');
            $("#minbalancenumber").attr('data-rule','a');
            $("#minbalancenumber").val('');
        }else{
            $("#isminbalance_div").attr('style','display:block');
            $("#minbalancenumber").attr('data-rule','required number');
        }
    })

    $("#nocontractgoods_save").click(function (){
        $('#treeform').isValid(function(v){
            if(v){
                var isladder = $("#isladder option:selected").val();
                var ladderstart = $("#ladderstart").val();
                var ladderend = $("#ladderend").val();
                if(isladder==1&&ladderend!=''&& parseFloat(ladderend) <= parseFloat(ladderstart)){
                    BJUI.alertmsg('warn','终止值不能小于起始值');
                    return false;
                }

                var data  = $("#treeform").serializeJson();
                var allData  = $.CurrentNavtab.find("#goods-detail-table").data('allData');

                var qualityname = $("#noquality option:selected").text();
                var companyname = $("#nocompany option:selected").text();
                if(qualityname!='请选择')
                    data.qualityname=qualityname;
                data.companyname=companyname;

                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }
                $.CurrentNavtab.find('#goods-detail-table').datagrid('reload',  {data:allData});

                BJUI.dialog('closeCurrent');
            }else{
                console.log('no');
            }
        })
    })

    $("#nocontractgoods_edit").click(function (){
        $('#treeform').isValid(function(v){
            if(v){
                var isladder = $("#isladder option:selected").val();
                var ladderstart = $("#ladderstart").val();
                var ladderend = $("#ladderend").val();
                if(isladder==1&&ladderend!=''&& parseFloat(ladderend) <= parseFloat(ladderstart)){
                    BJUI.alertmsg('warn','终止值不能小于起始值');
                    return false;
                }

                var data  = $("#treeform").serializeJson();
                var qualityname = $("#noquality option:selected").text();
                var companyname = $("#nocompany option:selected").text();
                if(qualityname!='请选择')
                    data.qualityname=qualityname;
                data.companyname=companyname;

                $.CurrentNavtab.find('#goods-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                var obj = $.CurrentNavtab.find('#goods-detail-table').data('allData');
                obj["{{$gridIndex}}"] = data;
                $.CurrentNavtab.find('#goods-detail-table').datagrid('reload', {data:obj});
                BJUI.dialog('closeCurrent');
            }else{
                console.log('no');
            }
        })
    })
</script>