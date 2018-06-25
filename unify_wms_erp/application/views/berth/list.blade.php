<div class="bjui-pageHeader">
    <form data-toggle="ajaxsearch" data-options="{searchDatagrid:$.CurrentNavtab.find('#berth-list-table')}">
        <fieldset>
            <legend style="font-weight:normal;">高级搜索</legend>
            <div class="bjui-row col-4">

                <label class="row-label">泊位号</label>
                <div class="row-input ">
                    <input type="text" name="berthname" value="{{$berthname}}"  placeholder="请输入泊位编码" ></div>


                <label class="row-label">码头</label>
                <div class="row-input">
                    <input type="text" name="wharfname" value="{{$wharfname}}" placeholder="请输入码头名称" ></div>


                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select data-toggle="selectpicker" data-width="100%" name="bar_status">
                        <option value="" selected="">不限</option>
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

    <table class="table table-bordered" id="berth-list-table" data-toggle="datagrid" data-options="{
        height: '100%',
        gridTitle : '',
        showToolbar: true,
        toolbarItem: '',
        toolbarCustom: '#berth-button',
        dataUrl: 'berth/listJson',
        dataType: 'json',
        jsonPrefix: 'obj',
       {{-- editMode: {dialog:{width:'800',height:'340',title:'泊位管理',mask:true}},--}}
       {{-- editUrl: '/berth/edit/id/{sysno}',--}}
        delUrl:'/berth/deljson',
        delPK:'sysno',
        paging: {pageSize:20},
        showCheckboxcol: true,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fullGrid:true
    }">
        <thead>
        <tr data-options="{name:'sysno'}">
            <th data-options="{name:'berthname',align:'center'}">泊位号</th>
            <th data-options="{name:'berthloadcapacity',align:'center'}">允许最大吃水(米)</th>
            <th data-options="{name:'berthlength',align:'center'}">泊位长度(米)</th>
            <th data-options="{name:'berthdeep',align:'center'}">泊位水深(米)</th>
            <th data-options="{name:'berthtype',align:'center',render:function(value){if(value==1) {return '不限'  } else {return '其他' } }}">核准停泊船型</th>
            <th data-options="{name:'berthloadweight',align:'center'}">核准停泊能力(吨)</th>
            <th data-options="{name:'wharfname',align:'center'}">码头</th>
            <th data-options="{name:'status',align:'center',render:function(value){return value =='1' ? '启用' : '停用'}}">状态</th>
            <th data-options="{name:'berthmarks',align:'center'}">备注</th>
            {{--<th data-options="{name:'updated_at',align:'center',type:'date',pattern:'yyyy-MM-dd HH:mm'}">修改时间</th>--}}
        </tr>
        </thead>
    </table>
</div>
<div id="berth-button">
    <button type="button" id="berth_add" class="btn btn-green" data-icon="add">添加</button>
    <button type="button" id="berth_edit" class="btn btn-green" data-icon="gavel">编辑</button>
    <button type="button" id="berth_del" class="btn btn-red" data-icon="delete">删除</button>
    <button type="button"  class="btn btn-green" data-icon="gavel" onclick="berth_history()">使用历史</button>
    <button type="button" id="berth_start" class="btn btn-green" data-icon="unlock-alt">启用</button>
    <button type="button" id="berth_stop" class="btn btn-green" data-icon="hand-paper-o">停用</button>
</div>

<script type="text/javascript">
    //添加
    $('#berth_add').click(function(){
        BJUI.dialog({
            url:'/berth/edit/',
            title:"添加泊位",
            width:900,
            height:600,
            mask:true
        });

    })

    //編輯
    $('#berth_edit').click(function(){
        var selectdata  =  $.CurrentNavtab.find('#berth-list-table').data('selectedDatas');
        console.log(selectdata);
        BJUI.dialog({
            url:'/berth/edit/id/'+selectdata[0].sysno,
            title:"编辑泊位",
            width:900,
            height:600,
            mask:true
        });

    })

//删除
    $('#berth_del').click(function(){
        var selectdata  =  $.CurrentNavtab.find('#berth-list-table').data('selectedDatas');
        console.log(selectdata);
        if(selectdata && selectdata.length > 0){
            BJUI.alertmsg('confirm','确定要删除吗?',{okCall:function(){

                BJUI.ajax('doajax', {
                    url: '/berth/deljson/',
                    type: 'POST',
                    data : {id:selectdata[0].sysno,berthname:selectdata[0].berthname},
                    loadingmask: true,
                    okCallback: function(json, options) {
                        BJUI.navtab('refresh', 'menu487');
                    }
                })
            } })
        }else {
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }

    })
//使用历史
    function berth_history()
    {
        var data = $('#berth-list-table').data('selectedDatas');


        if(data && data.length > 0){
            if(data.length>1){
                BJUI.alertmsg('warn','<h4>只能选择一条数据!<h4>');
                return;
            }

            var sysno = data[0]['sysno'];
            BJUI.navtab({
                id:'berth-history',
                mask:true,
                type:'POST',
                url:'/berth/berthhistory',
                data:{id:sysno},
                title:'泊位使用历史'
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中数据！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
        }
    }

    $('#berth_start').click(function(){
        var arr = $('#berth-list-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量启用吗！', {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/berth/Change/',
                        type: 'POST',
                        data: {data:data,state:'start'},
                        okcallback:function(option){
                            BJUI.navtab('refresh', 'menu487');
                        }
                    });
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });


    $('#berth_stop').click(function(){
        var arr = $('#berth-list-table').data('selectedDatas');
        if(arr && arr.length>0){
            data = [];
            for (var i = arr.length - 1; i >= 0; i--) {
                data[i] = arr[i]['sysno'];
            }
            BJUI.alertmsg('confirm', '确定要批量禁用吗！', {
                okCall: function() {
                    BJUI.ajax('doajax', {
                        url: '/berth/Change/',
                        type: 'POST',
                        data: {data:data,state:'stop'},
                        okcallback:function(option){
                            BJUI.navtab('refresh', 'menu487');
                        }
                    });
                }
            });

        }else{
            BJUI.alertmsg('warn','<h4>未选中任何行！</h4>');
        }
    });
</script>