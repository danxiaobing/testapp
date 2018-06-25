<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" action="{{$action}}" data-options="{searchDatagrid:$.CurrentNavtab.find('#qualitycheck-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务区间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" id="begin_time" value="{{$begin_time or ''}}" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" id="end_time" value="{{$end_time or ''}}" data-toggle="datepicker" placeholder="结束时间" ></div>

                <label class="row-label">业务单据类型：</label>
                <div class="row-input">
                    <select name="businesstype"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($type as $k => $item)
                        <option value="{{$k}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">单据状态:</label>
                <div class="row-input">
                    <select name="orderstatus" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="">全部</option>
                        <option value="2">暂存</option>
                        <option value="3">已提交</option>
                    </select>
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="qualitycheck-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:'#pendqualitycheck_btn',
        dataUrl: 'qualitycheck/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:11},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'qualitycheckno',align:'center'}">品质检查预约单号</th>
                <th data-options="{name:'bookingno',align:'center',render:function(value,data){if(data.stockno){return data.stockno;}else{return data.bookingno;}}}">业务单号</th>
                <th  data-options="{name:'businesstype',align:'center',render:function(value){if(value==1){ return '船入库预约'; }else if(value==2){return '船入库订单'}else if(value==3){return '车入库预约'}else if(value==4){return '车入库订单'}else if(value==5){return '管入库预约'}else if(value==6){return '管入库订单'}else if(value==7){return '船出库预约'}else if(value==8){return '船出库订单'}else if(value==9){return '车出库预约'}else if(value==10){return '车出库订单'}else if(value==11){return '管出库预约'}else if(value==12){return '管出库订单'}else if(value==13){return '靠泊装卸'}else if(value==14){return '靠泊装卸出预约'}else if(value==15){return '靠泊装卸入订单'}else if(value==16){return '靠泊装卸出订单'}  } }">业务单据类型</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd'}">创建时间</th>
                <th data-options="{name:'customername',align:'center' }">客户名称</th>
                <th data-options="{name:'goodsname',align:'center' }">货品名称</th>
                <th data-options="{name:'apply_employeename',align:'center' }">申请人</th>
                <th data-options="{name:'bookingdate',align:'center'}">预计到港时间/预约时间</th>
                <th data-options="{name:'shipname',align:'center'}">船名/车</th>
                <th data-options="{name:'orderstatus',align:'center',render:function(value){if(value==2){return '暂存';}else if(value==3){return '已提交';}} }">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="pendqualitycheck_btn">
    <button type="button" id="qualitycheck_stop" class="btn btn-green" data-icon="times" onclick="stop()" >审核</button>
</div>

<script>
    $('#qualitycheck_edit').click(function(){
        var checkdata=$('#qualitycheck-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;
            BJUI.navtab({
                id:'qualitycheck-edit',
                mask:true,
                type:'POST',
                url:'/Qualitycheck/qualitycheckedit/'+id,
                data:{sysno:id},
                title:'编辑品质检查单'
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选择数据</h4>');
        }
    });

    function qualitycheck_look()
    {
        var data = $('#qualitycheck-table').data('selectedDatas');
        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'qualitycheck-look'+sysno,
                mask:true,
                type:'POST',
                url:'/qualitycheck/showQualitychec',
                data:{id:sysno},
                title:'查看品质检查单'
            });
        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }





</script>