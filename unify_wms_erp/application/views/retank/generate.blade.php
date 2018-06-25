<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" action="" method="" data-options="{searchDatagrid:$.CurrentNavtab.find('#ge_userlist-table')}">
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

                <label class="row-label">倒罐申请单编号</label>
                <div class="row-input">
                    <input type="text" id="bookingretankno" name="bar_no" value="{{$bar_no or ''}}" placeholder="倒罐申请单编号">
                </div>
                <div class="row-input">
                    <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="ge_userlist-table" data-toggle="datagrid" data-options="{
            height: '100%',
            tableWidth : '99%',
            showToolbar: true,
            toolbarCustom : '#ge_retank-button',
            addLocation: 'last',
            dataUrl: 'retank/generatejson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: false,
            editUrl: '/retank/generateedit/mode/edit/id/{sysno}',
            paging: {pageSize:12},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true
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
<div id="ge_retank-button">
    <button type="button" id="ge_retank_back" class="btn btn-red" data-icon="reply" >退回</button>
    <button type="button" id="ge_retank_generate" class="btn btn-blue" data-icon="plus" >生成倒罐单</button>
</div>

<script type="text/javascript">
    //生成
    $('#ge_retank_generate').click(function(){
        var checkdata=$('#ge_userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockretankstatus = checkdata[0].stockretankstatus;
        if(checkdata.length==1){
            BJUI.navtab({
                id : 'navab302',
                url : '/retank/generateedit/mode/add/id/'+checkdata[0].sysno,
                type : 'post',
                title : '生成倒罐单',
            })
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })

    //退回
    $('#ge_retank_back').click(function(){
        var checkdata=$('#ge_userlist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>请选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        var stockretankstatus = checkdata[0].stockretankstatus;
        if(checkdata.length==1){
            BJUI.navtab({
                id : 'navab302',
                url : '/retank/applyedit/mode/back/id/'+checkdata[0].sysno,
                type : 'post',
                title : '退回倒罐预约单',
            })
        }else{
            BJUI.alertmsg('warn','<h4>只能选择一条数据!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    })
</script>