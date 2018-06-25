<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#supplementlist-table')}">
        <fieldset>
            <input type="hidden" name="bar_type" value="1" placeholder="入库单类型">
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-4">

                <label class="row-label">业务期间</label>
                <div class="row-input datawidth">
                    <input type="text" id='supplementdate' name="supplementdate" value="" data-toggle="datepicker" placeholder="业务时间"></div>

                <label class="row-label">客户</label>
                <div class="row-input">
                    <select name="customer_sysno" id="customer_sysno"
                            data-size="5" data-toggle="selectpicker" data-live-search="true" data-rule="" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}"
                                    @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">品名</label>
                <div class="row-input">
                    <input type="text" id='goodsname' name="goodsname" value="" placeholder="品名">
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                        <select id='supplementstatus' name="supplementstatus" data-toggle="selectpicker" data-width="100%" >
                            <option value="" selected="">不限</option>
                            <option value="2">暂存</option>
                            <option value="3">待审核</option>
                            <option value="4">已审核</option>
                            <option value="6">退回</option>
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
    <table class="table table-bordered" id="supplementlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'100',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'del_stocklist',
        toolbarCustom:'#custom_reback_tb',
        addLocation: 'last',
        dataUrl: '/supplement/listJson',
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
            <th data-options="{name:'supplementno',align:'center'}">补充单号</th>
            <th data-options="{name:'supplementdate',align:'center',render:function(value){return value.substr(0,10)}}">单据日期</th>
            <th  data-options="{name:'customername',align:'center'}">客户</th>
            <th  data-options="{name:'stockinno',align:'center'}">入库单号</th>
            <th  data-options="{name:'goodsname',align:'center'}">品名</th>
            <th  data-options="{name:'shipname',align:'center'}">船名</th>
            <th data-options="{name:'beqty',align:'center'}">补充数量（KG）</th>
            <th data-options="{name:'storagetankname',align:'center'}">储罐名称</th>
            <th data-options="{name:'supplementtype',align:'center',render:function(value){if(value==1){return '补充入库'} else if(value==2) {return '扣减入库' } }}">补充类型</th>
            <th data-options="{name:'supplementstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'}  else if(value=='4') {return '已审核'}  else if(value=='6') {return '退回'} else  {return '新建'}}}">单据状态</th>
        </tr>
        </thead>
    </table>

</div>
<div id="custom_reback_tb">
    <button type="button" id="supplement_view_btn" data-icon="eye" class="btn btn-blue">查看</button>
    <button type="button" id="supplement_edit_btn" data-icon="edit" class="btn btn-green">编辑</button>
    <button type="button" id="supplement_audit_btn" data-icon="gavel" class="btn btn-red">审核</button>
    <button type="button" id="supplement_del_btn" data-icon="del" class="btn btn-red">删除</button>

</div>


<script type="text/javascript">
    //编辑
    $("#supplement_edit_btn").click(function(){

        var data  = $("#supplementlist-table").data('selectedDatas');
        console.log(data);
        if (data == '' || data == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if (data.length != 1) {
            BJUI.alertmsg('warn', "请选择一条单据",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(data[0].supplementstatus==3){
            BJUI.alertmsg('warn', "只能编辑暂存状态的数据",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }


        BJUI.navtab({
            id:'navtab-supplement-001',
            url:"/supplement/edit/mode/edit/id/"+data[0].sysno,
            title:'编辑补充单'
        });


    });

    //审核
    $("#supplement_audit_btn").click(function () {
        var checkdata = $('#supplementlist-table').data('selectedDatas');
        console.log(checkdata);
        if (checkdata == '' || checkdata == null) {
            BJUI.alertmsg('warn', '请先选中一行数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }

        if (checkdata[0].supplementstatus !=3 ) {
            BJUI.alertmsg('warn', '必须选择待审核的数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
        if (checkdata.length == 1) {
            BJUI.navtab({
                id: 'auditsupplement',
                url: '/supplement/edit/mode/audit/id/' + checkdata[0].sysno,
                type: 'post',
                mask: true,
                data: {'supplementstatus': checkdata[0].supplementstatus},
                title: '审核补充订单'
            });
        } else if (checkdata.length > 1) {
            BJUI.alertmsg('warn', '不能审核多条数据', {displayPosition: 'middlecenter', displayMode: 'fade'});
            return false;
        }
    });
    //删除
    $("#supplement_del_btn").click(function () {
        var selectdata  =  $.CurrentNavtab.find('#supplementlist-table').data('selectedDatas');
        if (selectdata == ''||selectdata == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        if (selectdata.length !=1) {
            BJUI.alertmsg('warn','只能选择一条数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        if(selectdata[0].supplementstatus ==4){
            BJUI.alertmsg('warn','<h4>已审核的单据不能删除!</h4>');
            return;
        }

        BJUI.alertmsg('confirm','确认删除吗?',{
            okCall : function(){
                BJUI.ajax('doajax', {
                    url: 'supplement/delete',
                    type:'POST',
                    data: {selectdata:selectdata},
                    loadingmask: true,
                    okCallback: function(json, options) {
                        BJUI.navtab('refresh','navab566');
                        console.log('返回内容：\n'+ JSON.stringify(json))
                    }
                })
            }
        })
    });
    //查看
    $('#supplement_view_btn').click(function() {

        var data  = $("#supplementlist-table").data('selectedDatas');
        if (data == '' || data == null) {
            BJUI.alertmsg('warn', "未选中任何行",{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }

        BJUI.navtab({
            id:'navtab-reback-001',
            url:"/supplement/edit/mode/view/id/"+data[0].sysno,
            title:'查看补充单'
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
</script>