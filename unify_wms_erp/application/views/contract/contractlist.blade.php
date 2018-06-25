<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$('#contractlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="startdate" value="{{date('Y-m-d',strtotime('-1 year'))}}" data-toggle="datepicker" data-rule="required">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="enddate" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" data-rule="required">
                </div>

                <label class="row-label">合同编号</label>
                <div class="row-input">
                    <input id="contractnodisplay" type="text" name="contractnodisplay" value="{{$contractnodisplay}}" placeholder="请输入合同编号">
                </div>

                <label class="row-label">客户姓名</label>
                <div class="row-input">
                    <select name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">租罐方式</label>
                <div class="row-input">
                    <select id="contracttype" name="contracttype" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        <option value="1">长约</option>
                        <option value="2">短约</option>
                        <option value="3">包罐</option>
                        <option value="4">包罐容</option>
                        <option value="5">靠泊装卸</option>
                    </select>
                </div>

                <label class="row-label">业务员</label>
                <div class="row-input">
                    <select id="saleemployee_sysno" name="saleemployee_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($employee as $item)
                        <option value="{{$item['sysno']}}">{{$item['employeename']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">客服专员</label>
                <div class="row-input">
                    <select id="csemployee_sysno" name="csemployee_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($employee as $item)
                            <option value="{{$item['sysno']}}" >{{$item['employeename']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select id="contractstatusquery" name="contractstatus" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        <option value="2">暂存</option>
                        <option value="3">评审中</option>
                        <option value="4">待审核</option>
                        <option value="5">已审核</option>
                        <option value="6">退回</option>
                        <option value="7">作废</option>
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
    <table class="table table-bordered" id="contractlist-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:$('#contractlist_tb'),
            addLocation: 'last',
            dataUrl: 'contract/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: {pageSize:11},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true,
            hScrollbar:true
        }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'contractnodisplay',align:'center'}">合同编号</th>
                <th data-options="{name:'contractdate',align:'center'}">合同日期</th>
                <th data-options="{name:'customername',align:'center',width:280}">客户姓名</th>
                <th data-options="{name:'contractstartdate',align:'center'}">合同起始日</th>
                <th data-options="{name:'contractenddate',align:'center'}">合同终止日</th>
                <th data-options="{name:'contracttype',align:'center',render:function(value)
                   { if(value =='1') {return '长约'} else if(value=='2') {return '短约'}
                      else if(value =='3'){return '包罐'} else if(value =='4') {return '包罐容'}else if(value =='5') {return '靠泊装卸'}}}">租罐方式</th>
                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'}
                else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'} else {return '未限制'}}}">
                    货物性质
                </th>
                <th data-options="{name:'saleemployeename',align:'center'}">业务员</th>
                <th data-options="{name:'csemployeename',align:'center'}">客服专员</th>
                <th data-options="{name:'contractstatus',align:'center',render:function(value)
                    {if(value=='1') {return '新建'} else if(value=='2') {return '暂存'}
                    else if(value=='3') {return '评审中'} else if(value=='4') {return '待审核'}
                    else if(value=='5') {return '已审核'} else if(value=='6') {return '退回'}else if(value=='7') {return '作废'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>

<div id="contractlist_tb">
    <button type="button" id="viewcontract_btn" class="btn btn-blue" data-icon="eye">查看</button>
    <button type="button" id="delcontract" class="btn btn-red" data-icon="close">删除</button>
    <button type="button" id="editcontract" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" onclick="contract_list2_downloadSeal_list()" class="btn btn-green" data-icon="print">打印</button>
    <button type="button" class="btn btn-green" data-icon="filter" onclick="addattachment()">附件</button>
    <button type="button" id="copycontract" class="btn btn-green" data-icon="copy">复制</button>
    <button type="button" id="abolishcontract" class="btn btn-red" data-icon="scissors">作废</button>
    <button type="button"  class="btn btn-green" data-icon="sign-out" onclick="contract_list_signout()">EXCEL导出</button>
</div>


<script type="text/javascript">
$("#editcontract").click(function () {
    BJUI.navtab('closeTab','navab242');
    BJUI.navtab('closeTab','navab243');
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');

    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要编辑的合同再编辑');
    }else if(data.length>=2){
        BJUI.alertmsg('warn', '只能选择一条合同编辑');
    }else if(data[0]['contractstatus']==2||data[0]['contractstatus']==6){
        var sysno = data[0]['sysno'];
        var zuguantype = data[0]['contracttype'];
        var navid = 'navab488';
        if(zuguantype == 1||zuguantype == 2){
            navid = 'navab243';
        }else if(zuguantype == 3||zuguantype == 4){
            navid = 'navab242';
        }
        BJUI.navtab({
            id:navid,
            url:'/contract/list/id/'+sysno+'/zuguantype/'+zuguantype,
            title:'编辑合同',
        })
    }else{
        BJUI.alertmsg('warn', '只有暂存和退回的合同才能编辑');
    }
})

$("#delcontract").click(function () {
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');

    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要删除的合同再删除');
    }else if(data.length>=2){
        BJUI.alertmsg('warn', '只能选择一条合同删除');
    }else if(data[0]['contractstatus']==2||data[0]['contractstatus']==6){
        var sysno = data[0]['sysno'];
        var contractnodisplay = data[0]['contractnodisplay'];
        BJUI.ajax('doajax', {
            url: '/contract/delcontract',
            data:{sysno: sysno,contractnodisplay:contractnodisplay },
            okCallback: function(json, options) {
                BJUI.navtab('refresh', 'menu244');
            }
        });
        BJUI.navtab('refresh', 'menu244');

    }else{
        BJUI.alertmsg('warn', '只有暂存和退回的合同才能删除');
    }
})

$("#viewcontract_btn").click(function () {
    BJUI.navtab('closeTab','navab242');
    BJUI.navtab('closeTab','navab243');
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');
    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要查看的合同再查看');
    }else if(data.length>=2){
        BJUI.alertmsg('warn', '只能选择一条合同查看');
    }else{
        var sysno = data[0]['sysno'];
        var zuguantype = data[0]['contracttype'];
        var navid = 'navab488';
        if(zuguantype == 1||zuguantype == 2){
            navid = 'navab243';
        }else if(zuguantype == 3||zuguantype == 4){
            navid = 'navab242';
        }
        BJUI.navtab({
            id:navid,
            url:'/contract/list/mode/eye/id/'+sysno+'/zuguantype/'+zuguantype,
            title:'查看合同',
        })
    }
})

$("#copycontract").click(function () {
    BJUI.navtab('closeTab','navab242');
    BJUI.navtab('closeTab','navab243');
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');
    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要编辑的合同再复制');
    }else if(data.length>=2){
        BJUI.alertmsg('warn', '只能选择一条合同复制');
    }else if(data[0]['contractstatus']!=2){
        var sysno = data[0]['sysno'];
        var zuguantype = data[0]['contracttype'];
        var navid = 'navab488';
        if(zuguantype == 1||zuguantype == 2){
            navid = 'navab243';
        }else if(zuguantype == 3||zuguantype == 4){
            navid = 'navab242';
        }
        BJUI.navtab({
            id:navid,
            url:'/contract/list/id/'+sysno+'/zuguantype/'+zuguantype+'/copycontract/1',
            title:'复制合同',
        })
    }else{
        BJUI.alertmsg('warn', '暂存的合同不需复制可直接修改');
    }
})

