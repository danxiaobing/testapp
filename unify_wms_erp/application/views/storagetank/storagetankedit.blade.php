<div class="bjui-pageContent">
    <div class="bs-example">
        <form id="storagetankedit" action="{{$action}}" @if(!$clearstoragetank)data-toggle="validate" @endif class="datagrid-edit-form" data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <div class="bjui-row col-2">

                <label class="row-label">储罐编号</label>
                <div class="row-input required">
                    <input type="text" name="storagetankname" value="{{$storagetankname}}" data-rule="required"></div>

                <label class="row-label">储罐材质</label>
                <div class="row-input required">
                    <select name="storagetank_category_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($storagetankcategorylist as $item)
                        <option value="{{$item['sysno']}}" @if($item['sysno'] == $storagetank_category_sysno) selected @endif>{{$item['storagetank_categoryname']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">储罐性质</label>
                <div class="row-input required">
                    <select name="storagetanknature" data-toggle="selectpicker" data-rule="required" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($storagetanknaturelist as $item)
                        <option value="{{$item['id']}}" @if($item['id'] == $storagetanknature) selected @endif>{{$item['name']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">所属片区</label>
                <div class="row-input required">
                    <select name="area_sysno" data-toggle="selectpicker" data-rule="required" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($arealist as $item)
                        <option value="{{$item['sysno']}}" @if($item['sysno'] == $area_sysno) selected @endif>{{$item['areaname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">安全容量（立方米）</label>
                <div class="row-input required">
                    <input type="text" id="theoreticalcapacity" name="theoreticalcapacity" value="{{$theoreticalcapacity}}" data-rule="required;number">
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input required">
                    <input type="hidden" id="goods_sysno" name="goods_sysno" value="{{$goods_sysno}}">
                    <input type="text" id="goodsname" name="goodsname" value="{{$goodsname}}" data-rule="required" data-toggle="findgrid" readonly data-options="{
                            dialogOptions: {width:'1200',height:'600',title:'货品详情',resizable:true,mask:true},
                            gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'96%',
                            local: 'local',
                            paging: {pageSize:20},
                            data: {{$goodslist}} ,
                            columns: [
                                {name:'goods_sysno',hide:'true', label:'货品ID'},
                                {name:'goodsno', label:'货品编号'},
                                {name:'goodsname', label:'货品名称'},
                                {name:'density', label:'货品密度'},
                                {name:'controlprice', label:'控货单价'},
                                {name:'controlproportion', label:'控货比重‰'},
                                {name:'rate_waste', label:'内控损耗‰'},
                                {name:'unitname', label:'计量单位'},
                            ],
                            fullGrid:true,
                            showLinenumber:true
                        },
                    }" placeholder="点放大镜按钮查找">
                </div>

                <label class="row-label">密度（吨/立方米）</label>
                <div class="row-input required">
                    <input type="text" id="density" name="density" value="{{$density}}" data-rule="required" readonly>
                </div>

                <label class="row-label">可存放吨数</label>
                <div class="row-input required">
                    <input type="text" id="actualcapacity" name="actualcapacity" value="{{$actualcapacity}}" data-rule="required;range(0~);not0">
                </div>

                <label class="row-label">储蓄高度（米）</label>
                <div class="row-input required">
                    <input type="text" id="height" name="height" value="{{$height}}" data-rule="required;range(0~);not0">
                </div>

                <label class="row-label">储蓄直径（米）</label>
                <div class="row-input required">
                    <input type="text" id="diameter" name="diameter" value="{{$diameter}}" data-rule="required;range(0~);not0">
                </div>

                <label class="row-label">状态：</label>
                <div class="row-input required">
                    <input type="radio" name="status" data-toggle="icheck" value="1" data-rule="checked"
                       data-label="启用&nbsp;&nbsp;" @if( !$status || $status ==1) checked @endif>
                    <input type="radio" name="status" data-toggle="icheck" value="2" data-label="停用"
                       @if($status ==2) checked @endif>
                </div>

                @if($clearstoragetank)
                <label class="row-label">清罐时间</label>
                <div class="row-input required">
                    <input type="text" id="cleartankdate" name="cleartankdate" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" data-rule="required;date">
                </div>
                @endif
            </div>

        </form>
    </div>
     <div class="remarks">
            <fieldset>
                <legend>清罐记录</legend>
                <div class="table-edit">
                    <table class="table table-bordered" id="goodslist-table" data-toggle="datagrid" data-options="{
                        filterThead:false,
                        height: '100%',
                        tableWidth:'100%',
                        local: 'local',
                        data: {{$storagetankgoods}},
                        paging:false,
                        }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">品名</th>
                            <th data-options="{name:'cleartankdate',align:'center'}">清罐时间</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
</div>

<div class="bjui-pageFooter">
    <ul>
     <li>
            @if($clearstoragetank)
                <button id="storagetank_clear" type="submit" class="btn-green" data-icon="save">清罐</button>
            @else
                <button type="submit" class="btn-green" data-icon="save">保存</button>
            @endif
        </li>
        <li>
            <button type="button" class="btn-close" data-icon="close">取消</button>
        </li>
       
    </ul>
</div>
<script type="text/javascript">
    $("#goodsname").blur(function(){
        var cap = parseFloat($("#theoreticalcapacity").val()?$("#theoreticalcapacity").val():0);
        var den = parseFloat($("#density").val());

        var act = (cap*den).toFixed(3);
        $("#actualcapacity").val(act);
    });

    $("#theoreticalcapacity").keyup(function(){
        var cap = parseFloat($("#theoreticalcapacity").val()?$("#theoreticalcapacity").val():0);
        var den = parseFloat($("#density").val()?$("#density").val():0);

        var act = (cap*den).toFixed(3);
        $("#actualcapacity").val(act);
    })

    $("#theoreticalcapacity").blur(function(){
        var cap = parseFloat($("#theoreticalcapacity").val()?$("#theoreticalcapacity").val():0);
        var den = parseFloat($("#density").val()?$("#density").val():0);

        var act = (cap*den).toFixed(3);
        $("#actualcapacity").val(act);
    })

    $("#storagetank_clear").click(function(){
        BJUI.ajax('ajaxform', {
            url: '{{$action}}',
            form: $.CurrentDialog.find('#storagetankedit'),
            validate: true,
            loadingmask: true,
            okCallback: function(json, options) {
                BJUI.dialog('close','storagetank_clear');
                BJUI.navtab('refresh', 'navab338');
            },
        });

    })
</script>