
<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="pipelineorder-dialog-form" action="" class="datagrid-edit-form"  data-data-type="json" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="type" id="type" value="{{$type}}">
            <div class="bjui-row col-2">

                <label class="row-label">码头管线号</label>
                <div class="row-input @if(!in_array($businesstype,[5,6,11,12,17,18])) required @endif">
                    <select name="wharf_pipeline_sysno" id="wharf_pipeline_sysno" data-nextselect=""data-refurl="" data-toggle="selectpicker" @if(!in_array($businesstype,[5,6,11,12,17,18])) data-rule="required"  @endif data-width="100%" data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($wharf_pipeline as $value)
                            <option value="{{$value['sysno']}}" @if($value['sysno'] == $list['wharf_pipeline_sysno']) selected @endif>{{$value['pipelinename']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="wharf_pipelineno" id="wharf_pipelineno" value="{{$list['wharf_pipelineno']}}">


               </div>

                <label class="row-label">库区管线号</label>
                <div class="row-input @if(!in_array($businesstype,[13,14,15,16])) required @endif">
                    <select name="area_pipeline_sysno" id="area_pipeline_sysno" data-nextselect=""data-refurl="" data-toggle="selectpicker" @if(!in_array($businesstype,[13,14,15,16])) data-rule="required" @endif data-width="100%" data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($area_pipeline as $value)
                            <option value="{{$value['sysno']}}" @if($value['sysno'] == $list['area_pipeline_sysno']) selected @endif>{{$value['pipelinename']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="area_pipelineno" id="area_pipelineno" value="{{$list['area_pipelineno']}}">
                </div>

                <label class="row-label">品种：</label>
                <div class="row-input required">
                    <select name="goods_sysno" id="goods_sysno" data-nextselect="" data-refurl="" data-toggle="selectpicker" data-rule="required" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($goodsList as $value)
                            <option value="{{$value['goods_sysno']}}" @if($value['goods_sysno'] == $list['goods_sysno']) selected @endif>{{$value['goodsname']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="goodsname" name="goodsname" value="{{$goodsname}}">
                </div>

                <label class="row-label">预计吨数：</label>
                <div class="row-input required">
                    <input type="text" name="estimateqty" value="{{$list['estimateqty']}}" data-rule="required;range[0~]">
                </div>

                <label class="row-label">预计时间：</label>
                <div class="row-input required">
                    <input type="text" size="30" name="estimatedate" value="{{$list['estimatedate']}}" data-rule="required; " placeholder="预计时间" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm"  >
                </div>

                <label class="row-label">罐号</label>
                <div class="row-input   ">
                    <input type="hidden" name="tank.sysno" value="{{$list['storagetank_sysno']}}">
                    <input type="hidden" name="tank.tank_goods_sysno" value="{{$list['tank_goods_sysno']}}">
                    <input type="text" name="tank.storagetankname" value="{{$list['storagetankname']}}" readonly data-rule="" data-toggle="findgrid" data-options="{
                        group: 'tank',
                        include: 'sysno:sysno,storagetankname:storagetankname,tank_goods_sysno:goods_sysno',
                        dialogOptions: {width:'800',height:'600',title:'储罐资料',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                        width:'100%',
                        height:'100%',
                        tableWidth:'99.8%',
                        local: 'local',
                        paging: {pageSize:10},
                        dataUrl: '/Pipelineorder/getStocklistJson',
                        columns: [
                            {name:'sysno', label:'id'},
                            {name:'storagetankname', label:'储罐编号'},
                            {name:'goodsname', label:'品名'},
                            {name:'qualityname', label:'规格'},
                            {name:'unitname', label:'计量单位', render:function(value){return '吨'}},
                            {name:'tank_stockqty', label:'现存量'},
                            {name:'orderinqty', label:'待入量'},
                            {name:'orderoutqty', label:'待出量'}
                        ],
                        showLinenumber:false
                    },
                }"   placeholder="点放大镜按钮查找">
                </div>

{{--                <label class="row-label">船名</label>
                <div class="row-input required">

                    <input type="text" name="shipname" value="{{$list['shipname']}}" readonly data-rule="required" data-toggle="findgrid" data-options="{
                dialogOptions: {width:'800',height:'500',title:'船舶资料',maxable:true,resizable:true,mask:true},
                gridOptions: {
                    width:'100%',
                    height:'100%',
                    tableWidth:'99.8%',
                    local: 'local',
                    paging: {pageSize:20},
                    dataUrl: '/Pipelineorder/ShipJson/',
                    columns: [
                        {name:'sysno', label:'id'},
                        {name:'shipno', label:'船舶编号'},
                        {name:'shipname', label:'船名'},
                        {name:'company', label:'所属公司'},
                        {name:'captain', label:'船长'},
                        {name:'shipcontact', label:'联系方式'},
                    ],
                    showLinenumber:false
                },
            }" placeholder="点放大镜按钮查找"></div>

                <label class="row-label">船长</label>
                <div class="row-input">
                    <input type="text" name="captain" value="{{$list['captain']}}">
                </div>--}}

                <label class="row-label">前期储罐余量</label>
                <div class="row-input">
                    <input type="text" name="beforeqty" value="{{$list['beforeqty']}}" data-rule="range[0~]">
                </div>



                <label class="row-label">后期储罐余量：</label>
                <div class="row-input">
                    <input type="text" name="afterqty" value="{{$list['afterqty']}}" data-rule="range[0~]">
                </div>
                <label class="row-label">实际流量</label>
                <div class="row-input">
                    <input type="text" name="beqty" value="{{$list['beqty']}}">
                </div>
                <label class="row-label">启泵时间</label>
                <div class="row-input">
                    <input type="text" size="30" name="startpumptime" value="{{$list['startpumptime']}}" placeholder="启泵时间"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm" >
                </div>
                <label class="row-label">停泵时间</label>
                <div class="row-input">
                    <input type="text" size="30" name="stoppumptime" value="{{$list['stoppumptime']}}" placeholder="停泵时间"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm"  >
                </div>
                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight" cols="auto" rows="3">{{$list['memo']}}</textarea></div>
            </div>
        </form>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>

        <li>
            <button type="button" class="btn-green" data-icon="save" id="savepipelineorder">保存</button>
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>

    </ul>
</div>

<script type="text/javascript">
    // JS API 调用日期选择器
    $('#j_form_datepicker').datepicker({pattern:'yyyy-MM-dd HH:mm:ss', minDate:'2016-10-01'})
</script>

<script type="text/javascript">
 $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
//保存
    $('#savepipelineorder').click(function(){
        var type = $("#type").val();
        $('#pipelineorder-dialog-form').isValid(function(v){
            if(v){
                var goodsname = $('#goods_sysno option:selected').text();
                $('#goodsname').val(goodsname);

                var goods_sysno = $('#goods_sysno option:selected').val();
                //管线号
                var wharf_pipeline_sysno = $('#wharf_pipeline_sysno option:selected').text();
                $('#wharf_pipelineno').val(wharf_pipeline_sysno);
                //库区号
                var area_pipeline_sysno = $('#area_pipeline_sysno option:selected').text();
                $('#area_pipelineno').val(area_pipeline_sysno);

                if($('#area_pipeline_sysno option:selected').val()==''){
                    $('#area_pipelineno').val('');
                }

                if($('#wharf_pipeline_sysno option:selected').val()==''){
                    $('#wharf_pipelineno').val('');
                }

                var data  = $("#pipelineorder-dialog-form").serializeJson();
                var allData  = $("#pipelineorder-detail-table").data('allData');
                 data.storagetankname = data['tank.storagetankname'];
                 data.storagetank_sysno = data['tank.sysno'];
               //  data.goods_sysno = data['tank.goods_sysno'];
                console.log(data);
                var tank_goods_sysno = data['tank.tank_goods_sysno'];//储罐存放的货品
                if((tank_goods_sysno == goods_sysno) || tank_goods_sysno=='' || !tank_goods_sysno){

                    if (type == 'add') {

                        if(typeof  allData != 'undefined'){
                            allData.push(data);
                        }else{
                            allData = [data] ;
                        }
                        console.log(allData);
                        console.log(data);

                        $('#pipelineorder-detail-table').datagrid('reload',  {data:allData});
                        BJUI.dialog('closeCurrent','');
                    }else if (type == 'edit') {
                        $.CurrentNavtab.find('#pipelineorder-detail-table').datagrid('updateRow', "{{$list['gridIndex']}}" , data);
                        var obj = $.CurrentNavtab.find('#pipelineorder-detail-table').data('allData');
                        obj["{{$list['gridIndex']}}"] = data;
                        $('#pipelineorder-detail-table').datagrid('reload',  {data:obj});
                        BJUI.dialog('closeCurrent','');
                    }

                }else {
                    BJUI.alertmsg('warn','<h4>品种与储罐货品不一致!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }

            }
        })





    })



</script>