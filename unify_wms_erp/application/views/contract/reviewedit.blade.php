<div class="bjui-pageContent">
    <div style="padding:10px 0;width: 924px;margin: 0 auto;">
        <form id="contractform" action="{{$action}}" method="POST" class="datagrid-edit-form" data-toggle="validate"
              data-data-type="json">
            <input type="hidden" name="id" value="{{$id}}">
            <!--base message start-->
            <fieldset>
                <legend>合同信息</legend>
                <br>

                <div class="bjui-row col-3">
                    <label class="row-label">合同编号</label>

                    <div class="row-input">
                        <input type="text" name="contractno"
                               value="@if($contractnodisplay) {{$contractnodisplay}} @else {{系统自动生成}} @endif" disabled>
                    </div>
                    <label class="row-label">合同日期</label>

                    <div class="row-input required">
                        <input type="text" name="contractdate"
                               value="@if($contractdate){{date('Y-m-d',strtotime($contractdate))}}@else{{date('Y-m-d')}}@endif"
                               data-toggle="datepicker" data-rule="required;date" disabled="disabled"></div>
                    <label class="row-label">单据状态</label>

                    <div class="row-input required">
                        <select name="contractstatus" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled="disabled">
                            <option value="1" @if($contractstatus == 1) selected @endif>新建</option>
                            <option value="2" @if($contractstatus == 2) selected @endif>暂存</option>
                            <option value="3" @if($contractstatus == 3) selected @endif>评审中</option>
                            <option value="4" @if($contractstatus == 4) selected @endif >待审核</option>
                            <option value="5" @if($contractstatus == 5) selected @endif>已审核</option>
                            <option value="6" @if($contractstatus == 6) selected @endif>退回</option>
                            <option value="7" @if($contractstatus == 7) selected @endif>作废</option>

                        </select>
                    </div>
                    <label class="row-label">客户</label>

                    <div class="row-input required">
                        <input type="hidden" name="obj.customer_sysno" value="{{$customer_sysno}}">
                        <input type="text" name="obj.customername" value="{{$customername}}"
                               data-rule="required" data-toggle="findgrid" data-options="{
                        group: 'obj',
                        include: 'customerclass:customerclass,customerrelation:customerrelation,customername:customername,customer_sysno:sysno,customercredit:customercredit,customerabbreviation:customerabbreviation,customerterm:customerterm',
                        dialogOptions: {width:'800',height:'500',title:'客户资料',maxable:true,resizable:true,mask:true},
                        gridOptions: {
                            width:'100%',
                            height:'100%',
                            tableWidth:'99.8%',
                            local: 'local',
                            paging: {pageSize:20},
                            dataUrl: '/customer/listAllJson',
                            columns: [
                                {name:'sysno', label:'id',hide:true},
                                {name:'customername', label:'客户名称'},
                                {name:'customerrelation',label:'是否关联方',render:function(value){if(value=='1') {return '是'} else{return '否'} }},
                                {name:'customerabbreviation', label:'客户简称'},
                                {name:'customercredit',label:'授信额度'}
                            ],
                            showLinenumber:false
                        },
                        afterSelect:function(data) {
                           {{--console.log(data['customerrelation']);--}}
                           var customerrelation = $('#customerrelation').val();
                           if(customerrelation == 0){
                                $('#customerrelation').val('否');
                           }else if(customerrelation == 1){
                                $('#customerrelation').val('是');
                           }
                        }
                    }" placeholder="点放大镜按钮查找" disabled="disabled"></div>

                    <label class="row-label">客户性质</label>

                    <div class="row-input required">
                        <select name="obj.customerclass" data-toggle="selectpicker" data-rule="required"
                                data-width="100%"
                                data-live-search="true" data-size="10" disabled="disabled">
                            <option value="">请选择</option>
                            <option value="1" @if($customerclass == 1) selected @endif>重要</option>
                            <option value="2" @if($customerclass == 2) selected @endif>一般</option>
                            <option value="3" @if($customerclass == 3) selected @endif>战略</option>
                        </select>
                    </div>

                    <label class="row-label">关联方</label>

                    <div class="row-input">
                        @if($isbrother == 1)
                            <input name="obj.customerrelation" id="customerrelation" type="text" value="是" disabled>
                        @elseif($isbrother == "0")
                            <input name="obj.customerrelation" id="customerrelation" type="text" value="否" disabled>
                        @else
                            <input name="obj.customerrelation" id="customerrelation" type="text" value="" disabled>
                        @endif
                    </div>

                    <label class="row-label">授信额度</label>

                    <div class="row-input required">
                        <input type="text" name="obj.customercredit" value="{{$customercredit}}" disabled="disabled">
                    </div>
                    <label class="row-label">授信期限</label>

                    <div class="row-input required">
                        <input type="text" name="obj.customerterm" size="14" value="{{$customerterm}}"
                               disabled="disabled">
                        <a href="javascript:void(0);">月</a>
                    </div>
                    <label class="row-label">付款方式</label>

                    <div class="row-input">
                        <select name="settlement_sysno" data-nextselect="#cs_employeename" data-toggle="selectpicker"
                                data-rule="required" data-width="100%" data-live-search="true" data-size="10"
                                disabled="disabled">
                            <option value="">请选择</option>
                            @foreach($settlementlist as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $settlement_sysno) selected @endif>{{$item['settlementname']}}</option>
                            @endforeach
                        </select>
                    </div>
                   {{-- <label class="row-label">盖章状态</label>

                    <div class="row-input">
                        <select name="isseal" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled="disabled">
                            <option value="666">请选择</option>
                            <option value="0" @if($isseal=='0') selected @endif>未盖章</option>
                            <option value="1" @if($isseal=='1') selected @endif>已盖章</option>
                        </select>
                    </div>--}}

                    <label class="row-label">检验要求</label>

                    <div class="row-input">
                        <input type="text" name="testrequire" value="@if($testrequire){{$testrequire}}@endif"
                               disabled="disabled">
                    </div>

                    <label class="row-label">代理</label>

                    <div class="row-input ">
                        <input type="text" name="shipproxy" value="@if($shipproxy){{$shipproxy}}@endif"
                               disabled="disabled">
                    </div>

                    <label class="row-label">商检</label>

                    <div class="row-input ">
                        <input type="text" name="testrequirebusiness"
                               value="@if($testrequirebusiness){{$testrequirebusiness}}@endif" disabled="disabled">
                    </div>

                    <label class="row-label">业务员</label>

                    <div class="row-input">
                        <select name="saleemployee_sysno" data-toggle="selectpicker" data-rule="required"
                                data-width="100%" data-live-search="true" data-size="10" disabled="disabled">
                            <option value="">请选择</option>
                            @foreach($saleemployee['list'] as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $saleemployee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">客服专员</label>

                    <div class="row-input">
                        <select name="csemployee_sysno" data-toggle="selectpicker" data-rule="required"
                                data-width="100%" data-live-search="true" data-size="10" disabled="disabled">
                            <option value="">请选择</option>
                            @foreach($csemployee['list'] as $item)
                                <option value="{{$item['sysno']}}"
                                        @if($item['sysno'] == $csemployee_sysno) selected @endif>{{$item['employeename']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="row-label">租罐方式</label>

                    <div class="row-input">
                        <select name="contracttype" data-toggle="selectpicker" data-rule="required" data-width="100%"
                                data-live-search="true" data-size="10" disabled="disabled">
                            <option value="0">请选择</option>
                            <option value="1" @if($contracttype ==1) selected @endif>长约</option>
                            <option value="2" @if($contracttype ==2) selected @endif>短约</option>
                            <option value="3" @if($contracttype ==3) selected @endif>包罐</option>
                            <option value="4" @if($contracttype ==4) selected @endif>包罐容</option>
                            <option value="5" @if($contracttype ==5) selected @endif>靠泊装卸</option>
                        </select>
                    </div>

                    <label class="row-label">合同期限</label>

                    <div class="row-input">
                        <input type="text" name="contractstartdate"
                               value="@if($contractstartdate){{date('Y-m-d',strtotime($contractstartdate))}}@else{{date('Y-m-d')}}@endif"
                               data-toggle="datepicker" data-rule="required;date" disabled="disabled">
                    </div>
                    <div class="row-input">
                        <input type="text" name="contractenddate"
                               value="@if($contractenddate){{date('Y-m-d',strtotime($contractenddate))}}@else{{date('Y-m-d')}}@endif"
                               data-toggle="datepicker" data-rule="required;date" disabled="disabled">
                    </div>

                    <br>
                    <label class="row-label">合同备注</label>

                    <div class="row-input">
                        <textarea cols="76" name="contractmemo" rows="3" disabled="disabled">{{$contractmemo}}</textarea>
                    </div>
                </div>
            </fieldset>
            <div class="remarks">
                <fieldset>
                    <legend>合约明细</legend>
            <table class="table table-bordered" id="goods-detail-table" data-toggle="datagrid" data-options="{
                    height:'100%',
                    filterThead:false,
                    showToolbar: false,
                    toolbarItem:'add,|,del,|,refresh',
                    local: 'local',
                    addLocation: 'last',
                    dataUrl: '/contract/goodsdatail/id/{{$id}}',
                    dataType: 'json',
                    jsonPrefix: 'obj',
                    editMode: false,
                    editUrl: '/contract/goodsaddoredit',
                    paging: false,
                    linenumberAll: true,
                    fullGrid:true
                }">
                <thead>
            @if($contracttype == 3 || $contracttype==4)
                <tr data-options="{name:'sysno'}">
                    <th data-options="{name:'goodsname',align:'center'}">品名</th>
                    <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                        货物性质
                    </th>
                    <th data-options="{name:'qualityname',align:'center'}">规格</th>
                    <th data-options="{name:'unitname',align:'center'}">计量单位</th>
                    <th data-options="{name:'contractrate',align:'center'}">合约损率</th>
            
                    <th data-options="{name:'storagetankname',align:'center', @if($contracttype==4) hide:true @endif}">储罐编号</th>
                    <th data-options="{name:'capacity',align:'center'}">容量(m³)</th>
                    <th data-options="{name:'overcapacity',align:'center'}">溢罐吨数</th>
                    <th data-options="{name:'yearqty',align:'center'}">中转量(吨)</th>
                    <th data-options="{name:'memo',align:'center'}">备注</th>
                </tr>
            @elseif($contracttype < 3)

                <tr data-options="{name:'sysno'}">
                    <th data-options="{name:'goodsname',align:'center'}">品名</th>
                    <th data-options="{name:'qualityname',align:'center'}">规格</th>
                    <th data-options="{name:'goodsnature',align:'center',render:function(value){if(value=='1') {return '保税'} else if(value=='2') {return '外贸'} else if(value=='3') {return '内贸转出口'} else if(value=='4') {return '内贸内销'}}}">
                        货物性质
                    </th>
                    <th data-options="{name:'goodsqty',align:'center'}">数量</th>
                    <th data-options="{name:'goodsdate',align:'center'}">预计到货日期</th>
                    <!-- <th data-options="{name:'lastamount',align:'center'}">超期费</th> -->
                    <th data-options="{name:'firstlossrate',align:'center'}">首期损耗率‰</th>
                    <th data-options="{name:'lastlossrate',align:'center'}">超期损耗率‰</th>
                    <th data-options="{name:'memo',align:'center',hide:true}">备注</th>
                </tr>
            @elseif($contracttype==5)
                <tr data-options="{name:'sysno'}">
                    <th data-options="{name:'goodsname',align:'center'}">品名</th>
                    <th data-options="{name:'isladder',align:'center',render:function(value){if(value && value==1){return '是';}else{return '否';}}}">是否启用阶梯价</th>
                    <th data-options="{name:'ladderstart',align:'center',render:function(value,data){ if(data.isladder == 0 || !value || value==0){return '--';}}}">阶梯内最小吨数（吨）</th>
                    <th data-options="{name:'ladderend',align:'center',render:function(value,data){if(data.isladder == 0 || !value || value==0){return '--';}}}">阶梯内最大吨数（吨）</th>
                    <th data-options="{name:'berthcostforeign',align:'center',render:function(value){if(!value || value==0){return '--';}}}">外贸卸船（元/吨）</th>
                    <th data-options="{name:'berthcostdomestic',align:'center',render:function(value){if(!value || value==0){return '--';}}}">内贸卸船（元/吨）</th>
                    <th data-options="{name:'berthcost',align:'center',render:function(value){if(!value || value==0){return '--';}}}">装船（元/吨）</th>
                    <th data-options="{name:'memo',align:'center'}">备注</th>
                    <th data-options="{name:'goods_sysno',align:'center',hide:'true'}">货品id</th>
                     <th data-options="{name:'berthcosttype',align:'center',hide:'true',render:function(value){if(value=='1'){return '装货'}else{return '卸货'}}}">靠泊装卸收费类型</th>
                </tr>
            @endif
                </thead>
            </table>
        </fieldset>
    </div>
            <br>
            <div class="remarks">
                <fieldset>
                    <legend>评审状态</legend>
                        <table class="table table-bordered" id="othercost-detail-table" data-toggle="datagrid" data-options="{
                        height:'100%',
                        filterThead:false,
                        showToolbar: false,
                        toolbarItem:'refresh',
                        local: 'local',
                        addLocation: 'last',
                        dataUrl: '/contract/reviewdetail/id/{{$id}}',
                        dataType: 'json',
                        jsonPrefix: 'obj',
                        editMode: false,
                        paging: false,
                        mask:true,
                        linenumberAll: true,
                        fullGrid:true
                    }">
                        <thead>
                        <tr data-options="{name:'sysno'}">
                            <th data-options="{name:'departmnetname',align:'center'}">部门</th>
                            <th data-options="{name:'reviewstatus',align:'center',render:function(value){if(value=='2'){return '评审通过'}else if(value=='3'){return '评审未通过'}else{return '待评审'}}}">
                                评审状态
                            </th>
                            <th data-options="{name:'reviewmemo',align:'center'}">评审意见</th>
                            <th data-options="{name:'reviewemployeename',align:'center'}">评审签名</th>
                            <th data-options="{name:'updated_at',align:'center'}">评审日期</th>
                        </tr>
                        </thead>
                    </table>
                </fieldset>
            </div>
            <fieldset>
                <legend>评审操作</legend>
                <div class="bjui-row col-3">
                    <label class="row-label">评审意见：</label>

                    <div class="row-input">
                        <textarea cols="80" name="reviewmemo" id="reviewmemo" rows="5" value="{{$reviewmemo}}"></textarea>
                    </div>
                </div>
                <div class="text-center btns-user">
                    @if($reved==0)
                    <button id="reviewsubmitnoyes" type="button" class="btn btn-green btn-lg">评审通过</button>
                    <button id="reviewsubmitno" type="button" class="btn btn-danger btn-lg">评审不通过</button>
                    @endif
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#reviewsubmitnoyes').click(function () {
        var rmo = $('#reviewmemo').val();
        var cid ={{$id}};
        BJUI.ajax('doajax', {
            url: '/contract/reviewpost',
            data: {'status': '2', 'reviewmemo': rmo, 'cid': cid},
            loadingmask: true,
            okCallback:function(json, options){
                BJUI.navtab('reloadFlag','navab252,navab254');
                BJUI.dialog('closeCurrent', '')
            }
        });
    });
    $('#reviewsubmitno').click(function () {
        var rmo = $('#reviewmemo').val();
        var cid ={{$id}} ;

        if (rmo == '') {
            BJUI.alertmsg("warn", '请填写评审意见！');
            return false;
        } else {
            BJUI.ajax('doajax', {
                url: '/contract/reviewpost',
                data: {'status': '3', 'reviewmemo': rmo, 'cid': cid},
                loadingmask: true,
                okCallback:function(json, options){
                    BJUI.navtab('reloadFlag','navab252,navab254');
                    BJUI.dialog('closeCurrent', '')
                }
            });
        }
    });
</script>