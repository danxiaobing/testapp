<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="boshiplist-bar" data-options="{searchDatagrid:$.CurrentNavtab.find('#boshiplist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">预约单号</label>
                <div class="row-input">
                    <input type="text" id='boshiplist_bookingoutno' name="bookingoutno" data-toggle="" value="" placeholder="船出库预约单号">

                </div>

                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" id='boshiplist_begin_time' name="begin_time" value="" data-toggle="datepicker" placeholder="预约开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" id='boshiplist_end_time' name="end_time" value="" data-toggle="datepicker" placeholder="预约结束时间"></div>

                <label class="row-label">客户</label>
                <div class="row-input">
                    <input type="text" id='boshiplist_bar_name' name="bar_name" value="{{$bar_name or ''}}" placeholder="客户名称">
                </div>
                
                <label class="row-label">提货单号</label>
                <div class="row-input">
                    <input type="text" id='boshiplist_bar_receivenumber' name="bar_receivenumber" value="{{$bar_receivenumber or ''}}" placeholder="提货单号">
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="bar_goodsname" id='boshiplist_bar_goodsname' data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['goodsname']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div> 

                <label class="row-label">单据来源</label>
                <div class="row-input">
                    <select id='boshiplist_bar_docsource' name="bar_docsource" data-toggle="selectpicker"  data-width="100%">
                        <option value="">全部</option>
                        <option value="1">手工创建</option>
                        <option value="2">国烨云仓</option>
                    </select>
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select id='boshiplist_bar_status' name="bar_status" data-toggle="selectpicker"  data-width="100%">
                        <option value="">全部</option>
                        <option value="2">暂存</option>
                        <option value="4">待审核</option>
                        <option value="5">已审核</option>
                        <option value="6">已完成</option>
                        <option value="7">退回</option>
                        <option value="8">驳回</option>
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
    <table class="table table-bordered" id="boshiplist-table" data-toggle="datagrid" data-options="{
        tableWidth:'2500',
        height: '100%',
        showToolbar: true,
        toolbarItem: 'edit,|,del',
        toolbarCustom:'#custom_boship_tb',
        addLocation: 'last',
        dataUrl: '/bookout/shiplistJson',
        dataType: 'json',
        editMode: {navtab:{title:'船出库预约单编辑',id:'navab224'}},
        editUrl: '/bookout/shipedit/id/{sysno}',
        delUrl:'/bookout/deljson',
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
                <th data-options="{name:'bookingoutno',align:'center'}">预约单号</th>
                <th data-options="{name:'docsource',align:'center',render:function(value){switch(value) { case '1': return  '手工创建'; case '2': return '国烨云仓';} }}">单据来源</th>
                <th  data-options="{name:'receivestart',align:'center'}">提货开始日</th>
                <th  data-options="{name:'receiveend',align:'center'}">提货结束日</th>
                <th  data-options="{name:'receiveover',align:'center',render:function(value){if(value=='1') {return '是'} else  {return '否'}}}">是否逾期</th>
                <th data-options="{name:'customer_name',align:'center'}">客户</th>
                <th  data-options="{name:'receiveunitname',align:'center'}">提货单位</th>
                <th  data-options="{name:'receivenumber',align:'center'}">提货单号</th>
                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                <th data-options="{name:'qualityname',align:'center'}">规格</th>
                <th  data-options="{name:'goodsnature',align:'center',render:function(value){switch(value) { case '1': return  '保税'; case '2': return '外贸'; case '3': return '内贸转出口'; case '4': return '内贸内销';} }}">货物性质</th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'bookingoutqty',align:'center'}">提货数量</th>
                <!-- <th  data-options="{name:'bookingoutqty',align:'center',width:200}">通知数量</th> -->
                
                <th  data-options="{name:'cs_employeename',align:'center'}">客服</th>
                <th  data-options="{name:'shipname',align:'center'}">船名</th>
                <!-- <th  data-options="{name:'shipproxyname',align:'center',width:50}">船舶代理</th> -->
                
                <th data-options="{name:'bookingoutstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待确认'} else if(value=='4') {return '待审核'} else if(value=='5') {return '已审核'} else if(value=='6') {return '已完成'} else if(value=='7') {return '退回'} else if(value=='8') {return '驳回'}else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="custom_boship_tb">
    <button type="button" class="btn btn-blue" data-icon="eye"  id="custom_boship_view_btn">查看</button>
    <button type="button" class="btn btn-green" data-icon="filter"  id="custom_boship_addatt_btn">附件</button>
    <button type="button" class="btn btn-green" data-icon="sign-out"  id="custom_boship_view_export">EXCEL导出</button>
</div>

<script type="text/javascript">
        $('#custom_boship_view_btn').click(function() {
            var chks=$.CurrentNavtab.find("#boshiplist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#boshiplist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.navtab({
                id:'navab224',
                url:"/bookout/shipedit/type/view/id/"+data[0].sysno,
                title:'船出库预约单查看'
            });

        });

        $('#custom_boship_view_export').click(function(event) {
            var bookingoutno = $('#boshiplist_bookingoutno').val();
            var begin_time = $('#boshiplist_begin_time').val();
            var end_time = $('#boshiplist_end_time').val();
            var bar_name = $('#boshiplist_bar_name').val();
            var bar_status = $('#boshiplist_bar_status').val();
            var bar_docsource = $('#boshiplist_bar_docsource').val();
            var bar_receivenumber = $('#boshiplist_bar_receivenumber').val();
            var bar_goodsname = $('#boshiplist_bar_goodsname option:selected').val();

            BJUI.ajax('ajaxdownload', {
                url:'/bookout/shipdbtoexcel/',
                type:'POST',
                data:{bookingoutno:bookingoutno, begin_time:begin_time,end_time:end_time,bar_name:bar_name,bar_status:bar_status,bar_docsource:bar_docsource,bar_receivenumber:bar_receivenumber,bar_goodsname:bar_goodsname},
                successCallback: function(json, options) {

                }
            });
        });

        $('#custom_boship_addatt_btn').click(function() {
            var chks=$.CurrentNavtab.find("#boshiplist-table");
            if(chks.length<1)
            {
                BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var data  = $("#boshiplist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            if (data.length != 1) {
                BJUI.alertmsg('warn','不要选择多行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                return false;
            }
            BJUI.navtab({
                id:'navab224',
                url:"/bookout/shipedit/type/addatt/id/"+data[0].sysno,
                title:'船出库预约附件添加'
            });

        });
</script>