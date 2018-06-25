<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="stockcarin-receipe-form"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-1">

                <input type="hidden" name="sysno" value="{{$sysno}}">
                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                <input type="hidden" name="storagetank_sysno" value="{{$storagetank_sysno}}">

                <label class="row-label">品名</label>
                <div class="row-input required">
                    <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                </div>

                <label class="row-label">规格</label>
                <div class="row-input required">
                    <select data-toggle="selectpicker" name="goods_quality_sysno" id="goods_quality_sysno" data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                        <option value="" selected="">请选择</option>
                        @foreach($goodsqualitylist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $goods_quality_sysno) selected @endif>{{$item['qualityname']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="goods_quality_name" id="goods_quality_name" value="{{$goods_quality_name}}">
                </div>

                <label class="row-label">货物性质</label>
                <div class="row-input required">
                    <input type="hidden" name="goodsnature" value="{{$goodsnature}}">
                    <input type="text" name="goodsnaturename" value="@if($goodsnature==1){{保税}}@elseif($goodsnature==2){{外贸}}@elseif($goodsnature==3){{内贸转出口}}@elseif($goodsnature==4){{内贸内销}}@endif" readonly>
                </div>

                <label class="row-label">进罐编号</label>
                <div class="row-input required">
                    <input type="text" name="storagetankname" value="{{$storagetankname}}" readonly>
                </div>

                <label class="row-label">计量单位</label>
                <div class="row-input required">
                    <input type="text" name="unitname" value="@if($unitname){{$unitname}}@else{{吨}}@endif" readonly>
                </div>

                <label class="row-label">通知数量</label>
                <div class="row-input required">
                    <input type="text" name="tobeqty" value="{{$tobeqty}}" readonly>
                </div>

                <label class="row-label">预计日期</label>
                <div class="row-input required">
                    <input type="text" name="goodsreceiptdate" value="{{$goodsreceiptdate}}" readonly>
                </div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea  name="memo"  data-toggle="autoheight">{{$memo}}</textarea>
                </div>

            </div>
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveReceipe()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
    </ul>
</div>

<script type="text/javascript">
    function saveReceipe() {
        $('#stockcarin-receipe-form').isValid(function(v){
            if (v) {
                var data  =  $("#stockcarin-receipe-form").serializeJson();
                var qualityname = $("#goods_quality_sysno option:selected").text();
                data.goods_quality_name = qualityname;

                $.CurrentNavtab.find('#stockcarin-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                var obj = $.CurrentNavtab.find('#stockcarin-detail-table').data('allData');
                obj["{{$gridIndex}}"] = data;
                BJUI.dialog('closeCurrent');
            }else{
                console.log('no');
            }
        });
    }
</script>