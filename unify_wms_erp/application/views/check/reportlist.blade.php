<div id="toolBtn">
    <button type="button" id="generate_check" class="btn btn-blue" data-icon="">生成盘点单</button>
</div>
<div class="bjui-pageContent clearfix">
    <table class="table table-bordered" id="generatecheck-table" data-toggle="datagrid" data-options="{
            tableWidth:'100%',
            height: '100%',
            showToolbar: true,
            addLocation: 'last',
            toolbarCustom:'#toolBtn',
            dataUrl: 'check/reportJson',
            dataType: 'json',
            jsonPrefix: 'obj',
            paging: false,
            showCheckboxcol: true,
            linenumberAll: true,
            filterThead:false,
            showLinenumber:true
        }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'storagetankname',align:'center',width:100}">储罐编号</th>
            <th data-options="{name:'storagetanknature',align:'center',width:100,render:function(value){if(value=='1') {return '内贸罐'} else if(value=='2') {return '外贸罐'}else if(value=='3') {return '保税罐'} } }">储罐性质</th>
            <th data-options="{name:'goodsname',align:'center',width:100}">货品名称</th>
            <th data-options="{name:'checkdate',align:'center',width:100,render:function(value){if(value=='0000-00-00 00:00:00') {return '--'}}}">上次盘点时间</th>
        </tr>
        </thead>
    </table>
</div>

<script>
    $("#generate_check").click(function () {
        var data = $("#generatecheck-table").data('selectedDatas');
        console.log(data);
        if (data== ''||data == null) {
            BJUI.alertmsg('warn', '<h4>未选中任何行！</h4>');
        }else {
            BJUI.ajax('doajax', {
                url: '/check/generate_check/',
                type:'POST',
                data:{data:data},
                okCallback: function (json, options) {
                    BJUI.navtab('reloadFlag', 'navab563');
                    BJUI.dialog('closeCurrent', '');
                }
            });
        }
    })
</script>
