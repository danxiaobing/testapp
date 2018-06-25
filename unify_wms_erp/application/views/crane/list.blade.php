<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#cranelist-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">鹤位号</label>
                <div class="row-input">
                    <input type="text" name="cranename" value="{{$cranename}}" placeholder="请输入鹤位号" ></div>

                <label class="row-label">品种</label>
                <div class="row-input">
                    <select name="goods_sysno" data-size="5" data-toggle="selectpicker" data-live-search="true" data-width="100%">
                        <option value="">请选择</option>
                        @foreach($goodslist as $item)
                            <option value="{{$item['sysno']}}">{{$item['goodsname']}}</option>
                        @endforeach
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
        <table class="table table-bordered" id="cranelist-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarCustom:'#crane-button',
        dataUrl: 'crane/cranelistJson',
        dataType: 'json',
        jsonPrefix: 'obj',
        paging: {pageSize:20},
        showCheckboxcol: true,
        editMode:false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'cranename',align:'center'}">鹤位号</th>
            <th data-options="{name:'goodsname',align:'center'}">品种</th>
            <th data-options="{name:'storagetankname',align:'center'}">储罐</th>
            <th data-options="{name:'installtime',align:'center'}">安装时间</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            <th data-options="{name:'cranestatus',align:'center',render:function(value){if(value=='2') {return '暂存'} else if(value=='3') {return '待审核'} else if(value=='4') {return '已审核'} else if(value=='6') {return '退回'} else if(value=='7') {return '作废'} else  {return '新建'}}}">单据状态</th>
            <th data-options="{name:'goods_sysno',align:'center',hide:true}">品种id</th>
            <th data-options="{name:'storagetank_sysno',align:'center',hide:true}">储罐id</th>
        </tr>
        </thead>
    </table>
</div>
<div id="crane-button">
    <button type="button" id="crane_add" class="btn btn-blue" data-icon="plus" >添加</button>
    <button type="button" id="crane_edit" class="btn btn-green" data-icon="edit" >编辑</button>
    <button type="button" id="crane_del" class="btn btn-red" data-icon="times" onclick="del()" >删除</button>
{{--    <button type="button" id="crane_start" class="btn btn-green" data-icon="unlock-alt" onclick="setstatus(1)">启用</button>
    <button type="button" id="crane_stop" class="btn btn-blue"  data-icon="hand-paper-o" onclick="setstatus(2)">停用</button>--}}
    <button type="button" id="crane_check" class="btn btn-green" data-icon="gavel" >审核</button>
</div>

<script type="text/javascript">

    $('#crane_add').click(function(){
        BJUI.dialog({
            id:'crane-add',
            width:800,
            height:400,
            mask:true,
            type:'POST',
            url:'/crane/craneEdit',
            title:'添加鹤位',
        });
    });

    $('#crane_edit').click(function(){

        var checkdata=$('#cranelist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;

            if(checkdata[0].cranestatus != 1 && checkdata[0].cranestatus != 2 && checkdata[0].cranestatus != 6){
                BJUI.alertmsg('warn','非暂存或退回状态鹤位不允许编辑');
                return;
            }else {
                BJUI.dialog({
                    id: 'crane-edit' + id,
                    width: 800,
                    height: 400,
                    mask: true,
                    type: 'POST',
                    url: '/crane/craneEdit/id/' + id,
                    data: {sysno: id},
                    title: '鹤位管理'
                });
            }
        }else{
            BJUI.alertmsg('warn','<h4>未选择数据</h4>');
        }
    });

/*    function setstatus(step)
    {
        var data = $('#cranelist-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];

            var status = data[0]['status'];

            if(status == step){
                BJUI.alertmsg('warn','<h4>不要重复操作</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                return;
            }
            var alertmsg = '';
            if(step==1){
                alertmsg = '确定要启用吗?';
            }else{
                alertmsg = '确定要停用吗?';
            }
            BJUI.alertmsg('confirm', alertmsg, {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/crane/setstatus',
                        type:'POST',
                        loadingmask: true,
                        data:{id:sysno, status:step},
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu481');
                        }
                    })
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }*/

    function del()
    {
        var data = $('#cranelist-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];


            BJUI.alertmsg('confirm', '确定删除吗', {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/crane/craneDel',
                        type:'POST',
                        loadingmask: true,
                        data:{id:sysno},
                        okCallback: function(json, options) {
                            BJUI.navtab('refresh','menu481');
                        }
                    })
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    $('#crane_check').click(function(){

        var checkdata=$('#cranelist-table').data('selectedDatas');
        if(checkdata && checkdata.length>0){
            if(checkdata.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }
            var id = checkdata[0].sysno;

            if(checkdata[0].cranestatus != 3){
                BJUI.alertmsg('warn','待审核状态才能审核');
                return;
            }else{
                BJUI.dialog({
                id:'crane-check'+id,
                width:800,
                height:400,
                mask:true,
                type:'POST',
                url:'/crane/craneEdit/id/'+id,
                data:{sysno:id},
                title:'鹤位审核'
            });
            }
        }else{
            BJUI.alertmsg('warn','<h4>未选择数据</h4>');
        }
    });

</script>