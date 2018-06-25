<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#checklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">盘点日期</label>
                <div class="row-input">
                    <input type="text" name="checkrecorddate" value="{{$checkrecorddate or ''}}" id="check_created_at" data-toggle="datepicker" placeholder="盘点日期">
                </div>

                <label class="row-label">储罐编号:</label>
                <div class="row-input">
                    <select name="storagetank_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="" >全部</option>
                        @foreach($tanklist as $item)
                            <option value="{{$item['sysno']}}">{{$item['storagetankname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">货品名称:</label>
                <div class="row-input">
                    <select name="goodsname" data-size="5" data-toggle="selectpicker" data-live-search="true"
                            data-width="100%">
                        <option value="" selected="">全部</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['goodsname']}}">{{$item['goodsname']}}</option>
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
    <table class="table table-bordered" id="checklist-table" data-toggle="datagrid" data-options="{
    tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarItem: 'add,|,edit,|,del',
            toolbarCustom:'#stock_check_tb',
            addLocation: 'last',
            dataUrl: 'check/listJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            editMode: {navtab:{title:'库存盘点单信息',id:'navab290'}},
            editUrl: '/check/edit/mode/edit/id/{sysno}',
            delUrl:'/check/deljson',
            delPK:'sysno',
            paging: {pageSize:20},
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true
        }">
        <div id="stock_check_tb">
            <button id="show_check_btn" class="btn btn-blue"><i class="fa fa-eye" aria-hidden="true">&nbsp;&nbsp;查看</i></button>
            <button type="button" class="btn btn-green" data-icon="gavel"  id="examine_check_btn">审核</button>
            <button type="button" class="btn btn-red"  id="stock_check_cancel" data-icon="scissors">作废</button>
        </div>

        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center',width:100}">储罐编号</th>
            <th data-options="{name:'storagetanknature',align:'center',width:100,render:function(value){if(value=='1') {return '内贸罐'} else if(value=='2') {return '外贸罐'}else if(value=='3') {return '保税罐'} } }">储罐性质</th>
            <th data-options="{name:'goodsname',align:'center',width:100}">货品名称</th>
            <th data-options="{name:'checkrecorddate',align:'center',width:100}">盘点日期</th>
            <th data-options="{name:'temperature',align:'center',width:100}">温度(°)</th>
            <th data-options="{name:'liquid',align:'center',width:100}">液位(m)</th>
            <th data-options="{name:'rulerqty',align:'center',width:100}">打尺量(吨)</th>
            <th data-options="{name:'rulerdate',align:'center',width:100}">打尺时间</th>
            <th data-options="{name:'ischecked',align:'center',width:100,render:function(value){if(value=='1') {return '合格'} else if(value=='2') {return '不合格'}}}">品质检查</th>
            <th data-options="{name:'employeename',align:'center',width:100}">操作人员</th>
            <th data-options="{name:'stockcheckstatus',align:'center',width:70,render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='5') {return '作废'} else if(value=='6') {return '退回'} else  {return '新建'}}}">单据状态</th>
{{--            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">储罐id</th>
            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品名id</th>
            <th data-options="{name:'created_employee_sysno',align:'center',hide:true}">操作员id</th>--}}
        </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">
    $("#examine_check_btn").click(function() {
        var data  = $("#checklist-table").data('selectedDatas');
        if(typeof(data)=='undefined' || data=='' || data==null) {
            BJUI.alertmsg('warn', '未选中任何行！');
        }
        if(data[0].stockcheckstatus != 3){
            BJUI.alertmsg('warn','待审核状态才能审核');
            return;
        }else{
            BJUI.navtab({
                id: 'navab290',
                url: '/check/edit/mode/audit/id/' + data[0].sysno,
                title: '审核盘点单'
            });
        }
    });

    $("#stock_check_cancel").click(function(){
        var data  = $("#checklist-table").data('selectedDatas');
        if(data=='' || data==null) {
            BJUI.alertmsg('warn', '未选中任何行！');
        }else if(data[0].stockcheckstatus != 4){
            BJUI.alertmsg('warn','已审核状态才能作废');
            return;
        }else{
            BJUI.navtab({
                id: 'navab290',
                url: '/check/edit/mode/abolish/id/' + data[0].sysno,
                type: 'post',
                data: {'id': data[0].sysno},
                title: '作废'
            });
        }
    });

    $('#check_excel_btn').click(function(event) {
        var created_at = $('#check_created_at').val();
        var updated_at = $('#check_updated_at').val();
        var bar_stockcheckstatus = $('#check_stockcheckstatus').val();

        BJUI.ajax('ajaxdownload', {
            url:'/check/checktoexcel/',
            type:'POST',
            data:{created_at:created_at,updated_at:updated_at,bar_stockcheckstatus:bar_stockcheckstatus},
            successCallback: function(json, options) {
               // console.log(123);
            }
        });
    });

    $("#show_check_btn").click(function (){
        var data = $('#checklist-table').data('selectedDatas');
        if(data=='' || data==null) {
            BJUI.alertmsg('warn', '未选中任何行！');
        }else {
            BJUI.navtab({
                id: 'navab290',
                url: '/check/checksee/id/' + data[0].sysno,
                type: 'post',
                data: {'id': data[0].sysno},
                title: '查看'
            });
        }
    });

</script>