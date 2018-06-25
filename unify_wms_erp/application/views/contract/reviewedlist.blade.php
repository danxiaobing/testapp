<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#reviewedlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">合同编号</label>
                <div class="row-input ">
                    <input type="text" name="contractnodisplay" value="" placeholder="请输入合同编号">
                </div>

                <label class="row-label">业务期间：</label>
                <div class="row-input datawidth">
                    <input type="text" name="startDate" value="{{$startDate or ''}}" data-toggle="datepicker" data-rule="date" placeholder="开始时间" />
                </div>
                 <div class="row-input datawidth">
                 	<input type="text" name="endDate" value="{{$endDate or ''}}" data-toggle="datepicker" data-rule="date" placeholder="结束时间" />
                 </div>
                <label class="row-label">客户：</label>
                <div class="row-input">
                    <input type="hidden" name="obj.customerId" value="{{$customerId}}" />
                    <input type="text" name="obj.customername" value="{{$customername}}" readonly data-toggle="findgridbtn" data-options="{
                        group: 'obj',
                        include: 'customername:customername, customerId:sysno',
                        dialogOptions: {width:'800',height:'500',title:'客户资料',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',                       
                            local: 'local',
                            paging: {pageSize:20},
                            dataUrl: '/customer/listAllJson',
                            columns: [
                                {name:'sysno', label:'id'},
                                {name:'customername', label:'客户名称'},
                                {name:'customerabbreviation', label:'客户简称'}
                            ],
                            showLinenumber:false
                        },
                    }" placeholder="点击查找">
                </div>
                <label class="row-label">单据状态：</label>
                <div class="row-input">
                    <select name="contStatus" data-toggle="selectpicker" data-width="100%" data-live-search="true" data-size="10">
                        <option value="">请选择</option>
                        @foreach($statusarr as $key=>$item)
                            <option value="{{$key}}" @if($key == $contStatus) selected @endif>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                    
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="reviewedlist-table" data-toggle="datagrid" data-options="{
    	height:'100%',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom:$.CurrentNavtab.find('#reviewed_tb'),
        dataUrl: '/contract/reviewedJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode:false,
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
<!--                 <th data-options="{name:'contractno',align:'center'}">合同编号</th>
                <th data-options="{name:'customername',align:'center'}">客户</th>
                <th data-options="{name:'contracttype',align:'center',render:function(value){
                	if(value == '1') return '长约'; else if(value == '2') return '短约'; else return'包罐';}}">租罐方式</th>
                <th data-options="{name:'contractdate',align:'center'}">合同时间</th>
                <th data-options="{name:'saleemployeename',align:'center'}">业务员</th>
                <th data-options="{name:'csemployeename',align:'center'}">客服</th> -->

                <th data-options="{name:'contractnodisplay',align:'center'}">合同编号</th>
                <th data-options="{name:'contractdate',align:'center'}">合同日期</th>
                <th data-options="{name:'customername',align:'center'}">客户</th>
                <th data-options="{name:'contractstartdate',align:'center'}">合同起始日</th>
                <th data-options="{name:'contractenddate',align:'center'}">合同终止日</th>
                <th data-options="{name:'contracttype',align:'center',render:function(value){
                    if(value == '1') return '长约'; else if(value == '2') return '短约'; else if(value=='3') return '包罐'; else if(value==4) return '包罐容'; else if(value==5) return '靠泊装卸';}}">租罐方式</th>
                <th data-options="{name:'goodsname',align:'center'}">品名</th>
                <th data-options="{name:'',align:'center',render:function(value){ if(value=='') return '吨'; }}">计量单位</th>
                <th data-options="{name:'goodsnature',align:'center',render:function(value){ if(value=='1') return '保税'; else if(value == '2') return '外贸'; else if(value == '3') return '内贸转出口'; else return '内贸内销'; }}">货物性质</th>
                <th data-options="{name:'saleemployeename',align:'center'}">业务员</th>
                <th data-options="{name:'csemployeename',align:'center'}">客服</th>
                <th data-options="{name:'contractstatus',align:'center',width:100,render:function(value)
                        {if(value=='1') {return '新建'} else if(value=='2') {return '暂存'}
                        else if(value=='3') {return '评审中'} else if(value=='4') {return '待审核'}
                        else if(value=='5') {return '已审核'} else if(value=='6') {return '退回'} else if(value=='7') {return '作废'} }}">单据状态</th>  

            </tr>

        </thead>
    </table>
</div>
<div id="reviewed_tb">
    <button type="button" class="btn btn-blue" data-icon="eye" id="reviewed_btn">查看</button>
</div>
<script type="text/javascript">
    $('#reviewed_btn').click(function(){
        var checkdata=$('#reviewedlist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            BJUI.dialog({
                width:'1024',
                height:'768',
                id:'reviewed_btn',
                url:'/contract/reviewedit/id/'+checkdata[0].sysno+"/reved/"+1,
                title:'合同评审',
                mask:true,
            });
        }else{
            BJUI.alertmsg('info','<h4>请选数据！</h4>');
        }
    });

</script>