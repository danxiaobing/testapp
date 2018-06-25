<div class="bjui-pageHeader " style="background-color:#fefefe; border-bottom:none;padding: 0;">
    <form data-toggle="ajaxsearch" id='booktrans' data-options="{searchDatagrid:$.CurrentNavtab.find('#booktrans-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">
                <label class="row-label">预约日期</label>
                <div class="row-input datawidth">
                    <input type="text" name="bookingtransdate_start" value="{{$created_at or ''}}" data-toggle="datepicker" placeholder="转移开始时间"></div>
                <div class="row-input datawidth">
                    <input type="text" name="bookingtransdate_end" value="{{$updated_at or ''}}" data-toggle="datepicker" placeholder="转移结束时间"></div>
                <label class="row-label">转让方</label>
                <div class="row-input">                   
                    <select name="sale_customer_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                 <label class="row-label">受让方</label>
                <div class="row-input">                   
                    <select name="buy_customer_sysno" data-size="5"  data-toggle="selectpicker" data-live-search="true" data-width="100%" >
                        <option value="">请选择</option>
                        @foreach($customerlist as $item)
                            <option value="{{$item['sysno']}}" @if($item['sysno'] == $customer_sysno) selected @endif>{{$item['customername']}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="row-label">单据状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bookingtransstatus">
                        <option value="" selected="">不限</option>
                        <option value="1">新建</option>
                        <option value="2">暂存</option>
                        <option value="3">已提交</option>
                        <option value="4">已审核</option>
                        <option value="5">已完成</option>
                        <option value="6">作废</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                </div>

            </div>
        </fieldset>
    </form>

</div>
<div class="bjui-pageContent clearfix">

    <table class="table table-bordered" id="booktrans-list-table" data-toggle="datagrid" data-options="{        
        tableWidth:'99%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh,|,export',
        addLocation: 'last',
        dataUrl: 'booktrans/booklistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {navtab:{title:'编辑预约',id:'navab236'}},
        editUrl: '/booktrans/edit/id/{sysno}',
        delUrl:'/booktrans/delJson',
        delPK:'sysno',
        exportOption: {type:'file', options:{url:'/booktrans/export',form:$('#booktrans')}},
        paging: {pageSize:20},
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true
        
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'bookingtransno',align:'center',width:200}">单据编号</th>
            <!-- 
            <th data-options="{name:'goodsname',align:'center',width:200}">货品名称</th>
             -->
            <th data-options="{name:'sale_customername',align:'center',width:100}">转让方名称</th>
            <th data-options="{name:'buy_customername',align:'center',width:100}">受让方名称</th>
            <th data-options="{name:'bookingtransdate',align:'center',width:100}">预约日期</th>
            <!-- 
            <th data-options="{name:'isclearstock',align:'center',width:100,render:function(value){return (value==1)?'是':'否';}}">船名</th>            
            <th data-options="{name:'shipname',align:'center',width:100}">货物性质</th>
            -->
            
            <th data-options="{name:'buystartdate',align:'center',width:100}">受让方计费起始日</th>
           
            <th data-options="{name:'bookingtransstatus',align:'center',width:100,width:100,render:function(value){
            if(value==1){return '新建';}
            else if(value==2){return '暂存';}
            else if(value==3){return '已提交';}
            else if(value==4){return '已审核';}
            else if(value==5){return '已完成';}
            else if(value==6){return '作废';}}}">单据状态</th>
            <th data-options="{name:'created_at',align:'center',width:100}">创建时间</th>
        </tr>
        </thead>
    </table>
</div>