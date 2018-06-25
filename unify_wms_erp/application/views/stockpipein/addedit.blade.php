<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="stockpipein-receipe-form" class="datagrid-edit-form" data-toggle="validate"
              data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">

            <div class="bjui-row col-2">
                <input type="hidden" name="sysno" value="{{$sysno}}">
                <input type="hidden" name="stock_sysno" value="{{$stock_sysno}}">
                <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                <input type="hidden" name="bookin_detail_sysno" value="{{$bookin_detail_sysno}}">

                <label class="row-label">品名</label>
                <div class="row-input required">
                    <input type="text" name="goodsname" value="{{$goodsname}}" readonly>
                </div>

                <label class="row-label">规格</label>
                <div class="row-input required">
                    <select data-toggle="selectpicker" name="goods_quality_sysno" id="goods_quality_sysno"
                            data-width="100%" @if($status == 4) disabled @endif
                            data-rule="required" data-live-search="true" data-size="10">
                        <option value="" selected="">请选择</option>
                        @foreach($goodsqualitylist as $item)
                            <option value="{{$item['sysno']}}"
                                    @if($item['sysno'] == $goods_quality_sysno) selected @endif>
                                {{$item['qualityname']}}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="qualityname" id="goods_quality_name"
                           value="{{$goods_quality_name}}">
                </div>

                <label class="row-label">货物性质</label>
                <div class="row-input required">
                    <select data-toggle="selectpicker" data-width="100%" data-live-search="true"
                            @if($status == 4) disabled @endif
                            id="goodsnature_sysno" name="goodsnature_sysno">
                        <option value="1" @if($goodsnature==1) selected @endif>保税</option>
                        <option value="2" @if($goodsnature==2) selected @endif>外贸</option>
                        <option value="3" @if($goodsnature==3) selected @endif>内贸转出口</option>
                        <option value="4" @if($goodsnature==4) selected @endif>内贸内销</option>
                    </select>
                    <input type="hidden" name="goodsnature" id="goodsnature_name"
                           value="{{$goodsnature}}">
                </div>


                <label class="row-label">通知数量</label>
                <div class="row-input required">
                    <input type="text" name="tobeqty" readonly  id="tobeqty" value="{{$tobeqty}}" >
                </div>

                <label class="row-label">实际罐检量</label>
                <div class="row-input required">
                    <input type="text" name="beqty" id="beqty" data-rule="required" value="{{$beqty}}" >
                </div>

                <label class="row-label">罐号</label>
                <div class="row-input required">
                    @if(!$type)
                        <select data-toggle="selectpicker" name="storagetankname" id="storagetankname" data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                            <option value="" selected="">请选择</option>
                            @foreach($storageList as $item)
                                <option value="{{$item['storagetankname']}}" sysno="{{$item['sysno']}}" @if($item['storagetankname'] == $storagetankname) selected @endif>{{$item['storagetankname']}}</option>
                            @endforeach
                        </select>
                    @else if
                    <input type="text" name="storagetankname" id="storagetankname" value="{{$storagetankname}}"
                           readonly>
                    @endif
                    <input type="hidden" name="storagetank_sysno" id="storagetank_sysno" value="{{$storagetank_sysno}}"
                           readonly>
                </div>


                <label class="row-label">预计到货日期</label>
                <div class="row-input required">
                    <input type="text" name="shipbookingdate" data-rule="required"  id="shipbookingdate" value="{{$shipbookingdate}}" data-toggle="datepicker" placeholder="预计到货日期" >
                </div>


                <label class="row-label">实际到货日期</label>
                <div class="row-input required">
                    <input type="text" name="shipactualdate" data-rule="required" id="shipactualdate" value="{{$shipactualdate}}" data-toggle="datepicker" placeholder="实际到货日期" >
                </div>

                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight">{{$memo}}</textarea>
                </div>

            </div>


        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li>
            <button type="button" class="btn-green" data-icon="save" onclick="savepipedetail()">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
    </ul>
</div>


<script type="text/javascript">

    $("#storagetankname").change(function () {
        var sysno = $("#storagetankname option:selected").attr('sysno');
        $("#storagetank_sysno").val(sysno);
    });

    $("#goodsnature_sysno").change(function () {
        var name = $("#goodsnature_sysno option:selected").val();
        $("#goodsnature_name").val(name);
    });

    function savepipedetail() {
        $.CurrentNavtab.find('#goods_quality_sysno').removeAttr("disabled");
        $.CurrentNavtab.find('#goodsnature_sysno').removeAttr("disabled");
        var split = '{{$split}}';
        $('#stockpipein-receipe-form').isValid(function (v) {
            $("#goods_quality_name").val($("#goods_quality_sysno option:selected").text());
            if (v) {
                $("#stockpipein-receipe-form").attr('action', '/stockpipein/detailsubmit/');
                var data = $("#stockpipein-receipe-form").serializeJson();
                if (data.bookout_detail_sysno == '')
                    data.bookout_detail_sysno = data.sysno;
                $.CurrentNavtab.find('#stockpipein-detail-table').datagrid('updateRow', "{{$gridIndex}}", data);
                var obj = $.CurrentNavtab.find('#stockpipein-detail-table').data('allData');
                if(split==1){
                    obj.push(data);
                    $('#stockpipein-detail-table').datagrid('reload',  {data:obj});
                }else{
                    obj["{{$gridIndex}}"] = data;
                }
                BJUI.dialog('closeCurrent', 'stockin-pipe-{{$id}}');
//                $("#stockshipin-receipe-form").submit();
            } else {
                console.log('no');
            }

        });

    }


</script>