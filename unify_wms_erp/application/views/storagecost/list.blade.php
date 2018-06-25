<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#storagecostlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">仓储费管理编号</label>
                <div class="row-input">
                    <input type="text" name="storagecostno" value="{{$storagecostno}}" placeholder="仓储费管理编号">
                </div>

                <label class="row-label">仓储费用标准名称</label>
                <div class="row-input">
                    <input type="text" name="storagecostname" value="{{$storagecostname}}" placeholder="仓储费用标准名称">
                </div>

                <label class="row-label">仓储类型</label>
                <div class="row-input">
                    <select name="storagecosttype" data-toggle="selectpicker" data-rule="" data-width="100%">
                        <option value="">请选择</option>
                            <option value="1">长约</option>
                            <option value="2">短约</option>
                    </select>
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <select name="goods_sysno" data-toggle="selectpicker" data-rule="" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($goods as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">储罐类型</label>
                <div class="row-input">
                    <select name="storagetank_category_sysno" data-toggle="selectpicker" data-rule="" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($storagetank_category as $item)
                            <option value="{{$item['sysno']}}">{{$item['storagetank_categoryname']}}</option>
                        @endforeach
                    </select>
                </div>
               {{-- <label class="row-label">状态</label>
                <div class="row-input">
                    <input type="radio" name="status" data-toggle="icheck" value="1" data-rule=""
                           data-label="启用&nbsp;&nbsp;" >
                    <input type="radio" name="status" data-toggle="icheck" value="2" data-label="停用">
                </div>--}}
                 <label class="row-label">状态</label>
                <div class="row-input">
                    <select name="status" data-toggle="selectpicker" data-rule="" data-width="100%">
                        <option value=""  selected="">请选择</option>
                            <option value="1">已开启</option>
                            <option value="2">已禁用</option>
                    </select>
                </div>
                <label class="row-label"></label>
                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                       <!--  <button type="reset" class="btn-orange" data-icon="times">重置</button> -->
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="storagecostlist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom: '#storagecost-button',
        dataUrl: 'storagecost/datail',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1280',height:'350',title:'仓储费标准管理',mask:true}},
        editUrl: '/storagecost/storagecostaddedit/id/{sysno}',
        delUrl:'/storagecost/deletestoragecost',
        delPK:'sysno',
        paging: {pageSize:10},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>

        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagecostno',align:'center'}" width="200px">管理编号</th>
            <th data-options="{name:'storagecostname',align:'center'}">标准名称</th>
            <th data-options="{name:'storagecosttype',align:'center',render:function(value){return value =='1' ? '长约' : '短约'}}">仓储费类型</th>
            <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
            <th data-options="{name:'storagetank_categoryname',align:'center'}">储罐材质</th>
            <th data-options="{name:'unitname',align:'center'}">计量单位</th>
            <th data-options="{name:'startingprice',align:'center'}">首期单价(30天)</th>
            <th data-options="{name:'overdueprice',align:'center'}">超期单价(天)</th>
            <th data-options="{name:'minstock',align:'center'}">最小启存量(吨)</th>
<!--             <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th> -->
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="storagecost-button">
    <button type="button" id="storagecost_start" class="btn btn-green" data-icon="unlock-alt" >启用</button>
    <button type="button" id="storagecost_stop" class="btn btn-green" data-icon="hand-paper-o" >停用</button>
</div>

<script type="text/javascript">
        $('#storagecost_start').click(function(){
        var arr = $('#storagecostlist-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                        okCall: function() {
                            BJUI.ajax('doajax', {
                                url: '/storagecost/storagecostChange/',
                                type: 'POST',
                                data: {data:data,state:'start'},
                                okcallback:function(option){

                                }
                            });                    
                        }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });


        $('#storagecost_stop').click(function(){
        var arr = $('#storagecostlist-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量禁用吗！', {
                        okCall: function() {
                            BJUI.ajax('doajax', {
                                url: '/storagecost/storagecostChange/',
                                type: 'POST',
                                data: {data:data,state:'stop'},
                                okcallback:function(option){

                                }
                            });                    
                        }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    });        
</script>