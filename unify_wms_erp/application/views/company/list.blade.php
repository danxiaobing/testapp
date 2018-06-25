<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#companylist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">公司编号</label>
                <div class="row-input">
                    <input type="text" name="companyno" value="{{$companyno}}" placeholder="请输入公司编号"></div>

                <label class="row-label">公司名称</label>
                <div class="row-input">
                    <input type="text" name="companyname" value="{{$companyname}}" placeholder="请输入公司名称"></div>

                <label class="row-label">公司简称</label>
                <div class="row-input">
                    <input type="text" name="companysname" value="{{$companysname}}" placeholder="请输入公司简称"></div>

                <label class="row-label">联系电话</label>
                <div class="row-input">
                    <input type="text" name="contacttel" value="{{$contacttel}}" placeholder="请输入联系电话">
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="" selected="">不限</option>
                        <option value="1" >启用</option>
                        <option value="2">停用</option>
                    </select>
                </div>
                <label class="row-label"></label>
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

    <table class="table table-bordered" id="companylist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,refresh',
        toolbarCustom:'#companylist-button',
        dataUrl: 'company/companylistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1200',height:'400',title:'开票公司信息',mask:true}},
        editUrl: '/company/companyedit/id/{sysno}',
        delUrl:'/company/companydeljson',
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
            <th data-options="{name:'companyno',align:'center',width:80}">公司编号</th>
            <th data-options="{name:'companyname',align:'center',width:150}">公司名称</th>
            <th data-options="{name:'companysname',align:'center',width:150}">公司简称</th>
            <th data-options="{name:'contacttel',align:'center',width:150}">联系电话</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
            <th data-options="{name:'status',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="companylist-button">
    <button type="button" id="del" class="btn btn-red" data-icon="times" >删除</button>
    <button type="button" id="companylist_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="companylist_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
</div>
<script type="text/javascript">
    //批量删除

    $('#del').click(function(){
        var checkdata=$('#companylist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm','确认批量删除吗！',{
                okCall : function(){
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Company/companydeljson/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu185');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            });
        }
    });

    //批量启用
    $('#companylist_start').click(function(){
        var checkdata=$('#companylist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm','确定批量启用吗？',{
                okCall :function(){
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Company/statuschange/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu185');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }
    });

    //批量停止
    $('#companylist_stop').click(function(){
        var checkdata=$('#companylist-table').data('selectedDatas');
        if(typeof(checkdata)=='undefined' || checkdata=='' || checkdata==null){
            BJUI.alertmsg('warn','<h4>未选中任何行!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else {
            BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    var checkdata=$('#companylist-table').data('selectedDatas');
                    var date = [];
                    for(i=0;i<checkdata.length;i++){
                        date[i] = checkdata[i].sysno;
                    }
                    BJUI.ajax('doajax',{
                        type : 'POST',
                        url:'/Company/statusover/',
                        data:{date : date },
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu185');
                        }
                    });
                    BJUI.alertmsg('info', '你点击了确定按钮！');
                }
            })
        }
    });

</script>