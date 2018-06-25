<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;">
    <form data-toggle="ajaxsearch" id="introduce-auditlist-bar" data-options="{searchDatagrid:$.CurrentNavtab.find('#introduce-auditlist-table')}">
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
                    <select name="customer_sysno" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">全部</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}">{{$item['customername']}}</option>
                        @endforeach
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
    <table class="table table-bordered" id="introduce-auditlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'2500',
        height: '100%',
        showToolbar: true,
        toolbarCustom:'#introduce_auditlist_tb',
        addLocation: 'last',
        dataUrl: '/introduce/auditListJson',
        dataType: 'json',
        editMode: {navtab:{title:'提单审核',id:'introduce001'}},
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
                <th data-options="{name:'introductionno',align:'center'}">单据编号</th>
                <th data-options="{name:'introductiondate',align:'center'}">创建时间</th>
                <th  data-options="{name:'introductiontype',align:'center',render:function(value){if(value=='1') {return '可撤销'} else  {return '不可撤销'}}}">提单类型</th>
                <th data-options="{name:'customername',align:'center'}">开单公司</th>
                <th  data-options="{name:'takegoodsno',align:'center'}">提货单号</th>
                <th data-options="{name:'sale_customername',align:'center'}">转入方</th>
                <th data-options="{name:'buy_customername',align:'center'}">转出方</th>
                <th  data-options="{name:'receivestart',align:'center'}">提货开始日</th>
                <th  data-options="{name:'receiveend',align:'center'}">提货结束日</th>
                <th  data-options="{name:'freecostdate',align:'center'}">免仓期</th>
                <th  data-options="{name:'takegoodsnum',align:'center'}">提单数量(吨)</th>
                <th  data-options="{name:'bookingqty',align:'center',render:function(value){if(value=='0') {return '0.000'}}}">预约提货量(吨)</th>
                <th  data-options="{name:'takegoodsqty',align:'center'}">实提数量(吨)</th>
                <th  data-options="{name:'untakegoodsnum',align:'center'}">结存量(吨)</th>
                <th data-options="{name:'introductionstatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '提货中'} else if(value=='5') {return '已完成'} else if(value=='6') {return '退回'} else if(value=='7') {return '已撤销'} else if(value=='8') {return '驳回'}else  {return '新建'}}}">单据状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="introduce_auditlist_tb">
    <button type="button" class="btn btn-green" data-icon="filter"  id="introduce_audit">审核</button>
</div>

<script type="text/javascript">
        $("#introduce_audit").click(function() {
            var data  = $("#introduce-auditlist-table").data('selectedDatas');
            if (data == ''||data == null) {
                BJUI.alertmsg('warn','未选中任何行！');
                return false;
            }
            if(data.length > 1){
                BJUI.alertmsg('warn','请选择一条数据！');
                return false;
            }

            BJUI.navtab({
                id: 'introduce001',
                url: '/introduce/edit/type/audit/id/'+data[0].sysno,
                title: '审核提单'
            });

        });
</script>