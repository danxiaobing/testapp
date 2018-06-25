<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#temreceivable-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">时间范围</label>
                <div class="row-input required datawidth">
                    <input type="text" name="startTime" id="startTime" value="{{$startTime or date('Y-m-d') }}" placeholder="开始时间"  data-toggle="datepicker" data-rule="required" ></div>
                <div class="row-input required datawidth">
                    <input type="text" name="endTime" id="endTime" value="{{$endTime or date('Y-m-d')}}" placeholder="结束时间"  data-toggle="datepicker" data-rule="required"></div>


                <label class="row-label">客户</label>
                <div class="row-input ">
                    <select data-toggle="selectpicker" data-width="100%" name="customername" data-size="10">
                        <option value="" selected="">请选择</option>
                        @foreach($customerlist as $key=>$value)
                            <option value="{{$value['sysno']}}" >{{$value['customername']}}</option>
                        @endforeach

                    </select>
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="receivablestatus">
                        <option value="">请选择</option>
                        <option value="2">暂存</option>
                        <option value="3">已提交</option>
                        <option value="4">已审核</option>
                        <option value="5">作废</option>
                        <option value="6">退回</option>
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

    <table class="table table-bordered" id="temreceivable-list-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'del',
        toolbarCustom: '#Temreceivable-button',
        dataUrl: 'Temreceivable/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        delUrl:'/Temreceivable/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        showTfoot:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">

            <th data-options="{name:'receivableno',align:'center'}">临时收款单号</th>
            <th data-options="{name:'receivabledate',align:'center'}">日期</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
           {{-- <th data-options="{name:'',align:'center'}">合同编号</th>
            <th data-options="{name:'',align:'center'}">费用名称</th>--}}
            <th data-options="{name:'costreceivable',align:'center',calc:'sum'}">金额</th>
            <th data-options="{name:'receivablestatus',align:'center',width:70,render:function(value){if(value=='2') {return '暂存'}
                else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='5') {return '作废'}
                else if(value=='6') {return '退回'} else  {return '新建'}}}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="Temreceivable-button">
    <button type="button" id="Temreceivable_see" class="btn btn-green" data-icon="eye">查看</button>
    <button type="button" id="Temreceivable_edit" class="btn btn-green" data-icon="edit">编辑</button>
    <button type="button" id="Temreceivable_aduit" class="btn btn-green" data-icon="gavel">审核</button>
    <button type="button" id="Temreceivable_back" class="btn btn-red" data-icon="fa-scissors">作废</button>

</div>

<script type="text/javascript">

    //编辑
    $('#Temreceivable_edit').click(function(){
        var checkdata=$('#temreceivable-list-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        console.log(checkdata);
        var receivablestatus = checkdata[0].receivablestatus;
        if(checkdata.length==1){
            if(receivablestatus==2 || receivablestatus==6){
                BJUI.navtab({
                    id : 'navab515',
                    url : '/Temreceivable/edit/mode/edit/id/'+checkdata[0].sysno,
                    type : 'post',
                    title : '编辑临时费用单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择暂存或退回的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
               BJUI.alertmsg('warn','<h4>不能编辑多条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //审核 
    $('#Temreceivable_aduit').click(function(){
        var checkdata=$('#temreceivable-list-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata =='' || checkdata==null ){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var receivablestatus = checkdata[0].receivablestatus;
        if(checkdata.length==1){
            if(receivablestatus==3){
                BJUI.navtab({
                    id : 'navab302',
                    url : '/Temreceivable/edit/mode/audit/id/'+checkdata[0].sysno,
                    type : 'post',
                    title : '审核临时费用单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择待审核的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>不能审核多条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //作废
    $('#Temreceivable_back').click(function(){
        var checkdata=$('#temreceivable-list-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        var receivablestatus = checkdata[0].receivablestatus;
        if(receivablestatus == 4){
            if(checkdata.length==1){
                BJUI.navtab({
                    id : 'navab302',
                    url : '/Temreceivable/edit/mode/back/id/'+checkdata[0].sysno,
                    title:'作废临时费用单'
                })
            }else {
                BJUI.alertmsg('warn','<h4>不能作废多条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择已审核的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //查看
    $('#Temreceivable_see').click(function(){
        var checkdata = $('#temreceivable-list-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.navtab({
                id: 'navab302',
                url: '/Temreceivable/edit/mode/eye/id/' + checkdata[0].sysno + '/val/1',
                type: 'post',
                data: {'id': checkdata[0].sysno},
                title: '查看临时费用单'
            });

        }
    })
    //删除
    $('#pipelineorder_del').click(function(){
        var selectdata  =  $.CurrentNavtab.find('#pipeline-list-table').data('selectedDatas');
        console.log(selectdata);
        if(selectdata && selectdata.length > 0){
            BJUI.alertmsg('confirm','确定要删除吗?',{okCall:function(){

                BJUI.ajax('doajax', {
                    url: '/pipelineorder/deljson/',
                    type: 'POST',
                    data : {id:selectdata[0].sysno},
                    loadingmask: true,
                    okCallback: function(json, options) {
                        BJUI.navtab('refresh', 'menu487');
                    }
                })
            } })
        }else {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }

    })



</script>