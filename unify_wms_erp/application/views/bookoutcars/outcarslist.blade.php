<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#outcarslist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" value="{{$begin_time or ''}}" data-toggle="datepicker" placeholder="预约开始时间">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" value="{{$end_time or ''}}" data-toggle="datepicker" placeholder="预约结束时间">
                </div>

                <label class="row-label">客户名称:</label>
                <div class="row-input">
                    <input type="hidden" id="outcarcustomername" name="customername" value="">
                    <select id="outcarcusid" name="customer_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">车牌号:</label>
                <div class="row-input">
                    <input type="text" name="carid" value="{{$carid or ''}}" placeholder="车牌号">
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
    <table class="table table-bordered" id="outcarslist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100%',
        height: '100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:'#custom_outcarslist_tb',
        addLocation: 'last',
        dataUrl: '/bookoutcars/getListJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        hScrollbar:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'stockoutno',align:'center',width:200}">车出库订单号</th>
            <th data-options="{name:'storagetankname',align:'center',hide:'true'}">罐号</th>
            <th data-options="{name:'storagetank_sysno',align:'center',hide:'true'}">罐号ID</th>
            <th data-options="{name:'customername',align:'center'}">客户</th>
            <th data-options="{name:'takegoodsno',align:'center'}">提单号</th>
            <th data-options="{name:'takegoodscompany',align:'center'}">提货单位</th>
            <th data-options="{name:'receivestart',align:'center'}">提货开始日</th>
            <th data-options="{name:'receiveend',align:'center'}">提货结束日</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'qualityname',align:'center'}">规格</th>
            <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">货物性质</th>
        </tr>
        </thead>
    </table>
</div>
<div id="custom_outcarslist_tb">
    <button type="button" class="btn btn-green"  id="custom_outcar_view_btn" data-icon="plus">生成出库磅码单</button>
</div>

<script type="text/javascript">
    $("#outcarcusid").change(function (){
        $("#outcarcustomername").val($("#outcarcusid option:selected").text())
    });

    $("#custom_outcar_view_btn").click(function() {
        var data  = $("#outcarslist-table").data('selectedDatas');
        var  commitCarStatus = true;
        var  commitGoodStatus = true;
        var  commitStorageStatus = true;
        var chks = $.CurrentNavtab.find("#outcarslist-table");
        if(chks.length < 1)
        {
            BJUI.alertmsg('warn','未选中任何行');
            return false;
        }
        if (data == undefined || data=='') {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
        }else{
            var sysnoList = new Array();
            $.each(data,function (m, n){
                sysnoList[m] = [n.sysno, n.carid, n.goodsname,n.stockoutno,n.stockout_sysno,n.storagetank_sysno];
            })
            var count = sysnoList.length;
            if(count>4){
                BJUI.alertmsg('warn','最多选择4条数据!');
                return;
            }
            var type = false;
            console.log(sysnoList);
            if(count > 1){
                for (var i = 0;i< count-1; i++ ){
                    if(sysnoList[i][1] != sysnoList[i+1][1]){
                        commitCarStatus = false;
                    }
                    if(sysnoList[i][2] != sysnoList[i+1][2]){
                        commitGoodStatus = false;
                    }
                    if(sysnoList[i][5] != sysnoList[i+1][5]){
                        commitStorageStatus = false;
                    }
                }
                type =true;
                // commitCarStatus = false;
            }
            if( !commitCarStatus ){
                BJUI.alertmsg('warn', '不能合单!');
            }else if(!commitGoodStatus){
                BJUI.alertmsg('warn', '同一货品才能合单');
            //} else if(!commitStorageStatus){
            //    BJUI.alertmsg('warn', '同一储罐才能合单');
            }else {
                BJUI.navtab({
                    id : 'navtab_out_car',
                    url: '/bookoutcars/carEdit?'+Math.random(),
                    type:'POST',
                    data:{sysnoList:sysnoList,type:type},
                    title: '车出库核单',
                    width: 1300,
                    height: 800
                });
            }
        }
    });
</script>
