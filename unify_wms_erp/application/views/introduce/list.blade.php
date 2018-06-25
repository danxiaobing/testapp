<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="introducelist-bar" data-options="{searchDatagrid:$.CurrentNavtab.find('#introducelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">提货单号</label>
                <div class="row-input">
                    <input type="text" name="takegoodsno" value="{{$takegoodsno or ''}}" placeholder="提货单号">
                </div>

                <label class="row-label">创建时间</label>
                <div class="row-input">
                    <input type="text" name="introductiondate" value="{{$introductiondate or ''}}" data-toggle="datepicker" readonly>
                </div>

                <label class="row-label">公司名称</label>
                <div class="row-input">
                    <select name="customer_sysno" data-toggle="selectpicker" id="customer_sysno" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">全部</option>
                    </select>
                </div>

                <label class="row-label">受让方</label>
                <div class="row-input">
                    <select name="buy_customer_sysno" data-toggle="selectpicker" id="buy_customer_sysno" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">全部</option>
                    </select>
                </div>

                <label class="row-label">提单类型</label>
                <div class="row-input">
                    <select name="introductiontype" data-toggle="selectpicker"  data-width="100%">
                        <option value="">全部</option>
                        <option value="1" @if($introductiontype == 1) selected @endif>可撤销</option>
                        <option value="2" @if($introductiontype == 2) selected @endif>不可撤销</option>
                    </select>
                </div>

                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text" name="shipname" value="{{$shipname or ''}}" placeholder="船名">
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="introductionstatus" data-toggle="selectpicker"  data-width="100%">
                        <option value="">全部</option>
                        <option value="2">暂存</option>
                        <option value="3">待审核</option>
                        <option value="4">提货中</option>
                        <option value="5">已完成</option>
                        <option value="6">退回</option>
                        <option value="7">已撤销</option>
                    </select>
                </div>

                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" id="search" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
            </div>

        </fieldset>
    </form>

</div>

<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="introducelist-table" data-toggle="datagrid" data-options="{
        tableWidth:'2500',
        height: '100%',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del',
        toolbarCustom:'#introduce_list_tb',
        dataUrl: '/introduce/listJson',
        dataType: 'json',
        editMode: {navtab:{title:'提单编辑',id:'introduce001'}},
        editUrl: '/introduce/edit/id/{sysno}',
        delUrl:'/introduce/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
        hScrollbar:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'introductionno',align:'center',width:150}">单据编号</th>
                <th data-options="{name:'introductiondate',align:'center',width:100}">创建时间</th>
                <th  data-options="{name:'introductiontype',align:'center',width:80,render:function(value){if(value=='1') {return '可撤销'} else  {return '不可撤销'}}}">提单类型</th>
                <th data-options="{name:'customername',align:'center',width:200}">开单公司</th>
                <th data-options="{name:'shipname',align:'center',width:100,render:function(value){if(!value) {return '槽车'}}}">船名</th>
                <th  data-options="{name:'takegoodsno',align:'center',width:150}">提货单号</th>
                <th data-options="{name:'sale_customername',align:'center',width:200}">转出方</th>
                <th data-options="{name:'buy_customername',align:'center',width:200}">转入方</th>
                <th  data-options="{name:'receivestart',align:'center',width:100}">提货开始日</th>
                <th  data-options="{name:'receiveend',align:'center',width:100,width:100}">提货结束日</th>
                <th  data-options="{name:'freecostdate',align:'center',width:50,render:function(value){if(value=='0') {return '--'}}}">免仓期</th>
                <th  data-options="{name:'takegoodsnum',align:'center',width:80}">提单数量(吨)</th>
                <th  data-options="{name:'bookingqty',align:'center',width:80,render:function(value){if(value=='0') {return '0.000'}}}">预约提货量(吨)</th>
                <th  data-options="{name:'takegoodsqty',align:'center',width:80}">实提数量(吨)</th>
                <th  data-options="{name:'outqty',align:'center',width:80}">向下货转量(吨)</th>
                <th  data-options="{name:'untakegoodsnum',align:'center',width:80}">结存量(吨)</th>
                <th data-options="{name:'introductionstatus',align:'center',width:80,render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '提货中'} else if(value=='5') {return '已完成'} else if(value=='6') {return '退回'} else if(value=='7') {return '已撤销'} else if(value=='8') {return '驳回'} else if(value=='9') {return '作废'} else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="introduce_list_tb">
    <button type="button" class="btn btn-green" data-icon="filter"  id="introduce_list_tran_down">向下货转</button>
    <button type="button" class="btn btn-red" data-icon="scissors"  id="introduce_list_revocation">提单撤销</button>
    <button type="button" class="btn btn-green" data-icon="filter"  id="introduce_list_delay">提单延期</button>
    <button type="button" class="btn btn-blue" data-icon="eye"  id="introduce_list_view">查看</button>
    <button type="button" class="btn btn-blue" data-icon="eye"  id="introduce_list_stockout_view">出库查看</button>
    <button type="button" class="btn btn-red" data-icon="scissors"  id="introduce_list_cancel">提单作废</button>
