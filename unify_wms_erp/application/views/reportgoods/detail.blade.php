<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="reportgoodsdetail-excel"  data-options="{searchDatagrid:$.CurrentNavtab.find('#reportgoodsdeail-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">时间范围:</label>
                <div class="row-input datawidth">
                    <input type="text" name="Begin_time" id="Begin_time" value="@if($begin_time){{$begin_time}} @endif" data-toggle="datepicker" placeholder="开始时间" data-rule="required"></div>
                <div class="row-input datawidth">
                    <input type="text" name="End_time" id="End_time" value="{{$end_time or '' }}" data-toggle="datepicker" placeholder="结束时间" data-rule="required"></div>

                <label class="row-label">产品名称:</label>
                <div class="row-input">
                    <select name="goods_sysno" id="reportgoods_sysno" data-toggle="selectpicker" data-width="100%"  class="show-tick" data-rule="required" data-live-search="true" data-size='10'>
                        <option value="">请选择货品</option>
                        @foreach($goods as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno']==$id) selected="selected" @endif> {{$item['goodsname']}}</option>
                        @endforeach
                    </select></div>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" id="search_qty"  class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div> <br>

                <label class="row-label">期初数量:</label>
                <div class="row-input">
                    <input type="text" id="report_ghoststockqty" value="{{$ghoststockqty or ''}}" readonly="">
                </div>

                <label class="row-label">期末数量:</label>
                <div class="row-input">
                    <input type="text" id="report_laststockqty" value="{{$lastqty or ''}}" readonly="">
                </div>

            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reportgoodsdeail-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'export',
        dataUrl: '/Reportgoods/detailJson/id/{{$id}}/begin_time/{{$begin_time}}/end_time/{{$end_time}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        exportOption: {type:'file', options:{url:'/Reportgoods/Exceldetail',form:$('#reportgoodsdetail-excel')}},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        editMode:false,
        showTfoot:true,
        showNoDataTip:true,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'date',align:'center'}">单据日期</th>
            <th data-options="{name:'sno',align:'center'}">单据编号</th>
            <th data-options="{name:'type',align:'center',render:function(value){if(value=='1'){return '入库单';}else if(value=='2'){return '出库单';} else if(value==3){ return '货权转移单'; }  }}">单据类型</th>
            <th data-options="{name:'shipname',align:'center'}">槽车/船名</th>
            <th data-options="{name:'bussinesscheckqty',align:'center',calc:'sum'}">商检量</th>
            <th data-options="{name:'beqty',align:'center',calc:'sum'}">出货量</th>
            <th data-options="{name:'num',align:'center',calc:'sum'}">货权转出量</th>
            <th data-options="{name:'stockqty',align:'center',calc:'sum'}">结存量</th>
            <th data-options="{name:'sysno',align:'center',render:goodsdetail_operation}">操作</th>
        </tr>
        </thead>
    </table>
</div>
<script>
        function goodsdetail_operation(value,data) {
        return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="look_doc1('+data.type+','+data.sysno+','+data.stype+')">查看单据</button>';
    }

    function look_doc1(type,sysno,stype){
            var url = '';
            var id = sysno;
            var title = '查看单据';
            if(type==1){
                if(stype==2){
                    url = '/stockcarin/see/mode/eye/id/'+id;  
                    title = '查看车入库单据';  
                }else if(stype==1){
                    url = '/stockshipin/show/id/'+id;
                    title = '查看船入库单据';
                }
                
            }else if(type==2){
                if(stype==1){
                    url = "/stockout/shipview/id/"+id;
                    title = '查看船出库单据';
                }else if(stype==2){
                    url = "/stockout/view/type/"+stype+"/id/"+id,
                 title = '查看车出库单据';

                }
            }else if(type==3){
                url =  '/clearstock/lookclearstock/id/' + id + '/val/1';
                title = '查看清库单据';
            }

        // console.log(id); return;
        if(id==0 || id==null){
            BJUI.alertmsg('error','数据异常,无法查看');
            return;
        }

        BJUI.navtab({
            id: 'look_stock',
            url: url,
            type: 'post',
            data:{id:id},
            title:title,
        });
    }


$('#search_qty').click(function(){
        var begin_time = $('#Begin_time').val();
        var end_time = $('#End_time').val();
        var id = $('#reportgoods_sysno option:selected').val();
        // console.log(id); return;
        if(!id){
            return;
        } 
        $.ajax({
            url:'/Reportgoods/ajaxgetqty/',
            type:'POST',
            data:{id:id,begin_time:begin_time,end_time:end_time},
            success:function(option){
                var obj = $.parseJSON(option);
                console.log(obj.ghoststockqty);
                $('#report_ghoststockqty').val(obj.ghoststockqty);
                $('#report_laststockqty').val(obj.lastqty);
            }
        });
});
</script>