$("#abolishcontract").click(function () {
    BJUI.navtab('closeTab','navab242');
    BJUI.navtab('closeTab','navab243');
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');
    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要编辑的合同再作废');
    }else if(data.length>=2){
        BJUI.alertmsg('warn', '只能选择一条合同作废');
    }else if(data[0]['contractstatus']==5){
        var sysno = data[0]['sysno'];
        var zuguantype = data[0]['contracttype'];
        var navid = 'navab488';
        if(zuguantype == 1||zuguantype == 2){
            navid = 'navab243';
        }else if(zuguantype == 3||zuguantype == 4){
            navid = 'navab242';
        }
        BJUI.navtab({
            id:navid,
            url:'/contract/list/mode/back/id/'+sysno+'/zuguantype/'+zuguantype,
            title:'作废合同',
        })
    }else{
        BJUI.alertmsg('warn', '只有审核过的合同才能作废');
    }

})

function contract_list2_downloadSeal_list() {
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');

    if(data == ''||data == null) {
        BJUI.alertmsg('warn', '请先选中合同再打印');
        return false;
    }
    if(data.length > 1){
        BJUI.alertmsg('warn', '只能选择一条合同打印');
        return false;
    }

    BJUI.ajax('ajaxdownload', {
        url:'/contract/export/id/'+data[0]['sysno'],
    });
}

function addattachment() {
    var data = $.CurrentNavtab.find("#contractlist-table").data('selectedDatas');
    if(data == ''||data == null){
        BJUI.alertmsg('warn', '请先选中要添加附件的合同再添加');
    }else if(data[0]['contractstatus']!=5){
        BJUI.alertmsg('warn', '只有已审核的合同才能添加附件');
    }else if(data.length>=2){
        BJUI.alertmsg('warn', '只能选择一条合同添加附件');
    }else{
        var sysno = data[0]['sysno'];
        var zuguantype = data[0]['contracttype'];
        var navid = 'navab488';
        if(zuguantype == 1||zuguantype == 2){
            navid = 'navab243';
        }else if(zuguantype == 3||zuguantype == 4){
            navid = 'navab242';
        }
        BJUI.navtab({
            id:navid,
            url:'/contract/list/mode/addattach/id/'+sysno+'/zuguantype/'+zuguantype,
            title:'添加附件',
        })
        return;
    }
}

function contract_list_signout() {
    var contractnodisplay = $("#contractnodisplay").val();
    var customername = $("#customername").val();
    var contractstatus = $("#contractstatusquery option:selected").val();
    var contracttype = $("#contracttype option:selected").val();
    var saleemployee_sysno = $("#saleemployee_sysno option:selected").val();
    var csemployee_sysno = $("#csemployee_sysno option:selected").val();

    BJUI.ajax('ajaxdownload', {
        url:'/contract/excel/',
        type:'POST',
        data:{contractnodisplay: contractnodisplay,customername:customername,contractstatus:contractstatus,contracttype:contracttype,saleemployee_sysno:saleemployee_sysno,csemployee_sysno:csemployee_sysno },
        successCallback: function(json, options) {
            //console.log(123);
        }
    });
}

</script>