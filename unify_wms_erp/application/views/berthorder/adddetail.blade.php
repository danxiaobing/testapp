<div class="bjui-pageContent">
    <form id="berthadddetail" action="" class="datagrid-edit-form" data-toggle="validate" data-data-type="json">
        <input id="type" type="hidden" name="type" value="{{$type}}">
        <div class="bjui-row col-2">
            <label class="row-label">泊位号</label>
            <div class="row-input required">
                <input type="hidden" name="berth_sysno" value="{{$berth_sysno}}">
                <input type="text" name="berthname" value="{{$list['berthname']}}" readonly data-rule="required" size="29" data-toggle="findgrid" data-options="{
                dialogOptions: {width:'800',height:'500',title:'泊位资料',maxable:true,resizable:true,mask:true},
                include:'berth_sysno:sysno,berthname:berthname,berthloadcapacity:berthloadcapacity,berthlength:berthlength,berthdeep:berthdeep,berthtype:berthtype,berthloadweight:berthloadweight,wharfname:wharfname,wharf_sysno:wharf_sysno',
                gridOptions: {
                    width:'100%',
                    height:'100%',
                    tableWidth:'99.8%',
                    local: 'local',
                    paging: {pageSize:20},
                    dataUrl: '/berthorder/berthJson/',
                    columns: [
                        {name:'sysno', label:'id'},
                        {name:'berthname', label:'泊位号'},
                        {name:'berthloadcapacity', label:'允许最大吃水(米)'},
                        {name:'berthlength', label:'泊位长度(米)'},
                        {name:'berthdeep', label:'泊位水深(米)'},
                        {name:'berthtype', label:'核准停泊船型'},
                        {name:'berthloadweight', label:'核准停泊能力(吨)'},
                        {name:'wharfname', label:'码头'},
                        {name:'status', label:'操作状态'},
                    ],
                    showLinenumber:false
                },
            }" placeholder="点放大镜按钮查找"></div>

                <label class="row-label">允许最大吃水</label>
                <div class="row-input">
                    <input type="text" size="29" name="berthloadcapacity" value="{{$list['berthloadcapacity']}}" >米</div>

                <label class="row-label">泊位长度</label>
                <div class="row-input">
                    <input type="text" size="29" name="berthlength" value="{{$list['berthlength']}}" >米
                </div>

                <label class="row-label">泊位水深</label>
                <div class="row-input">
                    <input type="text" size="29" name="berthdeep" value="{{$list['berthdeep']}}">米
                </div>

                <label class="row-label">核准停泊船型</label>
                <div class="row-input ">
                    <input type="text" size="29" name="berthtype" value="{{$list['berthtype']}}">
                </div>

                <label class="row-label">核准停泊能力</label>
                <div class="row-input">
                    <input type="text" size="29" name="berthloadweight" value="{{$list['berthloadweight']}}">吨
                </div>

                <label class="row-label">码头</label>
                <div class="row-input">
                    <input type="text" size="29" name="wharfname" value="{{$list['wharfname']}}">
                </div>

                <div class="row-input hidden">
                    <input type="text" size="29" name="wharf_sysno" value="{{$list['wharf_sysno']}}">
                </div>

            <label class="row-label">船名</label>
            <div class="row-input required">
                <input type="hidden" name="shipno" value="{{$shipno}}">
                <input type="text" size="29" name="shipname" value="{{$list['shipname']}}" readonly data-rule="required" data-toggle="findgrid" data-options="{
                dialogOptions: {width:'800',height:'500',title:'船舶资料',maxable:true,resizable:true,mask:true},
                 include:'shipno:shipno,shipname:shipname,company:company,captain:captain,shipcontact:shipcontact',
                gridOptions: {
                    width:'100%',
                    height:'100%',
                    tableWidth:'99.8%',
                    local: 'local',
                    paging: {pageSize:20},
                    dataUrl: '/berthorder/ShipJson/',
                    columns: [
                        {name:'sysno', label:'id'},
                        {name:'shipno', label:'船舶编号'},
                        {name:'shipname', label:'船名'},
                        {name:'company', label:'所属公司'},
                        {name:'captain', label:'船长'},
                        {name:'shipcontact', label:'联系方式'},
                    ],
                    showLinenumber:false
                },
            }" placeholder="点放大镜按钮查找"></div>

                <label class="row-label">船长</label>
                <div class="row-input">
                    <input type="text" size="29" name="captain" value="{{$list['captain']}}">
                </div>
                <label class="row-label">联系方式</label>
                <div class="row-input">
                    <input type="text" size="29" name="shipcontact" value="{{$list['shipcontact']}}">
                </div>
                <label class="row-label">计划靠泊时间</label>
                <div class="row-input required">
                    <input type="text" size="29" name="planintime" value="{{$list['planintime']}}" data-rule="required;"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss">
                </div>
                <label class="row-label">计划离泊时间</label>
                <div class="row-input required">
                    <input type="text" size="29" name="planouttime" value="{{$list['planouttime']}}" data-rule="required;"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss">
                </div>
                <label class="row-label">实际靠泊时间</label>
                <div class="row-input">
                    <input type="text" size="29" name="beintime" value="{{$list['beintime']}}" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss">
                </div>
                <label class="row-label">实际离泊时间</label>
                <div class="row-input">
                    <input type="text" size="29" name="beouttime" value="{{$list['beouttime']}}" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss">
                </div>
                <label class="row-label">备注</label>
                <div class="row-input">
                    <textarea name="memo" data-toggle="autoheight" cols="auto" rows="4">{{$list['memo']}}</textarea></div>
            </div>
        </form>
    </div>
    <div class="bjui-pageFooter">
        <ul>
            <li><button type="button" class="btn-green" data-icon="save" onclick="adddetailsubmit()">保存</button></li>
            <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
            <li id="handlestatus" style="display: none">{{$handlestatus}}</li>
        </ul>
    </div>


    <script type="text/javascript">
        function adddetailsubmit() {

            var type = $("#type").val();

            $('#berthadddetail').isValid(function(v){
                if(v){
                    var data  = $("#berthadddetail").serializeJson();
                    var allData  = $.CurrentNavtab.find("#berthorder-detail-table").data('allData');

                    if (type == 'add') {

                        if(typeof  allData != 'undefined'){
                            allData.push(data);
                        }else{
                            allData = [data] ;
                        }
                        $.CurrentNavtab.find('#berthorder-detail-table').datagrid('reload',  {data:allData});
                        BJUI.dialog('closeCurrent','');
                    }else if (type == 'edit') {
                        $.CurrentNavtab.find('#berthorder-detail-table').datagrid('updateRow', "{{$list['gridIndex']}}" , data);
                        var obj = $.CurrentNavtab.find('#berthorder-detail-table').data('allData');
                        obj["{{$list['gridIndex']}}"] = data;
                        $.CurrentNavtab.find('#berthorder-detail-table').datagrid('reload',  {data:obj});
                        BJUI.dialog('closeCurrent','');
                    }
                }
            })
        }
    </script>


