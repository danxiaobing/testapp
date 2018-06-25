<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$('#contractlist2-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">业务期间:</label>
                <div class="row-input datawidth">
                    <input type="text" name="startdate" value="{{date('Y-m-d',strtotime('-1 months'))}}" data-toggle="datepicker" data-rule="required">
                </div>
                <div class="row-input datawidth">
                    <input type="text" name="enddate" value="{{date('Y-m-d',time())}}" data-toggle="datepicker" data-rule="required">
                </div>

                <label class="row-label">合同编号</label>
                <div class="row-input">
                    <input type="text" name="contractnodisplay" value="{{$contractnodisplay}}" placeholder="合同编号">
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
                    <select name="contracttype"  data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
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
                    <select name="saleemployee_sysno" data-toggle="selectpicker" data-width="100%"
                            data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($employee as $item)
                            <option value="{{$item['sysno']}}">{{$item['employeename']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">客服专员</label>
                <div class="row-input">
                    <select name="csemployee_sysno" data-toggle="selectpicker" data-width="100%"
                        data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($employee as $item)
                            <option value="{{$item['sysno']}}">{{$item['employeename']}}</option>
                        @endforeach
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
    <table class="table table-bordered" id="contractlist2-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            toolbarCustom:$('#pendcontractlist_tb'),
            addLocation: 'last',
            dataUrl: 'contract/pendlistJson',
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
            <th data-options="{name:'contractnodisplay',align:'center',width:120}">合同编号</th>
            <th data-options="{name:'contractdate',align:'center',width:120}">合同日期</th>
            <th data-options="{name:'customername',align:'center',width:280}">客户姓名</th>
            <th data-options="{name:'contractstartdate',align:'center',width:120}">合同起始日</th>
            <th data-options="{name:'contractenddate',align:'center',width:120}">合同终止日</th>
            <th data-options="{name:'contracttype',align:'center',width:120,render:function(value)
                   { if(value =='1') {return '长约'} else if(value=='2') {return '短约'}
                      else if(value =='3'){return '包罐'} else if(value =='4') {return '包罐容'}else if(value =='5') {return '靠泊装卸'}}}">租罐方式</th>
            <th data-options="{name:'goodsname',align:'center',width:120}">品名</th>
            <th data-options="{name:'unitname',align:'center',width:120}">计量单位</th>
            <th data-options="{name:'goodsnature',align:'center',width:120,render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'}
                else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'} else {return '未限制'}}}">货物性质</th>
            <th data-options="{name:'saleemployeename',align:'center',width:120}">业务员</th>
            <th data-options="{name:'csemployeename',align:'center',width:120}">客服专员</th>
            <th data-options="{name:'contractstatus',align:'center',width:120,render:function(value)
                    {if(value=='1') {return '新建'} else if(value=='2') {return '暂存'}
                    else if(value=='3') {return '评审中'} else if(value=='4') {return '待审核'}
                    else if(value=='5') {return '已审核'} else if(value=='6') {return '退回'}else if(value=='7') {return '作废'}}}">单据状态</th>
        </tr>
        </thead>
    </table>
</div>

<div id="pendcontractlist_tb">
    <button type="button" id="pend" class="btn btn-green" data-icon="edit" onclick="auditcontract()"><i class="fa fa-gavel"></i> 审核</button>
</div>

<script type="text/javascript">
    function auditcontract() {
        BJUI.navtab('closeTab','navab242');
        BJUI.navtab('closeTab','navab243');
        var data = $.CurrentNavtab.find("#contractlist2-table").data('selectedDatas');
        if(data == ''||data == null){
            BJUI.alertmsg('warn', '请先选中要审核的合同再审核');
        }else if(data.length>=2){
            BJUI.alertmsg('warn', '只能选择一条合同审核');
        }else{
            var sysno = data[0]['sysno'];
            var zuguantype = data[0]['contracttype'];
            //
            var navid = 'navab488';
            if(zuguantype == 1||zuguantype == 2){
                navid = 'navab243';
            }else if(zuguantype == 3||zuguantype == 4){
                navid = 'navab242';
            }
            BJUI.navtab({
                id:navid,
                url:'/contract/list/mode/audit/id/'+sysno+'/zuguantype/'+zuguantype,
                title:'审核合同',
            })
        }
    }

</script>