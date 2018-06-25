<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" action="{{$action}}" data-options="{searchDatagrid:$.CurrentNavtab.find('#qualitycheck-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
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

                <label class="row-label">船/车名</label>
                <div class="row-input">
                    <input type="text" name="carshipname" value="{{$carshipname or ''}}">
                </div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" name="customername" value="{{$customername or ''}}">
                </div>

                <label class="row-label">单据状态:</label>
                <div class="row-input">
                    <select name="orderstatus" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="">全部</option>
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">已审核</option>
                        <option value="5">退回</option>
                        <option value="6">让步待审核</option>
                        <option value="7">让步审核通过</option>
                        <option value="8">终止</option>
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
        toolbarCustom:'#qualitycheck_btn',
        dataUrl: 'qualitycheck/listJson',
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
                <!-- 1船入库预约、2船入库订单、3车入库磅码单、4管入库预约、5管入库订单、6船出库预约、7船出库订单、8管出库预约、9管出库订单、10退货 -->
                <th  data-options="{name:'businesstype',align:'center',render:function(value){if(value==1){ return '船入库预约'; }else if(value==2){return '船入库订单'}else if(value==3){return '车入库磅码单'}else if(value==4){return '管入库预约'}else if(value==5){return '管入库订单'}else if(value==6){return '船出库预约'}else if(value==7){return '船出库订单'}else if(value==8){return '管出库预约'}else if(value==9){return '管出库订单'}else if(value==10){return '退货'}}}">业务单据类型</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd'}">创建时间</th>
                <th data-options="{name:'customername',align:'center' }">客户名称</th>
                <th data-options="{name:'goodsname',align:'center' }">货品名称</th>
                <th data-options="{name:'apply_employeename',align:'center' }">申请人</th>
                <th data-options="{name:'bookingdate',align:'center'}">预计到港时间/预约时间</th>
                <th data-options="{name:'carshipname',align:'center',render:function(value,data){if(data.businesstype==4||data.businesstype==5||data.businesstype==8||data.businesstype==9){return '管输';}}}">船名/车</th>
                <th data-options="{name:'orderstatus',align:'center',render:function(value){if(value==2){return '暂存';}else if(value==3){return '待审核';}else if(value==4){return '已审核';}else if(value==5){return '退回';}else if(value==6){return '让步待审核';}else if(value==7){return '让步审核通过';}else if(value==8){return '终止';} }}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="qualitycheck_btn">
    <button type="button" id="qualitycheck_look" class="btn btn-blue" data-icon="eye"  onclick="qualitycheck_look()">查看</button>
    <button type="button" id="qualitycheck_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" id="qualitycheck_stop" class="btn btn-red" data-icon="times" onclick="stopQualitycheck()" >终止</button>
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
                BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'qualitycheck-edit',
                mask:true,
                type:'POST',
                url:'/qualitycheck/qualitycheckedit/tempType/view/' + sysno,
                data:{sysno:sysno},
                title:'查看品质检查单'
            });
        }else{
            BJUI.alertmsg('warn','未选中数据',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    function stopQualitycheck() {
        $selectData = $.CurrentNavtab.find('#qualitycheck-table').data('selectedDatas');
        if($selectData.length < 1){
            BJUI.alertmsg('warn','请选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        if($selectData.length > 1){
            BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        BJUI.ajax('doajax',{
            url: '/qualitycheck/examjson/',
            type: 'POST',
            data: {examstep : 8,id : $selectData[0].sysno},
            loadingmask: true,
            okCallback: function(json, options) {
                if(json.code == 200){
                    BJUI.navtab('refresh', 'navab578');
                }
            }
        })
    }



</script>