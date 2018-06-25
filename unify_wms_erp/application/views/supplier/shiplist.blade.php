<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#shiplist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">
                <label class="row-label">船编号</label>
                <div class="row-input">
                    <input type="text" name="bar_shipno" value="{{$bar_shipno or ''}}" placeholder="船编号">
                </div>
                <label class="row-label">船名</label>
                <div class="row-input">
                    <input type="text" name="bar_shipname" value="{{$bar_shipname or ''}}" placeholder="船名">
                </div>

                <label class="row-label">所属公司</label>
                <div class="row-input">
                    <input type="text" name="bar_company" value="{{$bar_company or ''}}" placeholder="所属公司">
                </div>

                <label class="row-label"></label>
                <div class="row-input"></div>

                <label class="row-label">船长</label>
                <div class="row-input">
                    <input type="text" name="bar_captain" value="{{$bar_captain or ''}}" placeholder="船长">
                </div>

                <label class="row-label">联系方式</label>
                <div class="row-input">
                    <input type="text" name="bar_shipcontact" value="{{$bar_shipcontact or ''}}" placeholder="联系方式" >
                </div>

                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="">请选择</option>
                        <option value="1">已启用</option>
                        <option value="2">已停用</option>
                    </select>
                    <!-- <input type="text" name="bar_status" value="{{$bar_status or ''}}" placeholder="操作状态"> -->
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
    <table class="table table-bordered" id="shiplist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: 'add,|,edit,|,del,|,refresh,',
        toolbarCustom:'#custom_ship_tb_ship',
        dataUrl: 'supplier/shiplistJson',
        dataType: 'json',
        paging: {pageSize:12},
        editMode: {dialog:{width:'1000',height:'600',title:'船舶管理',mask:true}},
        editUrl: '/supplier/shipedit/id/{sysno}',
        delUrl:'/supplier/shipdeljson',
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
                <th data-options="{name:'shipno',align:'center'}">船舶编号</th>
                <th data-options="{name:'shipname',align:'center'}">船名</th>
                <th  data-options="{name:'company',align:'center'}">所属公司</th>
                <th data-options="{name:'captain',align:'center'}">船长</th>
                <th data-options="{name:'shipcontact',align:'center'}">联系方式</th>
                <th data-options="{name:'shipwidth',align:'center'}">宽度(m)</th>
                <th data-options="{name:'shiplength',align:'center'}">长度(m)</th>
                <th data-options="{name:'shiploadcapacity',align:'center'}">吃水</th>
                <th data-options="{name:'shiploadweight',align:'center'}">载重(吨）</th>
                <!-- <th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm',width:160}">更新时间</th> -->
                <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="custom_ship_tb_ship">
    <button type="button" class="btn btn-green" data-icon="filter" onclick="viewFiles()">查看附件</button>
    <button type="button" class="btn btn-green" data-icon="unlock-alt" onclick="invoke()">启用选中</button>
    <button type="button" class="btn btn-green" data-icon="hand-paper-o" onclick="disable()">停用选中</button>
</div>

<script type="text/javascript">
    function viewFiles() {
        var chks=$.CurrentNavtab.find("#shiplist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#shiplist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }else{
            var obj = data[0];
            BJUI.dialog({
                id:'attach-ship-'+obj.sysno,
                url:'/attachment/view/supplier/ship/'+obj.sysno,
                title:'查看'+obj.shipname+"附件",
                width:820,
                height:660,
                mask:true
            });
        }
    }
    function invoke(){
        var chks=$.CurrentNavtab.find("#shiplist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#shiplist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    var data  = $("#shiplist-table").data('selectedDatas');
                    var idArray = new Array;
                    if (data && data.length>0) {
                        for (var i = 0; i < data.length; i++) {
                            idArray[i] = data[i].sysno;
                        }
                        idArray.unshift('start');
                        //console.log(idArray);
                        BJUI.ajax('doajax',{
                            url: '/Supplier/shipStatus/',
                            type: 'POST',
                            data:{idArray:idArray},
                            okCallback: function(json, options) {
                                //console.log('返回内容：\n'+ json.msg);
                                if (json.msg == '成功') {
                                    BJUI.navtab('refresh','navab358');
                                };
                            }
                        });
                    }else{
                        BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
                    }    
                }
            })

        
    }
    function disable(){
        var chks=$.CurrentNavtab.find("#shiplist-table");
        if(chks.length<1)
        {
            BJUI.alertmsg('warn','未选中任何行',{displayPosition:'middlecenter',displayMode:'fade'});
            return;
        }
        var data  = $("#shiplist-table").data('selectedDatas');
        if (data == ''||data == null) {
            BJUI.alertmsg('warn','请先选中一行数据',{displayPosition:'middlecenter',displayMode:'fade'});
            return false;
        }
        BJUI.alertmsg('confirm', '确定要批量停用吗！', {
                okCall: function() {
                    var data  = $("#shiplist-table").data('selectedDatas');
                    var idArray = new Array;
                    if (data && data.length>0) {
                        for (var i = 0; i < data.length; i++) {
                            idArray[i] = data[i].sysno;
                        }
                        idArray.unshift('stop');
                        //console.log(idArray);
                        BJUI.ajax('doajax',{
                            url: '/Supplier/shipStatus/',
                            type: 'POST',
                            data:{idArray:idArray},
                            okCallback: function(json, options) {
                                //console.log('返回内容：\n'+ json.msg);
                                    if (json.msg == '成功') {
                                       BJUI.navtab('refresh','navab358');
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