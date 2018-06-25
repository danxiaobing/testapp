<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#goodsqualitylist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">质量名称</label>
                <div class="row-input">
                    <input type="text" name="qualityname" value="{{$qualityname}}" placeholder="质量名称">
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
  <table class="table table-bordered" id="goodsqualitylist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del',
        toolbarCustom:$.CurrentNavtab.find('#goodsquality_btn'),
        dataUrl: 'quality/qualitylistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'500',height:'300',title:'质量标准',mask:true,id:'navab11'}},
        editUrl: '/quality/Qualityedit/id/{sysno}',
        delUrl:'/quality/delete',
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
            <th data-options="{name:'qualityname',align:'center'}">质量标准名称</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">最后更新时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>

<div id="goodsquality_btn">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="setqualitystatus(1)">启用</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="setqualitystatus(2)">停用</button>
</div>
<script>
    function setqualitystatus(status){

        var quality_sysnos =  $.CurrentNavtab.find("#goodsqualitylist-table").data('selectedDatas');

        if(status==1)
            $st = '启用';
        else
            $st = '停用';

        if(quality_sysnos == ''||quality_sysnos==null){
            BJUI.alertmsg('warn', '请先选中行再操作：'+$st,{displayPosition:'middlecenter',displayMode:'fade'});
        }
        else {
            var qus = new Array();
            for(var i=0;i<quality_sysnos.length;i++){
                qus[i]=quality_sysnos[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量'+$st+'吗！', {
                okCall: function() {
                    //回调操作
                    BJUI.ajax('doajax', {
                        url: 'quality/setqualitystatus/status/'+status,
                        data:{qus:qus},
                        loadingmask: true,
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh', 'navab354');
                        }
                    });
                }
            })
        }
        return;
    }

</script>







