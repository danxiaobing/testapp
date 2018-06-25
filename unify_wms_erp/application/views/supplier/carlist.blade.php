<div class="bjui-pageHeader ">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#carlist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>

            <div class="bjui-row col-4">

                <label class="row-label">车牌号</label>
                <div class="row-input">
                    <input type="text" name="carid" value="{{$carid}}" placeholder="请输入车牌号码"></div>

                <label class="row-label">司机姓名</label>
                <div class="row-input">
                    <input type="text" name="bar_name" value="{{$bar_name}}" placeholder="请输入司机姓名"></div>



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
    <table class="table table-bordered" id="carlist-table" data-toggle="datagrid" data-options="{
        tableWidth:'99%',
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh',
        toolbarCustom:'#custom_ship_tb_car',
        dataUrl: 'supplier/carlistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        editMode: {dialog:{width:'1200',height:'400',title:'车辆管理',mask:true}},
        editUrl: '/supplier/caredit/id/{sysno}',
        delUrl:'/supplier/cardeljson',
        delPK:'sysno',
        paging: {pageSize:12},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'carid',align:'center'}">车牌号</th>
            <th data-options="{name:'carname',align:'center'}">司机姓名</th>
            <th data-options="{name:'mobilephone',align:'center'}">司机手机号码</th>
            <th data-options="{name:'idcard',align:'center'}">身份证号</th>
            <th data-options="{name:'created_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">创建时间</th>
            <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
        </tr>
        </thead>
    </table>
</div>
<div id="custom_ship_tb_car">
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="invoke()">启用选中</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="disable()">停用选中</button>
</div>

<script type="text/javascript">
    function invoke(){
        var chks=$.CurrentNavtab.find("#carlist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#carlist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    var data  = $("#carlist-table").data('selectedDatas');
                    var idArray = new Array;
                    if (data && data.length>0) {
                        for (var i = 0; i < data.length; i++) {
                            idArray[i] = data[i].sysno;
                        }
                        idArray.unshift('start');
                        //console.log(idArray);
                        BJUI.ajax('doajax',{
                            url: '/Supplier/carStatus/',
                            type: 'POST',
                            data:{idArray:idArray},
                            okCallback: function(json, options) {
                                //console.log('返回内容：\n'+ JSON.stringify(json));
                                if (json.msg == '成功') {
                                       BJUI.navtab('refresh','navab362');
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
        var chks=$.CurrentNavtab.find("#carlist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#carlist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    var data  = $("#carlist-table").data('selectedDatas');
                    var idArray = new Array;
                    if (data && data.length>0) {
                        for (var i = 0; i < data.length; i++) {
                            idArray[i] = data[i].sysno;
                        }
                        idArray.unshift('stop');
                        //console.log(idArray);
                        BJUI.ajax('doajax',{
                            url: '/Supplier/carStatus/',
                            type: 'POST',
                            data:{idArray:idArray},
                            okCallback: function(json, options) {
                                //console.log('返回内容：\n'+ JSON.stringify(json));
                                if (json.msg == '成功') {
                                       BJUI.navtab('refresh','navab362');
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