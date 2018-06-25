<div class="bjui-pageHeader" style="background-color:#fefefe; border-bottom:none;">
    <form id="carpelinelist-bar">
        <fieldset>
           <input type="hidden" name="page" value="false" >
            <legend style="font-weight:normal;">高级搜索：</legend>
            <div class="bjui-row col-3">
                <label class="row-label">操作状态</label>
                <div class="row-input">
                    <select name="bar_queuetype" data-size="5"  data-toggle="selectpicker" data-width="100%">
                        <option value="1" selected="">车辆装货</option>
                        <option value="2">车辆卸货</option>
                    </select>
                </div>
            </div>
            
        </fieldset>
    </form>
</div>
<div class="bjui-pageContent clearfix">
    <div class="carlist clearfix"><ul>
    @foreach($queuelist as $item)
        <li data-id="{{$item['sysno']}}"><span>{{$item['queueno']}}</span><br><span>排列车辆：{{$item['countcars']}}</span></li>
    @endforeach
    </ul></div>
    <table class="table table-bordered" id="carlist_table" data-toggle="datagrid" data-options="{
        height: '100%',
        showToolbar: true,
        toolbarCustom:$.CurrentNavtab.find('#queue_list_tb'),
        addLocation: 'last',
        data: '{{$data}}',
        dataType: 'json',
        paging: false,
        editMode: false,
        showCheckboxcol: false,
        linenumberAll: true,
        filterThead:false,
        showLinenumber:true,
        fieldSortable: false,
        fullGrid: true,
        jsonPrefix: 'obj',
    }">
        <thead>
            <tr data-options="{name:'sysno'}">
                <th data-options="{name:'doc_type',align:'center',render:function(value){if(value==1){return '车辆装货';}else{return '车辆卸货';}}}">单据类型</th>
                <th data-options="{name:'carid',align:'center'}">车牌号</th>
                <th  data-options="{name:'carname',align:'center'}">司机</th>
                <th  data-options="{name:'mobilephone',align:'center'}">联系方式</th>
                <th data-options="{name:'queueno',align:'center'}">鹤位号</th>
                <th  data-options="{name:'goodsname',align:'center'}">货品</th>
                <th data-options="{name:'estimateqty',align:'center'}">预计作业吨数（吨）</th>
                <th data-options="{name:'loadometer',align:'center'}">地磅</th>
                <th  data-options="{name:'queuetime',align:'center'}">预计等候时间</th>
                <th data-options="{name:'queuestatus',align:'center'}">排队状态</th>
            </tr>
        </thead>
    </table>
</div>
<div id="queue_list_tb">
    <button type="button" class="btn btn-green" data-icon="star"  onclick="Carqueuechange('top')">置顶</button>
    <button type="button" class="btn btn-green" data-icon="arrow-circle-up" onclick="Carqueuechange('up')">上移</button>
    <button type="button" class="btn btn-green" data-icon="arrow-circle-down"  onclick="Carqueuechange('down')">下移</button>
    <button type="button" class="btn btn-red" data-icon="close" onclick="Carqueuedel()">取消排队</button>
</div>
<script type="text/javascript">





$.CurrentNavtab.find("select[name=bar_queuetype]").change(function (e) {
    if($(this).val()){

    BJUI.ajax('doajax', {
            url: '/Queuebase/ajaxgetQueuebase',
            loadingmask: true,
            data:{bar_queuetype:$(this).val()},
            okCallback: function(json, options) {
                
                $('.carlist ul').html('');
                var carlist = (json.map(function(item){
                    return "<li data-id="+item.sysno+"><span>"+item.queueno+"</span><br><span>排队车辆:"+item.countcars+"</span></li>"
                }).join(''));
                $('.carlist ul').append(carlist);
                get();

            }
        })
    }

    
});


$('.carlist').find('ul').on('click','li',function(event) {

    var bar_queuetype =  $('select[name=bar_queuetype]').find("option:selected").val();

    BJUI.ajax('doajax', {
        url: '/Carqueue/listJson',
        loadingmask: true,
        data:{doc_type:bar_queuetype,tp_sysno:$(this).attr('data-id'),page:false},
        okCallback: function(json, options) {
            data = json.list;
            $.CurrentNavtab.find("#carlist_table").datagrid('reload', {data: data});
        }
    })

});


    function Carqueuechange(action)
    {
        var checkdata=$.CurrentNavtab.find('#carlist_table').data('selectedDatas');
        var alldata=$.CurrentNavtab.find('#carlist_table').data('allData');
        var sysno = checkdata[0]['sysno'];
        var key = 0;    //当前选中行的下标
        var orderno = 0; //当前选中行的排位号
        var upsysno = 0; //上一行的ID
        var downsysno = 0; //下一行的ID
        var uporderno = 0; //上一行的排位号
        var downorderno = 0; //下一行的排位号
        var data = {}; //参数数组
        for (var i = alldata.length - 1; i >= 0; i--) {
            if(sysno==alldata[i].sysno)
            {
                key = i;
                if(action=='top')
                {
                    data['sysno'] = sysno;
                }else if(action=='up'){
                    if(i==0){
                        BJUI.alertmsg('warn','<h4>已经是最优先！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                        return false;
                    }
                    data['sysno'] = sysno;
                    data['upsysno'] = alldata[i-1].sysno;
                    data['orderno'] = alldata[i].orderno;
                    data['uporderno'] = alldata[i-1].orderno;
                    if(alldata[i-1].isup==1)
                    {
                        BJUI.alertmsg('warn','<h4>请把该条置顶后再上移!</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                        return false;
                    }
                }else if(action=='down')
                {
                    if((i+1)==alldata.length){
                        BJUI.alertmsg('warn','<h4>已经是最底了！</h4>',{displayPosition:'middlecenter',displayMode:'fade'});
                        return false;
                    }                    
                    data['sysno'] = sysno;
                    data['downsysno'] = alldata[i+1].sysno;
                    data['orderno'] = alldata[i].orderno;
                    data['downorderno'] = alldata[i+1].orderno;
                }
                break;
            }
        }
        // console.log(data);return;
        data = JSON.stringify(data);
        // console.log(JSON.stringify(data));
        BJUI.ajax('doajax', {
            url: '/Carqueue/carqueueChange',
            type:'POST',
            loadingmask: true,
            data:{data:data,action:action,key:key},
            okCallback: function(json, options) {
                BJUI.navtab('reload', 'navab527');
                get();
            }
        });
    }


    function Carqueuedel()
    {
        var checkdata=$.CurrentNavtab.find('#carlist_table').data('selectedDatas');
        var sysno = checkdata[0]['sysno'];   
             
        BJUI.ajax('doajax', {
            url: '/Carqueue/carqueuedel',
            type:'POST',
            loadingmask: true,
            data:{id:sysno},
            okCallback: function(json, options) {
                BJUI.navtab('reload', 'navab527');
            }
        });
    }   

function get(){
        var bar_queuetype =  $.CurrentNavtab.find('select[name=bar_queuetype]').find("option:selected").val();
        var tp_sysno = $.CurrentNavtab.find('.carlist').find('ul li:eq(0)').attr('data-id');
        if(tp_sysno){
            BJUI.ajax('doajax', {
                url: '/Carqueue/listJson',
                data:{doc_type:bar_queuetype,tp_sysno:tp_sysno,page:false},
                okCallback: function(json, options) {
                    data = json.list;
                    $.CurrentNavtab.find("#carlist_table").datagrid('reload', {data: data});
                }
            })   
        }
}
    get();
</script>