</div>

<script type="text/javascript">

$(function(){
        $('#introduce_list_view').click(function() {
            var chks=$.CurrentNavtab.find("#introducelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#introducelist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.navtab({
                id:'introduce001',
                url:"/introduce/edit/type/view/id/"+data[0].sysno,
                title:'提单查看'
            });

        });

        $('#introduce_list_stockout_view').click(function() {
            var chks=$.CurrentNavtab.find("#introducelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#introducelist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.navtab({
                id:'introduce_out_list',
                url:"/introduce/outList/id/"+data[0].sysno,
                title:'提单查看'
            });

        });

        $('#introduce_list_delay').click(function() {
            var chks=$.CurrentNavtab.find("#introducelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#introducelist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.navtab({
                id:'introduce001',
                url:"/introduce/edit/type/delay/id/"+data[0].sysno,
                title:'提单延期'
            });

        });

        $('#introduce_list_tran_down').click(function() {
            var chks=$.CurrentNavtab.find("#introducelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#introducelist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.navtab({
                id:'introduce001',
                url:"/introduce/edit/type/trandown/id/"+data[0].sysno,
                title:'向下货转'
            });

        });

        $('#introduce_list_revocation').click(function() {
            var chks=$.CurrentNavtab.find("#introducelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#introducelist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.alertmsg('confirm', '确定终止/撤销？', {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: "/introduce/stopIntroduce/id/"+data[0].sysno,
                        loadingmask: true,
                        okCallback: function(json, options) {
                            if(json.code == 200 ){
                                BJUI.navtab('reloadFlag', 'navab532,navab533');
                                BJUI.alertmsg('ok',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                                return false;
                            }else{
                                BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                                return false;
                            }
                        }
                    })
                }
            })
        });

        $('#introduce_list_cancel').click(function() {
            var chks=$.CurrentNavtab.find("#introducelist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#introducelist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.alertmsg('confirm', '确定作废？', {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: "/introduce/cancelIntroduce/id/"+data[0].sysno,
                        loadingmask: true,
                        okCallback: function(json, options) {
                            if(json.code == 200 ){
                                BJUI.navtab('reloadFlag', 'navab532,navab533');
                                BJUI.alertmsg('ok',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                                return false;
                            }else{
                                BJUI.alertmsg('warn',json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                                return false;
                            }
                        }
                    })
                }
            })
        });

       //异步获取公司信息列表
       var gets = getCompanyMessage('customer/listAllJson','#customer_sysno');
       var gets1 = getCompanyMessage('customer/listAllJson','#buy_customer_sysno');

});


function getCompanyMessage(Url,obj){

  var htm='<option value="">全部</option>';

   $(obj).empty();

    $.ajax({
        url: Url,
        dataType: 'json'
    })
    .done(function(data) {

        for (var i = data.length - 1; i >= 0; i--)
        {
            htm+="<option value="+data[i].sysno+">"+data[i].customername+"</option>";
        }

        $(obj).append(htm);
        $(obj).selectpicker('refresh');
        $(obj).selectpicker('render');

    })
    .fail(function() {
        BJUI.alertmsg('warn',"公司列表信息未成功获取，请刷新当前页面",{displayPosition:'middlecenter',displayMode:'fade'});
    });
}
</script>