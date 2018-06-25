<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#stockoutlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">出库单号</label>
                    <div class="row-input">
                        <input type="text" id='stockoutlist_bar_no' name="bar_no" value="" placeholder="出库单号"></div>

                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" id='stockoutlist_begin_time' name="begin_time" value="" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" id='stockoutlist_end_time' name="end_time" value="" data-toggle="datepicker" placeholder="结束时间"></div>

                <label class="row-label">客户名称</label>
                <div class="row-input">
                    <input type="text" id='stockoutlist_bar_name' name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称"></div>
                <br/>
                <label class="row-label">提货单号</label>
                <div class="row-input">
                    <input type="text" id='stockoutlist_bar_receivenumber' name="bar_receivenumber" placeholder="提货单号">
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="bar_goodsname" id='stockoutlist_bar_goodsname' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['goodsname']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    @if($stockouttype == '1')
                        <select id='stockoutlist_bar_stockoutstatus' name="bar_stockoutstatus" data-toggle="selectpicker" data-width="100%" name="bar_stockinstatus">
                            <option value="-100" selected="">不限</option>2暂存3出库中4已完成
                            <option value="2">暂存</option>
                            <option value="3">待审核</option>
                            <option value="4">已完成</option>
                            <option value="5">作废</option>
                        </select>
                    @else
                    <select id='stockoutlist_bar_stockoutstatus' name="bar_stockoutstatus" data-toggle="selectpicker" data-width="100%" name="bar_stockinstatus">
                        <option value="-100" selected="">不限</option>
                        <option value="2">暂存</option>
                        <option value="3">出库中</option>
                        <option value="4">已完成</option>
                    </select>
                    @endif
                </div>
                
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    </div>
                </div>
                <input type="hidden" name="stockouttype" value="{{$stockouttype}}">
            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
        <table class="table table-bordered" id="stockoutlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'2500',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'del_stocklist',
        toolbarCustom:'#custom_stockout_tb',
        addLocation: 'last',
        dataUrl: '/stockout/carlistJson',
        dataType: 'json',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
        hScrollbar:true
        

    }">

            <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'stockoutno',align:'center'}">出库单号</th>
                {{--<th data-options="{name:'stockouttype',align:'center',render:function(value){switch(value) { case '1': return '船出库'; case '2':return '车出库'; default: return '';  }}}">出库类型</th>--}}
                <th data-options="{name:'customername',align:'center'}">客户</th>

                <th  data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
                <th  data-options="{name:'takegoodscompany',align:'center'}">提货单位</th>
                <th  data-options="{name:'receivestart',align:'center'}">提货开始日</th>
                <th  data-options="{name:'receiveend',align:'center'}">提货结束日</th>
                <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                <th data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return '保税'; case '2':return '外贸'; case '3':return '内贸转出口'; case '4':return '内贸内销'; default: return '';  }}}">
                    货物性质
                </th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'tobeqty',align:'center'}">提单量</th>
                <th data-options="{name:'beqty',align:'center'}">实提数量</th>
                <th data-options="{name:'takeqty',align:'center'}">结存量</th>
                <th data-options="{name:'stockoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '出库中'}  else if(value=='4') {return '已完成'}  else if(value=='6') {return '退回'} else  {return '新建'}}}">单据状态</th>
            </tr>
            </thead>
        </table>



</div>
<div id="custom_stockout_tb">
    <button type="button" id="custom_stockout_view_btn" data-icon="eye" class="btn btn-blue">查看</button>
    <button type="button" class="btn btn-green" data-icon="car" id="custom_stockout_car">车辆变更</button>
    <button type="button" class="btn btn-green" data-icon="car" id="custom_stockout_delay">延长提货时间</button>
    <button type="button" class="btn btn-red" data-icon="stack-overflow" id="custom_stockout_stop">终止</button>
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="custom_stockout_excel_btn">EXCEL导出</button>
    

</div>


<script type="text/javascript">

        $("#custom_stockout_stop").click(function(){
            var chks=$.CurrentNavtab.find("#stockoutlist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#stockoutlist-table").data('selectedDatas');
            if (data == '' || data == null) {
                BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn', "请选择一条出库单终止",{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            } 
            BJUI.alertmsg('confirm', '请确认是否要终止提货单' , {okCall: function() {

                BJUI.ajax('doajax', {
                    url: "/stockout/stop/",
                    data:{id : data[0].sysno},
                    type:"POST",
                    okCallback: function(json, options) {
                         stopStockout(data);
                            BJUI.navtab('refresh', 'navab278');
                    }
                })
                       
                }
            }); 
        });
    function stopStockout(data){
        BJUI.ajax('doajax', {
            url: "/stockout/stopJson/",
            data:{id : data[0].sysno},
            type:"POST",
            okCallback: function(json, options) {
                BJUI.alertmsg('info', '终止成功');
            }
        });
    }
    $("#custom_stockout_car").click(function(){
        var chks=$.CurrentNavtab.find("#stockoutlist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var checkdata  = $("#stockoutlist-table").data('selectedDatas');
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        if (checkdata.length != 1) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }  

        BJUI.navtab({
            id:'navtab01234',
            url:'/stockout/edit/type/addcar/id/'+checkdata[0].sysno,
            title:'更新出库车辆'
        });
    });

    $('#custom_stockout_view_btn').click(function() {
        var chks=$.CurrentNavtab.find("#stockoutlist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#stockoutlist-table").data('selectedDatas');
        if (data == '' || data == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        BJUI.navtab({
            id:'navtab01234',
            url:"/stockout/edit/type/view/id/"+data[0].sysno,
            title:'查看车出库订单'
        });
    });

    $('#custom_stockout_excel_btn').click(function(event) {

        var bar_no = $('#stockoutlist_bar_no').val();
        var begin_time = $('#stockoutlist_begin_time').val();
        var end_time = $('#stockoutlist_end_time').val();
        var bar_name = $('#stockoutlist_bar_name').val();
        var bar_stockoutstatus = $('#stockoutlist_bar_stockoutstatus').val();
        var bar_receivenumber = $('#stockoutlist_bar_receivenumber').val();
        var bar_goodsname = $('#stockoutlist_bar_goodsname option:selected').val();

        BJUI.ajax('ajaxdownload', {
            url:'/stockout/cardbtoexcel/',
            type:'POST',
            data:{bar_no:bar_no, begin_time:begin_time,end_time:end_time,bar_name:bar_name,bar_stockoutstatus:bar_stockoutstatus,bar_receivenumber:bar_receivenumber,bar_goodsname:bar_goodsname},
            successCallback: function(json, options) {
                
            }
        });
    });

    $("#custom_stockout_delay").click(function(){
        var data  = $("#stockoutlist-table").data('selectedDatas');
        if (data == '' || data == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if (data.length != 1) {
            BJUI.alertmsg('warn', "只能选择一条出库单进行延期",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        } 

        BJUI.ajax('doajax', {
            url: "/stockout/stockoutDelay/",
            data:{id : data[0].sysno},
            type:"POST",
            okCallback: function(json, options) {
                if(json.code == 300){
                    BJUI.alertmsg('warn', json.msg,{displayPosition:'middlecenter',displayMode:'fade'});
                    return false;
                }else if(json.code == 200){
                    BJUI.navtab({
                        id:'navtab01234',
                        url:"/stockout/edit/type/delay/id/"+data[0].sysno,
                        title:'出库单延期'
                    });
                }
            }
        })  
    });
</script>