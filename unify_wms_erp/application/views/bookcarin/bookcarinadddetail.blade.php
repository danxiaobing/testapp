<div class="bjui-pageContent">
    <form id="bookcarin-detail-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
    <div class="bjui-row col-2">
        <label class="row-label">品名</label>
        <div class="row-input required">
            <input type="hidden" name="sysno" id="obj_goodsname" value="{{$goods_sysno}}">
            <input type="text" name="goodsname" value="{{$goodsname}}" id="g_goodsname" readonly data-rule="required" data-toggle="findgrid" data-options="{
            dialogOptions: {width:'800',height:'500',title:'货品资料',maxable:true,resizable:true,mask:true},
            gridOptions: {
                tableWidth:'90%',                       
                local: 'local',
                paging: {pageSize:20},
                postData: {customer_sysno:{{$customer_sysno}},contract_sysno:{{$contract_sysno}}},
                dataUrl: '/customer/customergoodslistJson',
                columns: [
                    {name:'sysno', label:'id'},
                    {name:'goodsno', label:'货品编号'},
                    {name:'goodsname', label:'货品名称'}
                ],
                showLinenumber:false,
                fullGrid:true
            },
        }" placeholder="点放大镜按钮查找"></div>

        <label class="row-label">计量单位</label>
        <div class="row-input required">
            <input type="text" name="unitname" value="吨" readonly>
        </div>

        <label class="row-label">规格</label>
        <div class="row-input required">
            <select data-toggle="selectpicker" name="goods_quality_sysno" id="goods_quality_sysno" @if($mode=='sure') disabled @endif data-width="100%" data-rule="required" data-live-search="true" data-size="10">
                <option value="" selected="">请选择</option>
                @foreach($goodsqualitylist as $item)
                    <option value="{{$item['sysno']}}" @if($item['sysno'] == $goods_quality_sysno) selected @endif>{{$item['qualityname']}}</option>
                @endforeach
            </select>
            <input type="hidden" name="qualityname" id="goods_quality_name" value="{{$qualityname}}">
        </div>

        <label class="row-label">货物性质</label>
        <div class="row-input required">
            <select data-toggle="selectpicker" name="goodsnature" id="goodsnature" data-width="100%" @if($mode=='sure') disabled @endif data-rule="required">
                <option value="" selected="">请选择</option>
                <option value="1" @if($goodsnature==1) selected @endif>保税</option>
                <option value="2" @if($goodsnature==2) selected @endif>外贸</option>
                <option value="3" @if($goodsnature==3) selected @endif>内贸转出口</option>
                <option value="4" @if($goodsnature==4) selected @endif>内贸内销</option>
            </select>
        </div>

        <label class="row-label">数量</label>
        <div class="row-input required">
            <input type="text" name="bookinginqty" value="{{$bookinginqty}}" @if($mode=='sure') readonly @endif data-rule="required;number;range[0~]">
        </div>

        <label class="row-label">预计到货日期</label>
        <div class="row-input required">
            <input type="text" name="bookingindate" value="{{$bookingindate}}" @if($mode=='sure') readonly @endif >
        </div>

        <label class="row-label">进货罐号</label>
        <div class="row-input @if($mode=='sure') required @endif ">
            <select data-toggle="selectpicker" name="storagetank_sysno" id="storagetank_sysno" data-width="100%" @if($mode=='sure') data-rule="required" @endif  data-live-search="true" data-size="10">
                <option value="">请选择</option>
                @foreach($storagetanklist as $item)
                    <option value="{{$item['sysno']}}" @if($item['sysno'] == $storagetank_sysno) selected @endif>{{$item['storagetankname']}}</option>
                @endforeach
            </select>
            <input type="hidden" name="storagetankname" id="storagetankname" value="{{$storagetankname}}">
        </div>

        <label class="row-label">备注:</label>
        <div class="row-input">
            <textarea name="memo" data-toggle="autoheight" @if($mode=='sure') readonly @endif  cols="auto" rows="3">{{$memo}}</textarea>
        </div>

    </div>

</form>
    </div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="saveBocarDetail()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
    </ul>
</div>

<script type="text/javascript">
// JS API 调用日期选择器
$('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    function saveBocarDetail() {
        $.CurrentDialog.find("#goods_quality_sysno").removeAttr("disabled");
        $.CurrentDialog.find("#goodsnature").removeAttr("disabled");

        var handlestatus = $.CurrentDialog.find("#handlestatus").html();
        var qualityname = $.CurrentDialog.find("#goods_quality_sysno option:selected").text();
        var storagetankname = $.CurrentDialog.find("#storagetank_sysno option:selected").text();
        $('#bookcarin-detail-form').isValid(function(v){
            if (v) {
                var data  = $.CurrentDialog.find("#bookcarin-detail-form").serializeJson();
                var allData  = $.CurrentDialog.find("#bookcarin-detail-table").data('allData');
                data.goods_sysno = data.sysno;
                data.qualityname = qualityname;
                if(storagetankname!='请选择'){
                    data.storagetankname = storagetankname;
                }else {
                    data.storagetankname = '';
                }

                if (handlestatus == 'add') {

                    if(typeof  allData != 'undefined'){
                        allData.push(data);
                    }else{
                        allData = [data] ;
                    }

                    $('#bookcarin-detail-table').datagrid('reload',  {data:allData});
                    BJUI.dialog('closeCurrent');
                }else if (handlestatus == 'edit') {
                    $.CurrentNavtab.find('#bookcarin-detail-table').datagrid('updateRow', "{{$gridIndex}}" , data);
                    var obj = $.CurrentNavtab.find('#bookcarin-detail-table').data('allData');
                    obj["{{$gridIndex}}"] = data;
                    $('#bookcarin-detail-table').datagrid('reload',  {data:obj});
                    BJUI.dialog('closeCurrent','');
                }
            }else{
                console.log('no');
            }
        });
    }
</script>