<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" action="{{$action}}" data-options="{searchDatagrid:$.CurrentNavtab.find('#quality_audit_table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">业务区间</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="{{$begin_time or ''}}" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="{{$end_time or ''}}" data-toggle="datepicker" placeholder="结束时间" ></div>

                <label class="row-label">业务单据类型</label>
                <div class="row-input">
                    <select name="businesstype"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($type as $k => $item)
                        <option value="{{$k}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">船/车名</label>
                <div class="row-input">
                    <input type="text" name="carshipname" value="{{$carshipname or ''}}">
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="customername" value="{{$customername or ''}}">
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
    <table class="table table-bordered" data-toggle="datagrid" id="quality_audit_table" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:'#quality_audit_bar',
        dataUrl: 'qualitycheck/auditListJson/orderstatus/3',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:11},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'qualitycheckno',align:'center'}">品质检查预约单号</th>
                <th data-options="{name:'bookingno',align:'center',render:function(value,data){if(data.stockno){return data.stockno;}else{return data.bookingno;}}}">业务单号</th>
                <th  data-options="{name:'businesstype',align:'center',render:function(value){if(value==1){ return '船入库预约'; }else if(value==2){return '船入库订单'}else if(value==3){return '车入库磅码单'}else if(value==4){return '管入库预约'}else if(value==5){return '管入库订单'}else if(value==6){return '船出库预约'}else if(value==7){return '船出库订单'}else if(value==8){return '管出库预约'}else if(value==9){return '管出库订单'}else if(value==10){return '退货'}}}">业务单据类型</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd'}">创建时间</th>
                <th data-options="{name:'customername',align:'center' }">客户名称</th>
                <th data-options="{name:'goodsname',align:'center' }">货品名称</th>
                <th data-options="{name:'apply_employeename',align:'center' }">申请人</th>
                <th data-options="{name:'bookingdate',align:'center'}">预计到港时间/预约时间</th>
                <th data-options="{name:'carshipname',align:'center',render:function(value,data){if(data.businesstype==4||data.businesstype==5||data.businesstype==8||data.businesstype==9){return '管输';}}}">船名/车</th>
                <th data-options="{name:'orderstatus',align:'center',render:function(value){if(value==2){return '暂存';}else if(value==3){return '待审核';}} }">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="quality_audit_bar">
    <button type="button" class="btn btn-green" data-icon="gavel"  id="quality_audit_btn">审核</button>
</div>

<script>
    $('#quality_audit_btn').click(function(){
        var checkdata=$('#quality_audit_table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var id = checkdata[0].sysno;
            BJUI.navtab({
                id:'qualitycheck-audit',
                mask:true,
                type:'POST',
                url:'/Qualitycheck/qualitycheckedit/tempType/audit/'+id,
                data:{sysno:id},
                title:'品检审核编辑'
            });
        }else{
            BJUI.alertmsg('warn','未选择数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
    });

</script>