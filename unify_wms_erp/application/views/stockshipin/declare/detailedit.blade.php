<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="stockshipin-declare-form" class="datagrid-edit-form" data-toggle="validate"
              data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="stockin_sysno" value="{{$stockin_sysno}}">

            <div class="bjui-row col-1">
                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">

                <label class="row-label">品名</label>

                <div class="row-input required">
                    <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                </div>


                <label class="row-label">代理报关公司</label>

                <div class="row-input">
                <input type="text" name="customername" id="declare_customername" value="{{$customername}}"
                           data-rule=""   >
                    <input type="hidden" name="customer_sysno" id="declare_customer_sysno" value="{{$customer_sysno}}"
                           readonly>
                </div>

                <label class="row-label">提单量</label>

                <div class="row-input required">
                    <input type="text" name="takegoodsnum"  value="{{$takegoodsnum}}" readonly >
                </div>

                <label class="row-label">商检数量</label>

                <div class="row-input required">
                    <input type="text" name="beqty" id="declare_bussinesscheckqty" value="{{$beqty}}"
                           data-rule="required number range[0~]"  readonly >
                </div>

                <label class="row-label">报关单号</label>

                <div class="row-input required">
                    <input type="text" name="declaration" value="{{$declaration}}" data-rule="required">
                </div>

                <label class="row-label">进罐编号</label>

                <div class="row-input required">
                    <select data-toggle="selectpicker" name="storagetankname" id="declare_storagetankname" data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                        <option value="" selected="">请选择</option>
                        @foreach($storageList as $item)
                            <option value="{{$item['storagetankname']}}" sysno="{{$item['sysno']}}" @if($item['storagetankname'] == $storagetankname) selected @endif>{{$item['storagetankname']}}</option>
                        @endforeach
                    </select>

                    <input type="hidden" name="storagetank_sysno" id="declare_storagetank_sysno" value="{{$storagetank_sysno}}"
                           readonly>
                </div>


                <label class="row-label">放行报关数量</label>

                <div class="row-input required">
                    <input type="text" name="release_num" id="declare_release_num" value="{{$release_num}}" data-rule="required;number;range[0~]">
                </div>

                <label class="row-label">未报关数量</label>

                <div class="row-input">
                    <input type="text" name="unrelease_num"  readonly value="{{$unrelease_num}}"  >
                </div>


            </div>


        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            <button type="button" class="btn-green" data-icon="save" onclick="saveReceipe()">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
    </ul>
</div>


<script type="text/javascript">

    $.CurrentDialog.find("#declare_storagetankname").change(function () {
        var sysno = $.CurrentDialog.find("#declare_storagetankname option:selected").attr('sysno');
        $.CurrentDialog.find("#declare_storagetank_sysno").val(sysno);
    });


    function saveReceipe() {

        $.CurrentDialog.find('#stockshipin-declare-form').isValid(function (v) {

            if (v) {
                var data = $.CurrentDialog.find("#stockshipin-declare-form").serializeJson();
                var obj = $.CurrentNavtab.find('#stockshipindeclare-detail-table').data('allData');
                var num = 0;//总报关量
                var takegoodsnum = parseFloat($.CurrentDialog.find(":input[name='takegoodsnum']").val()); //当前的提单数量
                if("{{$gridIndex}}")
                {
                    obj["{{$gridIndex}}"].release_num = parseFloat('0.000');
                }
                $(obj).each(function(i){
                    if(obj[i].release_num!= null){
                        num +=parseFloat(obj[i].release_num);
                    }
                });

                    data.unrelease_num = ( parseInt(takegoodsnum*1000)-(parseInt(num*1000)+parseInt(data.release_num*1000)))/1000 ; //计算出未报关数量
                    if("{{$gridIndex}}"){
                        obj["{{$gridIndex}}"] = data;
                        $.CurrentNavtab.find('#stockshipindeclare-detail-table').datagrid('updateRow', "{{$gridIndex}}", data); //更新当前编辑行
                    }else{
                        obj.push(data);
                    $.CurrentNavtab.find('#stockshipindeclare-detail-table').datagrid('reload',  {data:obj});
                    }
                BJUI.dialog('closeCurrent', '');

            } else {
                console.log('no');
            }

        });


    }


</script>