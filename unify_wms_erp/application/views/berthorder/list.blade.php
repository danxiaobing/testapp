<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#berthorder-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围</label>
                <div class="row-input required datawidth">
                    <input type="text" name="startTime" id="startTime" value="{{$startTime }}" placeholder="开始时间"  data-toggle="datepicker" data-rule="" ></div>
                <div class="row-input required datawidth">
                    <input type="text" name="endTime" id="endTime" value="{{$endTime}}" placeholder="结束时间"  data-toggle="datepicker" data-rule=""></div>


                <label class="row-label">业务单据类型</label>
                <div class="row-input ">
                    <select data-toggle="selectpicker" data-width="100%"  data-live-search="true" name="businesstype">
                        <option value="" >请选择</option>
                        @foreach($list as $key=>$value)
                            <option value="{{$key}}" >{{$value}}</option>
                        @endforeach

                    </select>
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="orderstatus">
                        <option value="">请选择</option>
                        <option value="2">暂存</option>
                        <option value="3">提交</option>
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

    <table class="table table-bordered" id="berthorder-list-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '{{--refresh--}}',
        toolbarCustom: '#berthorder-button',
        dataUrl: 'berthorder/ListJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        delUrl:'/berthorder/DelJson',
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'berthorderno',align:'center'}">泊位分配单号</th>
            <th data-options="{name:'orderno',align:'center'}">业务单号</th>
            <th  data-options="{name:'businesstype',align:'center',render:function(value){if(value==1){ return '船入库预约'; }else if(value==2){return '船入库订单'}else if(value==3){return '车入库预约'}else if(value==4){return '车入库订单'}else if(value==5){return '管入库预约'}else if(value==6){return '管入库订单'}else if(value==7){return '船出库预约'}else if(value==8){return '船出库订单'}else if(value==9){return '车出库预约'}else if(value==10){return '车出库订单'}else if(value==11){return '管出库预约'}else if(value==12){return '管出库订单'}else if(value==13){return '靠泊装卸'}else if(value==14){return '靠泊装卸出预约'}else if(value==15){return '靠泊装卸入订单'}else if(value==16){return '靠泊装卸出订单'}  } }">业务单据类型</th>
            <th data-options="{name:'created_at',align:'center'}">创建时间</th>
            <th data-options="{name:'apply_employeename',align:'center'}">申请人</th>
            <th data-options="{name:'bookingdate',align:'center'}">预计到港时间</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'orderstatus',align:'center',render:function(value){if(value==2) {return '暂存' } else if(value==3) {return '提交' } }}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="berthorder-button">
    <button type="button" id="berthorder_see" class="btn btn-green" data-icon="gavel">查看</button>
    <button type="button" id="berthorder_edit" class="btn btn-green" data-icon="gavel">编辑</button>
{{--    <button type="button" id="berthorder_del" class="btn btn-red" data-icon="delete">删除</button>--}}

</div>

<script type="text/javascript">
    //編輯
    $('#berthorder_edit').click(function(){
        var checkdata=$('#berthorder-list-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var orderstatus = checkdata[0].orderstatus;
        if(checkdata){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            if(orderstatus==1 || orderstatus==2 || orderstatus==3 ){
                BJUI.navtab({
                    id : 'navab503',
                    url : '/berthorder/Edit/mode/edit/id/'+checkdata[0].sysno,
                    type : 'POST',
                    title : '编辑泊位分配单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>  只能选择暂存或提交的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })


    //查看
    $('#berthorder_see').click(function(){
        var checkdata = $('#berthorder-list-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.navtab({
                id: 'navab508',
                url: '/berthorder/edit/mode/eye/id/'+checkdata[0].sysno,
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看泊位分配单'
            });

        }
    });


</script>