<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="carinfolist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh,',
        toolbarCustom:'#custom_carinfo_tb_carinfo',
        dataUrl: 'supplier/carinfolistJson',
        dataType: 'json',
        paging: {pageSize:12},
        editMode: {dialog:{width:'1000',height:'320',title:'船舶管理',mask:true}},
        editUrl: '/supplier/carinfoedit/id/{sysno}',
        delUrl:'/supplier/carinfodeljson',
        delPK:'sysno',
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true,
        fieldSortable: false,
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'axlenum',align:'center',width:300}">轴数</th>
                <th data-options="{name:'carloadweight',align:'center',width:200}">重限(吨)</th>
                <th data-options="{name:'status',align:'center',width:200,render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="custom_carinfo_tb_carinfo">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="invoke()">启用选中</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="disable()">停用选中</button>
</div>

<script type="text/javascript">
    function invoke(){
        var chks=$.CurrentNavtab.find("#carinfolist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#carinfolist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    var data  = $("#carinfolist-table").data('selectedDatas');
                    var idArray = new Array;
                    if (data && data.length>0) {
                        for (var i = 0; i < data.length; i++) {
                            idArray[i] = data[i].sysno;
                        }
                        idArray.unshift('start');
                        //console.log(idArray);
                        BJUI.ajax('doajax',{
                            url: '/Supplier/carinfoStatus',
                            type: 'POST',
                            data:{idArray:idArray},
                            okCallback: function(json, options) {
                                //console.log('返回内容：\n'+ json.msg);
                                if (json.msg == '成功') {
                                    BJUI.navtab('refresh','navab432');
                                };
                            }
                        });
                    }else{
                        BJUI.alertmsg('info','<h4>请选数据！</h4>');
                    }    
                }
            })

        
    }
    function disable(){
        var chks=$.CurrentNavtab.find("#carinfolist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#carinfolist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    var data  = $("#carinfolist-table").data('selectedDatas');
                    var idArray = new Array;
                    if (data && data.length>0) {
                        for (var i = 0; i < data.length; i++) {
                            idArray[i] = data[i].sysno;
                        }
                        idArray.unshift('stop');
                        //console.log(idArray);
                        BJUI.ajax('doajax',{
                            url: '/Supplier/carinfoStatus',
                            type: 'POST',
                            data:{idArray:idArray},
                            okCallback: function(json, options) {
                                //console.log('返回内容：\n'+ json.msg);
                                    if (json.msg == '成功') {
                                       BJUI.navtab('refresh','navab432');
                                    };
                            }
                        });
                    }else{
                        BJUI.alertmsg('info','<h4>请选数据！</h4>');
                    }    
                }
            })
    }

</script>

