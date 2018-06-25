<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#arealist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">片区编码</label>
                <div class="row-input ">
                    <input type="text" name="areaid" value="{{$areaid}}"  placeholder="请输入片区编码" ></div>


                <label class="row-label">片区名称</label>
                <div class="row-input">
                    <input type="text" name="areaname" value="{{$areaname}}" placeholder="请输入片区名称" ></div>


                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
                        <option value="2">已禁用</option>
                        <option value="1" >已启用</option>
                    </select>
                </div>

                <div class="row-input">
                    <div class="btn-group">
                        <button type="submit" class="btn-green" data-icon="search">开始搜索</button>
                        <!-- <button type="reset" class="btn-orange" data-icon="times">重置</button> -->
                    </div>
                </div>

            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">

    <table class="table table-bordered" id="arealist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom: '#area-button',
        dataUrl: 'area/arealistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'800',height:'340',title:'片区管理',mask:true}},
        editUrl: '/area/areaedit/id/{sysno}',
        delUrl:'/area/areadeljson',
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'areaid',align:'center'}">片区编号</th>
            <th data-options="{name:'areaname',align:'center'}">片区名称</th>
            <th data-options="{name:'areamarks',align:'center'}">备注</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="area-button">
    <button type="button" id="area_start" class="btn btn-green" data-icon="unlock-alt">启用</button>
    <button type="button" id="area_stop" class="btn btn-green" data-icon="hand-paper-o">停用</button>
</div>

<script type="text/javascript">
        $('#area_start').click(function(){
        var arr = $('#arealist-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量启用吗！', {

                        okCall: function() {
                            BJUI.ajax('doajax', {
                                url: '/area/AreaChange/',
                                type: 'POST',
                                data: {data:data,state:'start'},
                                okcallback:function(option){

                                }
                            });                    
                        }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });


        $('#area_stop').click(function(){
        var arr = $('#arealist-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量禁用吗！', {
                        okCall: function() {
                            BJUI.ajax('doajax', {
                                url: '/area/AreaChange/',
                                type: 'POST',
                                data: {data:data,state:'stop'},
                                okcallback:function(option){

                                }
                            });                    
                        }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });        
</script>