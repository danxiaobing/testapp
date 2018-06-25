<div class="bjui-pageContent">
    <form id="supplement-adddetail-form" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <div class="bjui-row col-2">
                <input type="hidden" name="goods_sysno" value="{{$list['goods_sysno']}}">
                <input type="hidden" name="stock_sysno" value="{{$list['stock_sysno']}}">
                <input type="hidden" name="goodsname" value="{{$list['goodsname']}}">
                <input type="hidden" name="shipname" value="{{$list['shipname']}}">

            <label class="row-label">入库罐号</label>
            <div class="row-input">
                <input type="text" id="storagetankname" name="storagetankname" onclick="findgrid_getstanklist(this);" value="{{$list['storagetankname']}}"  data-rule="required"placeholder="点击选择储罐" readonly>
                <input type="hidden" id="storagetank_sysno" name="storagetank_sysno" value="{{$list['storagetank_sysno']}}"  readonly>
            </div>


            <label class="row-label">规格</label>
            <div class="row-input required">
                <input type="text" name="qualityname" id="qualityname" value="{{$list['qualityname']}}" readonly>
                <input type="hidden" id="goods_quality_sysno" name="goods_quality_sysno" value="{{$list['goods_quality_sysno']}}"  readonly>

            </div>

            <label class="row-label">货物性质:</label>
            <div class="row-input required">
                <input type="text" name="goodsnature" id="goodsnature"
                       value="@if($list['goodsnature']==1) 保税
                               @elseif($list['goodsnature']==2) 外贸
                               @elseif($list['goodsnature']==3) 内贸转出口
                               @elseif($list['goodsnature']==4) 内贸内销
                               @endif " readonly>
            </div>


            <label class="row-label">计量单位</label>
            <div class="row-input required">
                <input type="text" name="unitname" id="unitname" value="吨" readonly>
            </div>


            <label class="row-label">商检数量</label>
            <div class="row-input required">
                <input type="text" name="bussinesscheckqty" id="bussinesscheckqty" value="{{$list['bussinesscheckqty']}}" readonly>
            </div>

            <label class="row-label">补充方式</label>
            <div class="row-input">
                <select name="supplementtype" id="supplementtype" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%">
                    <option value="1">补入库存</option>
                    <option value="2">扣减库存</option>
                </select>
            </div>
            <label class="row-label">补入数量</label>
            <div class="row-input required">
                <input type="text"id="beqty" name="beqty" value="{{$beqty}}" @if($mode=='audit') readonly @endif data-rule="required;number;range[0~]">
            </div>

            <label class="row-label">备注:</label>
            <div class="row-input">
                <textarea name="memo" data-toggle="autoheight" @if($mode=='audit') readonly @endif  cols="auto" rows="3">{{$memo}}</textarea>
            </div>

        </div>

    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-green" data-icon="save" onclick="savesupplement()">保存</button></li>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li id="type" style="display: none">{{$type}}</li>
    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
    /*
     * 绑定储罐单
     * */
    function findgrid_getstanklist(obj) {

        BJUI.findgrid({
            include: 'sysno:sysno,stockinno:stockinno,goods_sysno:goods_sysno,goodsname:goodsname,shipname:shipname',
            dialogOptions: {width:'1000',height:'500',title:'入库详细信息',maxable:true,resizable:true,mask:true},
            gridOptions:{
                width:'80%',
                tableWidth:'97%',
                local: 'local',
                paging: {pageSize:10},
                dataUrl: '/supplement/gettankJson/storagetank_sysno/'+{{$list['storagetank_sysno']}},
                columns: [
                    {name:'sysno', label:'id',align:'center'},
                    {name:'storagetankname', label:'储罐编号',align:'center'},
                    {name:'storagetanknature', label:'储罐性质',align:'center',
                        render:function(value){ if(value==1){return '内贸罐'} else if(value==2){return '外贸罐'} else{return '保税罐' }}},
                    {name:'actualcapacity', label:'可存放吨数',align:'center'},
                    {name:'tank_stockqty', label:'当前存放量',align:'center'},
                    {name:'orderinqty', label:'待入量',align:'center'},
                    {name:'orderoutqty', label:'待出量',align:'center'},
                ],
                showLinenumber:false,
            },
                afterSelect:function(data) {
                    // reloaddetail(data);
                }
        })
    }
    function savesupplement(){
        var type = $("#type").html();
        $('#supplement-adddetail-form').isValid(function(v){
            if(v){
                var data  = $("#supplement-adddetail-form").serializeJson();
                console.log(data);
                var allData  = $("#supplement-detail-table").data('allData');
                console.log(allData);
                    if (type == 'add') {

                        if(typeof  allData == 'undefined' || allData.length==0){

                            allData = [data] ;

                        }else{
                            //  allData.push(data);
                            BJUI.alertmsg('warn','<h4>只能添加一条明细!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                            return;
                        }
                        $('#supplement-detail-table').datagrid('reload',  {data:allData});
                        BJUI.dialog('closeCurrent','');
                    }else if (type == 'edit') {
                        $.CurrentNavtab.find('#supplement-detail-table').datagrid('updateRow', "{{$list['gridIndex']}}" , data);
                        var obj = $.CurrentNavtab.find('#supplement-detail-table').data('allData');
                        obj["{{$list['gridIndex']}}"] = data;
                        $('#supplement-detail-table').datagrid('reload',  {data:obj});
                        BJUI.dialog('closeCurrent','');
                    }
            }
        })
    }

</script>