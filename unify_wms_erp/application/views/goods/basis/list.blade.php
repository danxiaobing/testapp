<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#goodslist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">货品编号</label>
                <div class="row-input">
                    <input type="text" name="goodsno" value="{{$goodsno or ''}}" placeholder="货品编号">
                </div>

                <label class="row-label">货品名称</label>
                <div class="row-input">
                    <input type="text" name="goodsname" value="{{$goodsname or ''}}" placeholder="货品名称">
                </div>

                <label class="row-label">状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="status">
                        <option value="">请选择</option>
                        <option value="1">启用</option>
                        <option value="2">停用</option>
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
    <table class="table table-bordered" id="goodslist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del',
        toolbarCustom:$.CurrentNavtab.find('#goodsbasis_btn'),
        dataUrl: 'goods/basislistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1100',height:'320',title:'货品管理-基础属性',mask:true}},
        editUrl: '/goods/basisedit/id/{sysno}',
        delUrl:'/goods/basisdeljson',
        delPK:'sysno',
        paging: false,
        showCheckboxcol: true,
        filterThead:false,
        addLocation:'first',
        isTree: 'goodsname',
        treeOptions: {
            add: false,
            simpleData: true,
            keys: {
                key:'sysno',
                parentKey: 'parent_sysno',
                isExpand: 'isExpand'
            },
             expandAll: true
        },
        fullGrid:true
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th  data-options="{name:'goodsno',align:'center'}">货品编号</th>
                <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
                <th data-options="{name:'parent_goodsname',align:'center'}">货品分类</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
                <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="goodsbasis_btn">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="setbasisstatus(1)">启用</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="setbasisstatus(2)">停用</button>
</div>
<script>
    function setbasisstatus(status){

        var basis_sysnos =  $.CurrentNavtab.find("#goodslist-table").data('selectedDatas');

        if(status==1)
            $st = '启用';
        else
            $st = '停用';
        if(basis_sysnos == ''||basis_sysnos==null){
            BJUI.alertmsg('warn', '请先选中行再操作：'+$st,{displayPosition:'middlecenter',displayMode:'fade'});
        }
        else {
            var qus = new Array();
            for(var i=0;i<basis_sysnos.length;i++){
                qus[i]=basis_sysnos[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '请先选中行再操作：'+$st+'吗！', {
                okCall: function() {
                    //回调操作
                    BJUI.ajax('doajax', {
                        url: 'goods/setbasisstatus/status/'+status,
                        data:{qus:qus},
                        loadingmask: true,
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh', 'navab346');
                        }
                    });
                }
            })
        }
    }
</script>