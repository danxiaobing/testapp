<div class="bjui-pageContent">
    <div style="padding:50px 0;width: 90%;margin: 0 auto;">

        <form id="stockshipindeclare-form" action="/stockshipin/adddeclare" method="POST"
              class="datagrid-edit-form" data-data-type="json"
              data-validator-option="{stopOnError:false,timely:false}">
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="detail" id="declare_edit_detail" value="">
            <input type="hidden" id="declare_data" value="{{$detailData}}">
            <!--base message start-->
            <fieldset>
                <legend>入库单信息</legend>
                <br>
                <div class="bjui-row col-3">
                    <label class="row-label">入库单号</label>

                    <div class="row-input">
                        <input type="text" name="stockinno" value="@if($stockinno){{$stockinno}}@else{{系统自动生成}}@endif"
                               disabled>
                    </div>
                    <label class="row-label">入库日期</label>

                    <div class="row-input required">
                        <input type="text" name="stockindate" disabled
                               value="@if($stockindate){{date('Y-m-d',strtotime($stockindate))}}@else{{date('Y-m-d')}}@endif"
                               data-rule="required;date">
                    </div>
                    <label class="row-label">单据状态</label>
                    <div class="row-input required">
                    <select data-toggle="selectpicker" id="bar_stockinstatus" data-width="100%"
                            name="bar_stockinstatus" disabled>
                        <option value="1" @if($stockinstatus==1) selected @endif>新建</option>
                        <option value="2" @if($stockinstatus==2) selected @endif>暂存</option>
                        <option value="3" @if($stockinstatus==3) selected @endif>待审核</option>
                        <option value="4" @if($stockinstatus==4) selected @endif>已完成</option>
                        <option value="5" @if($stockinstatus==5) selected @endif>作废</option>
                        <option value="6" @if($stockinstatus==6) selected @endif>退回</option>
                    </select>
                    </div>
                    <label class="row-label">客户</label>
                    <div class="row-input required">
                        <input type="text" value="{{$customername}}" disabled>
                    </div>
                    <label class="row-label">预约单号</label>

                    <div class="row-input required">
                        <input type="hidden" name="booking_in_sysno"
                               value="@if($booking_in_sysno){{$booking_in_sysno}}@else{{''}}@endif">
                        <input type="text" name="bookingin_no"
                               value="@if($bookingin_no){{$bookingin_no}}@else{{''}}@endif" disabled>
                    </div>
                    <label class="row-label">合同编号</label>
                    <div class="row-input required">
                        <input type="hidden" name="contract_sysno"
                               value="@if($contract_sysno){{$contract_sysno}}@else{{1}} @endif">
                        <input type="text" name="contractno"
                               value="@if($contractno){{$contractno}}@else{{''}}@endif" disabled>
                    </div>
                    <label class="row-label">客服专员</label>
                    <div class="row-input">
                        <input type="text" value="{{$cs_employeename}}" disabled>
                    </div>
                    <label class="row-label">质计</label>
                    <div class="row-input">
                        <input type="text" value="{{$zj_employeename}}" disabled>
                    </div>
                    <label class="row-label">仓储</label>
                    <div class="row-input">
                        <input type="text" value="{{$cc_employeename}}" disabled>
                    </div>
                    <label class="row-label">类别</label>
                    <div class="row-input">
                        <select name="" data-toggle="selectpicker"  data-width="100%" disabled>
                            <option value="1" @if($stockintype==1) selected @endif>船入库</option>
                            <option value="2" @if($stockintype==2) selected @endif>车入库</option>
                            <option value="3" @if($stockintype==3) selected @endif>管入库</option>
                        </select>
                    </div>

                </div>
                <br>
            </fieldset>
            <!--base message end-->
            <br><br>
            <!--project start-->
            <div class="remarks">
                <fieldset>
                    <legend>报关信息明细</legend>
                    <table class="table table-bordered" id="stockshipindeclare-detail-table" height="40+50*{{count($list)}}"
                           data-toggle="datagrid" data-options="{
                        filterThead:false,
                        showToolbar: true,
                        @if(!$type)
                        toolbarCustom: $.CurrentNavtab.find('#stockshipindeclare_edit_btn1'),
                        @endif
                        editMode:false,
                        data: '{{$detailData}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        paging: false,
                        linenumberAll: true,
                        fullGrid:true,
                        fieldSortable:false,
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'goodsname',align:'center'}">产品名称</th>
                            <th data-options="{name:'customername',align:'center',}">代理报关公司</th>
                            <th data-options="{name:'takegoodsnum',align:'center'}">提单量</th>
                            <th data-options="{name:'beqty',align:'center'}">商检数量</th>
                            <th data-options="{name:'declaration',align:'center'}">报关单号</th>
                            <th data-options="{name:'storagetankname',align:'center'}">进货罐号</th>
                            <th data-options="{name:'release_num',align:'center'}">放行报关数量</th>
                            <th data-options="{name:'unrelease_num',align:'center'}">未报关数量</th>
                            <th data-options="{name:'goods_sysno',align:'center',hide:'ture' }">产品id</th>
                            <th data-options="{name:'storagetank_sysno',align:'center',hide:'ture'}">进货罐号</th>
                            <th data-options="{name:'stockin_sysno',align:'center',hide:'ture'}">入库单id</th>
                            <th data-options="{name:'customer_sysno',align:'center',hide:'ture'}">客户id</th>
                        </tr>
                        </thead>
                    </table>

                </fieldset>

            </div>
            <br>
            <!--upload start-->
            <fieldset class="" id="shipindeclare_release">
                                <!-- <fieldset class="customerfieldset" id="shipindeclare_release"> -->

            <legend>上传放行单</legend>
                        @if(!$type)
                            <input type="file"  data-name="attachment[]" data-toggle="webuploader" data-options="
                            {
                                pick: {label: '点击选择图片'},
                                server: '/attachment/uploadjson',
                                fileNumLimit: 10,
                                formData: {module:'stockshipin',action:'declare_release',doc_sysno:'{{$id}}'},
                                required: false,
                                uploaded: '{{ $uploaded1 }}',
                                basePath: '/attachment/preview/id/',
                                deletePath:'/attachment/deljson/',
                                accept: {
                                    title: '图片',
                                    extensions: 'jpg,png,pdf,txt',
                                    mimeTypes: '.jpg,.png,.pdf,.txt'
                                }
                            }"
                        >
                            @else
                                <ul class="filelist picthuild" id="boship_sample_div">
                                @foreach($uploadess as $v)
                                <li class="uploaded" >
                                    <p class="imgWrap" style="cursor:pointer;" data-toggle="dialog" data-options="{id:'bjui-dialog-view-upload-image', image:'/attachment/preview/id/{{$v}}', width:800, height:500, mask:true, title:'查看图片'}">
                                        <img src="/attachment/preview/id/{{$v}}">
                                    </p>
                                </li>
                                @endforeach
                                </ul>
                            @endif
            </fieldset>
            <!--upload end-->

            <br><br>
            <div class="text-center btns-user">
            @if(!$type)
                <button type="button" onclick="stockshipindeclaresubmit()"
                        class="btn btn-green btn-lg">保存
                </button>&nbsp;&nbsp;&nbsp;
            @endif
                <button type="button" onclick="declare_close()" class="btn btn-gray btn-lg">
                    返回
                </button>
            </div>
