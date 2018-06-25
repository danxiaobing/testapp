<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">
        <form id="send_versioneditfrom"   method="POST" class="datagrid-edit-form" data-data-type="json" >
            <input type="hidden" id="sendversion-detail-data" name="sendversion-detail-data" value="">
            <input type="hidden" name="id" value="{{ $id }}">
            <!--base message start-->
            <fieldset>
                <legend>汇签版本信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">编号：</label>
                    <div class="row-input">
                            <input type="text" name="versionno" value="{{$versionno or '系统自动生成'}}" readonly>
                        </div>
                    <label class="row-label">名称：</label>
                    <div class="row-input required">
                        <input type="text" name="versionname" value="{{$versionname}}" placeholder="方案名称" data-rule="required" @if( $status!= '' && $status !=3) readonly @endif ></div>

                    <label class="row-label">状态</label>
                    <div class="row-input required">
                        <input type="hidden" name="status" value="{{$status}}" >
                        <select data-size="5"  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%"  disabled="" >
                            <option value="1" @if($status == 1) selected @endif>发布</option>
                            <option value="2" @if($status == 2) selected @endif>停用</option>
                            <option value="3" @if($status == 3) selected @endif>未发布</option>
                        </select>    
                    </div>
                    <label class="row-label">适用单据</label>
                    <div class="row-input required">
                        <select data-size="5" name="versiontype"  data-toggle="selectpicker" data-live-search="true" data-rule="required" data-width="100%" @if( $status!= '' && $status !=3) disabled @endif >
                            <option value="1" @if($versiontype == 1) selected @endif>仓储合同</option>
                            <option value="2" @if($versiontype == 2) selected @endif>靠泊装卸合同</option>
                        </select>
                        {{--<input type="hidden" name="versiontype" value="{{$versiontype}}">--}}
                    </div>
<!--                     <label class="row-label">版本号</label>
                    <div class="row-input required">
                        <input type="text" name="versionshow" value="@if($versionshow){{$versionshow}}@else 1 @endif" placeholder="版本" data-rule="required" readonly="readonly">
                    </div> -->
                   
                </div>
                <br></fieldset>
            <!--base message end-->
            <!--project start-->
            <div class="remarks">
            <fieldset>
                <legend>评审部门详情</legend>
            <div class="table-edit">
            <table class="table table-bordered" id="sendversion-detail-table" data-toggle="datagrid" data-options="{
                        width:'100%',
                        tableWidth:'99%',
                        filterThead:false,
                        @if($status == 3)
                        showToolbar: true,
                        toolbarItem: 'del',
                        toolbarCustom:$.CurrentNavtab.find('#sendversion_tb'),
                        @endif
                        local: 'local',
                        dataUrl: 'sendversion/sinkdeail/vid/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        fullGrid:true,
                        linenumberAll: true
                    }">
                <thead>
                    <tr data-options="{name:'sysno'}">
                        <th data-options="{name:'departmentname',align:'center'}">评审部门</th>                 
                        <th data-options="{name:'memo',align:'center'}">备注</th>
                    </tr>
                </thead>
            </table>

            </div>
            </fieldset>
            <!--project end-->

            <div class="text-center btns-user">

<!--                 <button id="versionsubmit1" type="button" onclick="bookshipinsubmit(2)" class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp; -->
                <button id="send_versionsubmit2" type="button"   class="btn btn-success btn-lg">保存</button>&nbsp;&nbsp;&nbsp;

            </div>
        </form>
    </div>
</div>
@if($status == 3)
<div id="sendversion_tb">
    <button type="button" class="btn btn-blue" onclick="add()"><i class="fa fa-plus"></i> 添加</button>
</div>
@endif
<script type="text/javascript">
    function add() {
        BJUI.dialog({
            id: 'add-version-deail',
            url: '/Sendversion/sinkedit/',
            type: 'POST',
            title: '部门信息',
            width: 700,
            height: 300,
            mask:true
        });

        return;
    }

    $('#send_versionsubmit2').click(function(){
        var Obj = $.CurrentNavtab.find("#sendversion-detail-table").data('allData');

        if(Obj.length<1){
            BJUI.alertmsg('error','请添加评审部门!');
            return;
        }

        $.CurrentNavtab.find("#sendversion-detail-data").val(JSON.stringify(Obj));
        BJUI.ajax('ajaxform', {
            url: '{{  $action }}',
            form: $.CurrentNavtab.find('#send_versioneditfrom'),
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('closeCurrentTab', '');
                BJUI.navtab('refresh', 'menu430');
            }
        });
    });
</script>
