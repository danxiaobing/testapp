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
                    <div class="btn-group">
                        <input type="text" name="goodsname" value="{{$goodsname or ''}}" placeholder="货品名称">
                    </div>
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
        toolbarCustom:$.CurrentNavtab.find('#goodsattribute_btn'),
        dataUrl: 'goods/attributelistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1000',height:'500',title:'货品管理-其他属性',mask:true}},
        editUrl: '/goods/attributeedit/id/{sysno}',
        delUrl:'/goods/attributedeljson',
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
                <th data-options="{name:'goodsno',align:'center'}">货品编号</th>
                <th data-options="{name:'goodsname',align:'center'}">货品名称</th>
                <th data-options="{name:'density',align:'center'}">密度</th>
                <th data-options="{name:'controlprice',align:'center'}">控货单价</th>
                <th data-options="{name:'controlproportion',align:'center'}">控货比重</th>
                <th data-options="{name:'rate_waste',align:'center'}">内控损耗‰</th>
                <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                <th  data-options="{name:'islongterm',align:'center',width:70,render:function(value){return value =='1' ? '是' : '否'}}">长期品种</th>
                <th data-options="{name:'storagetank_categoryname',align:'center'}">不能存放的材质</th>
                <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
                <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
                <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="goodsattribute_btn">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="setattributestatus(1)">启用</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="setattributestatus(2)">停用</button>
</div>
<script>
    function setattributestatus(status){

        var attribute_sysnos =  $.CurrentNavtab.find("#goodslist-table").data('selectedDatas');

        if(status==1)
            $st = '启用';
        else
            $st = '停用';
        if(attribute_sysnos == ''||attribute_sysnos==null){
            BJUI.alertmsg('warn', '请先选中行再操作：'+$st,{displayPosition:'middlecenter',displayMode:'fade'});
        }
        else {
            var qus = new Array();
            for(var i=0;i<attribute_sysnos.length;i++){
                qus[i]=attribute_sysnos[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量'+$st+'吗！', {
                okCall: function() {
                    //回调操作
                    BJUI.ajax('doajax', {
                        url: 'goods/setattributestatus/status/'+status,
                        data:{qus:qus},
                        loadingmask: true,
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh', 'navab350');
                        }
                    });
                }
            })
        }
        return;
    }
</script>