<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#storagetankcategorylist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">储罐材质名称</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name or ''}}" placeholder="储罐材质名称"></div>

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
                    </div>
                </div>

            </div>

        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">

    <table class="table table-bordered" id="storagetankcategorylist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom:$.CurrentNavtab.find('#storagetankcategory_btn'),
        addLocation: 'last',
        dataUrl: 'storagetank/categorylistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'800',height:'300',title:'储罐材质',mask:true}},
        editUrl: '/storagetank/categoryedit/id/{sysno}',
        delUrl:'/storagetank/categorydeljson',
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
                <th data-options="{name:'storagetank_categoryname',align:'center',width:100}">储罐材质名称</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">修改时间</th>
                <th data-options="{name:'status',align:'center',width:70,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="storagetankcategory_btn">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="setstoragetankcategorystatus(1)"> 启用</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="setstoragetankcategorystatus(2)">停用</button>
</div>
<script>
    function setstoragetankcategorystatus(status){

        var storagetankcategory_sysnos =  $.CurrentNavtab.find("#storagetankcategorylist-table").data('selectedDatas');

        if(status==1)
            $st = '启用';
        else
            $st = '停用';
        if(storagetankcategory_sysnos == ''||storagetankcategory_sysnos==null){
            BJUI.alertmsg('warn', '请先选中储罐材质再'+$st,{displayPosition:'middlecenter',displayMode:'fade'});
        }
        else {
            var qus = new Array();
            for(var i=0;i<storagetankcategory_sysnos.length;i++){
                qus[i]=storagetankcategory_sysnos[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量'+$st+'吗！', {
                okCall: function() {
                    //回调操作
                    BJUI.ajax('doajax', {
                        url: 'storagetank/setstoragetankcategorystatus/status/'+status,
                        data:{qus:qus},
                        loadingmask: true,
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh', 'navab342');
                        }
                    });
                }
            })
        }
        return;
    }
</script>
