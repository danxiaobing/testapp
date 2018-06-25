<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" id="berth-history-from" action="{{$action}}" data-options="{searchDatagrid:$.CurrentNavtab.find('#berth-history-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">业务区间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="begin_time" id="begin_time" value="{{$begin_time or ''}}" data-toggle="datepicker" placeholder="开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="end_time" id="end_time" value="{{$end_time or ''}}" data-toggle="datepicker" placeholder="结束时间" ></div>

                <label class="row-label">码头：</label>
                <div class="row-input">
                    <select name="wharfname" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($wharf as $item)
                            <option value="{{$item['sysno']}}">{{$item['wharfname']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">业务类型:</label>
                <div class="row-input">
                    <select name="businesstype" data-toggle="selectpicker"  data-width="100%" class="show-tick" data-live-search="true" data-size='10'>
                        <option value="">请选择</option>
                        @foreach($businesstype as $key=>$item)
                            <option value="{{$key}}">{{$item}}</option>
                        @endforeach
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
    <table class="table table-bordered" id="berth-history-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'export',
        dataUrl: 'berth/historyJson/id/{{$id}}',
		exportOption: {type:'file', options:{url:'/berth/historyExcel/id/{{$id}}',form:$('#berth-history-from')}},
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:12},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'berthno',align:'center'}">泊位号</th>
            <th data-options="{name:'wharfname',align:'center'}">码头</th>
            <th data-options="{name:'shipname',align:'center'}">船名</th>
            <th  data-options="{name:'businesstype',align:'center',render:function(value){if(value==1){ return '船入库预约'; }else if(value==2){return '船入库订单'}else if(value==3){return '车入库预约'}else if(value==4){return '车入库订单'}else if(value==5){return '管入库预约'}else if(value==6){return '管入库订单'}else if(value==7){return '船出库预约'}else if(value==8){return '船出库订单'}else if(value==9){return '车出库预约'}else if(value==10){return '车出库订单'}else if(value==11){return '管出库预约'}else if(value==12){return '管出库订单'}else if(value==13){return '靠泊装卸'}else if(value==14){return '靠泊装卸出预约'}else if(value==15){return '靠泊装卸入订单'}else if(value==16){return '靠泊装卸出订单'}  } }">
                业务单据类型</th>
            <th data-options="{name:'businessno',align:'center',render:function(value,data){if(data.stockno){return data.stockno;}else{return data.bookingno;}}}">
                业务单号</th>
            <th data-options="{name:'usetime',align:'center',type:'date',pattern:'yyyy-MM-dd'}">使用时间</th>
            <th data-options="{name:'goodsname',align:'center' }">输送品种</th>
            <th data-options="{name:'created_employeename',align:'center'}">操作人</th>
            <th data-options="{name:'orderstatus',align:'center',render:berth_operation }">操作</th>
        </tr>
        </thead>
    </table>
</div>

<script>
    function berth_operation(value,data) {
        return '<button type="button" class="btn-green" data-toggle="datagrid.tr" onclick="look_berthdoc('+data.business_sysno+','+data.businesstype+')">查看单据</button>';
    }

    function look_berthdoc(business_sysno,businesstype)
    {
        var url = '';
        var dataname = 'id';
        var title = '';
        switch(businesstype)
        {
            case 1:
                url = '/bookshipin/show/id/'+business_sysno;
                title = '船入库预约单';
                break;
            case 2:
                url = '/stockshipin/show/id/'+business_sysno;
                title = '船入库订单';
                break;
            case 5:
                url = '/bookpipelinein/show';
                title = '管入库预约单';
                break;
            case 6:
                url = '/stockpipein/edit/type/eye/id/' + business_sysno;
                dataname = 'booking_sysno';
                title = '查看管入库订单';
                break;
            case 7:
                url = "/bookout/shipedit/type/view/id/"+business_sysno,
                        title = '船出库预约单查看';
                break;
            case 8:
                url="/stockout/shipedit/type/view/id/"+business_sysno;
                title='船出库订单查看';
                break;
            case 11:
                url = "/bookout/pipelineEdit/id/"+business_sysno+"/type/view";
                title= '管出库预约单查看';
                break;
            case 12:
                url = "/stockout/pipelineEdit/type/view/id/"+business_sysno;
                title ='管出库订单查看';
                break;
            case 13:
                url = '/bookberthin/edit/mode/eye/id/' + business_sysno;
                title = '靠泊装货预约单查看';
                break;
            case 14:
                url = '/bookberthout/edit/mode/eye/id/' + business_sysno;
                title = '靠泊卸货预约单查看';
                break;
            case 15:
                url = '/stockberthin/edit/mode/eye/id/' + business_sysno;
                title = '靠泊装货订单查看';
                break;
            case 16:
                url = '/stockberthout/see/mode/eye/id/' + business_sysno;
                itle = '靠泊卸货订单查看';
                break;
        }
        if(url==''){
            BJUI.alertmsg('warn','哎呀,该单据无法查看!');
            return;
        }
        BJUI.navtab({
            id:'berth-history-id',
            mask:true,
            type:'POST',
            url:url,
            data:{id:business_sysno},
            title:title,
        });
    }
</script>