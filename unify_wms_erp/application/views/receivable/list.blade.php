<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#receivablelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">

                <label class="row-label">单据期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" id="begin_time" @if($begin_time) value="{{$begin_time or ''}}" @endif data-toggle="datepicker" placeholder="开始时间" ></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" id="end_time" @if($end_time) value="{{$end_time or ''}}" @endif data-toggle="datepicker" placeholder="结束时间" ></div>

                <label class="row-label">客户名称:</label>
                <div class="row-input">
                    <select name="customer_sysno" id="customer_sysno" data-toggle="selectpicker"  data-width="100%" class="show-tick"  data-size="6" data-live-search="true" >
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno']==$customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select></div>

                <label class="row-label">单据状态:</label>
                <div class="row-input">
                    <select name="receivablestatus"  id="receivablestatus" data-toggle="selectpicker"  data-width="100%" class="show-tick" >
                        <option value="">全部</option>
                            <option value="2">暂存</option>
                            <option value="3">待审核</option>
                            <option value="4">已审核</option>
                            <option value="5">退回</option>
                            <option value="6">作废</option>
                            <option value="7">已核销</option>
                    </select></div>

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
    <table class="table table-bordered" id="receivablelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:'#receuvable_btn',
        toolbarItem: 'del,',
        addLocation: 'last',
        dataUrl: '/receivable/listJson/cus/{{$customer_sysno}}/start/{{$begin_time}}/end/{{$end_time}}',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode:false,
        delUrl:'/receivable/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'receivableno',align:'center'}">收款单号</th>
            <th  data-options="{name:'receivabledate',align:'center'}">收款日期</th>
            <th  data-options="{name:'customername',align:'center'}">客户名称</th>
            <th  data-options="{name:'settlementname',align:'center'}">结算方式</th>
            <th  data-options="{name:'base_companyname',align:'center'}">收款单位</th>
            <th  data-options="{name:'costreceivable',align:'center'}">收款金额</th>
            <th data-options="{name:'receivablestatus',align:'center',render:function(value){ if(value=='2'){return '暂存';}else if(value=='3'){return '待审核';}else if(value=='4'){return '已审核';}else if(value=='5'){return '退回';}else if(value==6){return '作废';}else if(value==7){ return '已核销' }else if(value==1){ return '新建';} }}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="receuvable_btn">
    <button type="button" id="receuvable_edit" class="btn btn-green" data-icon="edit" onclick="edit(1)">编辑</button>
    <button type="button" id="receuvable_look" class="btn btn-blue"  data-icon="eye">查看</button>
    <button type="button" id="receuvable" class="btn btn-green" data-icon="gavel" onclick="edit(2)">审核</button>
    <button type="button" id="receuvable_void" class="btn btn-red" data-icon="scissors">作废</button>
    <button id="excel_receuvable_btn" class="btn btn-green" data-icon="fa-file-excel-o">Excel导出</button>
</div>
<script>
    function edit(state){
        var checkdata=$('#receivablelist-table').data('selectedDatas');
        console.log(checkdata);
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var id = checkdata[0].sysno;
            console.log(id); 
            var status = checkdata[0].receivablestatus;
            console.log(status);
            if(state==2){
                if(status!=3){
                    BJUI.alertmsg('warn','<h4>该单据暂时无法审核!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;
                }
                var title = '审核收款单';
                var mode = 'audit';
            }else{
                if(status!=2 && status!=5){
                    BJUI.alertmsg('warn','<h4>该不是暂存状态的单据无法编辑!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                    return;                    
                }
                var title = '编辑收款单';
                var mode = 'edit';
            }

            BJUI.navtab({
                id:'pendcarin_edit',
                url:'/receivable/edit/mode/'+mode+'/id/'+id,
                data:{mode:mode},
                type:'get',
                title:title,
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    $('#receuvable_look').click(function(){
        var checkdata=$('#receivablelist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var id = checkdata[0].sysno;
            // console.log(id); 
            var status = checkdata[0].receivablestatus;
            title = '查看收款单';
            BJUI.navtab({
                id:'pendcarin_look',
                url:'/receivable/look/id/'+id,
                title:title,
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }  
    });


        $('#receuvable_void').click(function(){
        var checkdata=$('#receivablelist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>作废时只能选择一条数据!<h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var id = checkdata[0].sysno;
            var status = checkdata[0].receivablestatus;

            if(status!=4){
                BJUI.alertmsg('warn','<h4>该单据不能作废!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }

            BJUI.navtab({
                id:'receivable_void',
                url:'/receivable/edit/id/'+id+'/void/'+1,
                title:'作废收款单',
            });
        }else{
            BJUI.alertmsg('warn','<h4>请选数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });


 $("#excel_receuvable_btn").click(function () {
        var begin_time = $("#begin_time").val();
        var end_time = $("#end_time").val();
        var customer_sysno = $("#customer_sysno option:selected").val();
        var receivablestatus = $("#receivablestatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url: '/receivable/excel/',
            type: 'POST',
            data: {begin_time: begin_time, end_time: end_time, customer_sysno: customer_sysno, receivablestatus: receivablestatus},
            successCallback: function (json, options) {
                console.log(Success);
            }
        });
    });

</script>