@if(!$type)
<div id="stockshipindeclare_edit_btn1">
    <button type="button" class="btn btn-blue" data-icon='plus' onclick="declare_add()">添加</button>

    <button type="button" class="btn btn-green" data-icon='edit' onclick="declare_edit()">编辑</button>

    <button type="button" class="btn btn-red" data-icon='close' onclick="declare_dels()">删除</button>
</div>
@endif
<script src="/static/common/js/common.js"></script>
<script>
    function declare_edit() {

        var receiptdata = $.CurrentNavtab.find('#stockshipindeclare-detail-table').data('selectedDatas');

        if (receiptdata == undefined || receiptdata.length == 0 || receiptdata=='') {
            BJUI.alertmsg('warn', "请先选择报关明细", {displayPosition: 'middlecenter', displayMode: 'fade'});
        } else {
            BJUI.dialog({
                id: 'stockin-declareedit-{{$id}}',
                data: {data:receiptdata[0]},
                type: 'POST',
                url: '/stockshipin/declaredetailedit/',
                title: '报关信息明细',
                width: 700,
                height: 600,
                mask: true
            });
        }
        return;
    }

    function declare_add()
    {
        // var receiptdata = $.CurrentNavtab.find('#stockshipindeclare-detail-table').data('allData');
        var declare_data = JSON.parse($.CurrentNavtab.find('#declare_data').val());
        if(declare_data[0]!=undefined){
            declare_data = declare_data[0];
        }

        var data =  {};
        // console.log(JSON.parse($('#declare_data').val()));
        // return;
        data['goodsname'] = declare_data ? declare_data['goodsname'] : '';
        data['goods_sysno'] = declare_data ? declare_data['goods_sysno'] : '';
        data['storagetank_sysno'] = declare_data ? declare_data['storagetank_sysno'] : '';
        data['unrelease_num'] = declare_data ? declare_data['unrelease_num'] : 0;
        data['beqty'] = declare_data ? declare_data['beqty'] : '';
        data['stockin_sysno'] = declare_data ? declare_data['stockin_sysno'] : '';
        data['customername'] = declare_data ? declare_data['customername'] : '';
        data['customer_sysno'] = declare_data ? declare_data['customer_sysno'] : '';
        data['customer_sysno'] = declare_data ? declare_data['customer_sysno'] : '';

        data['takegoodsnum'] = declare_data ? declare_data['takegoodsnum'] : 0;

        // console.log(declare_data[0]);return;
        BJUI.dialog({
            id: 'stockin-declareedit-{{$id}}',
            data: {data:data},
            type: 'POST',
            url: '/stockshipin/declaredetailedit/',
            title: '报关信息明细',
            width: 700,
            height: 600,
            mask: true
        }); 
    }

    function declare_dels()
    {
        var selectdata = $.CurrentNavtab.find('#stockshipindeclare-detail-table').data('selectedDatas');

        if (selectdata == undefined) {
            BJUI.alertmsg('warn', BJUI.getRegional('datagrid.selectMsg'));
            return;
        } else {
            var allData = $.CurrentNavtab.find("#stockshipindeclare-detail-table").data('allData');
            for (var i = selectdata.length - 1; i >= 0; i--) {
                allData = allData.remove(selectdata[i].gridIndex);
            }
            $.CurrentNavtab.find('#stockshipindeclare-detail-table').datagrid('reload', {data: allData});
        }

    }

    function stockshipindeclaresubmit()
    {
        var data = $.CurrentNavtab.find('#stockshipindeclare-detail-table').data('allData');
        var releanum = 0;
        for (var i = data.length - 1; i >= 0; i--) {
            if(data[i].release_num== null || data[i].declaration == null || data[i].storagetankname == null){
               BJUI.alertmsg('warn', "请完善报关明细", {displayPosition: 'middlecenter', displayMode: 'fade'});
                return;
            }
            var releanum = releanum+parseFloat(data[i].release_num); 
        }

        if(data[data.length - 1].unrelease_num<0)
        {
           BJUI.alertmsg('warn', "报关数量超出", {displayPosition: 'middlecenter', displayMode: 'fade'});
            return; 
        }

        var detailData = JSON.stringify(data);

        $('#declare_edit_detail').val(detailData);
        if (!$.CurrentNavtab.find('#shipindeclare_release').find('.uploadBtn').hasClass('disabled') ) {
                            if ( $.CurrentNavtab.find('#shipindeclare_release').find(".filelist > li").length > 0) {
                                BJUI.alertmsg('warn', '请先上传放行单！',{displayPosition:'middlecenter',displayMode:'fade'})
                                return;
                            }else{
                                BJUI.alertmsg('warn', '请先提交放行单！',{displayPosition:'middlecenter',displayMode:'fade'})
                                return;
                            }   
        }

        // if($.CurrentNavtab.find('#shipindeclare_release').find(".filelist > li").length !=data.length)
        // {
        //         BJUI.alertmsg('warn', '请上传和明细相同数量的放行单！',{displayPosition:'middlecenter',displayMode:'fade'})
        //         return;
        // }
        var outqty = 0;
        $.ajax({
            url:'/stockshipin/AjaxgetoutQty',
            type:'POST',
            data:{stockin_sysno:"{{$id}}"},
            async:false,
            success:function(options){
                outqty = options;
            }
        });

        if(releanum<outqty){
            BJUI.alertmsg('warn', '当前放行量不能小于已出库量,当前入库单已出库'+outqty+'吨',{displayPosition:'middlecenter',displayMode:'fade'})
            return;
        }
        // return;
        BJUI.ajax('ajaxform', {
            url: '/stockshipin/adddeclare',
            form: $.CurrentNavtab.find('#stockshipindeclare-form'),
            type: 'POST',
            validate: true,
            loadingmask: true,
            okCallback: function (json, options) {
                BJUI.navtab('reloadFlag', 'navab466');
                BJUI.navtab('closeCurrentTab', '');
            }
        });
    }

    function declare_close()
    {
        BJUI.navtab('reloadFlag', 'navab466');
        BJUI.navtab('closeCurrentTab', '');
    }

</script>