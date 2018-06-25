<script src="/static/common/js/custom.js"></script>
<div class="bjui-pageContent">
    <div class="bs-example">
        <form action="{{$action}}" id="stockout-receipe-form"  class="datagrid-edit-form" data-toggle="validate" data-data-type="json " data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-1">
                <label class="row-label">库存</label>
                <div class="row-input ">
                    <input type="hidden" name="sysno" value="{{$sysno}}">
                    <input type="hidden" name="stock_sysno" value="{{$stock_sysno}}">
                    <input type="hidden" name="goods_sysno" value="{{$goods_sysno}}">
                    <input type="hidden" name="goodsname" value="{{$goodsname}}">
                    <input type="hidden" name="goodsnature" value="{{$goodsnature}}">
                    <input type="hidden" name="goods_quality_sysno" value="{{$goods_quality_sysno}}">
                    <input type="hidden" name="goodsqualityname" value="{{$goodsqualityname}}">
                    <input type="hidden" name="storagetank_sysno" value="{{$storagetank_sysno}}">


                    <input type="text" name="stockno" value="{{$stockno}}" readonly data-rule="required" data-toggle="findgridbtn" data-options="{

                        dialogOptions: {width:'1000',height:'500',title:'库存详情',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'96%',
                            local: 'local',
                            paging: {pageSize:20},
                            data: {{$stocklist}} ,
                            columns: [
                                {name:'sysno', label:'id',width:40},
                                {name:'stockinno', label:'入库单号'},
                                {name:'stockno', label:'库存编号'},
                                {name:'stockindate', label:'入库日期',width:120},
                                {name:'goodsname', label:'品名',width:100},
                                {name:'qualityname', label:'质量标准',width:100},
                                {name:'stockqty', label:'入库数量'}
                            ],
                            fullGrid:true,
                            showLinenumber:false
                        },
                    }" placeholder="点放大镜按钮查找">
                </div>

                <label class="row-label">日期</label>
                <div class="row-input required">
                    <input type="text" name="outdate" data-toggle="datepicker" readonly value="" >
                </div>

                <label class="row-label">通知数量</label>
                <div class="row-input required">
                    <input type="text" name="tobeqty" value="" data-rule="required number range[0~]">
                </div>
                <label class="row-label">实提数量</label>
                <div class="row-input required">
                    <input type="text" name="beqty" value="" data-rule="required number range[0~]">
                </div>
                <label class="row-label">规格</label>
                <div class="row-input required">
                    <input type="text" name="qualityname" value="">
                </div>
                <label class="row-label">计量单位</label>
                <div class="row-input required">
                    <input type="text" name="unitname" value="">
                </div>
                <label class="row-label">提货公司</label>
                <div class="row-input required">
                    <input type="text" name="expresscompanyname" value="" data-rule="required" >
                </div>

                @if($stockouttype == 1)
                    <label class="row-label">船名</label>
                    <div class="row-input ">
                        <input type="text" name="shipname" value=""  >
                    </div>
                @elseif($stockouttype == 2)
                    <label class="row-label">车牌号</label>
                    <div class="row-input required">
                        <input type="text" name="carid" value=""  >
                    </div>
                    <label class="row-label">重车时间</label>
                    <div class="row-input ">
                        <input type="text" name="fullcartime" value=""  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" readonly  >
                    </div>
                    <label class="row-label">重车重量</label>
                    <div class="row-input required">
                        <input type="text" name="fullcarqty" value="" data-rule="required number range[0~]" >
                    </div>
                    <label class="row-label">空车时间</label>
                    <div class="row-input ">
                        <input type="text" name="emptycartime" value=""  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" readonly >
                    </div>
                    <label class="row-label">空车重量</label>
                    <div class="row-input required">
                        <input type="text" name="emptycarqty" value="" data-rule="required number range[0~]" >
                    </div>
                @endif

                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea  name="memo"  data-toggle="autoheight"></textarea>
                </div>

            </div>
           

            
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="button" class="btn-green" data-icon="save" onclick="subOutReceipe()">保存</button></li>
    </ul>
</div>


<script type="text/javascript">
    function subOutReceipe() {

        $('#stockout-receipe-form').isValid(function(v){
            if (v) {
                var data  =  $("#stockout-receipe-form").serializeJson();
                data.stock_sysno = data.sysno;

                var allData  =  $.CurrentNavtab.find("#stockout-receipt-table").data('allData');
            //    console.log(allData);
                if(typeof  allData != 'undefined'){
                    allData.push(data);
                }else{
                    allData = [data] ;
                }

                $.CurrentNavtab.find('#stockout-receipt-table').datagrid('reload',  {data:allData});
                BJUI.dialog('closeCurrent','stockout-receipt-{{$id}}');
            }else{
                console.log('no');
            }

        });


    }


</script>