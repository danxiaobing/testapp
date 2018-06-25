<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" action="" method="" data-options="{searchDatagrid:$.CurrentNavtab.find('#ap_userlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">单据期间</label>
                <div class="row-input datawidth">
                    <input type="text" id="retankstartdate" name="bookingretankdate" value="" data-toggle="datepicker" data-rule="date" placeholder="开始日期">
                </div>
                <div class="row-input datawidth">
                    <input type="text" id="retankenddate" name="bookingretankdate_end" value="" data-toggle="datepicker" data-rule="date" placeholder="结束日期">
                </div>

                <label class="row-label">倒罐单编号</label>
                <div class="row-input">
                    <input type="text" id="bookingretankno" name="bar_no" value="{{$bar_no or ''}}" placeholder="倒罐申请单编号">
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select id="retankstatus" name="stockretankstatus" data-toggle="selectpicker" data-rule="required" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="0">请选择</option>
                        <option value="2">暂存</option>
                        <option value="4">待审核</option>
                        <option value="5">已审核</option>
                        <option value="6">作废</option>
                        <option value="7">退回</option>
                    </select>
                </div>
                <div class="row-input">
                    <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="ap_userlist-table" data-toggle="datagrid" data-options="{
            height: '100%',
            tableWidth : '99%',
            showToolbar: true,
            toolbarCustom : '#ap_retank-button',
            toolbarItem: 'del',
            addLocation: 'last',
            dataUrl: 'retank/applyjson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: false,
            editUrl: '/retank/applyedit/mode/edit/id/{sysno}',
            delUrl:'/retank/delapplyjson',
            delPK:'sysno',
            paging: {pageSize:12},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookingretankno',align:'center'}">倒罐申请单单号</th>
            <th  data-options="{name:'bookingretankdate',align:'center'}">申请日期</th>
            <th  data-options="{name:'goodsname',align:'center'}">品名</th>
            <th  data-options="{name:'stockretank_out_no',align:'center'}">倒出罐</th>
            <th  data-options="{name:'stockretank_in_no',align:'center'}">倒入罐</th>
            <th  data-options="{name:'zj_employeename',align:'center'}">创建人</th>
            <th data-options="{name:'stockretankstatus',align:'center',render:function(value){if(value=='2') {return '暂存'}
                else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6') {return '作废'}
                else if(value=='7') {return '退回'} else  {return '新建'}}}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="ap_retank-button">
    <button type="button" id="ap_retank_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" id="ap_retank_provide" class="btn btn-green" data-icon="gavel" >审核</button>
    <button type="button" id="ap_look_retank_data" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" class="btn btn-green" data-icon="sign-out" onclick="ap_retanksignout()">EXCEL导出</button>
</div>

<script type="text/javascript">
    //编辑
    $('#ap_retank_edit').click(function(){
        var checkdata=$('#ap_userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockretankstatus = checkdata[0].stockretankstatus;
        if(checkdata.length==1){
            if(stockretankstatus==2 || stockretankstatus==7){
                BJUI.navtab({
                    id : 'navab302',
                    url : '/retank/applyedit/mode/edit/id/'+checkdata[0].sysno,
                    type : 'post',
                    title : '编辑倒罐单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择暂存或退回的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //审核 
    $('#ap_retank_provide').click(function(){
        var checkdata=$('#ap_userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata =='' || checkdata==null ){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockretankstatus = checkdata[0].stockretankstatus;
        if(checkdata.length==1){
            if(stockretankstatus==4){
                BJUI.navtab({
                    id : 'navab302',
                    url : '/retank/applyedit/mode/audit/id/'+checkdata[0].sysno,
                    type : 'post',
                    title : '审核倒罐申请单',
                })
            }else{
                BJUI.alertmsg('warn','<h4>只能选择待审核的数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //查看
    $('#ap_look_retank_data').click(function(){
        var checkdata = $('#ap_userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            if(checkdata.length==1){
                BJUI.navtab({
                    id: 'navab302',
                    url: '/retank/lookretankapply/mode/eye/id/' + checkdata[0].sysno + '/val/1',
                    type: 'post',
                    data: {'id': checkdata[0].sysno},
                    title: '查看倒罐申请单'
                });
            }else {
                BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            }
        }
    })

    function retankaddattachment(){
        var data = $('#ap_').data('selectedDatas');
        if(typeof(data)=='undefined' || data=='' || data==null){
            BJUI.alertmsg('warn','<h4>请先选中要添加附件的单据再添加!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else{
            BJUI.navtab({
                id:'navab302',
                url: '/retank/edit/mode/addattach/id/' + data[0].sysno,
                title: '倒罐单上传附件'
            });
        }
    }

    //查看附件
    $("#look_trank_attachment").click(function() {
        var data  = $("#ap_userlist-table").data('selectedDatas');
        if (typeof(data)=='undefined' || data=='' || data==null) {
            BJUI.alertmsg('info', BJUI.getRegional('datagrid.selectMsg'));
            return false;
        }else {
            var obj = data[0];
            if (obj.sysno != '') {
                BJUI.dialog({
                    url:'/attachment/view/retank/retank-edit/'+obj.sysno,
                    title:'查看'+obj.stockretankno+"附件",
                    width:900,
                    height:600,
                    mask:true
                });
            }
        }
    });

    function ap_retanksignout(){
        var begin_time = $("#retankstartdate").val();
        var end_time = $("#retankenddate").val();
        var bookingretankno = $("#bookingretankno").val();
        var retankstatus = $("#retankstatus option:selected").val();

        BJUI.ajax('ajaxdownload', {
            url:'/retank/excelapply/',
            type:'POST',
            data:{begin_time: begin_time,end_time:end_time,bookingretankno:bookingretankno,retankstatus:retankstatus},
            successCallback: function(json, options) {
                console.log(123);
            }
        });
    }

</script>