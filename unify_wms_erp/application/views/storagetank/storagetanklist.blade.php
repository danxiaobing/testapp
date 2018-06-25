<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#storagetanklist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-3">

                <label class="row-label">储罐编号</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="储罐编号">
                </div>

                <label class="row-label">所属片区</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_areaid">
                        <option value="-100" selected="">不限</option>
                        @foreach($arealist as $item)
                            <option value="{{$item['sysno']}}">{{$item['areaname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">储罐材质</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_categoryid">
                        <option value="-100" selected="">不限</option>
                        @foreach($storagetankcategorylist as $item)
                            <option value="{{$item['sysno']}}">{{$item['storagetank_categoryname']}}</option>
                        @endforeach
                    </select>
                </div>

                <label class="row-label">储罐性质</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_typeid">
                        <option value="-100" selected="">不限</option>
                        <option value="1">内贸罐</option>
                        <option value="2">外贸罐</option>
                        <option value="3">保税罐</option>
                    </select>
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="-100" selected="">不限</option>
                        <option value="2">已禁用</option>
                        <option value="1" >已启用</option>
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
    <table class="table table-bordered" id="storagetanklist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom:$.CurrentNavtab.find('#storagetank_btn'),
        addLocation: 'last',
        dataUrl: 'storagetank/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1200',height:'630',title:'储罐详细信息',mask:true}},
        editUrl: '/storagetank/edit/id/{sysno}',
        delUrl:'/storagetank/deljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'storagetankname',align:'center'}">储罐编号</th>
                <th data-options="{name:'areaname',align:'center'}">所属片区</th>
                <th data-options="{name:'storagetank_categoryname',align:'center'}">储罐材质</th>
                <th data-options="{name:'height',align:'center'}">储罐高度(米)</th>
                <th data-options="{name:'diameter',align:'center'}">储罐直径(米)</th>
                <th data-options="{name:'storagetanknature',align:'center',render:function(value){return value =='1' ? '内贸罐' : value =='2' ? '外贸罐' : '保税罐'}}">储罐性质</th>
                <th  data-options="{name:'theoreticalcapacity',align:'center'}">实际容量</th>
                <th data-options="{name:'goodsname',align:'center'}">货品</th>
                <th  data-options="{name:'actualcapacity',align:'center'}">可存放吨数</th>
                <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="storagetank_btn">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="setstoragetankstatus(1)">启用</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="setstoragetankstatus(2)">停用</button>
    <button type="button" class="btn btn-green" data-icon="hourglass-o" onclick="clearstoragetank()">清罐</button>
</div>
<script>
    function setstoragetankstatus(status){

        var storagetank_sysnos =  $.CurrentNavtab.find("#storagetanklist-table").data('selectedDatas');
        if(status==1)
            $st = '启用';
        else
            $st = '停用';
        if(storagetank_sysnos==""||storagetank_sysnos == null){
            BJUI.alertmsg('warn','请先选中储罐再'+$st,{displayPosition:'middlecenter',displayMode:'fade'});
        }
        else {
            var qus = new Array();
            for(var i=0;i<storagetank_sysnos.length;i++){
                qus[i]=storagetank_sysnos[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量'+$st+'吗！', {
                displayPosition:'middlecenter',
                okCall: function() {
                    //回调操作
                    BJUI.ajax('doajax', {
                        url: 'storagetank/setstoragetankstatus/status/'+status,
                        data:{qus:qus},
                        loadingmask: true,
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh', 'navab338');
                        }
                    });
                }
            })
        }
        return;
    }

    function clearstoragetank(){
        var storagetank_sysno =  $.CurrentNavtab.find("#storagetanklist-table").data('selectedDatas');

        if(storagetank_sysno==""||storagetank_sysno == null){
            BJUI.alertmsg('warn', '请先选中储罐再清罐',{displayPosition:'middlecenter',displayMode:'fade'});
        }else if(storagetank_sysno.length>1){
            BJUI.alertmsg('warn', '一次只能选择一个储罐清理',{displayPosition:'middlecenter',displayMode:'fade'});
        }
        else {
            BJUI.dialog({
                id:'storagetank_clear',
                url:'/storagetank/edit/id/'+storagetank_sysno[0]['sysno']+'/clearstoragetank/'+1,
                title:'储罐清理',
                width:1200,
                height:650,
                mask:true
            });
        }
    }
</script>
